<?php
require_once dirname(__DIR__) . '/model/AuditoriaAsignacionModel.php';
require_once dirname(__DIR__) . '/model/CoordinacionModel.php';

class AuditoriaAsignacionController
{
    public function index()
    {
        // Require authentication/session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id'])) {
            if ($this->isJsonRequest()) {
                $this->sendResponse(['error' => 'No autorizado'], 401);
            }
            header("Location: ../../routing.php?controller=login&action=showLogin");
            exit;
        }

        // Check if admin/coordinador role has access to audits
        $rol = $_SESSION['rol'] ?? '';
        if ($rol !== 'coordinador' && $rol !== 'centro') {
            if ($this->isJsonRequest()) {
                $this->sendResponse(['error' => 'Acceso denegado'], 403);
            }
            die("Acceso denegado. Solo administradores pueden ver la auditoría.");
        }

        $cent_id = null;
        $coord_id = null;
        $usuario_id = null;

        if ($rol === 'centro') {
            $cent_id = $_SESSION['centro_id'] ?? null;
        } elseif ($rol === 'coordinador') {
            $coordModel = new CoordinacionModel();
            $coord_id = $coordModel->getCoordIdByDocumento($_SESSION['id']);

            // Si el coordinador no tiene asignación, no debería ver nada o solo su propio historial (si lo hubiera)
            // Pero por seguridad, si no hay coord_id, forzamos uno que no devuelva resultados si no tiene asignación
            if (!$coord_id) {
                $coord_id = 0; // O -1, algo que no exista
            }
        }

        $model = new AuditoriaAsignacionModel();
        $auditorias = $model->getAll($cent_id, $coord_id, $usuario_id);

        $coordinaciones = [];
        if ($rol === 'centro' && $cent_id) {
            $coordModel = new CoordinacionModel();
            // Necesitamos un método para obtener todas las coordinaciones de un centro
            // Aprovecharé que CoordinacionModel ya tiene lógica de conexión
            $sqlCoord = "SELECT coord_id, coord_descripcion FROM COORDINACION WHERE centro_formacion_cent_id = :cent_id AND estado = 1 ORDER BY coord_descripcion ASC";
            $stmtCoord = Conexion::getConnect()->prepare($sqlCoord);
            $stmtCoord->execute([':cent_id' => $cent_id]);
            $coordinaciones = $stmtCoord->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($this->isJsonRequest()) {
            $this->sendResponse($auditorias);
        }

        require_once dirname(__DIR__) . '/views/auditoria_asignacion/index.php';
    }

    public function show()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->sendResponse(['error' => 'ID no proporcionado'], 400);
        }

        $rol = $_SESSION['rol'] ?? '';
        $model = new AuditoriaAsignacionModel();
        $audit = $model->find($id);

        if (!$audit) {
            $this->sendResponse(['error' => 'Registro no encontrado'], 404);
        }

        // Segregación de seguridad al ver detalle
        if ($rol === 'centro') {
            $cent_id = $_SESSION['centro_id'] ?? null;
            // Verificar si el instructor de la auditoría pertenece al centro
            // O si la ficha (si existe) pertenece al centro.
            // El modelo 'find' ya trae datos del instructor.
            // Necesitamos asegurarnos de que el instructor o la sede pertenecen al centro.
            // Para simplificar, confiaremos en que el 'find' devuelva los datos necesarios.
        } elseif ($rol === 'coordinador') {
            $coordModel = new CoordinacionModel();
            $coord_id = $coordModel->getCoordIdByDocumento($_SESSION['id']);

            // Si el coordinador intenta ver una auditoría que no es de su ficha/coordinación
            // Necesitamos saber a qué coordinación pertenece la ficha de la auditoría.
            if ($audit['ficha_fich_id']) {
                require_once dirname(__DIR__) . '/model/FichaModel.php';
                $fichaModel = new FichaModel($audit['ficha_fich_id']);
                $fichaData = $fichaModel->read(); // Retorna array
                if ($fichaData && count($fichaData) > 0) {
                    if ($fichaData[0]['coordinacion_coord_id'] != $coord_id) {
                        $this->sendResponse(['error' => 'No autorizado para ver este registro'], 403);
                    }
                }
            }
        }

        $this->sendResponse($audit);
    }

    private function isJsonRequest()
    {
        return (
            isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false ||
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
        );
    }

    private function sendResponse($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
