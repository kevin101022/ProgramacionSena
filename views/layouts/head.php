<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Validate session
if (!isset($_SESSION['rol'])) {
    header("Location: ../../routing.php?controller=login&action=showLogin");
    exit;
}

$rol = isset($_SESSION['rol']) ? trim(strtolower($_SESSION['rol'])) : '';
$navItem = isset($activeNavItem) ? trim($activeNavItem) : '';

// --- Verificación de Coordinación para Coordinadores ---
$hasCoordinacion = true;
if ($rol === 'coordinador' && !empty($_SESSION['id'])) {
    require_once __DIR__ . '/../../Conexion.php';
    try {
        $db_head   = Conexion::getConnect();
        $stmt_head = $db_head->prepare("SELECT COUNT(*) FROM COORDINACION WHERE coordinador_actual = :id AND estado = 1");
        $stmt_head->execute([':id' => $_SESSION['id']]);
        $hasCoordinacion = ($stmt_head->fetchColumn() > 0);
    } catch (Exception $e) {
        error_log("Head coord query error: " . $e->getMessage());
    }
}

// RBAC based on activeNavItem
$allowed = true;
if ($navItem) {
    if ($rol === 'centro') {
        $allowed = in_array($navItem, ['dashboard', 'sedes', 'ambientes', 'programas', 'titulos', 'instructores', 'competencias', 'coordinaciones', 'usuarios_coordinadores', 'reportes', 'auditoria_asignacion']);
    } elseif ($rol === 'coordinador') {
        $allowed = in_array($navItem, ['dashboard', 'competencias', 'fichas', 'instruc_comp', 'asignaciones', 'reportes', 'auditoria_asignacion', 'setdata']);
    } elseif ($rol === 'instructor') {
        $allowed = in_array($navItem, ['dashboard', 'asignaciones', 'mis_competencias', 'mi_ficha', 'mis_fichas', 'fichas']);
    }

    if (!$allowed) {
        error_log("RBAC Denied: Rol=$rol, item=$navItem, File=" . $_SERVER['PHP_SELF']);
        if ($rol === 'centro' || $rol === 'coordinador') {
            header("Location: ../dashboard/index.php");
        } elseif ($rol === 'instructor') {
            header("Location: ../asignacion/instructor_index.php");
        } else {
            header("Location: ../asignacion/instructor_index.php");
        }
        exit;
    }

    // Redirigir al dashboard si es coordinador sin asignación y está fuera del dashboard
    if ($rol === 'coordinador' && !$hasCoordinacion && $navItem !== 'dashboard') {
        header("Location: ../dashboard/index.php");
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
    <style>
        /* Mobile Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 90;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile Header */
        .mobile-header {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 80;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .mobile-toggle {
            background: none;
            border: none;
            color: var(--gray-800);
            font-size: 28px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Mobile Back Bar (hidden on desktop) */
        .mobile-back-bar {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            position: fixed;
            top: 57px;
            left: 0;
            width: 100%;
            z-index: 79;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .mobile-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: none;
            border: 1px solid var(--gray-200, #e5e7eb);
            color: var(--gray-700, #374151);
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            flex-shrink: 0;
        }

        .mobile-back-btn:hover {
            background: var(--light-green, #E8F5E8);
            color: var(--primary-green, #39A900);
            border-color: var(--primary-green, #39A900);
        }

        .mobile-back-btn ion-icon {
            font-size: 16px;
        }

        .mobile-back-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--gray-800, #1f2937);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            text-align: right;
        }

        @media (max-width: 768px) {
            body {
                display: block;
                /* Disable row flex on mobile */
                overflow-y: auto;
            }

            .mobile-header {
                display: flex;
            }

            .mobile-back-bar {
                display: flex;
            }

            .main-header {
                display: none !important;
            }

            .sidebar {
                transform: translateX(-100%);
                padding-top: 20px;
                box-shadow: 10px 0 30px rgba(0, 0, 0, 0.1);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                height: auto;
                overflow: visible;
                padding-top: 104px;
                /* Space for fixed mobile header (57px) + back bar (47px) */
            }
        }
    </style>
</head>

<body class="bg-gray-50 flex">

    <!-- Mobile Header (Visible only on mobile) -->
    <div class="mobile-header">
        <div class="logo">
            <img src="../../assets/imagenes/LOGOsena.png" alt="SENA Logo" class="logo-img" style="height: 32px;">
            <div class="logo-divider"></div>
            <span class="logo-text" style="font-size: 16px;">Programaciones</span>
        </div>
        <button class="mobile-toggle" id="mobileMenuBtn">
            <ion-icon src="../../assets/ionicons/menu-outline.svg"></ion-icon>
        </button>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Mobile Back Bar (visible only on mobile, replaces hidden main-header) -->
    <div class="mobile-back-bar">
        <?php
        // Determine smart back URL based on role
        $sysRol = $_SESSION['rol'] ?? '';
        $backUrl = ($sysRol === 'instructor') ? '../asignacion/instructor_index.php' : '../dashboard/index.php';
        
        if (isset($activeNavItem)) {
            // Sub-pages: crear, editar, ver, calendarios → go to parent listing
            $currentFile = basename($_SERVER['SCRIPT_NAME'] ?? '', '.php');
            if (in_array($currentFile, ['crear', 'editar', 'ver', 'eliminar'])) {
                $backUrl = ($sysRol === 'instructor') ? 'javascript:history.back()' : 'index.php';
            } elseif (strpos($currentFile, 'calendario_') === 0) {
                // If it's a report calendar for the coordinator
                $backUrl = '../reportes/index.php';
            } elseif ($activeNavItem === 'dashboard') {
                $backUrl = '';
            } elseif ($sysRol === 'instructor' && strpos($currentFile, 'instructor_') === 0 && $currentFile !== 'instructor_index') {
                $backUrl = '../asignacion/instructor_index.php';
            } elseif ($sysRol === 'instructor' && $activeNavItem === 'mis_competencias') {
                 $backUrl = '../asignacion/instructor_index.php';
            }
        }
        ?>
        <?php if (!empty($backUrl)): ?>
            <a href="<?php echo $backUrl; ?>" class="mobile-back-btn">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Volver
            </a>
        <?php endif; ?>
        <span class="mobile-back-title"><?php echo isset($pageTitle) ? htmlspecialchars(str_replace(' - SENA', '', str_replace(' - Programaciones', '', $pageTitle))) : ''; ?></span>
    </div>