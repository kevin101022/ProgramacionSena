<?php
require_once dirname(__DIR__) . '/model/CentroFormacionModel.php';

class CentroFormacionController
{
    public function index()
    {
        $model = new CentroFormacionModel();
        $centros = $model->getAll();
        $this->sendResponse($centros);
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['cent_nombre'])) {
            $this->sendResponse(['error' => 'Datos incompletos'], 400);
            return;
        }

        $model = new CentroFormacionModel(null, $data['cent_nombre']);
        $newId = $model->create();
        if ($newId) {
            $this->sendResponse(['message' => 'Centro creado', 'id' => $newId]);
        } else {
            $this->sendResponse(['error' => 'Error al crear'], 500);
        }
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }
        $model = new CentroFormacionModel($id);
        $centro = $model->read();
        $this->sendResponse($centro[0] ?? ['error' => 'No encontrado'], $centro ? 200 : 404);
    }

    public function update()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['cent_id']) || !isset($data['cent_nombre'])) {
            $this->sendResponse(['error' => 'Datos incompletos'], 400);
            return;
        }

        $model = new CentroFormacionModel($data['cent_id'], $data['cent_nombre']);
        if ($model->update()) {
            $this->sendResponse(['message' => 'Centro actualizado']);
        } else {
            $this->sendResponse(['error' => 'Error al actualizar'], 500);
        }
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }
        $model = new CentroFormacionModel($id);
        if ($model->delete()) {
            $this->sendResponse(['message' => 'Centro eliminado']);
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
