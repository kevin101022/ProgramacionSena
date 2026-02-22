<?php
require_once dirname(__DIR__) . '/model/CoordinacionModel.php';

class coordinacionController
{
    public function index()
    {
        $model = new CoordinacionModel();
        $coordinaciones = $model->getAll();
        $this->sendResponse($coordinaciones);
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['coord_descripcion'])) {
            $this->sendResponse(['error' => 'Descripción de coordinación requerida'], 400);
            return;
        }

        $model = new CoordinacionModel(
            null,
            $data['coord_descripcion'],
            $data['centro_formacion_cent_id'] ?? null,
            $data['coord_nombre_coordinador'] ?? 'N/A',
            $data['coord_correo'] ?? 'N/A',
            $data['coord_password'] ?? '123456'
        );

        $newId = $model->create();
        if ($newId) {
            $this->sendResponse(['message' => 'Coordinación creada correctamente', 'id' => $newId]);
        } else {
            $this->sendResponse(['error' => 'Error al crear coordinación'], 500);
        }
    }

    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
        }

        $model = new CoordinacionModel($id);
        $coordinacion = $model->read();

        if ($coordinacion) {
            $this->sendResponse($coordinacion[0]);
        } else {
            $this->sendResponse(['error' => 'Coordinación no encontrada'], 404);
        }
    }

    public function update()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['coord_id'])) {
            $this->sendResponse(['error' => 'Datos incompletos'], 400);
        }

        $model = new CoordinacionModel(
            $data['coord_id'],
            $data['coord_descripcion'],
            $data['centro_formacion_cent_id'],
            $data['coord_nombre_coordinador'],
            $data['coord_correo'],
            $data['coord_password']
        );

        if ($model->update()) {
            $this->sendResponse(['message' => 'Coordinación actualizada correctamente']);
        } else {
            $this->sendResponse(['error' => 'Error al actualizar coordinación'], 500);
        }
    }

    public function destroy($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
        }

        $model = new CoordinacionModel($id);
        if ($model->delete()) {
            $this->sendResponse(['message' => 'Coordinación eliminada correctamente']);
        } else {
            $this->sendResponse(['error' => 'Error al eliminar coordinación'], 500);
        }
    }

    private function sendResponse($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
