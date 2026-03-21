<?php
require_once dirname(__DIR__) . '/model/AsignacionModel.php';
require_once dirname(__DIR__) . '/model/FichaModel.php';
require_once dirname(__DIR__) . '/model/InstruCompetenciaModel.php';
require_once dirname(__DIR__) . '/model/DetalleAsignacionModel.php';

class AsignacionController
{
    private $db;

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $rol     = $_SESSION['rol'] ?? null;
        $cent_id = $_SESSION['centro_id'] ?? null;
        $user_id = $_SESSION['id'] ?? null;

        $coord_id = null;
        if ($rol === 'coordinador' && $user_id) {
            $db = Conexion::getConnect();
            $stmt = $db->prepare("SELECT coord_id FROM COORDINACION WHERE coordinador_actual = :uid AND estado = 1 LIMIT 1");
            $stmt->execute([':uid' => $user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $coord_id = $row['coord_id'] ?? null;
        }

        $model = new AsignacionModel(null, null, null, null, null, null, null);
        $asignaciones = $model->readAll($cent_id, $coord_id);
        $this->sendResponse($asignaciones);
    }

    /**
     * Valida cada día seleccionado contra las 6 restricciones.
     * Retorna un array de errores; vacío = todo OK.
     */
    private function validateDias(array $diasSeleccionados, $asig_id = null)
    {
        $errors = [];
        $today = date('Y-m-d');

        foreach ($diasSeleccionados as $i => $dia) {
            $fecha = $dia['fecha'] ?? '';
            $horaIni = $dia['hora_ini'] ?? '';
            $horaFin = $dia['hora_fin'] ?? '';
            $label = date('d/m/Y', strtotime($fecha));

            // 1. Coherencia cronológica
            if ($horaIni >= $horaFin) {
                $errors[] = "[$label] La hora de inicio ($horaIni) debe ser menor a la hora de fin ($horaFin)";
            }

            // 2. Jornada institucional (06:00 - 22:00)
            if ($horaIni < '06:00' || $horaFin > '22:00') {
                $errors[] = "[$label] El horario debe estar dentro de la jornada institucional (06:00 AM - 10:00 PM)";
            }

            // 3. Fecha no en el pasado
            if ($fecha < $today) {
                $errors[] = "[$label] No se puede programar en una fecha que ya pasó";
            }
        }

        return $errors;
    }

    /**
     * Valida cruces de horario por cada día contra la BD.
     * Retorna array de conflictos encontrados.
     */
    private function checkDayConflicts(array $diasSeleccionados, $asig_id, $inst_id, $amb_id, $fich_id)
    {
        $allConflicts = [];
        $detalleModel = new DetalleAsignacionModel();

        foreach ($diasSeleccionados as $dia) {
            $conflicts = $detalleModel->checkGlobalConflicts(
                $asig_id,
                $dia['fecha'],
                $dia['hora_ini'],
                $dia['hora_fin']
            );
            if (!empty($conflicts)) {
                $label = date('d/m/Y', strtotime($dia['fecha']));
                foreach ($conflicts as $c) {
                    $c['dia_conflicto'] = $label;
                    $allConflicts[] = $c;
                }
            }
        }

        return $allConflicts;
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $labels = [
            'instructor_inst_id' => 'Instructor',
            'asig_fecha_ini' => 'Fecha de Inicio',
            'asig_fecha_fin' => 'Fecha de Fin',
            'ficha_fich_id' => 'Ficha',
            'ambiente_amb_id' => 'Ambiente',
            'competencia_comp_id' => 'Competencia'
        ];

        foreach ($labels as $field => $label) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->sendResponse(['error' => "El campo '$label' es obligatorio"], 400);
                return;
            }
        }

        // Validación de fecha no pasada (rango general)
        $today = date('Y-m-d');
        if ($data['asig_fecha_ini'] < $today) {
            $this->sendResponse(['error' => 'La fecha de inicio no puede ser menor a la fecha actual (' . $today . ')'], 400);
            return;
        }

        // 1. Obtener programa de la ficha
        $fichaModel = new FichaModel($data['ficha_fich_id']);
        $fichaData = $fichaModel->read()[0] ?? null;
        if (!$fichaData) {
            $this->sendResponse(['error' => 'La ficha seleccionada no existe'], 404);
            return;
        }
        $progId = $fichaData['programa_prog_id'];

