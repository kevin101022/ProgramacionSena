<?php
class ResultadoAprendizajeController {
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
        $model = new ResultadoAprendizajeModel();
        $centro_id = $_SESSION['centro_id'] ?? null;
        $data = $model->getAll($centro_id);
        
        if ($this->expectsJson()) {
            return $this->respondJson($data);
        }
        header("Location: views/resultado_aprendizaje/index.php");
        exit;
    }

    public function show($id) {
        $model = new ResultadoAprendizajeModel();
        $data = $model->getById($id);
        
        if ($this->expectsJson()) {
            return $this->respondJson($data);
        }
        header("Location: views/resultado_aprendizaje/show.php?id=" . $id);
        exit;
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $data = $_POST;
        if (empty($data['rap_codigo']) || empty($data['rap_descripcion']) || empty($data['competxprog_prog_id']) || empty($data['competxprog_comp_id'])) {
            return $this->respondJson(['error' => 'Faltan campos obligatorios'], 400);
        }
        
        try {
            $model = new ResultadoAprendizajeModel();
            $rap_id = $model->create($data);
            if (!$rap_id) {
                return $this->respondJson(['error' => 'No se pudo crear el registro en la base de datos'], 500);
            }
            return $this->respondJson(['success' => true, 'rap_id' => $rap_id], 201);
        } catch (Exception $e) {
            return $this->respondJson(['error' => 'Error BD: ' . $e->getMessage()], 500);
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $data = $_POST;
        $model = new ResultadoAprendizajeModel();
        $success = $model->update($id, $data);
        return $this->respondJson(['success' => $success]);
    }

    public function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') return;
        
        $model = new ResultadoAprendizajeModel();
        $success = $model->delete($id);
        return $this->respondJson(['success' => $success]);
    }

    public function getByCompetenciaPrograma() {
        $prog_id = $_GET['prog_id'] ?? null;
        $comp_id = $_GET['comp_id'] ?? null;
        
        if (!$prog_id || !$comp_id) return $this->respondJson([], 400);
        
        $model = new ResultadoAprendizajeModel();
        $data = $model->getByCompetenciaPrograma($prog_id, $comp_id);
        return $this->respondJson($data);
    }

    public function getByFase() {
        $fase_id = $_GET['fase_id'] ?? null;
        if (!$fase_id) return $this->respondJson([], 400);
        
        $model = new ResultadoAprendizajeModel();
        $data = $model->getByFase($fase_id);
        return $this->respondJson($data);
    }

    public function getByFaseYPrograma() {
        $fase_id = $_GET['fase_id'] ?? null;
        $prog_id = $_GET['prog_id'] ?? null;
        
        if (!$fase_id || !$prog_id) return $this->respondJson([], 400);
        
        $model = new ResultadoAprendizajeModel();
        $data = $model->getByFaseYPrograma($fase_id, $prog_id);
        return $this->respondJson($data);
    }

    public function asignarAFase() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $rap_id = $_POST['rap_id'] ?? null;
        $fase_id = $_POST['fase_id'] ?? null;
        
        if (!$rap_id || !$fase_id) return $this->respondJson(['error' => 'Datos incompletos'], 400);
        
        $model = new ResultadoAprendizajeModel();
        $success = $model->asignarAFase($rap_id, $fase_id);
        return $this->respondJson(['success' => $success]);
    }

    public function desasignarDeFase() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $rap_id = $_POST['rap_id'] ?? null;
        $fase_id = $_POST['fase_id'] ?? null;
        
        if (!$rap_id || !$fase_id) return $this->respondJson(['error' => 'Datos incompletos'], 400);
        
        $model = new ResultadoAprendizajeModel();
        $success = $model->desasignarDeFase($rap_id, $fase_id);
        return $this->respondJson(['success' => $success]);
    }
}
