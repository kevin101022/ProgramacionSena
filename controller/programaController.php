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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;
        $programas = $this->model->readAll($cent_id);
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
        try {
            $codigo = $_POST['prog_codigo'] ?? null;
            $denominacion = $_POST['prog_denominacion'] ?? null;
            $titpro_id = $_POST['tit_programa_titpro_id'] ?? null;
            $tipo = $_POST['prog_tipo'] ?? null;

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $cent_id = $_SESSION['centro_id'] ?? null;

            if (!$codigo || !$denominacion) {
                $this->sendResponse(['error' => 'Datos incompletos: Código y Denominación son obligatorios'], 400);
                return;
            }

            // Validar que el código sea numérico y esté en el rango de integer de Postgres (32-bit signed)
            if (!is_numeric($codigo)) {
                $this->sendResponse(['error' => 'El código del programa debe ser numérico'], 400);
                return;
            }

            if ($codigo > 2147483647) {
                $this->sendResponse(['error' => 'El código del programa es demasiado largo. Debe ser menor a 2,147,483,647.'], 400);
                return;
            }

            $this->model->setProgCodigo($codigo);
            $this->model->setProgDenominacion($denominacion);
            $this->model->setTitProgramaTitproId($titpro_id);
            $this->model->setProgTipo($tipo);
            $this->model->setCentroFormacionId($cent_id);

            if ($this->model->create()) {
                $this->sendResponse(['message' => 'Programa creado correctamente', 'id' => $codigo]);
            } else {
                $this->sendResponse(['error' => 'Error al ejecutar la creación en la base de datos'], 500);
            }
        } catch (Throwable $e) {
            error_log("Error en programaController::store: " . $e->getMessage());
            $this->sendResponse(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function show($id = null)
    {
        if (!$id) {
            $this->sendResponse(['error' => 'Código de programa requerido'], 400);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        $this->model->setProgCodigo($id);
        $programa = $this->model->read($cent_id);

        if ($programa) {
            $data = $programa[0];
            // Include associated competencias
            $data['competencias'] = $this->model->getCompetenciasByPrograma();
            $this->sendResponse($data);
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

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        if (!$codigo || !$denominacion) {
            $this->sendResponse(['error' => 'Datos incompletos'], 400);
            return;
        }

        $this->model->setProgCodigo($codigo);
        $this->model->setProgDenominacion($denominacion);
        $this->model->setTitProgramaTitproId($titpro_id);
        $this->model->setProgTipo($tipo);
        $this->model->setCentroFormacionId($cent_id);

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