        // 2. Validar que el instructor esté habilitado
        if (!InstruCompetenciaModel::isQualified($data['instructor_inst_id'], $progId, $data['competencia_comp_id'])) {
            $this->sendResponse(['error' => 'El instructor no está habilitado para esta competencia en este programa de formación'], 403);
            return;
        }

        $diasSeleccionados = $data['dias_seleccionados'] ?? [];
        if (empty($diasSeleccionados)) {
            $this->sendResponse(['error' => 'Debe seleccionar al menos un día con su respectivo horario'], 400);
            return;
        }

        // --- VALIDACIONES DE FECHAS CLASES VS RANGO ---
        $fechas = array_column($diasSeleccionados, 'fecha');
        $maxFecha = max($fechas);
        if ($data['asig_fecha_fin'] > $maxFecha) {
            // Si las clases no cubren la fecha fin, ajustar la fecha fin según la última clase.
            $data['asig_fecha_fin'] = $maxFecha; 
        } else if ($data['asig_fecha_fin'] < $maxFecha) {
            $this->sendResponse(['error' => "El rango de fechas es insuficiente para el número de clases. La última clase está programada para $maxFecha. Por favor, amplíe el rango."], 400);
            return;
        }

        // --- VALIDAR VIGENCIA DE CERTIFICACIÓN ---
        $db = Conexion::getConnect();
        $stmtVigencia = $db->prepare("SELECT inscomp_vigencia FROM INSTRU_COMPETENCIA WHERE INSTRUCTOR_inst_id = ? AND programa_prog_id = ? AND competencia_comp_id = ?");
        $stmtVigencia->execute([$data['instructor_inst_id'], $progId, $data['competencia_comp_id']]);
        $vigenciaRes = $stmtVigencia->fetch(PDO::FETCH_ASSOC);
        if ($vigenciaRes && !empty($vigenciaRes['inscomp_vigencia'])) {
            if ($vigenciaRes['inscomp_vigencia'] < $data['asig_fecha_fin']) {
                $this->sendResponse(['error' => "La certificación del instructor para esta competencia vence el {$vigenciaRes['inscomp_vigencia']}, no alcanza a cubrir hasta {$data['asig_fecha_fin']}"], 400);
                return;
            }
        }

        // --- CÁLCULO DE HORAS PROPUESTAS ---
        $hoursByMonth = [];
        foreach ($diasSeleccionados as $dia) {
            if (!empty($dia['hora_ini']) && !empty($dia['hora_fin'])) {
                $dateParts = explode('-', $dia['fecha']);
                if (count($dateParts) >= 2) {
                    $year = $dateParts[0];
                    $month = $dateParts[1];
                    $key = "$year-$month";

                    $ini = strtotime($dia['hora_ini']);
                    $fin = strtotime($dia['hora_fin']);
                    $diff = ($fin - $ini) / 3600;
                    
                    if ($diff > 0) {
                        if (!isset($hoursByMonth[$key])) $hoursByMonth[$key] = 0;
                        $hoursByMonth[$key] += $diff;
                    }
                }
            }
        }

        // --- VALIDACIÓN DE HORAS MENSUALES (MAX 160h) ---
        $asigModelCheck = new AsignacionModel();
        foreach ($hoursByMonth as $key => $hours) {
            list($year, $month) = explode('-', $key);
            $currentMonthlyHours = $asigModelCheck->getMonthlyHours($data['instructor_inst_id'], $month, $year, null);
            if (($currentMonthlyHours + $hours) > 160) {
                $this->sendResponse(['error' => "El instructor supera el límite de 160 horas en el mes $month/$year (Lleva: $currentMonthlyHours h, A asignar: $hours h)"], 400);
                return;
            }
        }

