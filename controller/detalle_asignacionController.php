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

            $model = new DetalleAsignacionModel($data['asignacion_asig_id'], $data['detasig_fecha'], $data['detasig_hora_ini'], $data['detasig_hora_fin'], null);
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

        try {
            // If no date or asig_id provided, fetch from existing record
            if (empty($data['detasig_fecha']) || empty($data['asignacion_asig_id'])) {
                $db = Conexion::getConnect();
                $stmt = $db->prepare("SELECT asignacion_asig_id, detasig_fecha FROM DETALLExASIGNACION WHERE detasig_id = :id");
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

            $model = new DetalleAsignacionModel($data['asignacion_asig_id'], $data['detasig_fecha'], $data['detasig_hora_ini'], $data['detasig_hora_fin'], $data['detasig_id']);
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
        $model = new DetalleAsignacionModel(null, null, null, $id);
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
