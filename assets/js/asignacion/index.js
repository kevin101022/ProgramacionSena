/**
 * Asignacion Management JavaScript
 * Refactored to Class-based manager for consistency.
 * Manages FullCalendar, custom searchable dropdowns, and conflict detection.
 */
class AsignacionManager {
    constructor() {
        this.calendar = null;
        this.fichas = [];
        this.selectedFicha = null;
        this.allAsignaciones = [];
        this.allCompetenciasPrograma = [];
        this.allHabilitaciones = [];
        this.ambientes = [];

        this.COLORS = [
            '#39a900', '#3b82f6', '#8b5cf6', '#ef4444',
            '#f59e0b', '#06b6d4', '#ec4899', '#14b8a6'
        ];

        this.init();
    }

    async init() {
        this.bindEvents();
        await Promise.all([
            this.loadFichas(),
            this.loadAmbientes()
        ]);
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

        const fichaSelector = document.getElementById('fichaSelector');
        if (fichaSelector) {
            fichaSelector.addEventListener('change', () => this.handleFichaChange());
        }

        const addBtn = document.getElementById('addBtn');
        if (addBtn) addBtn.onclick = () => this.openModal();

        const closeBtn = document.getElementById('closeModal');
        if (closeBtn) closeBtn.onclick = () => this.closeModal();

        const cancelBtn = document.getElementById('cancelBtn');
        if (cancelBtn) cancelBtn.onclick = () => this.closeModal();

        const form = document.getElementById('asignacionForm');
        if (form) {
            form.onsubmit = (e) => this.handleFormSubmit(e);
        }

        const competenciaSelect = document.getElementById('competencia_id');
        if (competenciaSelect) {
            competenciaSelect.addEventListener('change', () => this.handleCompetenciaChange());
        }
    }

