<?php
require_once dirname(__DIR__) . '/model/CompetenciaProgramaModel.php';

class CompetenciaProgramaController
{
    private $model;

    public function __construct()
    {
        $this->model = new CompetenciaProgramaModel();
    }

    /**
     * Obtener programas por competencia (vía GET)
     */
    public function index($compId = null)
    {
        if (!$compId) {
            $this->sendResponse(['error' => 'ID de competencia requerido'], 400);
            return;
        }
        $programas = $this->model->getProgramasByCompetencia($compId);
        $this->sendResponse($programas);
    }

    /**
     * Obtener competencias por programa (vía GET)
     */
    public function getByPrograma()
    {
        $progId = $_GET['prog_id'] ?? null;
        if (!$progId) {
            $this->sendResponse(['error' => 'ID de programa requerido'], 400);
            return;
        }
        $competencias = $this->model->getCompetenciasByPrograma($progId);
        $this->sendResponse($competencias);
    }

    /**
     * Sincronizar programas (vía POST)
     */
    public function sync()
    {
        try {
            $compId = $_POST['comp_id'] ?? null;
            $programas = $_POST['programas'] ?? [];

            if (!$compId) {
                $this->sendResponse(['error' => 'ID de competencia requerido'], 400);
                return;
            }

            if ($this->model->syncProgramas($compId, $programas)) {
                $this->sendResponse(['message' => 'Asociaciones actualizadas con éxito']);
            } else {
                $this->sendResponse(['error' => 'No se pudieron actualizar las asociaciones'], 500);
            }
        } catch (Exception $e) {
            $this->sendResponse(['error' => 'Error: ' . $e->getMessage()], 500);
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
