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
    /* Forzar que los mensajes de SweetAlert aparezcan por encima de los modales (.modal) */
    .swal2-container {
        z-index: 999999 !important;
    }

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
            </div>

            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/book-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">COMPETENCIAS PENDIENTES</span>
                    <div class="stat-card-icon text-amber-500 bg-amber-50">
                        <ion-icon src="../../assets/ionicons/book-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalCompetenciasPendientes">0</span>
                    <span class="stat-card-desc">por programar</span>
                    <p class="stat-card-context">Competencias del programa que aún no tienen designación.</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">INSTRUCTORES DISPONIBLES</span>
                    <div class="stat-card-icon text-blue-500 bg-blue-50">
                        <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalInstructoresDisp">0</span>
                    <span class="stat-card-desc">habilitados</span>
                    <p class="stat-card-context">Profesionales calificados para impartir en esta ficha.</p>
                </div>
            </div>
        </div>

        <!-- Tabs Nav -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
            <div class="flex space-x-2 border-b border-gray-100 pb-3 mb-4" id="tabsContainer">
                <button class="tab-btn active px-4 py-2 rounded-lg font-medium bg-green-50 text-green-700 transition-colors" data-tab="ficha">Ficha</button>
                <button class="tab-btn px-4 py-2 rounded-lg font-medium text-gray-500 hover:bg-gray-50 transition-colors" data-tab="instructor">Instructor</button>
                <button class="tab-btn px-4 py-2 rounded-lg font-medium text-gray-500 hover:bg-gray-50 transition-colors" data-tab="ambiente">Ambiente</button>
            </div>

            <!-- Tab Content -->
            <div class="action-bar flex-col md:flex-row gap-4">
                <!-- Ficha Tab -->
                <div id="tab-ficha" class="tab-pane w-full flex-1">
                    <div class="flex flex-col md:flex-row gap-2 items-start md:items-center w-full max-w-3xl">
                        <select id="fichaSelector" placeholder="Buscar ficha o programa..." class="w-full max-w-2xl">
                            <option value="">Buscar ficha o programa...</option>
                        </select>
                        <a id="verDetalleFichaBtn" href="#" class="btn-secondary whitespace-nowrap text-sm h-[42px] px-4 hidden" style="display: none;">
                            <ion-icon src="../../assets/ionicons/eye-outline.svg"></ion-icon> Ver Ficha
                        </a>
                    </div>
                </div>

                <!-- Instructor Tab -->
                <div id="tab-instructor" class="tab-pane w-full flex-1" style="display: none;">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-col md:flex-row gap-2 items-start md:items-center w-full max-w-3xl">
                            <select id="instructorSelector" placeholder="Buscar instructor..." class="w-full max-w-2xl">
                                <option value="">Buscar instructor...</option>
                            </select>
                            <a id="verDetalleInstructorBtn" href="#" class="btn-secondary whitespace-nowrap text-sm h-[42px] px-4 hidden" style="display: none;">
                                <ion-icon src="../../assets/ionicons/eye-outline.svg"></ion-icon> Ver Perfil
                            </a>
                        </div>
                        <div class="w-full max-w-2xl bg-gray-200 rounded-full h-3 mt-2 relative overflow-hidden">
                            <div id="instructorProgressBar" class="h-3 rounded-full transition-all duration-500 bg-gray-400" style="width: 0%"></div>
                        </div>
                        <p id="instructorProgressText" class="text-xs text-gray-600 font-medium">Seleccione un instructor para ver sus horas mensuales asignadas</p>
                    </div>
                </div>

                <!-- Ambiente Tab -->
                <div id="tab-ambiente" class="tab-pane w-full flex-1" style="display: none;">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="w-full max-w-sm">
                            <select id="sedeFilter" placeholder="Filtrar por sede..." class="w-full">
                                <option value="">Filtrar por sede...</option>
                            </select>
                        </div>
                        <div class="w-full max-w-sm">
                            <select id="ambienteSelectorTab" class="form-input w-full" disabled>
                                <option value="">Primero seleccione sede...</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 flex-shrink-0 self-start md:self-auto">
                    <button id="btnGenerarReporte" class="btn-secondary whitespace-nowrap hidden" style="display: none;">
                        <ion-icon src="../../assets/ionicons/print-outline.svg"></ion-icon> Generar Reporte
                    </button>
                    <button id="addBtn" class="btn-primary" disabled>
                        <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon> Nueva Asignación
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendar area -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div id="calendarPlaceholder" class="calendar-placeholder">
                <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                <p class="text-lg font-semibold" id="placeholderTitle">Seleccione una ficha</p>
                <p class="text-sm" id="placeholderSubtitle">El calendario se cargará con las asignaciones de la ficha seleccionada</p>
            </div>
            <div id="calendar" style="display: none;"></div>
        </div>
    </div>
