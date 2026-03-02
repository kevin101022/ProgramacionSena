<?php
require_once dirname(__DIR__) . '/model/AuditoriaAsignacionModel.php';

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
        $usuario_id = null;

        if ($rol === 'centro') {
            $cent_id = $_SESSION['centro_id'] ?? null;
        } elseif ($rol === 'coordinador') {
            $usuario_id = $_SESSION['id'] ?? null;
        }

        $model = new AuditoriaAsignacionModel();
        $auditorias = $model->getAll($cent_id, $usuario_id);

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

        $model = new AuditoriaAsignacionModel();
        $audit = $model->find($id);

        if ($audit) {
            $this->sendResponse($audit);
        }
        $this->sendResponse(['error' => 'Registro no encontrado'], 404);
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
