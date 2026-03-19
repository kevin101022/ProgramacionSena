<?php
$pageTitle = "Calendario Instructores - SENA";
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
        padding: 4px 8px;
        font-size: 0.75rem;
        border: none;
        cursor: pointer !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .fc .fc-event:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(57, 169, 0, 0.3);
    }

    .fc .fc-daygrid-day.fc-day-today {
        background: #f0fdf4;
    }


</style>

<?php require_once '../layouts/instructor_sidebar.php'; ?>

<main class="main-content">
<?php
    $ficha_id = $_GET['ficha'] ?? null;
    $titulo_calendario = $ficha_id ? "Calendario de Ficha: $ficha_id" : "Mi Calendario";
?>
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../asignacion/instructor_index.php">Inicio</a>
                <?php if ($ficha_id): ?>
                    <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                    <a href="../instructor/mi_ficha.php">Mi Ficha Liderada</a>
                <?php endif; ?>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span><?php echo $titulo_calendario; ?></span>
            </nav>
            <h1 class="page-title"><?php echo $titulo_calendario; ?></h1>
        </div>
    </header>

    <div class="content-wrapper">
        <p class="text-sm text-gray-500 mb-2">
            <?php echo $ficha_id ? "Consulta la programación completa de la ficha $ficha_id." : "Consulta tus clases como instructor. Vista semanal y mensual."; ?>
        </p>
        <!-- Calendar area: removed overflow-x-auto and min-width to fix the responsive issue -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 mt-2">
            <div id="calendar"></div>
        </div>
<!-- Modal de Detalle del Día -->
<div id="dayDetailModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 id="dayDetailTitle" style="font-size: 16px; font-weight: 600;">Detalle de Asignación</h3>
            <button class="modal-close" onclick="document.getElementById('dayDetailModal').classList.remove('show')">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <div class="modal-body" style="padding: 24px;">
            <div id="modalHeaderColor" class="flex items-center gap-3 mb-5 p-4 rounded-xl border">
                <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
                    <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-white text-2xl"></ion-icon>
                </div>
                <div class="flex-1">
                    <p id="dayDetailDate" class="text-sm font-bold text-white capitalize">--</p>
                    <p id="dayDetailTime" class="text-xs text-white/90">--</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/layers-outline.svg" id="iconFicha" class="text-gray-500"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Ficha</p>
                        <p id="dayDetailFicha" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/bookmarks-outline.svg" id="iconComp" class="text-gray-500"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Competencia</p>
                        <p id="dayDetailCompetencia" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/person-outline.svg" id="iconInst" class="text-gray-500"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Instructor</p>
                        <p id="dayDetailInstructor" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/cube-outline.svg" id="iconAmb" class="text-gray-500"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-semibold uppercase">Ambiente</p>
                        <p id="dayDetailAmbiente" class="text-sm font-bold text-gray-800">--</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="padding: 16px 24px;">
            <button type="button" class="btn-secondary text-sm w-full" onclick="document.getElementById('dayDetailModal').classList.remove('show')">Cerrar</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const fichaId = <?php echo json_encode($ficha_id); ?>;
        
        const COLORS = [
            '#39a900', '#2563eb', '#7c3aed', '#db2777', '#ea580c', '#0891b2', 
            '#4f46e5', '#16a34a', '#84cc16', '#f59e0b', '#dc2626', '#6366f1'
        ];
        const asigColorMap = {};

        // 1. Initialize empty calendar
        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: window.innerWidth < 768 ? 'listWeek' : 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día', list: 'Lista' },
            events: [],
            eventClick: function(info) {
                const props = info.event.extendedProps;
                const dateObj = new Date(props.fecha + 'T00:00:00');
                const dateLabel = dateObj.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                
                document.getElementById('dayDetailDate').textContent = dateLabel;
                document.getElementById('dayDetailTime').textContent = `${props.horaIni} - ${props.horaFin}`;
                document.getElementById('dayDetailCompetencia').textContent = props.comp_nombre || 'N/A';
                document.getElementById('dayDetailFicha').textContent = `Ficha ${props.fich_id || 'N/A'}`;
                document.getElementById('dayDetailInstructor').textContent = props.inst_nombre;
                document.getElementById('dayDetailAmbiente').textContent = `${props.amb_id} - ${props.amb_nombre}`;
                
                const modalHeader = document.getElementById('modalHeaderColor');
                modalHeader.style.backgroundColor = props.color;
                modalHeader.style.borderColor = props.color;
                
                ['iconFicha', 'iconComp', 'iconInst', 'iconAmb'].forEach(id => {
                    document.getElementById(id).style.color = props.color;
                });

                document.getElementById('dayDetailModal').classList.add('show');
            },
            windowResize: function(arg) {
                if (window.innerWidth < 768) {
                    calendar.changeView('listWeek');
                } else {
                    calendar.changeView('dayGridMonth');
                }
            }
        });
        calendar.render();

        // 2. Fetch data and update calendar
        let url = `../../routing.php?controller=reporte&action=calendarioTotal`;
        if (fichaId) {
            url += `&fich_id=${fichaId}`;
        }

        console.log("Fetching events from:", url);

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error("Network response was not ok");
                return response.json();
            })
            .then(data => {
                console.log("Data received:", data);
                if (!Array.isArray(data)) {
                    console.error("Expected array, received:", data);
                    return;
                }
                
                if (data.length === 0) {
                    console.warn("No events found for this view.");
                    // Optional: show a small notification or message
                    return;
                }

                const events = data.map(asig => {
                    const dia = asig.detasig_fecha || asig.asig_fecha_ini;
                    const horaIni = asig.detasig_hora_ini || '';
                    const horaFin = asig.detasig_hora_fin || '';
                    const formatHora = (hora) => hora ? hora.substring(0, 5) : 'ND';
                    
                    if (!asigColorMap[asig.asig_id]) {
                        const colorIdx = Object.keys(asigColorMap).length % COLORS.length;
                        asigColorMap[asig.asig_id] = COLORS[colorIdx];
                    }
                    const eventColor = asigColorMap[asig.asig_id];

                    return {
                        id: asig.asig_id,
                        title: `${formatHora(horaIni)}-${formatHora(horaFin)} | Ficha ${asig.fich_id} — ${asig.comp_nombre_corto || asig.comp_nombre}`,
                        start: dia,
                        backgroundColor: eventColor,
                        borderColor: eventColor,
                        allDay: true,
                        extendedProps: {
                            fecha: dia,
                            horaIni: formatHora(horaIni),
                            horaFin: formatHora(horaFin),
                            comp_nombre: asig.comp_nombre_corto || asig.comp_nombre,
                            fich_id: asig.fich_id,
                            amb_id: asig.ambiente_amb_id || asig.amb_id || 'N/A',
                            amb_nombre: asig.amb_nombre || 'S/N',
                            inst_nombre: asig.inst_nombres ? `${asig.inst_nombres} ${asig.inst_apellidos}` : 'Mi Asignación',
                            color: eventColor
                        }
                    };
                });

                calendar.addEventSource(events);
            })
            .catch(err => {
                console.error("Error loading events:", err);
            });
    });
</script>
</body>

</html>
