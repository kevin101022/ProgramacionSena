<?php
$activeNavItem = isset($activeNavItem) ? $activeNavItem : 'sedes';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="../../assets/imagenes/LOGOsena.png" alt="SENA Logo" class="logo-img">
            <div class="logo-divider"></div>
            <span class="logo-text">Gestión de Transversales</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <p class="nav-section">Principal</p>
        <a href="../dashboard/index.php" class="nav-item <?php echo ($activeNavItem === 'dashboard') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/grid-outline.svg"></ion-icon>
            Dashboard
        </a>

        <p class="nav-section">Gestión</p>
        <a href="../sede/index.php" class="nav-item <?php echo ($activeNavItem === 'sedes') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
            Sedes
        </a>
        <a href="../ambiente/index.php" class="nav-item <?php echo ($activeNavItem === 'ambientes') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/cube-outline.svg"></ion-icon>
            Ambientes
        </a>
        <a href="../centro_formacion/index.php" class="nav-item <?php echo ($activeNavItem === 'centros') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
            Centros de Formación
        </a>
        <a href="../coordinacion/index.php" class="nav-item <?php echo ($activeNavItem === 'coordinaciones') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/people-circle-outline.svg"></ion-icon>
            Coordinaciones
        </a>
        <a href="../ficha/index.php" class="nav-item <?php echo ($activeNavItem === 'fichas') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
            Fichas
        </a>
        <a href="../asignacion/index.php" class="nav-item <?php echo ($activeNavItem === 'asignaciones') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
            Asignaciones
        </a>
        <a href="../programa/index.php" class="nav-item <?php echo ($activeNavItem === 'programas') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
            Programas
        </a>
        <a href="../titulo_programa/index.php" class="nav-item <?php echo ($activeNavItem === 'titulos') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/ribbon-outline.svg"></ion-icon>
            Títulos de Programa
        </a>
        <a href="../competencia/index.php" class="nav-item <?php echo ($activeNavItem === 'competencias') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
            Competencias
        </a>
        <a href="../instructor/index.php" class="nav-item <?php echo ($activeNavItem === 'instructores') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
            Instructores
        </a>


        <p class="nav-section">Informes</p>
        <a href="../reportes/index.php" class="nav-item <?php echo ($activeNavItem === 'reportes') ? 'active' : ''; ?>">
            <ion-icon src="../../assets/ionicons/bar-chart-outline.svg"></ion-icon>
            Reportes
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <img src="../../assets/imagenes/Foto-nota-2024-01-03T181318.611.webp" alt="Coordinador" class="profile-img">
            <div class="profile-info">
                <p class="profile-name">Carlos Rodriguez</p>
                <p class="profile-role">Coordinador Académico</p>
            </div>
            <ion-icon src="../../assets/ionicons/log-out-outline.svg"></ion-icon>
        </div>
    </div>
</aside>

<!-- Custom Notifications -->
<?php require_once dirname(__DIR__) . '/layouts/notifications.php'; ?>
<script src="../../assets/js/utils/notifications.js?v=<?php echo time(); ?>"></script>