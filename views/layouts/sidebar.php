<?php
$activeNavItem = isset($activeNavItem) ? $activeNavItem : 'dashboard';
$rol_usuario = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'centro';
$nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
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
        <!-- Dashboard for Centro and Coordinador -->
        <?php if ($rol_usuario === 'centro' || $rol_usuario === 'coordinador'): ?>
            <p class="nav-section">Principal</p>
            <a href="../dashboard/index.php" class="nav-item <?php echo ($activeNavItem === 'dashboard') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/grid-outline.svg"></ion-icon>
                Dashboard
            </a>
        <?php endif; ?>

        <?php if ($rol_usuario === 'centro'): ?>
            <!-- Menú Centro Formación -->
            <p class="nav-section">Gestión Centro</p>
            <a href="../sede/index.php" class="nav-item <?php echo ($activeNavItem === 'sedes') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                Sedes
            </a>
            <a href="../ambiente/index.php" class="nav-item <?php echo ($activeNavItem === 'ambientes') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/cube-outline.svg"></ion-icon>
                Ambientes
            </a>
            <a href="../programa/index.php" class="nav-item <?php echo ($activeNavItem === 'programas') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                Programas
            </a>
            <a href="../titulo_programa/index.php" class="nav-item <?php echo ($activeNavItem === 'titulos') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/ribbon-outline.svg"></ion-icon>
                Títulos de Programa
            </a>
            <a href="../instructor/index.php" class="nav-item <?php echo ($activeNavItem === 'instructores') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                Instructores
            </a>
            <a href="../competencia/index.php" class="nav-item <?php echo ($activeNavItem === 'competencias') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                Competencias
            </a>
            <a href="../usuario_coordinador/index.php" class="nav-item <?php echo ($activeNavItem === 'usuarios_coordinadores') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/person-add-outline.svg"></ion-icon>
                Coordinadores (Persona)
            </a>
            <a href="../coordinacion/index.php" class="nav-item <?php echo ($activeNavItem === 'coordinaciones') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/people-circle-outline.svg"></ion-icon>
                Áreas de Coordinación
            </a>
            <a href="../auditoria_asignacion/index.php" class="nav-item <?php echo ($activeNavItem === 'auditoria_asignacion') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/receipt-outline.svg"></ion-icon>
                Auditoría
            </a>


        <?php elseif ($rol_usuario === 'coordinador'): ?>
            <!-- Menú Coordinador -->
            <p class="nav-section">Gestión Académica</p>

            <?php if (isset($hasCoordinacion) && $hasCoordinacion): ?>
                <a href="../competencia/index.php" class="nav-item <?php echo ($activeNavItem === 'competencias') ? 'active' : ''; ?>">
                    <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                    Competencias (Consulta)
                </a>
                <a href="../ficha/index.php" class="nav-item <?php echo ($activeNavItem === 'fichas') ? 'active' : ''; ?>">
                    <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                    Fichas
                </a>
                <a href="../instru_competencia/index.php" class="nav-item <?php echo ($activeNavItem === 'instruc_comp') ? 'active' : ''; ?>">
                    <ion-icon src="../../assets/ionicons/git-merge-outline.svg"></ion-icon>
                    Instructor x Competencia
                </a>
                <a href="../asignacion/index.php" class="nav-item <?php echo ($activeNavItem === 'asignaciones') ? 'active' : ''; ?>">
                    <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                    Asignaciones
                </a>
                <a href="../auditoria_asignacion/index.php" class="nav-item <?php echo ($activeNavItem === 'auditoria_asignacion') ? 'active' : ''; ?>">
                    <ion-icon src="../../assets/ionicons/receipt-outline.svg"></ion-icon>
                    Auditoría
                </a>
                <a href="../setdata/index.php" class="nav-item <?php echo ($activeNavItem === 'setdata') ? 'active' : ''; ?>">
                    <ion-icon src="../../assets/ionicons/analytics-outline.svg"></ion-icon>
                    Sincronizar Datos (CSV)
                </a>
            <?php else: ?>
                <div class="px-4 py-2 text-xs text-amber-600 font-medium bg-amber-50 rounded-lg mx-2 my-1 border border-amber-100 flex items-center gap-2">
                    <ion-icon src="../../assets/ionicons/warning-outline.svg" class="text-lg"></ion-icon>
                    Sin coordinación asignada
                </div>
            <?php endif; ?>

        <?php elseif ($rol_usuario === 'instructor'): ?>
            <!-- Menú Instructor -->
            <p class="nav-section">Mi Espacio</p>
            <a href="../asignacion/index.php" class="nav-item <?php echo ($activeNavItem === 'asignaciones') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                Mis Asignaciones
            </a>
            <a href="../instructor/competencias.php" class="nav-item <?php echo ($activeNavItem === 'mis_competencias') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                Mis Competencias
            </a>
            <a href="../instructor/fichas.php" class="nav-item <?php echo ($activeNavItem === 'mis_fichas') ? 'active' : ''; ?>">
                <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                Mis Fichas (Líder)
            </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="profile-img bg-[#39A900] text-white flex items-center justify-center font-bold text-xl rounded-full h-10 w-10">
                <?php echo strtoupper(substr($nombre_usuario, 0, 1)); ?>
            </div>
            <div class="profile-info">
                <p class="profile-name"><?php echo htmlspecialchars($nombre_usuario); ?></p>
                <p class="profile-role text-xs text-slate-500"><?php echo ucfirst($rol_usuario); ?></p>
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