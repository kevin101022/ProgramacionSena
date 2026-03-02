<?php
require_once dirname(__DIR__) . '/model/CoordinacionModel.php';
require_once dirname(__DIR__) . '/model/UsuarioCoordinadorModel.php';

class coordinacionController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        $model = new CoordinacionModel();
        $coordinaciones = $model->getAll($cent_id);
        $this->sendResponse($coordinaciones);
    }

    public function get_coordinadores_disponibles()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        $userModel = new UsuarioCoordinadorModel();
        $disponibles = $userModel->getActivosDisponibles($cent_id);
        $this->sendResponse($disponibles);
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['coord_descripcion'])) {
            $this->sendResponse(['error' => 'La descripción de coordinación es requerida'], 400);
            return;
        }

        $coordinador = !empty($data['coordinador_actual']) ? $data['coordinador_actual'] : null;

        $model = new CoordinacionModel(
            null, // ID autoincremental
            $data['coord_descripcion'],
            $data['centro_formacion_cent_id'] ?? $cent_id,
            $coordinador,
            1 // Estado
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }

        $model = new CoordinacionModel($id);
        $coordinacion = $model->read($cent_id);

        if ($coordinacion) {
            $this->sendResponse($coordinacion[0]);
        } else {
            $this->sendResponse(['error' => 'Coordinación no encontrada o sin acceso'], 404);
        }
    }

    public function update()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['coord_id'])) {
            $this->sendResponse(['error' => 'Datos incompletos'], 400);
            return;
        }

        $coordinador = !empty($data['coordinador_actual']) ? $data['coordinador_actual'] : null;

        $model = new CoordinacionModel(
            $data['coord_id'],
            $data['coord_descripcion'],
            $data['centro_formacion_cent_id'] ?? $cent_id,
            $coordinador
        );

        if ($model->update()) {
            $this->sendResponse(['message' => 'Coordinación actualizada correctamente']);
        } else {
            $this->sendResponse(['error' => 'Error al actualizar coordinación'], 500);
        }
    }

    public function desvincular($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
        }

        $model = new CoordinacionModel($id);
        if ($model->desvincular()) {
            $this->sendResponse(['message' => 'Coordinador desvinculado correctamente']);
        } else {
            $this->sendResponse(['error' => 'Error al desvincular al coordinador'], 500);
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

    public function getProgramas()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->sendResponse(['error' => 'ID de coordinación requerido'], 400);
        }

        $model = new CoordinacionModel($id);
        $programas = $model->getProgramas();
        $this->sendResponse($programas);
    }

    private function sendResponse($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
