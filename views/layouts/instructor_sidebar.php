<?php
$activeNavItem = isset($activeNavItem) ? $activeNavItem : 'asignaciones';
$instructorNombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Instructor';
?>
<aside class="sidebar glass-container">
    <div class="sidebar-header">
        <div class="logo">
            <img src="../../assets/imagenes/LOGOsena.png" alt="SENA Logo" class="logo-img">
            <div class="logo-divider"></div>
            <span class="logo-text">Programaciones</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <p class="nav-section">Mi Espacio</p>
        <a href="../asignacion/index.php" class="nav-item <?php echo ($activeNavItem === 'asignaciones') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
            Mis Asignaciones
        </a>
        <a href="../competencia/index.php" class="nav-item <?php echo ($activeNavItem === 'competencias') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
            Mis Competencias
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="profile-img bg-[#39A900] text-white flex items-center justify-center font-bold text-xl rounded-full h-10 w-10">
                <?php echo strtoupper(substr($instructorNombre, 0, 1)); ?>
            </div>
            <div class="profile-info">
                <p class="profile-name"><?php echo htmlspecialchars($instructorNombre); ?></p>
                <p class="profile-role text-xs text-slate-500">Instructor SENA</p>
            </div>
            <a href="../../routing.php?controller=login&action=logout" class="text-slate-500 hover:text-red-500 transition-colors" title="Cerrar Sesión">
                <ion-icon src="../../assets/ionicons/log-out-outline.svg"></ion-icon>
            </a>
        </div>
    </div>
</aside>

<!-- Custom Notifications -->
<?php require_once dirname(__DIR__) . '/layouts/notifications.php'; ?>
<script src="../../assets/js/utils/notifications.js?v=<?php echo time(); ?>"></script>