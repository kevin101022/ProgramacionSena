<?php
require_once dirname(__DIR__) . '/model/InstruCompetenciaModel.php';

class InstruCompetenciaController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $centro_id = $_SESSION['centro_id'] ?? null;

        $model = new InstruCompetenciaModel();
        $datos = $model->readAll($centro_id);
        return $this->sendResponse($datos);
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['instructor_inst_id']) || !isset($data['competencia_comp_id'])) {
            return $this->sendResponse(['error' => 'Datos insuficientes'], 400);
        }

        $inst_id = $data['instructor_inst_id'];
        $comp_id = $data['competencia_comp_id'];
        
        // En la nueva lógica (1:N), una competencia solo tiene un programa
        require_once dirname(__DIR__) . '/model/CompetenciaModel.php';
        $compModel = new CompetenciaModel($comp_id);
        $competencia = $compModel->read();
        
        if (empty($competencia)) {
            return $this->sendResponse(['error' => 'Competencia no encontrada'], 404);
        }
        
        $p_id = $competencia[0]['programa_prog_id'];
        
        if (!$p_id) {
            return $this->sendResponse(['error' => 'La competencia no tiene un programa asociado'], 400);
        }

        if (!InstruCompetenciaModel::isQualified($inst_id, $p_id, $comp_id)) {
            $model = new InstruCompetenciaModel(null, $inst_id, $p_id, $comp_id);
            if ($model->create()) {
                return $this->sendResponse(['message' => "Habilitación creada correctamente"]);
            }
        } else {
            return $this->sendResponse(['message' => 'La habilitación ya existe']);
        }
        return $this->sendResponse(['error' => 'Error al crear la habilitación'], 500);
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->sendResponse(['error' => 'ID no proporcionado'], 400);
        }

        $model = new InstruCompetenciaModel($id);
        $dato = $model->read();

        if ($dato) {
            return $this->sendResponse($dato[0]);
        }
        return $this->sendResponse(['error' => 'Vínculo no encontrado'], 404);
    }

    public function update()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['inscomp_id'])) {
            return $this->sendResponse(['error' => 'Datos incompletos'], 400);
        }

        $id = $data['inscomp_id'];
        $modelOld = new InstruCompetenciaModel($id);
        $oldData = $modelOld->read();

        if (!$oldData) {
            return $this->sendResponse(['error' => 'No se encontró la habilitación original'], 404);
        }

        $inst_id = $data['instructor_inst_id'] ?? $oldData[0]['instructor_inst_id'];
        $comp_id = $data['competencia_comp_id'] ?? $oldData[0]['competencia_comp_id'];

        // Eliminamos el registro actual
        $modelOld->delete();

        // Reutilizamos la lógica directa
        require_once dirname(__DIR__) . '/model/CompetenciaModel.php';
        $compModel = new CompetenciaModel($comp_id);
        $competencia = $compModel->read();
        $p_id = $competencia[0]['programa_prog_id'];

        $model = new InstruCompetenciaModel(null, $inst_id, $p_id, $comp_id);
        $model->create();

        return $this->sendResponse(['message' => 'Habilitación actualizada correctamente']);
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->sendResponse(['error' => 'ID no proporcionado'], 400);
        }

        $model = new InstruCompetenciaModel($id);
        $dato = $model->read();

        if (!$dato) {
            return $this->sendResponse(['error' => 'Habilitación no encontrada'], 404);
        }

        $inst_id = $dato[0]['instructor_inst_id'];
        $comp_id = $dato[0]['competencia_comp_id'];

        $db = Conexion::getConnect();
        $sql = "DELETE FROM INSTRU_COMPETENCIA 
                WHERE INSTRUCTOR_inst_id = :inst_id 
                AND competencia_comp_id = :comp_id";
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([
            ':inst_id' => $inst_id,
            ':comp_id' => $comp_id
        ]);

        if ($success) {
            return $this->sendResponse(['message' => 'Habilitación eliminada globalmente para el instructor']);
        }
        return $this->sendResponse(['error' => 'Error al eliminar la habilitación'], 500);
    }

    private function sendResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