    async loadFichas() {
        try {
            const res = await fetch('../../routing.php?controller=ficha&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.fichas = await res.json();
        } catch (e) {
            console.error('Error cargando fichas:', e);
        }
    }

    async loadAmbientes() {
        try {
            const res = await fetch('../../routing.php?controller=ambiente&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.ambientes = await res.json();
            const ambienteSelect = document.getElementById('ambiente_id');
            if (ambienteSelect) {
                ambienteSelect.innerHTML = '<option value="">Seleccione ambiente...</option>';
                this.ambientes.forEach(a => {
                    const opt = document.createElement('option');
                    opt.value = a.amb_id;
                    opt.textContent = `${a.amb_id} - ${a.amb_nombre || 'Sin nombre'}`;
                    ambienteSelect.appendChild(opt);
                });
            }
        } catch (e) {
            console.error('Error cargando ambientes:', e);
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
                    <div class="ficha-num">Ficha ${f.fich_id}</div>
                    <div class="prog-name">${f.prog_denominacion || f.titpro_nombre || 'Sin nombre'}</div>
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
        const fichaSelector = document.getElementById('fichaSelector');

        if (fichaSearch) fichaSearch.value = `Ficha ${f.fich_id} — ${f.prog_denominacion || f.titpro_nombre || ''}`;
        if (fichaDropdown) fichaDropdown.style.display = 'none';

        if (fichaSelector) {
            fichaSelector.innerHTML = `<option value="${f.fich_id}" selected>Ficha ${f.fich_id}</option>`;
            fichaSelector.dispatchEvent(new Event('change'));
        }
    }

    async handleFichaChange() {
        const fichaSelector = document.getElementById('fichaSelector');
        const fichId = fichaSelector ? fichaSelector.value : null;

        const addBtn = document.getElementById('addBtn');
        const calendarEl = document.getElementById('calendar');
        const placeholder = document.getElementById('calendarPlaceholder');

        if (!fichId) {
            this.selectedFicha = null;
            if (addBtn) addBtn.disabled = true;
            if (calendarEl) calendarEl.style.display = 'none';
            if (placeholder) placeholder.style.display = '';
            return;
        }

        this.selectedFicha = this.fichas.find(f => f.fich_id == fichId);
        if (addBtn) addBtn.disabled = false;
        if (placeholder) placeholder.style.display = 'none';
        if (calendarEl) calendarEl.style.display = '';

        await Promise.all([
            this.loadAsignacionesFicha(fichId),
            this.loadCompetenciasPrograma(),
            this.loadHabilitaciones()
        ]);

        this.initCalendar();
    }

    async loadAsignacionesFicha(fichId) {
        try {
            const res = await fetch('../../routing.php?controller=asignacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            this.allAsignaciones = (Array.isArray(data) ? data : []).filter(
                a => a.ficha_fich_id == fichId || a.fich_id == fichId
            );
            const totalLabel = document.getElementById('totalAsignaciones');
            if (totalLabel) totalLabel.textContent = this.allAsignaciones.length;
        } catch (e) {
            console.error('Error:', e);
        }
    }

    async loadCompetenciasPrograma() {
        if (!this.selectedFicha) return;
        const progId = this.selectedFicha.programa_prog_codigo || this.selectedFicha.programa_prog_id;
        try {
            const res = await fetch(`../../routing.php?controller=competencia_programa&action=getByPrograma&prog_id=${progId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            this.allCompetenciasPrograma = Array.isArray(data) ? data : [];
        } catch (e) {
            console.error('Error:', e);
        }
    }

    async loadHabilitaciones() {
        try {
            const res = await fetch('../../routing.php?controller=instru_competencia&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.allHabilitaciones = await res.json();
        } catch (e) {
            console.error('Error:', e);
        }
    }

    initCalendar() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;
        if (this.calendar) this.calendar.destroy();

        const events = this.allAsignaciones.map((a, i) => ({
            id: a.asig_id,
            title: `${a.comp_nombre_corto || 'Comp.'} — ${a.inst_nombres || ''} ${a.inst_apellidos || ''}`,
            start: a.asig_fecha_ini,
            end: a.asig_fecha_fin,
            backgroundColor: this.COLORS[i % this.COLORS.length],
            extendedProps: a
        }));

        this.calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: events,
            selectable: true,
            select: (info) => this.openModal(null, info.startStr, info.endStr),
            eventClick: (info) => window.location.href = `ver.php?id=${info.event.id}`,
            height: 'auto',
            buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', list: 'Lista' }
        });

        this.calendar.render();
    }

    openModal(asig = null, startDate = null, endDate = null) {
        const modal = document.getElementById('asignacionModal');
        const form = document.getElementById('asignacionForm');
        if (!form || !this.selectedFicha) return;
        form.reset();

        const modalTitle = document.getElementById('modalTitle');
        const asigIdInput = document.getElementById('asig_id');
        const fichaInput = document.getElementById('modal_ficha_id');
        const fichaDisplay = document.getElementById('fichaDisplay');
        const fechaIni = document.getElementById('asig_fecha_ini');
        const fechaFin = document.getElementById('asig_fecha_fin');
        const competenciaSelect = document.getElementById('competencia_id');
        const instructorSelect = document.getElementById('instructor_id');

        if (fichaInput) fichaInput.value = this.selectedFicha.fich_id;
        if (fichaDisplay) fichaDisplay.value = `Ficha ${this.selectedFicha.fich_id} — ${this.selectedFicha.prog_denominacion || this.selectedFicha.titpro_nombre || ''}`;
        if (startDate && fechaIni) fechaIni.value = startDate;
        if (endDate && fechaFin) fechaFin.value = endDate;

        const assignedCompIds = this.allAsignaciones.map(a => a.competencia_comp_id);
        const availableComps = this.allCompetenciasPrograma.filter(c => !assignedCompIds.includes(c.comp_id));

        if (competenciaSelect) {
            competenciaSelect.innerHTML = '<option value="">Seleccione competencia...</option>';
            if (availableComps.length === 0) {
                competenciaSelect.innerHTML = '<option value="">Todas las competencias asignadas</option>';
            } else {
                availableComps.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.comp_id;
                    opt.textContent = c.comp_nombre_corto || c.comp_nombre_unidad_competencia;
                    competenciaSelect.appendChild(opt);
                });
            }
        }

        if (instructorSelect) {
            instructorSelect.innerHTML = '<option value="">Primero seleccione competencia...</option>';
            instructorSelect.disabled = true;
        }

        if (asig) {
            if (modalTitle) modalTitle.textContent = 'Editar Asignación';
            if (asigIdInput) asigIdInput.value = asig.asig_id;
        } else {
            if (modalTitle) modalTitle.textContent = 'Nueva Asignación';
            if (asigIdInput) asigIdInput.value = '';
        }

        if (modal) modal.classList.add('show');
    }

    closeModal() {
        const modal = document.getElementById('asignacionModal');
        if (modal) modal.classList.remove('show');
    }

    handleCompetenciaChange() {
        const competenciaSelect = document.getElementById('competencia_id');
        const instructorSelect = document.getElementById('instructor_id');
        const compId = competenciaSelect.value;

        if (!instructorSelect) return;

        if (!compId) {
            instructorSelect.innerHTML = '<option value="">Primero seleccione competencia...</option>';
            instructorSelect.disabled = true;
            return;
        }

        const progId = this.selectedFicha.programa_prog_codigo || this.selectedFicha.programa_prog_id;
        const habilitados = this.allHabilitaciones.filter(h =>
            h.competxprograma_competencia_comp_id == compId &&
            h.competxprograma_programa_prog_id == progId
        );

        instructorSelect.innerHTML = '<option value="">Seleccione instructor...</option>';
        if (habilitados.length === 0) {
            instructorSelect.innerHTML = '<option value="">Sin instructores habilitados</option>';
            instructorSelect.disabled = true;
        } else {
            const seen = new Set();
            habilitados.forEach(h => {
                if (!seen.has(h.instructor_inst_id)) {
                    seen.add(h.instructor_inst_id);
                    const opt = document.createElement('option');
                    opt.value = h.instructor_inst_id;
                    opt.textContent = `${h.inst_nombres} ${h.inst_apellidos}`;
                    instructorSelect.appendChild(opt);
                }
            });
            instructorSelect.disabled = false;
        }
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('asig_id').value;
        const action = id ? 'update' : 'store';

        const data = {
            ficha_fich_id: document.getElementById('modal_ficha_id').value,
            competencia_comp_id: document.getElementById('competencia_id').value,
            instructor_inst_id: document.getElementById('instructor_id').value,
            ambiente_amb_id: document.getElementById('ambiente_id').value,
            asig_fecha_ini: document.getElementById('asig_fecha_ini').value,
            asig_fecha_fin: document.getElementById('asig_fecha_fin').value
        };
        if (id) data.asig_id = id;

        const conflictAlert = document.getElementById('modalConflictAlert');
        if (conflictAlert) {
            conflictAlert.classList.add('hidden');
            conflictAlert.innerHTML = '';
        }

        try {
            const res = await fetch(`../../routing.php?controller=asignacion&action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();

            if (res.ok) {
                NotificationService.showSuccess(id ? '¡Asignación actualizada!' : '¡Asignación registrada!');
                this.closeModal();
                await this.loadAsignacionesFicha(this.selectedFicha.fich_id);
                this.initCalendar();
            } else if (res.status === 409) {
                this.showConflictAlert(result.details || []);
            } else {
                NotificationService.showError(result.error || 'Error al guardar.');
            }
        } catch (error) {
            NotificationService.showError('Error de conexión.');
        }
    }

    showConflictAlert(conflicts) {
        const conflictAlert = document.getElementById('modalConflictAlert');
        if (!conflictAlert) return;

        const types = new Set();
        conflicts.forEach(c => c.conflict_type.forEach(t => types.add(t === 'instructor' ? 'el Instructor' : 'el Ambiente')));
        const typeMsg = Array.from(types).join(' y ');
        const fichasStr = conflicts.map(c => `Ficha ${c.fich_id}`).join(', ');

        conflictAlert.classList.remove('hidden');
        conflictAlert.innerHTML = `
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <ion-icon src="../../assets/ionicons/warning-outline.svg" class="text-red-500 text-xl"></ion-icon>
                    <span class="text-sm font-bold text-red-700">Cruce Detectado</span>
                </div>
                <p class="text-xs text-red-600 leading-relaxed">
                    ${typeMsg} ya están asignados a <strong>${fichasStr}</strong> en este rango de fechas.
                </p>
            </div>
        `;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.asignacionManager = new AsignacionManager();
});
