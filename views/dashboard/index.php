<?php
$pageTitle = 'Dashboard - Programaciones';
$activeNavItem = 'dashboard';
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
                <span class="user-role">Administrador</span>
                <span class="user-name">Usuario SENA</span>
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
                <h2>¡Bienvenido de nuevo!</h2>
                <p>Aquí tienes un resumen de lo que está sucediendo hoy en <strong>Programaciones</strong>.</p>
            </div>
            <div class="welcome-actions">
                <a href="../asignacion/index.php" class="btn btn-primary">
                    <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                    Ver Programación
                </a>
            </div>
        </section>
        <!-- Stats grid -->
        <div class="stats-grid">
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
                    <p class="stat-card-context">Infraestructura física distribuida para cobertura nacional.</p>
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
                    <p class="stat-card-context">Oferta académica vigente para formación titulada.</p>
                </div>
                <div class="stat-card-pill-container">
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/star-outline.svg"></ion-icon>
                        Calidad
                    </div>
                </div>
            </div>
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
            <a href="../ficha/index.php" class="quick-access-card accent-purple">
                <div class="quick-access-icon purple">
                    <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                </div>
                <div class="quick-access-info">
                    <h4>Fichas</h4>
                    <p>Administrar fichas de formación</p>
                </div>
            </a>
            <a href="../instructor/index.php" class="quick-access-card accent-amber">
                <div class="quick-access-icon amber">
                    <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                </div>
                <div class="quick-access-info">
                    <h4>Instructores</h4>
                    <p>Gestionar instructores</p>
                </div>
            </a>
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

        <!-- Activity Feed -->
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const headers = {
            'Accept': 'application/json'
        };
        const endpoints = {
            statSedes: 'sede',
            statProgramas: 'programa',
            statFichas: 'ficha',
            statInstructores: 'instructor',
            statAsignaciones: 'asignacion'
        };

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