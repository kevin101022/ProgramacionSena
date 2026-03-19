<?php
$pageTitle = "Calendario Total - SENA";
$activeNavItem = 'reportes';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>
<!-- FullCalendar CDN -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>
<style>
    .fc { font-family: 'Public Sans', sans-serif; }
    .fc .fc-toolbar-title { font-size: 1.1rem; font-weight: 700; color: #1a1a2e; }
    .fc .fc-button { background: #39a900; border-color: #39a900; font-size: 0.8rem; text-transform: capitalize; }
    .fc .fc-button:hover { background: #2d8a00; border-color: #2d8a00; }
    .fc .fc-button-active { background: #1e6b00 !important; border-color: #1e6b00 !important; }
    .fc .fc-daygrid-day-number { font-weight: 600; color: #4a5568; }
    .fc .fc-event { border-radius: 6px; padding: 2px 6px; font-size: 0.75rem; border: none; }
    .fc .fc-daygrid-day.fc-day-today { background: #f0fdf4; }

    .legend-dot {
        width: 12px; height: 12px; border-radius: 50%; display: inline-block; flex-shrink: 0;
    }
</style>

<!-- Inject session context for JS -->
<script>
    const USER_ROL = '<?php echo $_SESSION["rol"] ?? ""; ?>';
</script>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../reportes/index.php">Reportes</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Calendario Total</span>
            </nav>
            <h1 class="page-title">Calendario Total</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/calendar-number-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL EVENTOS</span>
                    <div class="stat-card-icon" style="background:#f0fdf4;">
                        <ion-icon src="../../assets/ionicons/calendar-number-outline.svg" style="color:#39a900;"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalAsignaciones">0</span>
                    <span class="stat-card-desc" id="statDesc">programadas</span>
                    <p class="stat-card-context" id="statContext">Cargando...</p>
                </div>
                <div class="stat-card-pill-container">
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                        Formación
                    </div>
                </div>
            </div>
        </div>

        <!-- Action bar -->
        <div class="action-bar flex-col md:flex-row gap-4">
            <div id="legendContainer" class="flex flex-wrap gap-3 items-center"></div>
            <div class="flex gap-3 ml-auto">
                <button id="downloadPdfBtn" class="btn-primary whitespace-nowrap" style="display:none;">
                    <ion-icon src="../../assets/ionicons/download-outline.svg"></ion-icon>
                    Descargar PDF
                </button>
                <a href="../reportes/index.php" class="btn-secondary whitespace-nowrap">
                    <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                    Volver
                </a>
            </div>
        </div>

        <!-- Loading state -->
        <div id="calendarLoading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 mt-4 text-center">
            <div class="w-8 h-8 border-2 border-sena-green border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-500 text-sm">Cargando asignaciones...</p>
        </div>

        <!-- Calendar -->
        <div id="calendarWrapper" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-4" style="display:none;">
            <div id="calendar"></div>
        </div>

        <!-- Empty state -->
        <div id="calendarEmpty" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 mt-4 text-center" style="display:none;">
            <ion-icon src="../../assets/ionicons/calendar-outline.svg" style="font-size:4rem;color:#e5e7eb;margin-bottom:1rem;"></ion-icon>
            <p class="text-lg font-semibold text-gray-700">Sin asignaciones</p>
            <p class="text-sm text-gray-500">No hay asignaciones registradas para tu área.</p>
        </div>
    </div>
</main>

<!-- Modal de Detalle -->
<div id="dayDetailModal" class="modal">
    <div class="modal-content" style="max-width: 520px;">
        <div class="modal-header">
            <h3 style="font-size: 16px; font-weight: 600;">Detalle de Asignación</h3>
            <button class="modal-close" id="closeDayDetail">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <div class="flex items-center gap-3 mb-5 p-4 bg-green-50 rounded-xl border border-green-100">
                <div class="w-12 h-12 rounded-lg bg-sena-green flex items-center justify-center">
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
                        <ion-icon src="../../assets/ionicons/people-circle-outline.svg" class="text-sena-green"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Coordinación</p>
                        <p id="dayDetailCoord" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/layers-outline.svg" class="text-sena-green"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Ficha</p>
                        <p id="dayDetailFicha" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/bookmarks-outline.svg" class="text-sena-green"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Competencia</p>
                        <p id="dayDetailCompetencia" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/person-outline.svg" class="text-sena-green"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Instructor</p>
                        <p id="dayDetailInstructor" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/cube-outline.svg" class="text-sena-green"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Ambiente</p>
                        <p id="dayDetailAmbiente" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3 hidden" id="dayDetailObsContainer">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/chatbubble-ellipses-outline.svg" class="text-amber-500"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Observaciones</p>
                        <p id="dayDetailObservaciones" class="text-sm font-medium text-gray-700 italic mt-1">--</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="padding: 16px 24px;">
            <button type="button" class="btn-secondary text-sm w-full" id="closeDayDetailBtn">Cerrar</button>
        </div>
    </div>
</div>

<script src="../../assets/js/reportes/calendario_total.js?v=<?php echo time(); ?>"></script>
</body>
</html>
