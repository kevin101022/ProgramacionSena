<?php
class CompetenciaHorasProgramaController {
    private function respondJson($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        return;
    }

    private function expectsJson() {
        return (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));
    }

    public function index() {
        if ($this->expectsJson()) {
            return $this->respondJson([]);
        }
        require_once 'views/competencia_horas_programa/index.php';
    }

    public function getByPrograma() {
        $prog_codigo = $_GET['prog_codigo'] ?? null;
        if (!$prog_codigo) return $this->respondJson([], 400);
        
        $model = new CompetenciaHorasProgramaModel();
        $data = $model->getByPrograma($prog_codigo);
        return $this->respondJson($data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $prog_codigo = $_POST['prog_codigo'] ?? null;
        $comp_id = $_POST['comp_id'] ?? null;
        $horas_requeridas = $_POST['horas_requeridas'] ?? 0;
        $aplica = isset($_POST['aplica']) ? filter_var($_POST['aplica'], FILTER_VALIDATE_BOOLEAN) : true;
        
        if (!$prog_codigo || !$comp_id) return $this->respondJson(['error' => 'Datos incompletos'], 400);
        
        $model = new CompetenciaHorasProgramaModel();
        $success = $model->upsert($prog_codigo, $comp_id, $horas_requeridas, $aplica);
        
        return $this->respondJson(['success' => $success]);
    }

    public function destroy() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') return;
        
        $prog_codigo = $_REQUEST['prog_codigo'] ?? null;
        $comp_id = $_REQUEST['comp_id'] ?? null;
        
        if (!$prog_codigo || !$comp_id) return $this->respondJson(['error' => 'Datos incompletos'], 400);
        
        $model = new CompetenciaHorasProgramaModel();
        $success = $model->delete($prog_codigo, $comp_id);
        
        return $this->respondJson(['success' => $success]);
    }

    public function getEstadoFicha() {
        $fich_id = $_GET['fich_id'] ?? null;
        if (!$fich_id) return $this->respondJson([], 400);
        
        $model = new CompetenciaHorasProgramaModel();
        $data = $model->getEstadoCompetenciasFicha($fich_id);
        
        return $this->respondJson($data);
    }
}
