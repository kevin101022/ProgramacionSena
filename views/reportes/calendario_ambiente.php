<?php
$pageTitle = "Calendario de Ambiente - SENA";
$activeNavItem = 'reportes';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>
<!-- FullCalendar CDN -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>
<style>
    .fc {
        font-family: 'Public Sans', sans-serif;
    }

    .fc .fc-toolbar-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a2e;
    }

    .fc .fc-button {
        background: #39a900;
        border-color: #39a900;
        font-size: 0.8rem;
        text-transform: capitalize;
    }

    .fc .fc-button:hover {
        background: #2d8a00;
        border-color: #2d8a00;
    }

    .fc .fc-button-active {
        background: #1e6b00 !important;
        border-color: #1e6b00 !important;
    }

    .fc .fc-daygrid-day-number {
        font-weight: 600;
        color: #4a5568;
    }

    .fc .fc-event {
        border-radius: 6px;
        padding: 2px 6px;
        font-size: 0.75rem;
        border: none;
    }

    .fc .fc-daygrid-day.fc-day-today {
        background: #f0fdf4;
    }

    .custom-dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-top: none;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 50;
        max-height: 300px;
        overflow-y: auto;
        display: none;
        margin-top: -1px;
    }

    .custom-dropdown-item {
        padding: 10px 16px;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid #f3f4f6;
    }

    .custom-dropdown-item:last-child {
        border-bottom: none;
    }

    .custom-dropdown-item:hover {
        background: #f0fdf4;
        color: #39a900;
    }
</style>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../reportes/index.php">Reportes</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Calendario de Ambiente</span>
            </nav>
            <h1 class="page-title">Calendario de Ambiente</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/cube-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">ASIGNACIONES</span>
                    <div class="stat-card-icon blue">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalAsignaciones">0</span>
                    <span class="stat-card-desc">programadas en este ambiente</span>
                    <p class="stat-card-context">Visualización completa de la ocupación del ambiente seleccionado.</p>
                </div>
                <div class="stat-card-pill-container">
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon>
                        Ocupación
                    </div>
                </div>
            </div>
        </div>

        <!-- Selector de ambiente -->
        <div class="action-bar flex-col md:flex-row gap-4">
            <div class="w-full max-w-lg relative">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></ion-icon>
                <input type="text" id="ambienteSearch" autocomplete="off" placeholder="Buscar ambiente por ID o nombre..." class="search-input w-full pl-10" style="padding-left: 2.5rem !important; border-radius: 12px;">
                <div id="ambienteDropdown" class="custom-dropdown-list"></div>
            </div>
            <div class="flex gap-3">
                <button id="downloadPdfBtn" class="btn-primary whitespace-nowrap" disabled>
                    <ion-icon src="../../assets/ionicons/download-outline.svg"></ion-icon>
                    Descargar PDF
                </button>
                <a href="../reportes/index.php" class="btn-secondary whitespace-nowrap">
                    <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                    Volver
                </a>
            </div>
        </div>

        <!-- Calendar area -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-4">
            <div id="calendarPlaceholder" class="calendar-placeholder" style="text-align: center; padding: 3rem;">
                <ion-icon src="../../assets/ionicons/cube-outline.svg" style="font-size: 4rem; color: #e5e7eb; margin-bottom: 1rem;"></ion-icon>
                <p class="text-lg font-semibold text-gray-700">Seleccione un ambiente</p>
                <p class="text-sm text-gray-500">El calendario mostrará todas las asignaciones programadas</p>
            </div>
            <div id="calendar" style="display: none;"></div>
        </div>
    </div>
</main>

<!-- Modal de Detalle del Día -->
<div id="dayDetailModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 id="dayDetailTitle" style="font-size: 16px; font-weight: 600;">Detalle de Asignación</h3>
            <button class="modal-close" id="closeDayDetail">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <div class="flex items-center gap-3 mb-5 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <div class="w-12 h-12 rounded-lg bg-blue-500 flex items-center justify-center">
                    <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-white text-2xl"></ion-icon>
                </div>
                <div class="flex-1">
                    <p id="dayDetailDate" class="text-sm font-bold text-gray-800 capitalize">--</p>
                    <p id="dayDetailTime" class="text-xs text-gray-500">--</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/layers-outline.svg" class="text-blue-500"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Ficha</p>
                        <p id="dayDetailFicha" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/bookmarks-outline.svg" class="text-blue-500"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Competencia</p>
                        <p id="dayDetailCompetencia" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/person-outline.svg" class="text-blue-500"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Instructor</p>
                        <p id="dayDetailInstructor" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="padding: 16px 24px;">
            <button type="button" class="btn-secondary text-sm w-full" id="closeDayDetailBtn">Cerrar</button>
        </div>
    </div>
</div>

<script src="../../assets/js/reportes/calendario_ambiente.js?v=<?php echo time(); ?>"></script>
</body>
</html>
