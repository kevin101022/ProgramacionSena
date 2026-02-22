<?php
$pageTitle = 'Dashboard - Gestión de Transversales';
$activeNavItem = 'dashboard';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <h1 class="page-title" style="font-size: 1.5rem;">Panel de Control</h1>
            <p class="text-sm text-gray-400 mt-1">Resumen general del sistema académico</p>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Stats grid -->
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div class="stat-card">
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
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-label">PROGRAMAS</span>
                    <div class="stat-card-icon" style="background: #eff6ff; color: #3b82f6;">
                        <ion-icon src="../../assets/ionicons/book-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="statProgramas">—</span>
                    <span class="stat-card-desc">activos</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-label">FICHAS</span>
                    <div class="stat-card-icon" style="background: #faf5ff; color: #8b5cf6;">
                        <ion-icon src="../../assets/ionicons/folder-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="statFichas">—</span>
                    <span class="stat-card-desc">en formación</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-label">INSTRUCTORES</span>
                    <div class="stat-card-icon" style="background: #fef3c7; color: #f59e0b;">
                        <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="statInstructores">—</span>
                    <span class="stat-card-desc">vinculados</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-label">ASIGNACIONES</span>
                    <div class="stat-card-icon" style="background: #ecfdf5; color: #10b981;">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="statAsignaciones">—</span>
                    <span class="stat-card-desc">programadas</span>
                </div>
            </div>
        </div>

        <!-- Quick access cards -->
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mt-8 mb-4">Accesos Rápidos</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="../sede/index.php" class="bg-white rounded-xl border border-gray-100 p-5 hover:border-green-300 hover:shadow-md transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center group-hover:bg-green-100 transition-colors">
                        <ion-icon src="../../assets/ionicons/business-outline.svg" class="text-sena-green text-xl"></ion-icon>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Gestionar Sedes</h4>
                        <p class="text-xs text-gray-400">Administrar sedes y ambientes</p>
                    </div>
                </div>
            </a>
            <a href="../programa/index.php" class="bg-white rounded-xl border border-gray-100 p-5 hover:border-blue-300 hover:shadow-md transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                        <ion-icon src="../../assets/ionicons/book-outline.svg" class="text-blue-500 text-xl"></ion-icon>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Programas</h4>
                        <p class="text-xs text-gray-400">Gestionar programas de formación</p>
                    </div>
                </div>
            </a>
            <a href="../ficha/index.php" class="bg-white rounded-xl border border-gray-100 p-5 hover:border-purple-300 hover:shadow-md transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                        <ion-icon src="../../assets/ionicons/folder-outline.svg" class="text-purple-500 text-xl"></ion-icon>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Fichas</h4>
                        <p class="text-xs text-gray-400">Administrar fichas de formación</p>
                    </div>
                </div>
            </a>
            <a href="../instructor/index.php" class="bg-white rounded-xl border border-gray-100 p-5 hover:border-amber-300 hover:shadow-md transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                        <ion-icon src="../../assets/ionicons/people-outline.svg" class="text-amber-500 text-xl"></ion-icon>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Instructores</h4>
                        <p class="text-xs text-gray-400">Gestionar instructores</p>
                    </div>
                </div>
            </a>
            <a href="../asignacion/index.php" class="bg-white rounded-xl border border-gray-100 p-5 hover:border-emerald-300 hover:shadow-md transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-emerald-500 text-xl"></ion-icon>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Asignaciones</h4>
                        <p class="text-xs text-gray-400">Calendario de asignaciones</p>
                    </div>
                </div>
            </a>
            <a href="../reportes/index.php" class="bg-white rounded-xl border border-gray-100 p-5 hover:border-rose-300 hover:shadow-md transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center group-hover:bg-rose-100 transition-colors">
                        <ion-icon src="../../assets/ionicons/bar-chart-outline.svg" class="text-rose-500 text-xl"></ion-icon>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Reportes</h4>
                        <p class="text-xs text-gray-400">Informes del sistema</p>
                    </div>
                </div>
            </a>
        </div>
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