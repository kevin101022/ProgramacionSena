/**
 * Calendario Total - Reporte
 * Muestra todas las asignaciones filtradas por rol:
 *   - centro: todas las asignaciones del centro
 *   - coordinador: todas las asignaciones de su coordinación
 */
class CalendarioTotalManager {
    constructor() {
        this.calendar = null;
        this.data = [];

        this.COLORS = [
            '#39a900', '#3b82f6', '#8b5cf6', '#ef4444',
            '#f59e0b', '#06b6d4', '#ec4899', '#14b8a6',
            '#10b981', '#f97316', '#6366f1', '#84cc16'
        ];

        // Map asig_id -> color index
        this.asigColorMap = {};
        this.asigColorIndex = 0;

        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadData();
    }

    bindEvents() {
        const closeDayDetail = document.getElementById('closeDayDetail');
        const closeDayDetailBtn = document.getElementById('closeDayDetailBtn');
        if (closeDayDetail) closeDayDetail.onclick = () => this.closeModal();
        if (closeDayDetailBtn) closeDayDetailBtn.onclick = () => this.closeModal();

        const pdfBtn = document.getElementById('downloadPdfBtn');
        if (pdfBtn) pdfBtn.onclick = () => this.downloadPDF();
    }

    getAsigColor(asigId) {
        if (!(asigId in this.asigColorMap)) {
            this.asigColorMap[asigId] = this.asigColorIndex % this.COLORS.length;
            this.asigColorIndex++;
        }
        return this.COLORS[this.asigColorMap[asigId]];
    }

    async loadData() {
        try {
            const res = await fetch('../../routing.php?controller=reporte&action=calendarioTotal', {
                headers: { 'Accept': 'application/json' }
            });
            this.data = await res.json();

            if (!Array.isArray(this.data) || this.data.length === 0) {
                this.showEmpty();
                return;
            }

            this.showCalendar();
        } catch (e) {
            console.error('Error cargando calendario total:', e);
            this.showEmpty();
        }
    }

    showEmpty() {
        document.getElementById('calendarLoading').style.display = 'none';
        document.getElementById('calendarWrapper').style.display = 'none';
        document.getElementById('calendarEmpty').style.display = '';
        const descEl = document.getElementById('statDesc');
        const ctxEl  = document.getElementById('statContext');
        if (descEl) descEl.textContent = 'programadas';
        if (ctxEl)  ctxEl.textContent  = 'Sin datos disponibles';
    }

    showCalendar() {
        document.getElementById('calendarLoading').style.display = 'none';
        document.getElementById('calendarEmpty').style.display = 'none';
        document.getElementById('calendarWrapper').style.display = '';

        // Show PDF button
        const pdfBtn = document.getElementById('downloadPdfBtn');
        if (pdfBtn) pdfBtn.style.display = '';

        // Contar asignaciones únicas
        const uniqueAsigs = new Set(this.data.map(d => d.asig_id)).size;
        const totalEl = document.getElementById('totalAsignaciones');
        if (totalEl) totalEl.textContent = uniqueAsigs;

        // Texto descriptivo según rol y coordinación
        const rol = typeof USER_ROL !== 'undefined' ? USER_ROL : '';
        const descEl = document.getElementById('statDesc');
        const ctxEl  = document.getElementById('statContext');

        if (rol === 'coordinador') {
            // Todas las filas tienen la misma coordinación — tomar la primera
            const coordNombre = this.data[0]?.coord_descripcion || 'tu coordinación';
            if (descEl) descEl.textContent = `programadas para la coordinación ${coordNombre}`;
            if (ctxEl)  ctxEl.textContent  = 'Programación académica completa de tu coordinación.';
        } else {
            // Rol centro: puede haber varias coordinaciones
            const coords = new Set(this.data.map(d => d.coord_descripcion).filter(Boolean));
            const numCoords = coords.size;
            if (descEl) descEl.textContent = `programadas en ${numCoords} coordinación${numCoords !== 1 ? 'es' : ''}`;
            if (ctxEl)  ctxEl.textContent  = 'Programación académica completa del centro de formación.';
        }

        this.renderLegend();
        this.initCalendar();
    }

    renderLegend() {
        const container = document.getElementById('legendContainer');
        if (!container) return;
        container.innerHTML = '<div class="text-xs text-gray-500 italic">Cada color representa una asignación académica única</div>';
    }

    formatTime(timeStr) {
        if (!timeStr) return '';
        const parts = timeStr.split(':');
        return parts.length >= 2
            ? `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}`
            : timeStr;
    }

    initCalendar() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;
        if (this.calendar) this.calendar.destroy();

        const events = this.data.map(d => {
            const color = this.getAsigColor(d.asig_id);
            const horaIni = this.formatTime(d.detasig_hora_ini);
            const horaFin = this.formatTime(d.detasig_hora_fin);

            return {
                id: `det_${d.detasig_id}`,
                title: `${horaIni}-${horaFin} | ${d.comp_nombre_corto || 'Comp.'} — ${d.inst_nombres || ''} ${d.inst_apellidos || ''}`,
                start: d.detasig_fecha,
                allDay: true,
                backgroundColor: color,
                borderColor: color,
                extendedProps: { ...d }
            };
        });

        this.calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: events,
            eventClick: (info) => this.handleEventClick(info),
            height: 'auto',
            buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', list: 'Lista' },
            dayMaxEvents: 4,
            moreLinkText: 'más'
        });

        this.calendar.render();
    }

    handleEventClick(info) {
        const d = info.event.extendedProps;
        const dateObj = new Date(d.detasig_fecha + 'T00:00:00');
        const dateLabel = dateObj.toLocaleDateString('es-CO', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        });

        document.getElementById('dayDetailDate').textContent = dateLabel;
        document.getElementById('dayDetailTime').textContent =
            `${this.formatTime(d.detasig_hora_ini)} - ${this.formatTime(d.detasig_hora_fin)}`;
        document.getElementById('dayDetailCoord').textContent = d.coord_descripcion || 'N/A';
        document.getElementById('dayDetailFicha').textContent = `Ficha ${d.fich_id}`;
        document.getElementById('dayDetailCompetencia').textContent = d.comp_nombre_corto || 'N/A';
        document.getElementById('dayDetailInstructor').textContent =
            `${d.inst_nombres || ''} ${d.inst_apellidos || ''}`.trim() || 'N/A';
        document.getElementById('dayDetailAmbiente').textContent =
            d.amb_nombre ? `${d.ambiente_amb_id} — ${d.amb_nombre}` : (d.ambiente_amb_id || 'N/A');

        const obsEl = document.getElementById('dayDetailObservaciones');
        const obsContainer = document.getElementById('dayDetailObsContainer');
        if (obsEl && obsContainer) {
            if (d.observaciones && d.observaciones.trim() !== '') {
                obsEl.textContent = d.observaciones;
                obsContainer.classList.remove('hidden');
            } else {
                obsEl.textContent = '--';
                obsContainer.classList.add('hidden');
            }
        }

        const modal = document.getElementById('dayDetailModal');
        if (modal) modal.classList.add('show');
    }

    closeModal() {
        const modal = document.getElementById('dayDetailModal');
        if (modal) modal.classList.remove('show');
    }

    downloadPDF() {
        const btn = document.getElementById('downloadPdfBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Generando...';
        }
        window.open('../../routing.php?controller=reporte_pdf&action=calendarioTotal', '_blank');
        setTimeout(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<ion-icon src="../../assets/ionicons/download-outline.svg"></ion-icon> Descargar PDF';
            }
        }, 1000);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.calendarioTotalManager = new CalendarioTotalManager();
});