        // --- VALIDACIÓN DE HORAS DE COMPETENCIA ---
        require_once dirname(__DIR__) . '/model/CompetenciaModel.php';
        $compModel = new CompetenciaModel($data['competencia_comp_id']);
        $compData = $compModel->read()[0] ?? null;
        if ($compData) {
            $compMaxHours = (float)$compData['comp_horas'];
            $currentAssignedHours = $asigModelCheck->getCompetenceHoursAssigned($data['ficha_fich_id'], $data['competencia_comp_id'], null);
            $totalProposedHours = array_sum($hoursByMonth);

            if (($currentAssignedHours + $totalProposedHours) > $compMaxHours) {
                $this->sendResponse(['error' => "Las horas asignadas superan las horas de la competencia ($compMaxHours h). Ya tiene asignadas $currentAssignedHours h e intentas asignar $totalProposedHours h."], 400);
                return;
            }

            // Alerta 80% (Solo si no viene confirmado)
            $percentage = ($compMaxHours > 0) ? (($currentAssignedHours + $totalProposedHours) / $compMaxHours) * 100 : 100;
            if ($percentage < 80 && !isset($data['confirm_80_percent'])) {
                $this->sendResponse([
                    'warning' => '80_percent_alert',
                    'message' => "El porcentaje de horas programadas (" . round($percentage, 2) . "%) es menor al recomendado (80%). ¿Deseas continuar con la asignación de todos modos?"
                ], 202);
                return;
            }
        }

        // 3. Validar cada día (coherencia, jornada, fecha vigente)
        $dayErrors = $this->validateDias($diasSeleccionados);
        if (!empty($dayErrors)) {
            $this->sendResponse(['error' => implode("\n", $dayErrors)], 400);
            return;
        }

        try {
            $this->db = Conexion::getConnect();
            $this->db->beginTransaction();

            $model = new AsignacionModel(
                null,
                $data['instructor_inst_id'],
                $data['asig_fecha_ini'],
                $data['asig_fecha_fin'],
                $data['ficha_fich_id'],
                $data['ambiente_amb_id'],
                $data['competencia_comp_id']
            );

            $id = $model->create();
            if (!$id) {
                $this->db->rollBack();
                $this->sendResponse(['error' => 'No se pudo crear el registro principal'], 500);
                return;
            }

            // 4. Verificar cruces de horario por cada día
            $conflicts = $this->checkDayConflicts(
                $diasSeleccionados,
                $id,
                $data['instructor_inst_id'],
                $data['ambiente_amb_id'],
                $data['ficha_fich_id']
            );
            if (!empty($conflicts)) {
                $this->db->rollBack();
                $this->sendResponse(['error' => 'Cruce de horario detectado', 'details' => $conflicts], 409);
                return;
            }

            // 5. Guardar detalles por día
            foreach ($diasSeleccionados as $dia) {
                $detalleModel = new DetalleAsignacionModel(
                    $id,
                    $dia['fecha'],
                    $dia['hora_ini'],
                    $dia['hora_fin']
                );
                $detalleModel->create();
            }

            $this->db->commit();
            $this->sendResponse(['message' => 'Asignación creada', 'id' => $id]);
        } catch (Throwable $e) {
            if (isset($this->db) && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error in store: " . $e->getMessage());
            $this->sendResponse(['error' => 'Error de base de datos: ' . $e->getMessage()], 500);
        }
    }

