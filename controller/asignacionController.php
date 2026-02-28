<?php
require_once dirname(__DIR__) . '/model/AsignacionModel.php';
require_once dirname(__DIR__) . '/model/FichaModel.php';
require_once dirname(__DIR__) . '/model/InstruCompetenciaModel.php';

class AsignacionController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        $model = new AsignacionModel(null, null, null, null, null, null, null);
        $asignaciones = $model->readAll($cent_id);
        $this->sendResponse($asignaciones);
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        // human-readable labels for validation
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

        // Validación de fecha no pasada
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

        // 2. Validar que el instructor esté habilitado para esa competencia y programa
        if (!InstruCompetenciaModel::isQualified($data['instructor_inst_id'], $progId, $data['competencia_comp_id'])) {
            $this->sendResponse(['error' => 'El instructor no está habilitado para esta competencia en este programa de formación'], 403);
            return;
        }

        $model = new AsignacionModel();
        // Check for conflicts informational purposes or to log if needed
        // But we ALLOW overlapping dates as requested, real block is in Details
        $conflicts = $model->checkConflicts(
            $data['instructor_inst_id'],
            $data['ambiente_amb_id'],
            $data['ficha_fich_id'],
            $data['asig_fecha_ini'],
            $data['asig_fecha_fin']
        );

        // We could send a warning, but to allow flexibility we proceed
        // The user specifically requested to allow two fichas in same ambiente different hours

        $model = new AsignacionModel(
            null,
            $data['instructor_inst_id'],
            $data['asig_fecha_ini'],
            $data['asig_fecha_fin'],
            $data['ficha_fich_id'],
            $data['ambiente_amb_id'],
            $data['competencia_comp_id']
        );

        try {
            $id = $model->create();
            if ($id) {
                $this->sendResponse(['message' => 'Asignación creada', 'id' => $id]);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear el registro'], 500);
            }
        } catch (Throwable $e) {
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
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['asig_id'])) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }

        // human-readable labels for validation
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

        // Validación de fecha no pasada
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

        // 2. Validar que el instructor esté habilitado para esa competencia y programa
        if (!InstruCompetenciaModel::isQualified($data['instructor_inst_id'], $progId, $data['competencia_comp_id'])) {
            $this->sendResponse(['error' => 'El instructor no está habilitado para esta competencia en este programa de formación'], 403);
            return;
        }

        $model = new AsignacionModel();
        $conflicts = $model->checkConflicts(
            $data['instructor_inst_id'],
            $data['ambiente_amb_id'],
            $data['ficha_fich_id'],
            $data['asig_fecha_ini'],
            $data['asig_fecha_fin'],
            $data['asig_id']
        );

        $model = new AsignacionModel(
            $data['asig_id'],
            $data['instructor_inst_id'],
            $data['asig_fecha_ini'],
            $data['asig_fecha_fin'],
            $data['ficha_fich_id'],
            $data['ambiente_amb_id'],
            $data['competencia_comp_id']
        );

        try {
            if ($model->update()) {
                $this->sendResponse(['message' => 'Asignación actualizada']);
            } else {
                $this->sendResponse(['error' => 'Error al actualizar'], 500);
            }
        } catch (Throwable $e) {
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
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }
        $model = new AsignacionModel($id, null, null, null, null, null, null);
        if ($model->delete()) {
            $this->sendResponse(['message' => 'Asignación eliminada']);
        } else {
            $this->sendResponse(['error' => 'Error al eliminar'], 500);
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
