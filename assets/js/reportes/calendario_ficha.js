/**
 * Calendario de Ficha - Reporte
 */
class CalendarioFichaManager {
    constructor() {
        this.calendar = null;
        this.fichas = [];
        this.selectedFicha = null;
        this.allDetalles = [];
        
        this.COLORS = [
            '#39a900', '#3b82f6', '#8b5cf6', '#ef4444',
            '#f59e0b', '#06b6d4', '#ec4899', '#14b8a6'
        ];

        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadFichas();
    }

    bindEvents() {
        const fichaSearch = document.getElementById('fichaSearch');
        if (fichaSearch) {
            fichaSearch.addEventListener('focus', () => this.renderFichaDropdown(fichaSearch.value));
            fichaSearch.addEventListener('input', (e) => this.renderFichaDropdown(e.target.value));
            fichaSearch.addEventListener('click', (e) => {
                e.stopPropagation();
                this.renderFichaDropdown(e.target.value);
            });
        }

        document.addEventListener('click', (e) => {
            const fichaDropdown = document.getElementById('fichaDropdown');
            if (fichaDropdown && fichaSearch && !fichaSearch.contains(e.target) && !fichaDropdown.contains(e.target)) {
                fichaDropdown.style.display = 'none';
            }
        });

        // Modal events
        const closeDayDetail = document.getElementById('closeDayDetail');
        const closeDayDetailBtn = document.getElementById('closeDayDetailBtn');
        if (closeDayDetail) closeDayDetail.onclick = () => this.closeDayDetailModal();
        if (closeDayDetailBtn) closeDayDetailBtn.onclick = () => this.closeDayDetailModal();

        // PDF download
        const downloadPdfBtn = document.getElementById('downloadPdfBtn');
        if (downloadPdfBtn) downloadPdfBtn.onclick = () => this.downloadPDF();
    }

    async loadFichas() {
        try {
            const res = await fetch('../../routing.php?controller=ficha&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            this.fichas = Array.isArray(data) ? data : [];
        } catch (e) {
            console.error('Error cargando fichas:', e);
        }
    }

    renderFichaDropdown(filter = '') {
        const fichaDropdown = document.getElementById('fichaDropdown');
        if (!fichaDropdown) return;

        fichaDropdown.innerHTML = '';
        const searchTerm = filter.toLowerCase().trim();

        const filtered = this.fichas.filter(f => {
            const id = String(f.fich_id).toLowerCase();
            const prog = (f.prog_denominacion || f.titpro_nombre || '').toLowerCase();
            return id.includes(searchTerm) || prog.includes(searchTerm);
        });

        if (filtered.length === 0) {
            fichaDropdown.innerHTML = '<div class="p-4 text-center text-sm text-gray-500">No se encontraron fichas</div>';
        } else {
            filtered.forEach(f => {
                const item = document.createElement('div');
                item.className = 'custom-dropdown-item';
                item.innerHTML = `
                    <div style="font-weight: 700; font-size: 0.9rem;">Ficha ${f.fich_id}</div>
                    <div style="font-size: 0.75rem; color: #6b7280;">${f.prog_denominacion || f.titpro_nombre || 'Sin nombre'}</div>
                `;
                item.onclick = (e) => {
                    e.stopPropagation();
                    this.selectFicha(f);
                };
                fichaDropdown.appendChild(item);
            });
        }
        fichaDropdown.style.display = 'block';
    }

    async selectFicha(f) {
        if (!f) return;
        this.selectedFicha = f;
        const fichaSearch = document.getElementById('fichaSearch');
        const fichaDropdown = document.getElementById('fichaDropdown');
        const downloadPdfBtn = document.getElementById('downloadPdfBtn');

        if (fichaSearch) fichaSearch.value = `Ficha ${f.fich_id} — ${f.prog_denominacion || f.titpro_nombre || ''}`;
        if (fichaDropdown) fichaDropdown.style.display = 'none';
        if (downloadPdfBtn) downloadPdfBtn.disabled = false;

        const calendarEl = document.getElementById('calendar');
        const placeholder = document.getElementById('calendarPlaceholder');

        if (placeholder) placeholder.style.display = 'none';
        if (calendarEl) calendarEl.style.display = '';

        await this.loadAsignacionesFicha(f.fich_id);
        await this.loadAllDetalles();
        this.initCalendar();
    }

    async loadAsignacionesFicha(fichId) {
        try {
            const res = await fetch('../../routing.php?controller=asignacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            // Filter by ficha; for coordinador the asignacion index is already scoped server-side via session
            this.allAsignaciones = (Array.isArray(data) ? data : []).filter(
                a => a.ficha_fich_id == fichId || a.fich_id == fichId
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
                title: `${horaIni}-${horaFin} | ${asig.comp_nombre_corto || 'Comp.'} — ${asig.inst_nombres || ''} ${asig.inst_apellidos || ''} (Amb. ${asig.ambiente_amb_id || ''})`,
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
        document.getElementById('dayDetailAmbiente').textContent = `Ambiente ${asig.ambiente_amb_id || 'N/A'}`;

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
        if (!this.selectedFicha || !this.calendar) {
            alert('Primero seleccione una ficha');
            return;
        }

        const downloadBtn = document.getElementById('downloadPdfBtn');
        if (downloadBtn) {
            downloadBtn.disabled = true;
            downloadBtn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Generando...';
        }

        try {
            // Abrir PDF en nueva ventana (el navegador mostrará el diálogo de impresión automáticamente)
            const url = `../../routing.php?controller=reporte_pdf&action=calendarioFicha&fich_id=${this.selectedFicha.fich_id}`;
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
    window.calendarioFichaManager = new CalendarioFichaManager();
});
