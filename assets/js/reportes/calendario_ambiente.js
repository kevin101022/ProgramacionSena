/**
 * Calendario de Ambiente - Reporte
 */
class CalendarioAmbienteManager {
    constructor() {
        this.calendar = null;
        this.ambientes = [];
        this.selectedAmbiente = null;
        this.allDetalles = [];
        
        this.COLORS = [
            '#39a900', '#3b82f6', '#8b5cf6', '#ef4444',
            '#f59e0b', '#06b6d4', '#ec4899', '#14b8a6'
        ];

        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadSedes();
        await this.loadAllAmbientes();
    }

    bindEvents() {
        const sedeSelect = document.getElementById('sedeSelect');
        const ambienteSelect = document.getElementById('ambienteSelect');

        if (sedeSelect) {
            sedeSelect.addEventListener('change', () => this.handleSedeChange());
        }

        if (ambienteSelect) {
            ambienteSelect.addEventListener('change', (e) => {
                const ambId = e.target.value;
                const amb = this.ambientes.find(a => a.amb_id == ambId);
                if (amb) this.selectAmbiente(amb);
            });
        }

        // Modal events
        const closeDayDetail = document.getElementById('closeDayDetail');
        const closeDayDetailBtn = document.getElementById('closeDayDetailBtn');
        if (closeDayDetail) closeDayDetail.onclick = () => this.closeDayDetailModal();
        if (closeDayDetailBtn) closeDayDetailBtn.onclick = () => this.closeDayDetailModal();

        // PDF download
        const downloadPdfBtn = document.getElementById('downloadPdfBtn');
        if (downloadPdfBtn) downloadPdfBtn.onclick = () => this.downloadPDF();
    }

