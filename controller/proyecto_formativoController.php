<?php
class ProyectoFormativoController {
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
        $model = new ProyectoFormativoModel();
        $centro_id = $_SESSION['centro_id'] ?? null;
        $data = $model->getAll($centro_id);
        
        if ($this->expectsJson()) {
            return $this->respondJson($data);
        }
        header("Location: views/proyecto_formativo/index.php");
        exit;
    }

    public function show($id) {
        $model = new ProyectoFormativoModel();
        $data = $model->getById($id);
        
        if ($this->expectsJson()) {
            return $this->respondJson($data);
        }
        header("Location: views/proyecto_formativo/show.php?id=" . $id);
        exit;
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $proyecto = $_POST['proyecto'] ?? [];
        $fases = $_POST['fases'] ?? [];
        
        $num_fases = count($fases);
        if ($num_fases < 4 || $num_fases > 6) {
            return $this->respondJson(['error' => 'El proyecto debe tener entre 4 y 6 fases estrictamente.'], 400);
        }
        
        $proyecto['centro_formacion_cent_id'] = $_SESSION['centro_id'] ?? null;
        
        $model = new ProyectoFormativoModel();
        try {
            $pf_id = $model->createConFases($proyecto, $fases);
            return $this->respondJson(['success' => true, 'pf_id' => $pf_id], 201);
        } catch (Exception $e) {
            return $this->respondJson(['error' => $e->getMessage()], 500);
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $data = $_POST;
        $model = new ProyectoFormativoModel();
        $success = $model->update($id, $data);
        
        return $this->respondJson(['success' => $success]);
    }

    public function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') return;
        
        $model = new ProyectoFormativoModel();
        $success = $model->delete($id);
        
        return $this->respondJson(['success' => $success]);
    }

    public function getFases($id) {
        $model = new ProyectoFormativoModel();
        $fases = $model->getFasesByProyecto($id);
        return $this->respondJson($fases);
    }

    public function getByPrograma() {
        $prog_codigo = $_GET['prog_id'] ?? null;
        if (!$prog_codigo) return $this->respondJson([], 400);
        
        $model = new ProyectoFormativoModel();
        $data = $model->getByPrograma($prog_codigo);
        return $this->respondJson($data);
    }

    public function asociarFicha() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $fich_id = $_POST['fich_id'] ?? null;
        $pf_id = $_POST['pf_id'] ?? null;
        
        if (!$fich_id || !$pf_id) return $this->respondJson(['error' => 'Datos incompletos'], 400);
        
        $model = new ProyectoFormativoModel();
        $success = $model->asociarFicha($fich_id, $pf_id);
        
        return $this->respondJson(['success' => $success]);
    }

    public function getProyectoByFicha() {
        $fich_id = $_GET['fich_id'] ?? null;
        if (!$fich_id) return $this->respondJson([], 400);
        
        $model = new ProyectoFormativoModel();
        $data = $model->getProyectoByFicha($fich_id);
        return $this->respondJson($data);
    }

    // --- ACTIVITY METHODS ---
    public function storeActivity() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $data = $_POST;
        require_once 'model/ActividadProyectoModel.php';
        $model = new ActividadProyectoModel();
        $id = $model->create($data);
        return $this->respondJson(['success' => true, 'act_id' => $id]);
    }

    public function updateActivity($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $data = $_POST;
        require_once 'model/ActividadProyectoModel.php';
        $model = new ActividadProyectoModel();
        $success = $model->update($id, $data);
        return $this->respondJson(['success' => $success]);
    }

    public function destroyActivity($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') return;
        require_once 'model/ActividadProyectoModel.php';
        $model = new ActividadProyectoModel();
        $success = $model->delete($id);
        return $this->respondJson(['success' => $success]);
    }

    public function assignRap() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $rap_ids = $_POST['rap_id'] ?? null;
        $act_id = $_POST['act_id'] ?? null;
        
        if (!$rap_ids || !$act_id) return $this->respondJson(['error' => 'Datos incompletos'], 400);

        require_once 'model/ActividadProyectoModel.php';
        $model = new ActividadProyectoModel();
        
        try {
            if (is_array($rap_ids)) {
                foreach ($rap_ids as $r_id) {
                    $model->asignarRap($r_id, $act_id);
                }
            } else {
                $model->asignarRap($rap_ids, $act_id);
            }
            return $this->respondJson(['success' => true]);
        } catch (Exception $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400);
        }
    }

    public function unassignRap() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $rap_id = $_POST['rap_id'] ?? null;
        $act_id = $_POST['act_id'] ?? null;
        require_once 'model/ActividadProyectoModel.php';
        $model = new ActividadProyectoModel();
        $success = $model->desasignarRap($rap_id, $act_id);
        return $this->respondJson(['success' => $success]);
    }

    // --- PHASE METHODS ---
    public function getPhase($id) {
        $model = new ProyectoFormativoModel();
        $data = $model->getFaseById($id);
        return $this->respondJson($data);
    }

    public function storePhase() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $data = $_POST;
        $model = new ProyectoFormativoModel();
        try {
            $id = $model->createFase($data);
            return $this->respondJson(['success' => true, 'fase_id' => $id]);
        } catch (Exception $e) {
            return $this->respondJson(['error' => $e->getMessage()], 500);
        }
    }

    public function updatePhase($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $data = $_POST;
        $model = new ProyectoFormativoModel();
        $success = $model->updateFase($id, $data);
        return $this->respondJson(['success' => $success]);
    }

    public function destroyPhase($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') return;
        $model = new ProyectoFormativoModel();
        $success = $model->deleteFase($id);
        return $this->respondJson(['success' => $success]);
    }
}
