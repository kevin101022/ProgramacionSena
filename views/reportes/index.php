<?php
$pageTitle = "Reportes - SENA";
$activeNavItem = 'reportes';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Reportes</span>
            </nav>
            <h1 class="page-title">Reportes del Sistema</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Selector de reporte -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <button class="report-card" data-report="instructoresPorCentro">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center mb-3">
                    <ion-icon src="../../assets/ionicons/people-outline.svg" class="text-sena-green text-2xl"></ion-icon>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-1">Instructores por Centro</h3>
                <p class="text-xs text-gray-400">Listado agrupado por centro de formación</p>
            </button>

            <button class="report-card" data-report="fichasActivasPorPrograma">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center mb-3">
                    <ion-icon src="../../assets/ionicons/folder-outline.svg" class="text-blue-500 text-2xl"></ion-icon>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-1">Fichas por Programa</h3>
                <p class="text-xs text-gray-400">Fichas activas agrupadas por programa</p>
            </button>

            <button class="report-card" data-report="asignacionesPorInstructor">
                <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center mb-3">
                    <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-purple-500 text-2xl"></ion-icon>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-1">Asignaciones por Instructor</h3>
                <p class="text-xs text-gray-400">Carga académica de cada instructor</p>
            </button>

            <button class="report-card" data-report="competenciasPorPrograma">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center mb-3">
                    <ion-icon src="../../assets/ionicons/book-outline.svg" class="text-amber-500 text-2xl"></ion-icon>
                </div>
                <h3 class="font-bold text-gray-900 text-sm mb-1">Competencias por Programa</h3>
                <p class="text-xs text-gray-400">Relación competencias-programa</p>
            </button>
        </div>

        <!-- Report output area -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div id="reportPlaceholder" class="text-center py-12">
                <ion-icon src="../../assets/ionicons/bar-chart-outline.svg" class="text-5xl text-gray-200 mb-4 block mx-auto"></ion-icon>
                <p class="text-gray-400 font-semibold">Seleccione un reporte para visualizar</p>
            </div>
            <div id="reportLoading" class="text-center py-12" style="display: none;">
                <div class="w-8 h-8 border-3 border-sena-green border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-gray-500">Generando reporte...</p>
            </div>
            <div id="reportContent" style="display: none;"></div>
        </div>
    </div>
</main>

<style>
    .report-card {
        background: white;
        border: 2px solid #f3f4f6;
        border-radius: 16px;
        padding: 1.25rem;
        text-align: left;
        cursor: pointer;
        transition: all 0.2s;
    }

    .report-card:hover {
        border-color: #39a900;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(57, 169, 0, 0.1);
    }

    .report-card.active {
        border-color: #39a900;
        background: #f0fdf4;
    }

    .report-group {
        margin-bottom: 1.5rem;
    }

    .report-group-header {
        font-weight: 700;
        font-size: 1rem;
        color: #1a1a2e;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .report-group-header .badge {
        background: #39a900;
        color: white;
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 999px;
        font-weight: 600;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th {
        text-align: left;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #9ca3af;
        padding: 0.5rem 0.75rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .report-table td {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        color: #374151;
        border-bottom: 1px solid #f9fafb;
    }
</style>

<script src="../../assets/js/reportes/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>