    async loadSedes() {
        try {
            const res = await fetch('../../routing.php?controller=sede&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const sedes = await res.json();
            const sedeSelect = document.getElementById('sedeSelect');
            if (sedeSelect && Array.isArray(sedes)) {
                sedeSelect.innerHTML = '<option value="">Seleccione Sede...</option>';
                sedes.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.sede_id;
                    opt.textContent = s.sede_nombre;
                    sedeSelect.appendChild(opt);
                });
            }
        } catch (e) {
            console.error('Error cargando sedes:', e);
        }
    }

    async loadAllAmbientes() {
        try {
            const res = await fetch('../../routing.php?controller=ambiente&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.ambientes = await res.json();
            if (!Array.isArray(this.ambientes)) this.ambientes = [];
        } catch (e) {
            console.error('Error cargando ambientes:', e);
            this.ambientes = [];
        }
    }

    handleSedeChange() {
        const sedeId = document.getElementById('sedeSelect')?.value;
        const ambSelect = document.getElementById('ambienteSelect');
        
        if (!ambSelect) return;

        ambSelect.innerHTML = '<option value="">Seleccione Ambiente...</option>';
        if (!sedeId) {
            ambSelect.disabled = true;
            return;
        }

        const filtered = this.ambientes.filter(a => a.sede_sede_id == sedeId);
        if (filtered.length === 0) {
            ambSelect.innerHTML = '<option value="">No hay ambientes en esta sede</option>';
            ambSelect.disabled = true;
        } else {
            filtered.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.amb_id;
                opt.textContent = `${a.amb_id} — ${a.amb_nombre}`;
                ambSelect.appendChild(opt);
            });
            ambSelect.disabled = false;
        }
    }

    async selectAmbiente(a) {
        if (!a) return;
        this.selectedAmbiente = a;
        const ambienteSearch = document.getElementById('ambienteSearch');
        const ambienteDropdown = document.getElementById('ambienteDropdown');
        const downloadPdfBtn = document.getElementById('downloadPdfBtn');

        if (ambienteSearch) ambienteSearch.value = `Ambiente ${a.amb_id} — ${a.amb_nombre || 'Sin nombre'}`;
        if (ambienteDropdown) ambienteDropdown.style.display = 'none';
        if (downloadPdfBtn) downloadPdfBtn.disabled = false;

        const calendarEl = document.getElementById('calendar');
        const placeholder = document.getElementById('calendarPlaceholder');

        if (placeholder) placeholder.style.display = 'none';
        if (calendarEl) calendarEl.style.display = '';

        await this.loadAsignacionesAmbiente(a.amb_id);
        await this.loadAllDetalles();
        this.initCalendar();
    }

    async loadAsignacionesAmbiente(ambId) {
        try {
            const res = await fetch('../../routing.php?controller=asignacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            this.allAsignaciones = (Array.isArray(data) ? data : []).filter(
                a => a.ambiente_amb_id == ambId
            );
            const totalLabel = document.getElementById('totalAsignaciones');
            if (totalLabel) totalLabel.textContent = this.allAsignaciones.length;
        } catch (e) {
            console.error('Error:', e);
        }
    }

    async loadAllDetalles() {
        this.allDetalles = [];
        try {
            for (const asig of this.allAsignaciones) {
                const res = await fetch(`../../routing.php?controller=detalle_asignacion&action=index&asig_id=${asig.asig_id}&_=${Date.now()}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const detalles = await res.json();
                if (Array.isArray(detalles)) {
                    detalles.forEach(d => {
                        d._asig = asig;
                    });
                    this.allDetalles.push(...detalles);
                }
            }
        } catch (e) {
            console.error('Error cargando detalles:', e);
        }
    }

    formatTime(timeStr) {
        if (!timeStr) return '';
        const parts = timeStr.split(':');
        if (parts.length >= 2) {
            return `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}`;
        }
        return timeStr;
    }

    initCalendar() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;
        if (this.calendar) this.calendar.destroy();

        const events = this.allDetalles.map((d) => {
            const asig = d._asig;
            const colorIndex = this.allAsignaciones.findIndex(a => a.asig_id == asig.asig_id);
            const horaIni = this.formatTime(d.detasig_hora_ini);
            const horaFin = this.formatTime(d.detasig_hora_fin);

            return {
                id: `det_${d.detasig_id}`,
                title: `${horaIni}-${horaFin} | Ficha ${asig.ficha_fich_id || asig.fich_id} — ${asig.comp_nombre_corto || 'Comp.'} — ${asig.inst_nombres || ''} ${asig.inst_apellidos || ''}`,
                start: d.detasig_fecha,
                allDay: true,
                backgroundColor: this.COLORS[colorIndex % this.COLORS.length],
                borderColor: this.COLORS[colorIndex % this.COLORS.length],
                extendedProps: { ...d, asig }
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
            dayMaxEvents: 3,
            moreLinkText: 'más'
        });

        this.calendar.render();
    }

    handleEventClick(info) {
        const props = info.event.extendedProps;
        if (!props || !props.asig) return;

        const asig = props.asig;
        const dateObj = new Date(props.detasig_fecha + 'T00:00:00');
        const dateLabel = dateObj.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        
        // Fill modal
        document.getElementById('dayDetailDate').textContent = dateLabel;
        document.getElementById('dayDetailTime').textContent = `${this.formatTime(props.detasig_hora_ini)} - ${this.formatTime(props.detasig_hora_fin)}`;
        document.getElementById('dayDetailFicha').textContent = `Ficha ${asig.ficha_fich_id || asig.fich_id}`;
        document.getElementById('dayDetailCompetencia').textContent = asig.comp_nombre_corto || 'N/A';
        document.getElementById('dayDetailInstructor').textContent = `${asig.inst_nombres || ''} ${asig.inst_apellidos || ''}`.trim() || 'N/A';

        const obsEl = document.getElementById('dayDetailObservaciones');
        const obsContainer = document.getElementById('dayDetailObsContainer');
        if (obsEl && obsContainer) {
            if (props.observaciones && props.observaciones.trim() !== '') {
                obsEl.textContent = props.observaciones;
                obsContainer.classList.remove('hidden');
            } else {
                obsEl.textContent = '--';
                obsContainer.classList.add('hidden');
            }
        }

        // Show modal
        const modal = document.getElementById('dayDetailModal');
        if (modal) modal.classList.add('show');
    }

    closeDayDetailModal() {
        const modal = document.getElementById('dayDetailModal');
        if (modal) modal.classList.remove('show');
    }

    async downloadPDF() {
        if (!this.selectedAmbiente || !this.calendar) {
            alert('Primero seleccione un ambiente');
            return;
        }

        const downloadBtn = document.getElementById('downloadPdfBtn');
        if (downloadBtn) {
            downloadBtn.disabled = true;
            downloadBtn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Generando...';
        }

        try {
            // Abrir PDF en nueva ventana (el navegador mostrará el diálogo de impresión automáticamente)
            const url = `../../routing.php?controller=reporte_pdf&action=calendarioAmbiente&amb_id=${this.selectedAmbiente.amb_id}`;
            window.open(url, '_blank');
            
            // Restaurar botón
            setTimeout(() => {
                if (downloadBtn) {
                    downloadBtn.disabled = false;
                    downloadBtn.innerHTML = '<ion-icon src="../../assets/ionicons/download-outline.svg"></ion-icon> Descargar PDF';
                }
            }, 1000);
        } catch (error) {
            console.error('Error generando PDF:', error);
            alert('Error al generar el PDF');
            if (downloadBtn) {
                downloadBtn.disabled = false;
                downloadBtn.innerHTML = '<ion-icon src="../../assets/ionicons/download-outline.svg"></ion-icon> Descargar PDF';
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.calendarioAmbienteManager = new CalendarioAmbienteManager();
});
