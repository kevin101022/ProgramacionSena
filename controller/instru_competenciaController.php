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
        if (!$data || !isset($data['instructor_inst_id']) || !isset($data['competxprograma_competencia_comp_id'])) {
            return $this->sendResponse(['error' => 'Datos insuficientes'], 400);
        }

        $inst_id = $data['instructor_inst_id'];
        $comp_id = $data['competxprograma_competencia_comp_id'];
        $prog_id = $data['competxprograma_programa_prog_id'] ?? null;

        require_once dirname(__DIR__) . '/model/CompetenciaProgramaModel.php';
        $cpModel = new CompetenciaProgramaModel();

        // Si no viene programa, buscamos todos los programas asociados a esa competencia
        $programas = $prog_id ? [['PROGRAMA_prog_id' => $prog_id]] : $cpModel->getProgramasByCompetencia($comp_id);

        if (empty($programas)) {
            return $this->sendResponse(['error' => 'La competencia no está asociada a ningún programa'], 400);
        }

        $successCount = 0;
        foreach ($programas as $p) {
            $p_id = $p['PROGRAMA_prog_id'] ?? $p['prog_id'];
            
            if (!InstruCompetenciaModel::isQualified($inst_id, $p_id, $comp_id)) {
                $model = new InstruCompetenciaModel(null, $inst_id, $p_id, $comp_id);
                if ($model->create()) {
                    $successCount++;
                }
            }
        }

        if ($successCount > 0) {
            return $this->sendResponse(['message' => "Habilitación creada en $successCount programas correctamente"]);
        } else {
            return $this->sendResponse(['message' => 'La habilitación ya existía para estos programas o no se crearon nuevos registros']);
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

        // Para simplificar, si se edita, eliminamos las habilitaciones previas para ese instructor/competencia 
        // y recreamos según la nueva lógica (auto-resolviendo programas)
        $id = $data['inscomp_id'];
        $modelOld = new InstruCompetenciaModel($id);
        $oldData = $modelOld->read();

        if (!$oldData) {
            return $this->sendResponse(['error' => 'No se encontró la habilitación original'], 404);
        }

        $inst_id = $data['instructor_inst_id'] ?? $oldData[0]['instructor_inst_id'];
        $comp_id = $data['competxprograma_competencia_comp_id'] ?? $oldData[0]['competxprograma_competencia_comp_id'];

        // Eliminamos el registro actual
        $modelOld->delete();

        // Reutilizamos la lógica de store para crear los nuevos registros
        require_once dirname(__DIR__) . '/model/CompetenciaProgramaModel.php';
        $cpModel = new CompetenciaProgramaModel();
        $programas = $cpModel->getProgramasByCompetencia($comp_id);

        $successCount = 0;
        foreach ($programas as $p) {
            $p_id = $p['PROGRAMA_prog_id'] ?? $p['prog_id'];
            $model = new InstruCompetenciaModel(null, $inst_id, $p_id, $comp_id);
            if ($model->create()) {
                $successCount++;
            }
        }

        return $this->sendResponse(['message' => 'Habilitación actualizada correctamente']);
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->sendResponse(['error' => 'ID no proporcionado'], 400);
        }

        // Primero obtenemos los datos de la habilitación para saber qué instructor y competencia borrar globalmente
        $model = new InstruCompetenciaModel($id);
        $dato = $model->read();

        if (!$dato) {
            return $this->sendResponse(['error' => 'Habilitación no encontrada'], 404);
        }

        $inst_id = $dato[0]['instructor_inst_id'];
        $comp_id = $dato[0]['competxprograma_competencia_comp_id'];

        // Usamos un nuevo método (o lógica manual) para borrar todos los programas de ese par
        $db = Conexion::getConnect();
        $sql = "DELETE FROM INSTRU_COMPETENCIA 
                WHERE INSTRUCTOR_inst_id = :inst_id 
                AND COMPETxPROGRAMA_COMPETENCIA_comp_id = :comp_id";
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
