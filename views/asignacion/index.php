<?php
$pageTitle = "Asignaciones Académicas - SENA";
$activeNavItem = 'asignaciones';
require_once '../layouts/head.php';
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

<script src="../../assets/js/asignacion/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>