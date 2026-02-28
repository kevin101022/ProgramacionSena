<?php

/**
 * SedeController - Gestión de peticiones para Sedes
 * Sigue principios de Clean Code y estandarización de respuestas JSON.
 */

require_once dirname(__DIR__) . '/model/SedeModel.php';

class SedeController
{
    private $model;

    public function __construct()
    {
        // El modelo requiere parámetros en el constructor, los inicializamos nulos
        $this->model = new SedeModel(null, null);
    }

    /**
     * Obtener listado de todas las sedes
     */
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;
        $sedes = $this->model->readAll($cent_id);
        $this->sendResponse($sedes);
    }

    /**
     * Obtener una sede específica por ID
     */
    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de sede requerido'], 400);
            return;
        }

        $this->model->setSedeId($id);
        $result = $this->model->read();

        if (empty($result)) {
            $this->sendResponse(['error' => 'Sede no encontrada'], 404);
            return;
        }

        $this->sendResponse($result[0]);
    }

    /**
     * Crear una nueva sede
     */
    public function store()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $nombre = $_POST['sede_nombre'] ?? null;
            $centro_id = $_POST['centro_formacion_cent_id'] ?? $_SESSION['centro_id'] ?? null;

            if (!$nombre || !$centro_id) {
                $this->sendResponse(['error' => 'El nombre de la sede y el centro son obligatorios'], 400);
                return;
            }

            $this->model->setSedeNombre($nombre);
            $this->model->setCentroFormacionId($centro_id);
            $createdId = $this->model->create();

            if ($createdId) {
                $this->sendResponse(['message' => 'Sede creada correctamente', 'id' => $createdId], 201);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear la sede'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al crear la sede', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar una sede existente
     */
    public function update()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $id = $_POST['sede_id'] ?? null;
            $nombre = $_POST['sede_nombre'] ?? null;
            $centro_id = $_POST['centro_formacion_cent_id'] ?? $_SESSION['centro_id'] ?? null;

            if (!$id || !$nombre || !$centro_id) {
                $this->sendResponse(['error' => 'ID, nombre y centro son obligatorios'], 400);
                return;
            }

            $this->model->setSedeId($id);
            $this->model->setSedeNombre($nombre);
            $this->model->setCentroFormacionId($centro_id);

            if ($this->model->update()) {
                $this->sendResponse(['message' => 'Sede actualizada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo actualizar la sede'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al actualizar la sede', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar una sede
     */
    public function destroy($sede_id = null)
    {
        try {
            if (!$sede_id) {
                $this->sendResponse(['error' => 'ID de sede requerido para eliminar'], 400);
                return;
            }

            $this->model->setSedeId($sede_id);

            if ($this->model->delete()) {
                $this->sendResponse(['message' => 'Sede eliminada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo eliminar la sede'], 500);
            }
        } catch (Exception $e) {
            // Error común en pgsql: 23503 es violación de llave foránea
            $message = 'No se puede eliminar la sede porque tiene ambientes o datos asociados.';
            if (method_exists($e, 'getCode') && $e->getCode() != '23503') {
                $message = 'Error al eliminar la sede: ' . $e->getMessage();
            }
            $this->sendResponse(['error' => $message], 500);
        }
    }

    public function getProgramas($sede_id = null)
    {
        if (!$sede_id) {
            $this->sendResponse(['error' => 'ID de sede requerido'], 400);
            return;
        }

        $this->model->setSedeId($sede_id);
        $programas = $this->model->getProgramasBySede();
        $this->sendResponse($programas);
    }

    public function getFichas($sede_id = null)
    {
        if (!$sede_id) {
            $this->sendResponse(['error' => 'ID de sede requerido'], 400);
            return;
        }

        $this->model->setSedeId($sede_id);
        $fichas = $this->model->getFichasBySede();
        $this->sendResponse($fichas);
    }

    /**
     * Helper para enviar respuestas JSON estandarizadas
     */
    private function sendResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
