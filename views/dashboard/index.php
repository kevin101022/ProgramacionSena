<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$pageTitle     = 'Dashboard - Programaciones';
$activeNavItem = 'dashboard';

$nombreUsuario = $_SESSION['nombre'] ?? 'Usuario SENA';
$rolUsuario    = $_SESSION['rol']    ?? '';
$rolLabel = match ($rolUsuario) {
    'centro'      => 'Centro de Formación',
    'coordinador' => 'Coordinador Académico',
    'instructor'  => 'Instructor',
    default       => 'Administrador'
};

// Obtener coordinación activa del coordinador
$nombreCoordinacion = null;
$centNombre         = null;
if ($rolUsuario === 'coordinador' && !empty($_SESSION['id'])) {
    $conexionPath = __DIR__ . '/../../Conexion.php';
    if (file_exists($conexionPath)) {
        require_once $conexionPath;
        try {
            $db   = Conexion::getConnect();
            $stmt = $db->prepare(
                "SELECT c.coord_descripcion, cf.cent_nombre
                 FROM COORDINACION c
                 LEFT JOIN CENTRO_FORMACION cf ON c.centro_formacion_cent_id = cf.cent_id
                 WHERE c.coordinador_actual = :num_doc AND c.estado = 1
                 LIMIT 1"
            );
            $stmt->execute([':num_doc' => $_SESSION['id']]);
            $coord              = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($coord) {
                $nombreCoordinacion = $coord['coord_descripcion'] ?? null;
                $centNombre         = $coord['cent_nombre']        ?? null;
            }
        } catch (Exception $e) {
            error_log("Dashboard coord query error: " . $e->getMessage());
        }
    }
}

