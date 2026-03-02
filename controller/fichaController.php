<?php
require_once dirname(__DIR__) . '/model/FichaModel.php';
require_once dirname(__DIR__) . '/Conexion.php';

class fichaController
{
    /**
     * Obtiene en tiempo real el coord_id del coordinador logueado desde la DB.
     * Así si le cambian la coordinación, ve solo la nueva inmediatamente.
     */
    private function getCoordIdActual(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $rol = $_SESSION['rol'] ?? null;
        if ($rol !== 'coordinador') return null;

        $num_doc = $_SESSION['id'] ?? null;
        if (!$num_doc) return null;

        $db = Conexion::getConnect();
        $stmt = $db->prepare(
            "SELECT coord_id FROM COORDINACION WHERE coordinador_actual = :num_doc AND estado = 1 LIMIT 1"
        );
        $stmt->execute([':num_doc' => $num_doc]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['coord_id'] : null;
    }

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $rol = $_SESSION['rol'] ?? null;
        $cent_id = $_SESSION['centro_id'] ?? null;

        // Si es coordinador, filtramos por SU coordinación actual (consultada en DB)
        $coord_id = null;
        if ($rol === 'coordinador') {
            $coord_id = $this->getCoordIdActual();
        }

        $model = new FichaModel();
        $fichas = $model->readAll($cent_id, $coord_id);
        $this->sendResponse($fichas);
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = $_POST['fich_id'] ?? null;
        $prog_id = $_POST['programa_prog_id'] ?? null;
        $inst_id = $_POST['instructor_inst_id'] ?? null;
        $jornada = $_POST['fich_jornada'] ?? null;
        $fecha_ini = $_POST['fich_fecha_ini_lectiva'] ?? null;
        $fecha_fin = $_POST['fich_fecha_fin_lectiva'] ?? null;

        // Auto-asignar coordinación consultando la DB en tiempo real
        $rol = $_SESSION['rol'] ?? null;
        if ($rol === 'coordinador') {
            $coord_id = $this->getCoordIdActual(); // Siempre fresco desde la DB
        } else {
            $coord_id = $_POST['coordinacion_id'] ?? null;
        }

        if (!$id || !$prog_id) {
            $this->sendResponse(['error' => 'Datos incompletos: Número de ficha y programa son obligatorios'], 400);
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cent_id = $_SESSION['centro_id'] ?? null;

        if (!$id) {
            $this->sendResponse(['error' => 'Número de ficha requerido'], 400);
            return;
        }

        $model = new FichaModel($id);
        $ficha = $model->read($cent_id);

        if ($ficha) {
            $this->sendResponse($ficha[0]);
        } else {
            $this->sendResponse(['error' => 'Ficha no encontrada o sin acceso'], 404);
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