</main>

<!-- Create/Edit Modal -->
<?php require_once 'modal_edit.php'; ?>

<!-- Quick Day Edit Modal -->
<div id="dayEditModal" class="modal">
    <div class="modal-content" style="max-width: 460px;">
        <div class="modal-header" style="padding: 16px 20px;">
            <h3 id="dayEditTitle" style="font-size: 15px;">Detalle de Asignación</h3>
            <button class="modal-close" id="closeDayEdit">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <div id="dayEditBody">
            <input type="hidden" id="dayEdit_detasig_id">
            <input type="hidden" id="dayEdit_asig_id">

            <div class="modal-body" style="padding: 16px 20px;">
                <div id="dayEditError" class="hidden mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded text-sm text-red-700">
                    <ion-icon src="../../assets/ionicons/warning-outline.svg" class="mr-1"></ion-icon>
                    <span id="dayEditErrorMsg">Error</span>
                </div>

                <!-- Fecha + Hora header -->
                <div class="mb-4 p-3 bg-green-50 rounded-xl border border-green-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-sena-green flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-white text-xl"></ion-icon>
                    </div>
                    <div>
                        <p id="dayEditDateLabel" class="text-sm font-bold text-gray-800 capitalize"></p>
                        <p id="dayEditAsigInfo" class="text-xs text-gray-500 mt-0.5"></p>
                    </div>
                </div>

                <!-- Ficha, Programa, Competencia, Instructor, Ambiente -->
                <div class="space-y-2">
                    <div class="flex items-start gap-2">
                        <div class="w-7 h-7 rounded-md bg-gray-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <ion-icon src="../../assets/ionicons/layers-outline.svg" class="text-sena-green" style="font-size:14px;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Ficha</p>
                            <p id="dayEditFichaLabel" class="text-sm font-bold text-gray-800">--</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-7 h-7 rounded-md bg-gray-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <ion-icon src="../../assets/ionicons/school-outline.svg" class="text-sena-green" style="font-size:14px;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Programa</p>
                            <p id="dayEditProgramaLabel" class="text-sm font-medium text-gray-700">--</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-7 h-7 rounded-md bg-gray-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <ion-icon src="../../assets/ionicons/bookmarks-outline.svg" class="text-sena-green" style="font-size:14px;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Competencia</p>
                            <p id="dayEditCompetenciaLabel" class="text-sm font-medium text-gray-700">--</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-7 h-7 rounded-md bg-gray-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <ion-icon src="../../assets/ionicons/person-outline.svg" class="text-sena-green" style="font-size:14px;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Instructor</p>
                            <p id="dayEditInstructorLabel" class="text-sm font-medium text-gray-700">--</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-7 h-7 rounded-md bg-gray-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <ion-icon src="../../assets/ionicons/cube-outline.svg" class="text-sena-green" style="font-size:14px;"></ion-icon>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wide">Ambiente</p>
                            <p id="dayEditAmbienteLabel" class="text-sm font-medium text-gray-700">--</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer: Eliminar izq | Cancelar + Editar der -->
            <div class="modal-footer" style="justify-content: space-between; padding: 14px 20px; border-top: 1px solid #f3f4f6;">
                <div class="flex gap-2">
                    <button type="button" class="btn-danger-soft" id="deleteDayOnly"
                        style="padding: 8px 12px; font-size: 0.82rem;"
                        title="Eliminar solo este día">
                        <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon>
                        Eliminar Día
                    </button>
                    <button type="button" class="btn-danger-soft" id="deleteDayAsig"
                        style="padding: 8px 12px; font-size: 0.82rem;"
                        title="Eliminar asignación completa">
                        <ion-icon src="../../assets/ionicons/trash-bin-outline.svg"></ion-icon>
                        Eliminar Completa
                    </button>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="btn-secondary" id="cancelDayEdit" style="font-size: 0.85rem;">Cancelar</button>
                    <button type="button" class="btn-primary" id="editDayAsigBtn" style="font-size: 0.85rem;">
                        <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                        Editar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../assets/js/utils/tom-select-utils.js?v=<?php echo time(); ?>"></script>
<script src="../../assets/js/asignacion/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>