require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <h1 class="page-title">Panel de Control</h1>
            <p class="stat-card-desc">Resumen general del sistema académico</p>
        </div>
        <div class="user-profile-header">
            <div class="user-info">
                <span class="user-role"><?php echo htmlspecialchars($rolLabel); ?></span>
                <span class="user-name"><?php echo htmlspecialchars($nombreUsuario); ?></span>
            </div>
            <div class="user-avatar">
                <ion-icon src="../../assets/ionicons/person-circle-outline.svg"></ion-icon>
            </div>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Welcome Section -->
        <section class="welcome-section glass-container">
            <div class="welcome-content">
                <?php if ($rolUsuario === 'coordinador' && $nombreCoordinacion): ?>
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                        <span style="background:var(--sena-green,#39a900); color:#fff; font-size:10px; font-weight:900; letter-spacing:0.15em; text-transform:uppercase; padding:3px 12px; border-radius:999px;">
                            Coordinación
                        </span>
                    </div>
                    <h2 style="color:var(--sena-green,#39a900); margin-bottom:4px;"><?php echo htmlspecialchars($nombreCoordinacion); ?></h2>
                    <?php if ($centNombre): ?>
                        <p style="font-size:12px; color:#666; font-weight:600; margin-bottom:8px;">
                            <ion-icon src="../../assets/ionicons/business-outline.svg" style="vertical-align:middle; margin-right:4px;"></ion-icon>
                            <?php echo htmlspecialchars($centNombre); ?>
                        </p>
                    <?php endif; ?>
                    <p>Aquí tienes el resumen de tu área en <strong>Programaciones SENA</strong>.</p>
                <?php elseif ($rolUsuario === 'coordinador'): ?>
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                        <span style="background:#e67e22; color:#fff; font-size:10px; font-weight:900; letter-spacing:0.15em; text-transform:uppercase; padding:3px 12px; border-radius:999px;">
                            Atención
                        </span>
                    </div>
                    <h2 style="color:#e67e22; margin-bottom:4px;">Perfil sin Asignación</h2>
                    <p style="color:#666; font-size:13px; font-weight:500; margin-top:10px;">
                        <ion-icon src="../../assets/ionicons/warning-outline.svg" style="vertical-align:middle; margin-right:4px; font-size:18px; color:#e67e22;"></ion-icon>
                        Actualmente no tienes un <strong>Área de Coordinación</strong> vinculada a tu cuenta.
                    </p>
                    <p style="font-size:13px; color:#777; margin-top:8px;">
                        Para poder gestionar fichas, instructores y programaciones, es necesario que el administrador del <strong>Centro de Formación</strong> te asigne a una coordinación desde el panel administrativo.
                    </p>
                <?php else: ?>
                    <h2>¡Bienvenido de nuevo!</h2>
                    <p>Aquí tienes un resumen de lo que está sucediendo hoy en <strong>Programaciones</strong>.</p>
                <?php endif; ?>
            </div>
            <?php if ($rolUsuario !== 'coordinador' || $nombreCoordinacion): ?>
                <div class="welcome-actions">
                    <a href="../asignacion/index.php" class="btn btn-primary">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                        Ver Programación
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <?php if ($rolUsuario !== 'coordinador' || $nombreCoordinacion): ?>
            <!-- Stats grid -->
            <div class="stats-grid">
                <?php if ($rolUsuario === 'centro'): ?>
                    <div class="stat-card">
                        <div class="stat-card-bg-icon">
                            <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                        </div>
                        <div class="stat-card-header">
                            <span class="stat-card-label">SEDES</span>
                            <div class="stat-card-icon green">
                                <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                            </div>
                        </div>
                        <div class="stat-card-body">
                            <span class="stat-card-number" id="statSedes">—</span>
                            <span class="stat-card-desc">registradas</span>
                        </div>
                        <div class="stat-card-pill-container">
                            <div class="stat-pill">
                                <ion-icon src="../../assets/ionicons/location-outline.svg"></ion-icon>
                                Cobertura
                            </div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-bg-icon">
                            <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                        </div>
                        <div class="stat-card-header">
                            <span class="stat-card-label">PROGRAMAS</span>
                            <div class="stat-card-icon blue">
                                <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                            </div>
                        </div>
                        <div class="stat-card-body">
                            <span class="stat-card-number" id="statProgramas">—</span>
                            <span class="stat-card-desc">activos</span>
                        </div>
                        <div class="stat-card-pill-container">
                            <div class="stat-pill">
                                <ion-icon src="../../assets/ionicons/star-outline.svg"></ion-icon>
                                Calidad
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="stat-card">
                    <div class="stat-card-bg-icon">
                        <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                    </div>
                    <div class="stat-card-header">
                        <span class="stat-card-label">FICHAS</span>
                        <div class="stat-card-icon purple">
                            <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                        </div>
                    </div>
                    <div class="stat-card-body">
                        <span class="stat-card-number" id="statFichas">—</span>
                        <span class="stat-card-desc">en formación</span>
                        <p class="stat-card-context">Grupos vinculados a procesos de aprendizaje.</p>
                    </div>
                    <div class="stat-card-pill-container">
                        <div class="stat-pill">
                            <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                            Aprendices
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-bg-icon">
                        <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                    </div>
                    <div class="stat-card-header">
                        <span class="stat-card-label">INSTRUCTORES</span>
                        <div class="stat-card-icon amber">
                            <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                        </div>
                    </div>
                    <div class="stat-card-body">
                        <span class="stat-card-number" id="statInstructores">—</span>
                        <span class="stat-card-desc">vinculados</span>
                        <p class="stat-card-context">Personal docente y técnico de la institución.</p>
                    </div>
                    <div class="stat-card-pill-container">
                        <div class="stat-pill">
                            <ion-icon src="../../assets/ionicons/checkmark-done-outline.svg"></ion-icon>
                            Activos
                        </div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-bg-icon">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                    </div>
                    <div class="stat-card-header">
                        <span class="stat-card-label">ASIGNACIONES</span>
                        <div class="stat-card-icon emerald">
                            <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                        </div>
                    </div>
                    <div class="stat-card-body">
                        <span class="stat-card-number" id="statAsignaciones">—</span>
                        <span class="stat-card-desc">programadas</span>
                        <p class="stat-card-context">Actividades y horarios académicos establecidos.</p>
                    </div>
                    <div class="stat-card-pill-container">
                        <div class="stat-pill">
                            <ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon>
                            Control
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick access cards -->
            <h3 class="section-title">Accesos Rápidos</h3>
            <div class="quick-access-grid">
                <?php if ($rolUsuario === 'centro'): ?>
                    <a href="../sede/index.php" class="quick-access-card accent-green">
                        <div class="quick-access-icon green">
                            <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                        </div>
                        <div class="quick-access-info">
                            <h4>Gestionar Sedes</h4>
                            <p>Administrar sedes y ambientes</p>
                        </div>
                    </a>
                    <a href="../programa/index.php" class="quick-access-card accent-blue">
                        <div class="quick-access-icon blue">
                            <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                        </div>
                        <div class="quick-access-info">
                            <h4>Programas</h4>
                            <p>Gestionar programas de formación</p>
                        </div>
                    </a>
                <?php endif; ?>
                <a href="../ficha/index.php" class="quick-access-card accent-purple">
                    <div class="quick-access-icon purple">
                        <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                    </div>
                    <div class="quick-access-info">
                        <h4>Fichas</h4>
                        <p>Administrar fichas de formación</p>
                    </div>
                </a>
                <?php if ($rolUsuario === 'coordinador'): ?>
                    <a href="../instru_competencia/index.php" class="quick-access-card accent-cyan">
                        <div class="quick-access-icon" style="background: #06b6d420; color: #06b6d4;">
                            <ion-icon src="../../assets/ionicons/git-merge-outline.svg"></ion-icon>
                        </div>
                        <div class="quick-access-info">
                            <h4>Habilitaciones</h4>
                            <p>Instructor x Competencia</p>
                        </div>
                    </a>
                <?php else: ?>
                    <a href="../instructor/index.php" class="quick-access-card accent-amber">
                        <div class="quick-access-icon amber">
                            <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                        </div>
                        <div class="quick-access-info">
                            <h4>Instructores</h4>
                            <p>Gestionar instructores</p>
                        </div>
                    </a>
                <?php endif; ?>
                <a href="../asignacion/index.php" class="quick-access-card accent-emerald">
                    <div class="quick-access-icon emerald">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                    </div>
                    <div class="quick-access-info">
                        <h4>Asignaciones</h4>
                        <p>Calendario de asignaciones</p>
                    </div>
                </a>
                <a href="../reportes/index.php" class="quick-access-card accent-rose">
                    <div class="quick-access-icon rose">
                        <ion-icon src="../../assets/ionicons/bar-chart-outline.svg"></ion-icon>
                    </div>
                    <div class="quick-access-info">
                        <h4>Reportes</h4>
                        <p>Informes del sistema</p>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const headers = {
            'Accept': 'application/json'
        };
        const endpoints = {
            statFichas: 'ficha',
            statInstructores: 'instructor',
            statAsignaciones: 'asignacion'
        };
        <?php if ($rolUsuario === 'centro'): ?>
            endpoints.statSedes = 'sede';
            endpoints.statProgramas = 'programa';
        <?php endif; ?>

        for (const [elId, ctrl] of Object.entries(endpoints)) {
            try {
                const res = await fetch(`../../routing.php?controller=${ctrl}&action=index`, {
                    headers
                });
                const data = await res.json();
                const el = document.getElementById(elId);
                if (el && Array.isArray(data)) el.textContent = data.length;
            } catch (e) {
                console.error(`Error fetching ${ctrl}:`, e);
            }
        }
    });
</script>
</body>

</html>