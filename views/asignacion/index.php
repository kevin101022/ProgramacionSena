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

<!-- Tom Select CDN -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
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

        <!-- Ficha selector -->
        <div class="action-bar flex-col md:flex-row gap-4">
            <div class="w-full" style="max-width: 600px;">
                <select id="fichaSelector" placeholder="Buscar ficha o programa..." class="w-full">
                    <option value="">Buscar ficha o programa...</option>
                </select>
            </div>
            <button id="addBtn" class="btn-primary flex-shrink-0" disabled>
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
            
            <div class="modal-body" style="padding: 16px 20px;">
                <div id="dayEditError" class="hidden mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded text-sm text-red-700">
                    <ion-icon src="../../assets/ionicons/warning-outline.svg" class="mr-1"></ion-icon>
                    <span id="dayEditErrorMsg">Error</span>
                </div>

                <div class="mb-4 text-center">
                    <p id="dayEditDateLabel" class="text-lg font-bold text-sena-green capitalize"></p>
                    <p id="dayEditAsigInfo" class="text-xs text-gray-500 mt-1"></p>
                </div>

                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-1">
                        <label class="form-label text-xs">Hora Inicio</label>
                        <input type="time" id="dayEdit_hora_ini" class="form-input text-sm" required min="06:00" max="22:00">
                    </div>
                    <div class="flex-1">
                        <label class="form-label text-xs">Hora Fin</label>
                        <input type="time" id="dayEdit_hora_fin" class="form-input text-sm" required min="06:00" max="22:00">
                    </div>
                </div>
                
                <div class="form-group mb-0">
                    <label class="form-label text-xs">Observaciones (Opcional)</label>
                    <textarea id="dayEdit_observaciones" class="form-input text-sm resize-none" rows="2" placeholder="Ej. Cambio de ambiente temporal..."></textarea>
                </div>
            </div>

            <div class="form-actions mt-4" style="justify-content: space-between; padding: 12px 20px;">
                <div class="flex gap-2">
                    <button type="button" class="btn-danger-soft" id="deleteDayOnly" style="padding: 8px 12px; font-size: 0.85rem;" title="Eliminar solo este horario">
                        <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon>
                        Día
                    </button>
                    <button type="button" class="btn-danger-soft" id="deleteDayAsig" style="padding: 8px 12px; font-size: 0.85rem;" title="Eliminar asignación completa">
                        <ion-icon src="../../assets/ionicons/trash-bin-outline.svg"></ion-icon>
                        Asig. Completa
                    </button>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="btn-secondary" id="cancelDayEdit">Cancelar</button>
                    <button type="submit" class="btn-primary" id="saveDayEdit">
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