<?php
$pageTitle = "Mis Asignaciones - SENA";
$activeNavItem = 'asignaciones';
require_once '../layouts/head.php';

// Enforce role
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'instructor') {
    header("Location: ../../routing.php?controller=login&action=showLogin");
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
        cursor: default !important;
    }

    .fc .fc-daygrid-day.fc-day-today {
        background: #f0fdf4;
    }
</style>

<?php require_once '../layouts/instructor_sidebar.php'; ?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Mis Asignaciones</span>
            </nav>
            <h1 class="page-title">Mi Horario de Formación</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">MIS ASIGNACIONES</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalAsignaciones">0</span>
                    <span class="stat-card-desc">clases programadas</span>
                    <p class="stat-card-context">Visualiza todas tus responsabilidades semanales de manera read-only.</p>
                </div>
            </div>
        </div>

        <!-- Calendar area -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-4">
            <div id="calendar"></div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        // We fetch assignments for THIS instructor
        // We need an endpoint in InstructorController to get assignments for logged instructor
        const instructorId = <?php echo json_encode($_SESSION['id']); ?>;

        fetch(`../../routing.php?controller=instructor&action=getAsignaciones&id=${instructorId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalAsignaciones').textContent = data.length || 0;

                const events = data.map(asig => {
                    return {
                        id: asig.asig_id,
                        title: `Ficha: ${asig.fich_numero} | ${asig.comp_codigo} - ${asig.amb_nombre}`,
                        start: asig.asig_fecha_ini,
                        end: asig.asig_fecha_fin,
                        backgroundColor: '#39a900',
                        borderColor: '#39a900'
                    };
                });

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'es',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día',
                        list: 'Lista'
                    },
                    events: events,
                    eventClick: function(info) {
                        // Prevent default action and any popup
                        info.jsEvent.preventDefault();
                    }
                });

                calendar.render();
            })
            .catch(err => console.error("Error loading events", err));
    });
</script>
</body>

</html>