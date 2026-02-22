<?php
require_once dirname(__DIR__) . '/model/AmbienteModel.php';
require_once dirname(__DIR__) . '/model/SedeModel.php';

class AmbienteController
{
    private $model;

    public function __construct()
    {
        $this->model = new AmbienteModel();
    }

    public function index($sede_id = null)
    {
        if ($sede_id) {
            $this->model->setSedeSedeId($sede_id);
            $ambientes = $this->model->read();
        } else {
            $ambientes = $this->model->readAll();
        }
        $this->sendResponse($ambientes);
    }

    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de ambiente requerido'], 400);
        }

        $ambiente = $this->model->readById($id);
        if (!$ambiente) {
            $this->sendResponse(['error' => 'Ambiente no encontrado'], 404);
        }

        $this->sendResponse($ambiente);
    }

    public function store()
    {
        $nombre = $_POST['amb_nombre'] ?? null;
        $sede = $_POST['sede_sede_id'] ?? null;

        if (!$nombre || !$sede) {
            $this->sendResponse(['error' => 'El nombre y la sede son obligatorios'], 400);
            return;
        }

        $this->model->setAmbnombre($nombre);
        $this->model->setSedeSedeId($sede);

        if ($this->model->create()) {
            $this->sendResponse(['message' => 'Ambiente creado correctamente'], 201);
        } else {
            $this->sendResponse(['error' => 'No se pudo crear el ambiente'], 500);
        }
    }

    public function update()
    {
        $id = $_POST['amb_id'] ?? null;
        $nombre = $_POST['amb_nombre'] ?? null;
        $sede = $_POST['sede_sede_id'] ?? null;

        if (!$id) {
            $this->sendResponse(['error' => 'ID obligatorio'], 400);
        }

        $this->model->setAmbId($id);
        $this->model->setAmbnombre($nombre);
        $this->model->setSedeSedeId($sede);

        if ($this->model->update()) {
            $this->sendResponse(['message' => 'Ambiente actualizado correctamente']);
        } else {
            $this->sendResponse(['error' => 'No se pudo actualizar el ambiente'], 500);
        }
    }

    public function destroy($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
        }

        $this->model->setAmbId($id);
        if ($this->model->delete()) {
            $this->sendResponse(['message' => 'Ambiente eliminado correctamente']);
        } else {
            $this->sendResponse(['error' => 'Error al eliminar (puede tener datos asociados)'], 500);
        }
    }

    private function sendResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
