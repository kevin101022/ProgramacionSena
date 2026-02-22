<?php
require_once dirname(__DIR__) . '/model/InstruCompetenciaModel.php';

class InstruCompetenciaController
{
    public function index()
    {
        $model = new InstruCompetenciaModel();
        $datos = $model->readAll();
        return $this->sendResponse($datos);
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            return $this->sendResponse(['error' => 'No se recibieron datos'], 400);
        }

        $model = new InstruCompetenciaModel(
            null,
            $data['instructor_inst_id'],
            $data['competxprograma_programa_prog_id'],
            $data['competxprograma_competencia_comp_id'],
            $data['inscomp_vigencia']
        );

        $id = $model->create();
        if ($id) {
            return $this->sendResponse(['message' => 'Vínculo instructor-competencia creado correctamente', 'id' => $id]);
        }
        return $this->sendResponse(['error' => 'Error al crear el vínculo'], 500);
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

        $model = new InstruCompetenciaModel(
            $data['inscomp_id'],
            $data['instructor_inst_id'],
            $data['competxprograma_programa_prog_id'],
            $data['competxprograma_competencia_comp_id'],
            $data['inscomp_vigencia']
        );

        if ($model->update()) {
            return $this->sendResponse(['message' => 'Vínculo actualizado correctamente']);
        }
        return $this->sendResponse(['error' => 'Error al actualizar el vínculo'], 500);
    }

    public function destroy()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            return $this->sendResponse(['error' => 'ID no proporcionado'], 400);
        }

        $model = new InstruCompetenciaModel($id);
        if ($model->delete()) {
            return $this->sendResponse(['message' => 'Vínculo eliminado correctamente']);
        }
        return $this->sendResponse(['error' => 'Error al eliminar el vínculo'], 500);
    }

    private function sendResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
