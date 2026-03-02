<?php
require_once dirname(__DIR__) . '/model/CompetenciaModel.php';

class CompetenciaController
{
    private $model;

    public function __construct()
    {
        $this->model = new CompetenciaModel();
    }

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;
        $competencias = $this->model->readAll($cent_id);
        $this->sendResponse($competencias);
    }

    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }
        $this->model->setCompId($id);
        $result = $this->model->read();
        if (empty($result)) {
            $this->sendResponse(['error' => 'Competencia no encontrada'], 404);
            return;
        }

        $competencia = $result[0];
        $competencia['programas'] = $this->model->getProgramasByCompetencia();
        $competencia['instructores'] = $this->model->getInstructoresByCompetencia();

        $this->sendResponse($competencia);
    }

    public function store()
    {
        try {
            $nombre_corto = $_POST['comp_nombre_corto'] ?? null;
            $horas = $_POST['comp_horas'] ?? null;
            $unidad = $_POST['comp_nombre_unidad_competencia'] ?? null;
            $programas = $_POST['programas'] ?? []; // Array of program codes

            if (!$nombre_corto || !$horas) {
                $this->sendResponse(['error' => 'El nombre corto y las horas son campos obligatorios'], 400);
                return;
            }

            $this->model->setCompNombreCorto($nombre_corto);
            $this->model->setCompHoras($horas);
            $this->model->setCompNombreUnidadCompetencia($unidad);

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $cent_id = $_SESSION['centro_id'] ?? null;
            $this->model->setCentroFormacionId($cent_id);

            $id = $this->model->create();
            if ($id) {
                $this->model->setCompId($id);
                $this->model->assignProgramas($programas);
                $this->sendResponse(['message' => 'Competencia creada correctamente', 'id' => $id], 201);
            } else {
                $this->sendResponse(['error' => 'No se pudo crear la competencia'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update()
    {
        try {
            $id = $_POST['comp_id'] ?? null;
            $nombre_corto = $_POST['comp_nombre_corto'] ?? null;
            $horas = $_POST['comp_horas'] ?? null;
            $unidad = $_POST['comp_nombre_unidad_competencia'] ?? null;
            $programas = $_POST['programas'] ?? [];

            if (!$id || !$nombre_corto || !$horas) {
                $this->sendResponse(['error' => 'Faltan campos obligatorios'], 400);
                return;
            }

            $this->model->setCompId($id);
            $this->model->setCompNombreCorto($nombre_corto);
            $this->model->setCompHoras($horas);
            $this->model->setCompNombreUnidadCompetencia($unidad);

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $cent_id = $_SESSION['centro_id'] ?? null;
            $this->model->setCentroFormacionId($cent_id);

            if ($this->model->update()) {
                $this->model->assignProgramas($programas);
                $this->sendResponse(['message' => 'Competencia actualizada correctamente']);
            } else {
                $this->sendResponse(['error' => 'No se pudo actualizar la competencia'], 500);
            }
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../debug_error.log', "[" . date('Y-m-d H:i:s') . "] Error update competencia: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
            $this->sendResponse(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'ID requerido'], 400);
            return;
        }
        $this->model->setCompId($id);
        if ($this->model->delete()) {
            $this->sendResponse(['message' => 'Competencia eliminada con éxito']);
        } else {
            $this->sendResponse(['error' => 'No se pudo eliminar la competencia'], 500);
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
