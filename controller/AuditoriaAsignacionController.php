<?php
require_once 'model/AuditoriaAsignacionModel.php';

class AuditoriaAsignacionController
{
    public function index()
    {
        // Require authentication/session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id'])) {
            header("Location: routing.php?controller=login&action=showLogin");
            exit;
        }

        // Check if admin/coordinador role has access to audits
        $rol = $_SESSION['rol'] ?? '';
        if ($rol !== 'coordinador' && $rol !== 'centro') {
            die("Acceso denegado. Solo administradores pueden ver la auditoría.");
        }

        $cent_id = $_SESSION['centro_id'] ?? null;
        $model = new AuditoriaAsignacionModel();
        $auditorias = $model->getAll($cent_id);

        require_once 'views/auditoria_asignacion/index.php';
    }
}
