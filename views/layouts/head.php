<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Validate session
if (!isset($_SESSION['rol'])) {
    header("Location: ../../routing.php?controller=login&action=showLogin");
    exit;
}

$rol = $_SESSION['rol'];
$navItem = isset($activeNavItem) ? $activeNavItem : '';

// RBAC based on activeNavItem
$allowed = true;
if ($navItem) {
    if ($rol === 'centro') {
        $allowed = in_array($navItem, ['dashboard', 'sedes', 'ambientes', 'programas', 'titulos', 'instructores', 'competencias', 'coordinaciones', 'usuarios_coordinadores', 'reportes', 'auditoria_asignacion']);
    } elseif ($rol === 'coordinador') {
        $allowed = in_array($navItem, ['dashboard', 'competencias', 'fichas', 'instruc_comp', 'asignaciones', 'reportes', 'auditoria_asignacion', 'setdata']);
    } elseif ($rol === 'instructor') {
        $allowed = in_array($navItem, ['asignaciones', 'mis_competencias']);
    }

    if (!$allowed) {
        if ($rol === 'centro' || $rol === 'coordinador') {
            header("Location: ../dashboard/index.php");
        } elseif ($rol === 'instructor') {
            header("Location: ../asignacion/instructor_index.php");
        } else {
            header("Location: ../asignacion/instructor_index.php");
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'SENA Académico'; ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="../../assets/js/sede/tailwind-config.js"></script>
    <link href="../../assets/css/styles.css?v=<?php echo time(); ?>" rel="stylesheet">
    <meta name="description" content="Sistema de Gestión Académica de Transversales - SENA Colombia">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-50">