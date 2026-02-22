<?php

/**
 * TituloProgramaController - Gestión de peticiones para Títulos de Programa
 */

require_once dirname(__DIR__) . '/model/TituloProgramaModel.php';

class TituloProgramaController
{
    private $model;

    public function __construct()
    {
        $this->model = new TituloProgramaModel();
    }

    /**
     * Obtener listado de todos los títulos
     */
    public function index()
    {
        $titulos = $this->model->readAll();
        $this->sendResponse($titulos);
    }

    /**
     * Obtener un título específico por ID
     */
    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID de título requerido'], 400);
            return;
        }

        $this->model->setTitproId($id);
        $result = $this->model->read();

        if (empty($result)) {
            $this->sendResponse(['error' => 'Título no encontrado'], 404);
            return;
        }

        $this->sendResponse($result[0]);
    }

    /**
     * Crear un nuevo título
     */
    public function store()
    {
        try {
            $nombre = $_POST['titpro_nombre'] ?? null;

            if (!$nombre) {
                $this->sendResponse(['error' => 'El nombre del título es obligatorio'], 400);
                return;
            }

            $this->model->setTitproNombre($nombre);
            $newId = $this->model->create();

            if ($newId) {
                $this->sendResponse(['message' => 'Título creado correctamente', 'id' => $newId], 201);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear el título', 'details' => 'El modelo no devolvió un ID válido'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al crear el título', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un título existente
     */
    public function update()
    {
        try {
            $id = $_POST['titpro_id'] ?? null;
            $nombre = $_POST['titpro_nombre'] ?? null;

            if (!$id || !$nombre) {
                $this->sendResponse(['error' => 'ID y nombre son obligatorios'], 400);
                return;
            }

            $this->model->setTitproId($id);
            $this->model->setTitproNombre($nombre);

            if ($this->model->update()) {
                $this->sendResponse(['message' => 'Título actualizado correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo actualizar el título'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error al actualizar el título', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un título
     */
    public function destroy($id = null)
    {
        try {
            if (!$id) {
                $this->sendResponse(['error' => 'ID de título requerido'], 400);
                return;
            }

            $this->model->setTitproId($id);

            if ($this->model->delete()) {
                $this->sendResponse(['message' => 'Título eliminado correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo eliminar el título'], 500);
            }
        } catch (Exception $e) {
            $message = 'No se puede eliminar el título porque tiene programas asociados.';
            if (method_exists($e, 'getCode') && $e->getCode() != '23503') {
                $message = 'Error al eliminar el título: ' . $e->getMessage();
            }
            $this->sendResponse(['error' => $message], 500);
        }
    }

    /**
     * Helper para enviar respuestas JSON
     */
    private function sendResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
