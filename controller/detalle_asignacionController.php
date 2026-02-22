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
            'detasig_hora_ini' => 'Hora de Inicio',
            'detasig_hora_fin' => 'Hora de Finalización'
        ];

        foreach ($labels as $field => $label) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->sendResponse(['error' => "El campo '$label' es obligatorio"], 400);
                return;
            }
        }

        try {
            $model = new DetalleAsignacionModel();
            $conflicts = $model->checkTimeConflicts($data['asignacion_asig_id'], $data['detasig_hora_ini'], $data['detasig_hora_fin']);
            if (!empty($conflicts)) {
                $this->sendResponse(['error' => 'Cruce de horario detectado con otra franja de esta misma asignación'], 409);
                return;
            }

            $model = new DetalleAsignacionModel($data['asignacion_asig_id'], $data['detasig_hora_ini'], $data['detasig_hora_fin'], null);
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

        $labels = [
            'asignacion_asig_id' => 'Asignación',
            'detasig_hora_ini' => 'Hora de Inicio',
            'detasig_hora_fin' => 'Hora de Finalización'
        ];

        foreach ($labels as $field => $label) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->sendResponse(['error' => "El campo '$label' es obligatorio"], 400);
                return;
            }
        }

        try {
            $model = new DetalleAsignacionModel();
            $conflicts = $model->checkTimeConflicts($data['asignacion_asig_id'], $data['detasig_hora_ini'], $data['detasig_hora_fin'], $data['detasig_id']);
            if (!empty($conflicts)) {
                $this->sendResponse(['error' => 'Cruce de horario detectado con otra franja de esta misma asignación'], 409);
                return;
            }

            $model = new DetalleAsignacionModel($data['asignacion_asig_id'], $data['detasig_hora_ini'], $data['detasig_hora_fin'], $data['detasig_id']);
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