    public function show()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }
        $model = new AsignacionModel($id, null, null, null, null, null, null);
        $asig = $model->read($cent_id);
        $this->sendResponse($asig[0] ?? ['error' => 'No encontrada'], $asig ? 200 : 404);
    }

    public function update()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['asig_id'])) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }

        $labels = [
            'instructor_inst_id' => 'Instructor',
            'asig_fecha_ini' => 'Fecha de Inicio',
            'asig_fecha_fin' => 'Fecha de Fin',
            'ficha_fich_id' => 'Ficha',
            'ambiente_amb_id' => 'Ambiente',
            'competencia_comp_id' => 'Competencia'
        ];

        foreach ($labels as $field => $label) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->sendResponse(['error' => "El campo '$label' es obligatorio"], 400);
                return;
            }
        }

        $today = date('Y-m-d');
        if ($data['asig_fecha_ini'] < $today) {
            $this->sendResponse(['error' => 'La fecha de inicio no puede ser menor a la fecha actual (' . $today . ')'], 400);
            return;
        }

        $fichaModel = new FichaModel($data['ficha_fich_id']);
        $fichaData = $fichaModel->read()[0] ?? null;
        if (!$fichaData) {
            $this->sendResponse(['error' => 'La ficha seleccionada no existe'], 404);
            return;
        }
        $progId = $fichaData['programa_prog_id'];

        if (!InstruCompetenciaModel::isQualified($data['instructor_inst_id'], $progId, $data['competencia_comp_id'])) {
            $this->sendResponse(['error' => 'El instructor no está habilitado para esta competencia en este programa de formación'], 403);
            return;
        }

        $diasSeleccionados = $data['dias_seleccionados'] ?? [];
        if (empty($diasSeleccionados)) {
            $this->sendResponse(['error' => 'Debe seleccionar al menos un día con su respectivo horario'], 400);
            return;
        }

        // --- VALIDACIONES DE FECHAS CLASES VS RANGO ---
        $fechas = array_column($diasSeleccionados, 'fecha');
        $maxFecha = max($fechas);
        if ($data['asig_fecha_fin'] > $maxFecha) {
            $data['asig_fecha_fin'] = $maxFecha;
        } else if ($data['asig_fecha_fin'] < $maxFecha) {
            $this->sendResponse(['error' => "El rango de fechas es insuficiente para el número de clases. La última clase está programada para $maxFecha. Por favor, amplíe el rango."], 400);
            return;
        }

        // --- VALIDAR VIGENCIA DE CERTIFICACIÓN ---
        $db = Conexion::getConnect();
        $stmtVigencia = $db->prepare("SELECT inscomp_vigencia FROM INSTRU_COMPETENCIA WHERE INSTRUCTOR_inst_id = ? AND programa_prog_id = ? AND competencia_comp_id = ?");
        $stmtVigencia->execute([$data['instructor_inst_id'], $progId, $data['competencia_comp_id']]);
        $vigenciaRes = $stmtVigencia->fetch(PDO::FETCH_ASSOC);
        if ($vigenciaRes && !empty($vigenciaRes['inscomp_vigencia'])) {
            if ($vigenciaRes['inscomp_vigencia'] < $data['asig_fecha_fin']) {
                $this->sendResponse(['error' => "La certificación del instructor para esta competencia vence el {$vigenciaRes['inscomp_vigencia']}, no alcanza a cubrir hasta {$data['asig_fecha_fin']}"], 400);
                return;
            }
        }

        // --- CÁLCULO DE HORAS PROPUESTAS ---
        $hoursByMonth = [];
        foreach ($diasSeleccionados as $dia) {
            if (!empty($dia['hora_ini']) && !empty($dia['hora_fin'])) {
                $dateParts = explode('-', $dia['fecha']);
                if (count($dateParts) >= 2) {
                    $year = $dateParts[0];
                    $month = $dateParts[1];
                    $key = "$year-$month";

                    $ini = strtotime($dia['hora_ini']);
                    $fin = strtotime($dia['hora_fin']);
                    $diff = ($fin - $ini) / 3600;
                    
                    if ($diff > 0) {
                        if (!isset($hoursByMonth[$key])) $hoursByMonth[$key] = 0;
                        $hoursByMonth[$key] += $diff;
                    }
                }
            }
        }

        // --- VALIDACIÓN DE HORAS MENSUALES (MAX 160h) ---
        $asigModelCheck = new AsignacionModel();
        foreach ($hoursByMonth as $key => $hours) {
            list($year, $month) = explode('-', $key);
            $currentMonthlyHours = $asigModelCheck->getMonthlyHours($data['instructor_inst_id'], $month, $year, $data['asig_id']);
            if (($currentMonthlyHours + $hours) > 160) {
                $this->sendResponse(['error' => "El instructor supera el límite de 160 horas en el mes $month/$year (Lleva: $currentMonthlyHours h, A asignar: $hours h)"], 400);
                return;
            }
        }

        // --- VALIDACIÓN DE HORAS DE COMPETENCIA ---
        require_once dirname(__DIR__) . '/model/CompetenciaModel.php';
        $compModel = new CompetenciaModel($data['competencia_comp_id']);
        $compData = $compModel->read()[0] ?? null;
        if ($compData) {
            $compMaxHours = (float)$compData['comp_horas'];
            $currentAssignedHours = $asigModelCheck->getCompetenceHoursAssigned($data['ficha_fich_id'], $data['competencia_comp_id'], $data['asig_id']);
            $totalProposedHours = array_sum($hoursByMonth);

            if (($currentAssignedHours + $totalProposedHours) > $compMaxHours) {
                $this->sendResponse(['error' => "Las horas asignadas superan las horas de la competencia ($compMaxHours h). Ya tiene asignadas $currentAssignedHours h e intentas asignar $totalProposedHours h."], 400);
                return;
            }

            // Alerta 80% (Solo si no viene confirmado)
            $percentage = ($compMaxHours > 0) ? (($currentAssignedHours + $totalProposedHours) / $compMaxHours) * 100 : 100;
            if ($percentage < 80 && !isset($data['confirm_80_percent'])) {
                $this->sendResponse([
                    'warning' => '80_percent_alert',
                    'message' => "El porcentaje de horas programadas (" . round($percentage, 2) . "%) es menor al recomendado (80%). ¿Deseas continuar con la asignación de todos modos?"
                ], 202);
                return;
            }
        }

        // Validar cada día
        $dayErrors = $this->validateDias($diasSeleccionados);
        if (!empty($dayErrors)) {
            $this->sendResponse(['error' => implode("\n", $dayErrors)], 400);
            return;
        }

        try {
            $this->db = Conexion::getConnect();
            $this->db->beginTransaction();

            $model = new AsignacionModel(
                $data['asig_id'],
                $data['instructor_inst_id'],
                $data['asig_fecha_ini'],
                $data['asig_fecha_fin'],
                $data['ficha_fich_id'],
                $data['ambiente_amb_id'],
                $data['competencia_comp_id']
            );

            if (!$model->update()) {
                $this->db->rollBack();
                $this->sendResponse(['error' => 'Error al actualizar registro principal'], 500);
                return;
            }

            // Borrar detalles anteriores
            $stmt = $this->db->prepare("DELETE FROM DETALLExASIGNACION WHERE ASIGNACION_asig_id = :asig_id");
            $stmt->execute([':asig_id' => $data['asig_id']]);

            // Verificar cruces con los nuevos días
            $conflicts = $this->checkDayConflicts(
                $diasSeleccionados,
                $data['asig_id'],
                $data['instructor_inst_id'],
                $data['ambiente_amb_id'],
                $data['ficha_fich_id']
            );
            if (!empty($conflicts)) {
                $this->db->rollBack();
                $this->sendResponse(['error' => 'Cruce de horario detectado', 'details' => $conflicts], 409);
                return;
            }

            // Insertar nuevos detalles
            foreach ($diasSeleccionados as $dia) {
                $detalleModel = new DetalleAsignacionModel(
                    $data['asig_id'],
                    $dia['fecha'],
                    $dia['hora_ini'],
                    $dia['hora_fin']
                );
                $detalleModel->create();
            }

            $this->db->commit();
            $this->sendResponse(['message' => 'Asignación actualizada']);
        } catch (Throwable $e) {
            if (isset($this->db) && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error in update: " . $e->getMessage());
            $this->sendResponse(['error' => 'Error de base de datos: ' . $e->getMessage()], 500);
        }
    }

    public function conflicts()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }
        $model = new AsignacionModel($id);
        $asig = $model->read($cent_id)[0] ?? null;
        if (!$asig) {
            $this->sendResponse(['error' => 'No encontrada'], 404);
            return;
        }

        $conflicts = $model->checkConflicts(
            $asig['instructor_inst_id'],
            $asig['ambiente_amb_id'],
            $asig['ficha_fich_id'],
            $asig['asig_fecha_ini'],
            $asig['asig_fecha_fin'],
            $id
        );
        $this->sendResponse($conflicts);
    }

    public function destroy()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }

        try {
            $this->db = Conexion::getConnect();
            $this->db->beginTransaction();

            // Primero eliminar detalles hijos (cascade manual)
            $stmt = $this->db->prepare("DELETE FROM DETALLExASIGNACION WHERE ASIGNACION_asig_id = :asig_id");
            $stmt->execute([':asig_id' => $id]);

            // Luego eliminar la asignación padre
            $model = new AsignacionModel($id, null, null, null, null, null, null);
            if ($model->delete()) {
                $this->db->commit();
                $this->sendResponse(['message' => 'Asignación eliminada']);
            } else {
                $this->db->rollBack();
                $this->sendResponse(['error' => 'Error al eliminar'], 500);
            }
        } catch (Throwable $e) {
            if (isset($this->db) && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error in destroy: " . $e->getMessage());
            $this->sendResponse(['error' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }

    private function sendResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
