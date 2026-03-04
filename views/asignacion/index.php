<?php
$pageTitle = "Asignaciones Académicas - SENA";
$activeNavItem = 'asignaciones';
require_once '../layouts/head.php';

// Prevent instructors from accessing the Coordinator's Assignment View
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'instructor') {
    header("Location: instructor_index.php");
    exit;
}
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

    .ficha-selector {
        position: relative;
        max-width: 500px;
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

    .custom-dropdown-item .ficha-num {
        font-weight: 700;
        font-size: 0.9rem;
    }

    .custom-dropdown-item .prog-name {
        font-size: 0.75rem;
        color: #6b7280;
    }
</style>

<?php require_once '../layouts/sidebar.php'; ?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Asignaciones</span>
            </nav>
            <h1 class="page-title">Asignaciones Académicas</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL ASIGNACIONES</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalAsignaciones">0</span>
                    <span class="stat-card-desc">asignaciones de la ficha</span>
                    <p class="stat-card-context">Distribución cronológica y técnica del tiempo de formación profesional.</p>
                </div>
                <div class="stat-card-pill-container">
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon>
                        Horario
                    </div>
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/analytics-outline.svg"></ion-icon>
                        Optimizado
                    </div>
                </div>
            </div>
        </div>

        <!-- Ficha selector -->
        <div class="action-bar flex-col md:flex-row gap-4">
            <div class="ficha-selector w-full">
                <div class="relative">
                    <ion-icon src="../../assets/ionicons/search-outline.svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></ion-icon>
                    <input type="text" id="fichaSearch" autocomplete="off" placeholder="Buscar ficha o programa..." class="search-input w-full pl-10" style="padding-left: 2.5rem !important; border-radius: 12px;">
                    <div id="fichaDropdown" class="custom-dropdown-list">
                        <!-- Items dynamycally loaded -->
                    </div>
                </div>
                <!-- Hidden native select to maintain JS compatibility -->
                <select id="fichaSelector" class="hidden">
                    <option value="">Seleccione una ficha...</option>
                </select>
            </div>
            <button id="addBtn" class="btn-primary" disabled>
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                Nueva Asignación
            </button>
        </div>

        <!-- Calendar area -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-4">
            <div id="calendarPlaceholder" class="calendar-placeholder">
                <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                <p class="text-lg font-semibold">Seleccione una ficha</p>
                <p class="text-sm">El calendario se cargará con las asignaciones de la ficha seleccionada</p>
            </div>
            <div id="calendar" style="display: none;"></div>
        </div>
    </div>
</main>

<!-- Create/Edit Modal -->
<?php require_once 'modal_edit.php'; ?>

<!-- Quick Day Edit Modal -->
<div id="dayEditModal" class="modal">
    <div class="modal-content" style="max-width: 420px;">
        <div class="modal-header" style="padding: 16px 20px;">
            <h3 id="dayEditTitle" style="font-size: 15px;">Editar Horario</h3>
            <button class="modal-close" id="closeDayEdit">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="dayEditForm">
            <input type="hidden" id="dayEdit_detasig_id">
            <input type="hidden" id="dayEdit_asig_id">
            <div class="modal-body p-5">
                <div class="flex items-center gap-3 mb-5 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="w-10 h-10 rounded-lg bg-sena-green/10 flex items-center justify-center">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-sena-green text-lg"></ion-icon>
                    </div>
                    <div>
                        <p id="dayEditDateLabel" class="text-sm font-bold text-gray-800 capitalize">--</p>
                        <p id="dayEditAsigInfo" class="text-[11px] text-gray-500">--</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label text-xs">Hora Inicio <span class="text-red-500">*</span></label>
                        <input type="time" id="dayEdit_hora_ini" required class="search-input" style="padding-left: 12px !important;" min="06:00" max="22:00">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-xs">Hora Fin <span class="text-red-500">*</span></label>
                        <input type="time" id="dayEdit_hora_fin" required class="search-input" style="padding-left: 12px !important;" min="06:00" max="22:00">
                    </div>
                </div>
                <div id="dayEditError" class="mt-3 hidden">
                    <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                        <p class="text-xs text-red-600" id="dayEditErrorMsg"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 12px 20px; justify-content: space-between;">
                <button type="button" class="text-sm flex items-center gap-1 px-3 py-2 rounded-lg text-red-500 hover:bg-red-50 transition-colors" id="deleteDayAsig" title="Eliminar asignación completa">
                    <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon>
                    Eliminar
                </button>
                <div class="flex gap-2">
                    <button type="button" class="btn-secondary text-sm" id="cancelDayEdit">Cancelar</button>
                    <button type="submit" class="btn-primary text-sm" id="saveDayEdit">
                        <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                        Guardar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="../../assets/js/asignacion/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>