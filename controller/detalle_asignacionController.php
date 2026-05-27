<?php
require_once dirname(__DIR__) . '/model/DetalleAsignacionModel.php';

class DetalleAsignacionController
{
    public function index()
    {
        $asig_id = $_GET['asig_id'] ?? null;
        $model = new DetalleAsignacionModel(null, null, null, null);

        if ($asig_id) {
            $detalles = $model->readAllByAsignacion($asig_id);
        } else {
            $detalles = [];
        }
        $this->sendResponse($detalles);
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $labels = [
            'asignacion_asig_id' => 'Asignación',
            'detasig_fecha' => 'Fecha Específica',
            'detasig_hora_ini' => 'Hora de Inicio',
            'detasig_hora_fin' => 'Hora de Finalización'
        ];

        foreach ($labels as $field => $label) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->sendResponse(['error' => "El campo '$label' es obligatorio"], 400);
                return;
            }
        }

        // 1. Coherencia cronológica
        if ($data['detasig_hora_ini'] >= $data['detasig_hora_fin']) {
            $this->sendResponse(['error' => 'La hora de inicio debe ser menor a la hora de fin'], 400);
            return;
        }

        // 2. Horario Institucional (06:00 AM - 10:00 PM)
        if ($data['detasig_hora_ini'] < '06:00' || $data['detasig_hora_fin'] > '22:00') {
            $this->sendResponse(['error' => 'El horario debe estar dentro de la jornada institucional (06:00 AM - 10:00 PM)'], 400);
            return;
        }

        try {
            $model = new DetalleAsignacionModel();
            $conflicts = $model->checkGlobalConflicts($data['asignacion_asig_id'], $data['detasig_fecha'], $data['detasig_hora_ini'], $data['detasig_hora_fin']);
            if (!empty($conflicts)) {
                $this->sendResponse(['error' => 'Cruce de horario detectado', 'details' => $conflicts], 409);
                return;
            }

            $observaciones = $data['observaciones'] ?? null;
            $model = new DetalleAsignacionModel($data['asignacion_asig_id'], $data['detasig_fecha'], $data['detasig_hora_ini'], $data['detasig_hora_fin'], null, $observaciones);
            $id = $model->create();
            if ($id) {
                $this->sendResponse(['message' => 'Detalle de asignación creado', 'id' => $id]);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear el detalle'], 500);
            }
        } catch (Throwable $e) {
            error_log("Error in DetalleAsignacionController::store: " . $e->getMessage());
            $this->sendResponse(['error' => 'Error de base de datos: ' . $e->getMessage()], 500);
        }
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->sendResponse(['error' => 'ID de detalle requerido'], 400);
            return;
        }
        $model = new DetalleAsignacionModel(null, null, null, $id);
        $detalle = $model->readAllByAsignacion($id)[0] ?? null; // Simplificado para este contexto
        $this->sendResponse($detalle ? $detalle : ['error' => 'No encontrado'], $detalle ? 200 : 404);
    }

    public function update()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['detasig_id'])) {
            $this->sendResponse(['error' => 'ID de detalle requerido'], 400);
            return;
        }

        // Hours are always required
        if (empty($data['detasig_hora_ini']) || empty($data['detasig_hora_fin'])) {
            $this->sendResponse(['error' => 'La hora de inicio y fin son obligatorias'], 400);
            return;
        }

        // 1. Coherencia cronológica
        if ($data['detasig_hora_ini'] >= $data['detasig_hora_fin']) {
            $this->sendResponse(['error' => 'La hora de inicio debe ser menor a la hora de fin'], 400);
            return;
        }

        // 2. Horario Institucional (06:00 AM - 10:00 PM)
        if ($data['detasig_hora_ini'] < '06:00' || $data['detasig_hora_fin'] > '22:00') {
            $this->sendResponse(['error' => 'El horario debe estar dentro de la jornada institucional (06:00 AM - 10:00 PM)'], 400);
            return;
        }

        // VALIDACIÓN DE SEGURIDAD: Solo el coordinador dueño de la ficha puede editar
        if (session_status() === PHP_SESSION_NONE) session_start();
        $rol = $_SESSION['rol'] ?? null;
        if ($rol === 'coordinador' && isset($_SESSION['id'])) {
            $db = Conexion::getConnect();
            $stmtCoord = $db->prepare("SELECT coord_id FROM coordinacion WHERE coordinador_actual = :uid AND estado = 1 LIMIT 1");
            $stmtCoord->execute([':uid' => $_SESSION['id']]);
            $coord_id = $stmtCoord->fetchColumn();

            if ($coord_id) {
                // Sacar asig_id si no viene
                $checkAsigId = $data['asignacion_asig_id'] ?? null;
                if (!$checkAsigId) {
                    $stmtAsig = $db->prepare("SELECT asignacion_asig_id FROM detallexasignacion WHERE detasig_id = :did");
                    $stmtAsig->execute([':did' => $data['detasig_id']]);
                    $checkAsigId = $stmtAsig->fetchColumn();
                }

                if ($checkAsigId) {
                    $stmtCheck = $db->prepare("SELECT f.COORDINACION_coord_id FROM asignacion a JOIN ficha f ON a.FICHA_fich_id = f.fich_id WHERE a.asig_id = :aid");
                    $stmtCheck->execute([':aid' => $checkAsigId]);
                    $asigFichaCoord = $stmtCheck->fetchColumn();
                    if ($asigFichaCoord && $asigFichaCoord != $coord_id) {
                        $this->sendResponse(['error' => 'No tienes permisos para modificar una asignación de otra coordinación'], 403);
                        return;
                    }
                }
            }
        }

        try {
            // If no date or asig_id provided, fetch from existing record
            if (empty($data['detasig_fecha']) || empty($data['asignacion_asig_id'])) {
                $db = Conexion::getConnect();
                $stmt = $db->prepare("SELECT asignacion_asig_id, detasig_fecha FROM detallexasignacion WHERE detasig_id = :id");
                $stmt->execute([':id' => $data['detasig_id']]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$existing) {
                    $this->sendResponse(['error' => 'Detalle no encontrado'], 404);
                    return;
                }
                if (empty($data['detasig_fecha'])) $data['detasig_fecha'] = $existing['detasig_fecha'];
                if (empty($data['asignacion_asig_id'])) $data['asignacion_asig_id'] = $existing['asignacion_asig_id'];
            }

            $model = new DetalleAsignacionModel();
            $conflicts = $model->checkGlobalConflicts($data['asignacion_asig_id'], $data['detasig_fecha'], $data['detasig_hora_ini'], $data['detasig_hora_fin'], $data['detasig_id']);
            if (!empty($conflicts)) {
                $this->sendResponse(['error' => 'Cruce de horario detectado', 'details' => $conflicts], 409);
                return;
            }

            $observaciones = $data['observaciones'] ?? null;
            $model = new DetalleAsignacionModel($data['asignacion_asig_id'], $data['detasig_fecha'], $data['detasig_hora_ini'], $data['detasig_hora_fin'], $data['detasig_id'], $observaciones);
            if ($model->update()) {
                $this->sendResponse(['message' => 'Detalle actualizado']);
            } else {
                $this->sendResponse(['error' => 'Error al actualizar'], 500);
            }
        } catch (Throwable $e) {
            error_log("Error in DetalleAsignacionController::update: " . $e->getMessage());
            $this->sendResponse(['error' => 'Error de base de datos: ' . $e->getMessage()], 500);
        }
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }

        // VALIDACIÓN DE SEGURIDAD: Solo el coordinador dueño de la ficha puede eliminar
        if (session_status() === PHP_SESSION_NONE) session_start();
        $rol = $_SESSION['rol'] ?? null;
        if ($rol === 'coordinador' && isset($_SESSION['id'])) {
            $db = Conexion::getConnect();
            $stmtCoord = $db->prepare("SELECT coord_id FROM coordinacion WHERE coordinador_actual = :uid AND estado = 1 LIMIT 1");
            $stmtCoord->execute([':uid' => $_SESSION['id']]);
            $coord_id = $stmtCoord->fetchColumn();

            if ($coord_id) {
                $stmtCheck = $db->prepare("SELECT f.COORDINACION_coord_id FROM detallexasignacion d JOIN asignacion a ON d.ASIGNACION_asig_id = a.asig_id JOIN ficha f ON a.FICHA_fich_id = f.fich_id WHERE d.detasig_id = :did");
                $stmtCheck->execute([':did' => $id]);
                $asigFichaCoord = $stmtCheck->fetchColumn();
                if ($asigFichaCoord && $asigFichaCoord != $coord_id) {
                    $this->sendResponse(['error' => 'No tienes permisos para eliminar una asignación de otra coordinación'], 403);
                    return;
                }
            }
        }

        $model = new DetalleAsignacionModel(null, null, null, null, $id);
        if ($model->delete()) {
            $this->sendResponse(['message' => 'Detalle eliminado']);
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
