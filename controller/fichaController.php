<?php
require_once dirname(__DIR__) . '/model/FichaModel.php';

class fichaController
{
    public function index()
    {
        $model = new FichaModel();
        $fichas = $model->readAll();
        $this->sendResponse($fichas);
    }

    public function store()
    {
        $id = $_POST['fich_id'] ?? null;
        $prog_id = $_POST['programa_prog_id'] ?? null;
        $inst_id = $_POST['instructor_inst_id'] ?? null;
        $jornada = $_POST['fich_jornada'] ?? null;
        $coord_id = $_POST['coordinacion_id'] ?? null;
        $fecha_ini = $_POST['fich_fecha_ini_lectiva'] ?? null;
        $fecha_fin = $_POST['fich_fecha_fin_lectiva'] ?? null;

        if (!$id || !$prog_id) {
            $this->sendResponse(['error' => 'Datos incompletos'], 400);
            return;
        }

        $model = new FichaModel(
            $id,
            $prog_id,
            $inst_id,
            $jornada,
            $coord_id,
            $fecha_ini,
            $fecha_fin
        );

        if ($model->create()) {
            $this->sendResponse(['message' => 'Ficha creada correctamente', 'id' => $id]);
        } else {
            $this->sendResponse(['error' => 'Error al crear ficha'], 500);
        }
    }

    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'Número de ficha requerido'], 400);
        }

        $model = new FichaModel($id);
        $ficha = $model->read();

        if ($ficha) {
            $this->sendResponse($ficha[0]);
        } else {
            $this->sendResponse(['error' => 'Ficha no encontrada'], 404);
        }
    }

    public function update()
    {
        $id = $_POST['fich_id'] ?? null;
        $prog_id = $_POST['programa_prog_id'] ?? null;
        $inst_id = $_POST['instructor_inst_id'] ?? null;
        $jornada = $_POST['fich_jornada'] ?? null;
        $coord_id = $_POST['coordinacion_id'] ?? null;
        $fecha_ini = $_POST['fich_fecha_ini_lectiva'] ?? null;
        $fecha_fin = $_POST['fich_fecha_fin_lectiva'] ?? null;

        if (!$id) {
            $this->sendResponse(['error' => 'Datos incompletos'], 400);
        }

        $model = new FichaModel(
            $id,
            $prog_id,
            $inst_id,
            $jornada,
            $coord_id,
            $fecha_ini,
            $fecha_fin
        );

        if ($model->update()) {
            $this->sendResponse(['message' => 'Ficha actualizada correctamente']);
        } else {
            $this->sendResponse(['error' => 'Error al actualizar ficha'], 500);
        }
    }

    public function destroy($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'Número de ficha requerido'], 400);
        }

        $model = new FichaModel($id);
        if ($model->delete()) {
            $this->sendResponse(['message' => 'Ficha eliminada correctamente']);
        } else {
            $this->sendResponse(['error' => 'Error al eliminar ficha'], 500);
        }
    }

    private function sendResponse($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
