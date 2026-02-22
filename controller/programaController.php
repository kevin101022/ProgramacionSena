<?php
require_once dirname(__DIR__) . '/model/ProgramaModel.php';
require_once dirname(__DIR__) . '/model/TituloProgramaModel.php';

class programaController
{
    private $model;

    public function __construct()
    {
        $this->model = new ProgramaModel();
    }

    public function index()
    {
        $programas = $this->model->readAll();
        $this->sendResponse($programas);
    }

    public function getTitulos()
    {
        $tituloModel = new TituloProgramaModel();
        $titulos = $tituloModel->readAll();
        $this->sendResponse($titulos);
    }

    public function store()
    {
        $codigo = $_POST['prog_codigo'] ?? null;
        $denominacion = $_POST['prog_denominacion'] ?? null;
        $titpro_id = $_POST['tit_programa_titpro_id'] ?? null;
        $tipo = $_POST['prog_tipo'] ?? null;

        if (!$codigo || !$denominacion) {
            $this->sendResponse(['error' => 'Datos incompletos'], 400);
            return;
        }

        $this->model->setProgCodigo($codigo);
        $this->model->setProgDenominacion($denominacion);
        $this->model->setTitProgramaTitproId($titpro_id);
        $this->model->setProgTipo($tipo);

        $newId = $this->model->create();
        if ($newId) {
            $this->sendResponse(['message' => 'Programa creado correctamente', 'id' => $newId]);
        } else {
            $this->sendResponse(['error' => 'Error al crear programa'], 500);
        }
    }

    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'Código de programa requerido'], 400);
        }

        $this->model->setProgCodigo($id);
        $programa = $this->model->read();

        if ($programa) {
            $this->sendResponse($programa[0]);
        } else {
            $this->sendResponse(['error' => 'Programa no encontrado'], 404);
        }
    }

    public function update()
    {
        $codigo = $_POST['prog_codigo'] ?? null;
        $denominacion = $_POST['prog_denominacion'] ?? null;
        $titpro_id = $_POST['tit_programa_titpro_id'] ?? null;
        $tipo = $_POST['prog_tipo'] ?? null;

        if (!$codigo || !$denominacion) {
            $this->sendResponse(['error' => 'Datos incompletos'], 400);
            return;
        }

        $this->model->setProgCodigo($codigo);
        $this->model->setProgDenominacion($denominacion);
        $this->model->setTitProgramaTitproId($titpro_id);
        $this->model->setProgTipo($tipo);

        if ($this->model->update()) {
            $this->sendResponse(['message' => 'Programa actualizado correctamente']);
        } else {
            $this->sendResponse(['error' => 'Error al actualizar programa'], 500);
        }
    }

    public function destroy($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'Código requerido'], 400);
        }

        $this->model->setProgCodigo($id);
        if ($this->model->delete()) {
            $this->sendResponse(['message' => 'Programa eliminado correctamente']);
        } else {
            $this->sendResponse(['error' => 'Error al eliminar programa'], 500);
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
