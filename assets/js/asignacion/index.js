/**
 * Asignacion Management JavaScript
 * Full calendar with per-day events, inline day editing, and conflict detection.
 */
function getColombianHolidays(year) {
    const holidays = new Set();
    const formatDate = (date) => {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    };
    const getNextMonday = (date) => {
        const day = date.getDay();
        if (day === 1) return date;
        const diff = (day === 0) ? 1 : (8 - day);
        const newDate = new Date(date);
        newDate.setDate(date.getDate() + diff);
        return newDate;
    };

    // Fixed Dates
    holidays.add(formatDate(new Date(year, 0, 1)));   // Año Nuevo: Jan 1
    holidays.add(formatDate(new Date(year, 4, 1)));   // Día del Trabajo: May 1
    holidays.add(formatDate(new Date(year, 6, 20)));  // Grito Independencia: Jul 20
    holidays.add(formatDate(new Date(year, 7, 7)));   // Batalla de Boyacá: Aug 7
    holidays.add(formatDate(new Date(year, 11, 8)));  // Inmaculada Concepción: Dec 8
    holidays.add(formatDate(new Date(year, 11, 25))); // Navidad: Dec 25

    // Emiliani Dates (moves to next Monday)
    holidays.add(formatDate(getNextMonday(new Date(year, 0, 6))));   // Reyes Magos: Jan 6
    holidays.add(formatDate(getNextMonday(new Date(year, 2, 19))));  // San José: Mar 19
    holidays.add(formatDate(getNextMonday(new Date(year, 5, 29))));  // San Pedro y San Pablo: Jun 29
    holidays.add(formatDate(getNextMonday(new Date(year, 7, 15))));  // Asunción: Aug 15
    holidays.add(formatDate(getNextMonday(new Date(year, 9, 12))));  // Día de la Raza: Oct 12
    holidays.add(formatDate(getNextMonday(new Date(year, 10, 1))));  // Todos los Santos: Nov 1
    holidays.add(formatDate(getNextMonday(new Date(year, 10, 11)))); // Independencia Cartagena: Nov 11

    // Easter-relative Dates (Gauss Easter Algorithm)
    const a = year % 19;
    const b = Math.floor(year / 100);
    const c = year % 100;
    const d = Math.floor(b / 4);
    const e = b % 4;
    const f = Math.floor((b + 8) / 25);
    const g = Math.floor((b - f + 1) / 3);
    const h = (19 * a + b - d - g + 15) % 30;
    const i = Math.floor(c / 4);
    const k = c % 4;
    const L = (32 + 2 * e + 2 * i - h - k) % 7;
    const m = Math.floor((a + 11 * h + 22 * L) / 451);
    const month = Math.floor((h + L - 7 * m + 114) / 31);
    const day = ((h + L - 7 * m + 114) % 31) + 1;
    
    const easter = new Date(year, month - 1, day);

    // Jueves Santo: Easter - 3
    const juevesSanto = new Date(easter);
    juevesSanto.setDate(easter.getDate() - 3);
    holidays.add(formatDate(juevesSanto));

    // Viernes Santo: Easter - 2
    const viernesSanto = new Date(easter);
    viernesSanto.setDate(easter.getDate() - 2);
    holidays.add(formatDate(viernesSanto));

    // Ascensión: Easter + 43
    const ascension = new Date(easter);
    ascension.setDate(easter.getDate() + 43);
    holidays.add(formatDate(ascension));

    // Corpus Christi: Easter + 64
    const corpusChristi = new Date(easter);
    corpusChristi.setDate(easter.getDate() + 64);
    holidays.add(formatDate(corpusChristi));

    // Sagrado Corazón: Easter + 71
    const sagradoCorazon = new Date(easter);
    sagradoCorazon.setDate(easter.getDate() + 71);
    holidays.add(formatDate(sagradoCorazon));

    return holidays;
}

class AsignacionManager {
    constructor() {
        this.calendar = null;
        this.fichas = [];
        this.instructores = [];
        this.activeTab = 'ficha';
        this.selectedFicha = null;
        this.selectedInstructor = null;
        this.selectedAmbiente = null;
        this.allAsignaciones = [];
        this.allCompetenciasPrograma = [];
        this.allHabilitaciones = [];
        this.ambientes = [];
        this.sedes = []; // All available sedes
        this.allDetalles = []; // All detail events for calendar

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
            this.loadInstructores(),
            this.loadSedes(),
            this.loadAmbientes()
        ]);
        
        this.processUrlParams();
    }

    processUrlParams() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        
        if (tab) {
            const btn = document.querySelector(`.tab-btn[data-tab="${tab}"]`);
            if (btn) btn.click();
            
            setTimeout(() => {
                if (tab === 'ficha' && urlParams.get('fich')) {
                    const fichaSelector = document.getElementById('fichaSelector');
                    if (fichaSelector && fichaSelector.tomselect) {
                        fichaSelector.tomselect.setValue(urlParams.get('fich'));
                    }
                } else if (tab === 'instructor' && urlParams.get('inst')) {
                    const instructorSelector = document.getElementById('instructorSelector');
                    if (instructorSelector && instructorSelector.tomselect) {
                        instructorSelector.tomselect.setValue(urlParams.get('inst'));
                    }
                }
            }, 300);
        }
    }

    bindEvents() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        tabBtns.forEach(btn => {
            btn.addEventListener('click', (e) => this.handleTabChange(e.target.dataset.tab));
        });

        const fichaSelector = document.getElementById('fichaSelector');
        if (fichaSelector) {
            fichaSelector.addEventListener('change', () => this.handleFichaChange());
        }

        const instructorSelector = document.getElementById('instructorSelector');
        if (instructorSelector) {
            instructorSelector.addEventListener('change', () => this.handleInstructorTabChange());
        }

        const sedeFilter = document.getElementById('sedeFilter');
        if (sedeFilter) {
            sedeFilter.addEventListener('change', () => this.handleSedeFilterChange());
        }

        const ambienteSelectorTab = document.getElementById('ambienteSelectorTab');
        if (ambienteSelectorTab) {
            ambienteSelectorTab.addEventListener('change', () => this.handleAmbienteTabChange());
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

        // Date change listeners for dynamic day generation
        const fechaIni = document.getElementById('asig_fecha_ini');
        const fechaFin = document.getElementById('asig_fecha_fin');
        if (fechaIni) fechaIni.addEventListener('change', () => this.onDateRangeChange());
        if (fechaFin) fechaFin.addEventListener('change', () => this.onDateRangeChange());

        const sedeSelect = document.getElementById('sede_id');
        if (sedeSelect) {
            sedeSelect.addEventListener('change', () => this.handleSedeChange());
        }

        // Quick day edit modal bindings
        const closeDayEdit = document.getElementById('closeDayEdit');
        const cancelDayEdit = document.getElementById('cancelDayEdit');
        if (closeDayEdit) closeDayEdit.onclick = () => this.closeDayEditModal();
        if (cancelDayEdit) cancelDayEdit.onclick = () => this.closeDayEditModal();


        const deleteAsigBtn = document.getElementById('deleteDayAsig');
        if (deleteAsigBtn) deleteAsigBtn.onclick = () => this.handleDeleteAsig();

        const deleteDayOnlyBtn = document.getElementById('deleteDayOnly');
        if (deleteDayOnlyBtn) deleteDayOnlyBtn.onclick = () => this.handleDeleteDayOnly();

        const btnGenerarReporte = document.getElementById('btnGenerarReporte');
        if (btnGenerarReporte) {
            btnGenerarReporte.addEventListener('click', () => this.handleGenerarReporte());
        }

        const applyDefaultHoursBtn = document.getElementById('applyDefaultHours');
        if (applyDefaultHoursBtn) applyDefaultHoursBtn.onclick = () => this.handleApplyDefaultHours();
    }

    handleGenerarReporte() {
        if (!this.calendar) return;
        
        let url = '../../routing.php?controller=reporte_pdf';
        let midDate;
        
        try {
            const dateStr = this.calendar.getDate();
            midDate = new Date(dateStr);
        } catch(e) {
            midDate = new Date();
        }
        
        let mes = midDate.getMonth() + 1;
        let anio = midDate.getFullYear();
        let params = `&mes=${mes}&anio=${anio}`;
        
        if (this.activeTab === 'ficha' && this.selectedFicha) {
            url += `&action=calendarioFicha&fich_id=${this.selectedFicha.fich_id}${params}`;
        } else if (this.activeTab === 'instructor' && this.selectedInstructor) {
            url += `&action=calendarioInstructor&inst_id=${this.selectedInstructor}${params}`;
        } else if (this.activeTab === 'ambiente' && this.selectedAmbiente) {
            url += `&action=calendarioAmbiente&amb_id=${this.selectedAmbiente}${params}`;
        } else {
            return;
        }

        window.open(url, '_blank');
    }

    async handleDeleteAsig() {
        const asigId = document.getElementById('dayEdit_asig_id').value;
        if (!asigId) return;

        NotificationService.showConfirm('¿Está seguro de eliminar esta asignación completa? Se eliminarán todos los días y horarios asociados.', async () => {
            try {
                const res = await fetch(`../../routing.php?controller=asignacion&action=destroy&id=${asigId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    NotificationService.showSuccess('Asignación eliminada');
                    this.closeDayEditModal();
                    // Reload assignments and calendar
                    const fichId = this.selectedFicha?.fich_id;
                    if (fichId) await this.loadAsignacionesFicha(fichId);
                    if (this.calendar) this.calendar.refetchEvents();
                    this.updateDashboardStats();
                } else {
                    const data = await res.json();
                    NotificationService.showError(data.error || 'Error al eliminar');
                }
            } catch (err) {
                NotificationService.showError('Error de conexión');
            }
        });
    }

    async handleDeleteDayOnly() {
        const detId = document.getElementById('dayEdit_detasig_id').value;
        if (!detId) return;

        NotificationService.showConfirm('¿Está seguro de eliminar solo este horario para este día?', async () => {
            try {
                const res = await fetch(`../../routing.php?controller=detalle_asignacion&action=destroy&id=${detId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    NotificationService.showSuccess('Día de asignación eliminado');
                    this.closeDayEditModal();
                    if (this.calendar) this.calendar.refetchEvents();
                    this.updateDashboardStats();
                } else {
                    const data = await res.json();
                    NotificationService.showError(data.error || 'Error al eliminar');
                }
            } catch (err) {
                NotificationService.showError('Error de conexión');
            }
        });
    }

    handleApplyDefaultHours() {
        const defaultIni = document.getElementById('default_hora_ini')?.value || '08:00';
        const defaultFin = document.getElementById('default_hora_fin')?.value || '12:00';
        
        if (defaultIni >= defaultFin) {
            NotificationService.showError('La hora de inicio por defecto debe ser menor a la hora de fin.');
            return;
        }

        const container = document.getElementById('diasListContainer');
        if (!container) return;

        const checkboxes = container.querySelectorAll('.day-checkbox:not(:disabled)');
        if (checkboxes.length === 0) {
            NotificationService.showError('Selecciones un rango de fechas válido primero.');
            return;
        }

        checkboxes.forEach(chk => {
            chk.checked = true;
            chk.dispatchEvent(new Event('change'));
            
            const dateISO = chk.id.split('chk_')[1];
            const ini = document.getElementById(`ini_${dateISO}`);
            const fin = document.getElementById(`fin_${dateISO}`);
            
            if (ini) ini.value = defaultIni;
            if (fin) fin.value = defaultFin;
        });
        
        NotificationService.showSuccess('Horario predeterminado aplicado a todos los días.');
    }

    onDateRangeChange() {
        const fechaIni = document.getElementById('asig_fecha_ini')?.value;
        const fechaFin = document.getElementById('asig_fecha_fin')?.value;
        if (fechaIni && fechaFin && fechaIni <= fechaFin) {
            this.generateDaysInputs(fechaIni, fechaFin, null, true);
        }
    }

    async loadFichas() {
        try {
            const res = await fetch('../../routing.php?controller=ficha&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.fichas = await res.json();

            const fichaSelector = document.getElementById('fichaSelector');
            if (fichaSelector) {
                this.fichas.forEach(f => {
                    const opt = document.createElement('option');
                    opt.value = f.fich_id;
                    opt.textContent = `Ficha ${f.fich_id} — ${f.prog_denominacion || f.titpro_nombre || 'Sin nombre'}`;
                    fichaSelector.appendChild(opt);
                });
                
                initTS('#fichaSelector', 'Buscar ficha o programa...');
            }

        } catch (e) {
            console.error('Error cargando fichas:', e);
        }
    }

    async loadSedes() {
        try {
            const res = await fetch('../../routing.php?controller=sede&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.sedes = await res.json();
            
            const sedeFilter = document.getElementById('sedeFilter');
            if (sedeFilter) {
                this.sedes.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.sede_id;
                    opt.textContent = s.sede_nombre;
                    sedeFilter.appendChild(opt);
                });
                if (window.initTS) {
                    initTS('#sedeFilter', 'Filtrar por sede...');
                }
            }
        } catch (e) {
            console.error('Error cargando sedes:', e);
        }
    }

    async loadAmbientes() {
        try {
            const res = await fetch('../../routing.php?controller=ambiente&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.ambientes = await res.json();
            // We don't populate the select here, it will be done when sede is selected
        } catch (e) {
            console.error('Error cargando ambientes:', e);
        }
    }

    handleSedeChange(targetAmbienteId = null) {
        const sedeId = document.getElementById('sede_id')?.value;
        const ambienteSelect = document.getElementById('ambiente_id');
        if (!ambienteSelect) return;

        ambienteSelect.innerHTML = '<option value="">Seleccione ambiente...</option>';
        
        if (!sedeId) {
            ambienteSelect.innerHTML = '<option value="">Primero seleccione sede...</option>';
            return;
        }

        const filtered = this.ambientes.filter(a => a.sede_sede_id == sedeId);
        if (filtered.length === 0) {
            ambienteSelect.innerHTML = '<option value="">No hay ambientes en esta sede</option>';
        } else {
            filtered.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.amb_id;
                opt.textContent = `${a.amb_id} - ${a.amb_nombre || 'Sin nombre'} (${a.tipo_ambiente || 'Convencional'})`;
                ambienteSelect.appendChild(opt);
            });
            if (targetAmbienteId) {
                ambienteSelect.value = targetAmbienteId;
            }
        }
    }

    async handleFichaChange() {
        const fichaSelector = document.getElementById('fichaSelector');
        const fichId = fichaSelector ? fichaSelector.value : null;
        
        const btnFicha = document.getElementById('verDetalleFichaBtn');

        if (fichId) {
            this.selectedFicha = this.fichas.find(f => f.fich_id == fichId) || null;
            if (btnFicha) {
                btnFicha.href = `../ficha/ver.php?id=${fichId}&from=asignaciones_ficha`;
                btnFicha.style.display = 'inline-flex';
                btnFicha.classList.remove('hidden');
            }
            await Promise.all([
                this.loadAsignacionesFicha(fichId),
                this.loadCompetenciasPrograma(),
                this.loadHabilitaciones()
            ]);
            this.updateDashboardStats();
        } else {
            this.selectedFicha = null;
            if (btnFicha) {
                btnFicha.style.display = 'none';
                btnFicha.classList.add('hidden');
            }
        }

        this.updateCalendarVisibility();
    }

    handleTabChange(tabName) {
        this.activeTab = tabName;
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            if (btn.dataset.tab === tabName) {
                btn.classList.add('active', 'bg-green-50', 'text-green-700');
                btn.classList.remove('text-gray-500', 'hover:bg-gray-50');
            } else {
                btn.classList.remove('active', 'bg-green-50', 'text-green-700');
                btn.classList.add('text-gray-500', 'hover:bg-gray-50');
            }
        });

        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.style.display = 'none';
        });
        document.getElementById(`tab-${tabName}`).style.display = 'block';

        this.updateCalendarVisibility();
    }

    handleInstructorTabChange() {
        const sel = document.getElementById('instructorSelector');
        this.selectedInstructor = sel ? sel.value : null;
        
        const btnInst = document.getElementById('verDetalleInstructorBtn');
        if (this.selectedInstructor) {
            if (btnInst) {
                btnInst.href = `../instructor/ver.php?id=${this.selectedInstructor}&from=asignaciones_instructor`;
                btnInst.style.display = 'inline-flex';
                btnInst.classList.remove('hidden');
            }
        } else {
            if (btnInst) {
                btnInst.style.display = 'none';
                btnInst.classList.add('hidden');
            }
        }
        
        this.updateCalendarVisibility();
    }

    handleSedeFilterChange() {
        const sedeId = document.getElementById('sedeFilter')?.value;
        const ambSelect = document.getElementById('ambienteSelectorTab');
        
        ambSelect.innerHTML = '<option value="">Seleccione ambiente...</option>';
        if (!sedeId) {
            ambSelect.innerHTML = '<option value="">Primero seleccione sede...</option>';
            ambSelect.disabled = true;
            this.selectedAmbiente = null;
            if (ambSelect.tomselect) ambSelect.tomselect.destroy();
            this.updateCalendarVisibility();
            return;
        }

        ambSelect.disabled = false;
        const filtered = this.ambientes.filter(a => a.sede_sede_id == sedeId);
        
        if (window.refreshTS) {
            const options = filtered.map(a => ({
                value: a.amb_id,
                text: `${a.amb_id} - ${a.amb_nombre || 'Sin nombre'} (${a.tipo_ambiente || 'Convencional'})`
            }));
            const pText = filtered.length === 0 ? 'No hay ambientes en esta sede' : 'Buscar ambiente...';
            window.refreshTS('#ambienteSelectorTab', options, pText);
        } else {
            if (filtered.length === 0) {
                ambSelect.innerHTML = '<option value="">No hay ambientes en esta sede</option>';
            } else {
                filtered.forEach(a => {
                    const opt = document.createElement('option');
                    opt.value = a.amb_id;
                    opt.textContent = `${a.amb_id} - ${a.amb_nombre || 'Sin nombre'} (${a.tipo_ambiente || 'Convencional'})`;
                    ambSelect.appendChild(opt);
                });
            }
        }
        
        this.selectedAmbiente = null;
        this.updateCalendarVisibility();
    }

    handleAmbienteTabChange() {
        const sel = document.getElementById('ambienteSelectorTab');
        this.selectedAmbiente = sel ? sel.value : null;
        this.updateCalendarVisibility();
    }

    updateCalendarVisibility() {
        const addBtn = document.getElementById('addBtn');
        const calendarEl = document.getElementById('calendar');
        const placeholder = document.getElementById('calendarPlaceholder');
        const title = document.getElementById('placeholderTitle');
        const subtitle = document.getElementById('placeholderSubtitle');
        const statsGrid = document.querySelector('.stats-grid');

        let isSelected = false;

        if (this.activeTab === 'ficha' && this.selectedFicha) {
            isSelected = true;
        } else if (this.activeTab === 'instructor' && this.selectedInstructor) {
            isSelected = true;
        } else if (this.activeTab === 'ambiente' && this.selectedAmbiente) {
            isSelected = true;
        }

        if (statsGrid) {
            statsGrid.style.display = this.activeTab === 'ficha' ? 'grid' : 'none';
        }

        const btnGenerarReporte = document.getElementById('btnGenerarReporte');

        if (isSelected) {
            addBtn.disabled = false;
            
            if (btnGenerarReporte) {
                btnGenerarReporte.style.display = 'inline-flex';
                btnGenerarReporte.classList.remove('hidden');
            }

            placeholder.style.display = 'none';
            calendarEl.style.display = '';
            
            if (!this.calendar) {
                this.initCalendar();
            } else {
                // Must call render to recalculate dimensions after being display:none
                this.calendar.render();
                this.calendar.refetchEvents();
            }
        } else {
            addBtn.disabled = true;
            
            if (btnGenerarReporte) {
                btnGenerarReporte.style.display = 'none';
                btnGenerarReporte.classList.add('hidden');
            }

            calendarEl.style.display = 'none';
            placeholder.style.display = '';
            
            if (this.activeTab === 'ficha') {
                if(title) title.textContent = 'Seleccione una ficha';
                if(subtitle) subtitle.textContent = 'El calendario se cargará con las asignaciones de la ficha seleccionada';
            } else if (this.activeTab === 'instructor') {
                if(title) title.textContent = 'Seleccione un instructor';
                if(subtitle) subtitle.textContent = 'El calendario se cargará con las asignaciones del instructor seleccionado';
                this.updateInstructorProgress(0, true);
            } else if (this.activeTab === 'ambiente') {
                if(title) title.textContent = 'Seleccione un ambiente';
                if(subtitle) subtitle.textContent = 'El calendario se cargará con la ocupación del ambiente seleccionado';
            }
        }
    }

    updateInstructorProgress(horas, reset = false) {
        const bar = document.getElementById('instructorProgressBar');
        const text = document.getElementById('instructorProgressText');
        if (!bar || !text) return;
        
        if (reset) {
            bar.style.width = '0%';
            bar.className = 'h-3 rounded-full transition-all duration-500 bg-gray-400';
            text.textContent = 'Seleccione un instructor para ver sus horas mensuales asignadas';
            return;
        }

        const limit = 160;
        let percentage = (horas / limit) * 100;
        
        // Colores: Verde < 80% (128h), Amarillo 80-100%, Rojo > 100% (160h)
        if (percentage < 80) {
            bar.className = 'h-3 rounded-full transition-all duration-500 bg-green-500';
        } else if (percentage <= 100) {
            bar.className = 'h-3 rounded-full transition-all duration-500 bg-yellow-400';
        } else {
            bar.className = 'h-3 rounded-full transition-all duration-500 bg-red-500';
        }

        if (percentage > 100) percentage = 100;
        bar.style.width = `${percentage}%`;
        
        if (horas > limit) {
            const exceso = (horas - limit).toFixed(1);
            text.textContent = `${horas}h / ${limit}h (¡Superó el límite por ${exceso}h!)`;
            text.className = 'text-xs font-medium text-red-600 mt-2 block';
        } else {
            const faltan = (limit - horas).toFixed(1);
            text.textContent = `${horas}h / ${limit}h (Faltan ${faltan}h para completar el mes)`;
            text.className = 'text-xs font-medium text-gray-600 mt-2 block';
        }
    }

    async loadInstructores() {
        try {
            const res = await fetch('../../routing.php?controller=instructor&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.instructores = await res.json();
            
            const instSelect = document.getElementById('instructorSelector');
            if (instSelect) {
                this.instructores.forEach(i => {
                    const opt = document.createElement('option');
                    const docId = i.numero_documento || i.inst_id;
                    opt.value = docId;
                    opt.textContent = `${i.inst_nombres} ${i.inst_apellidos} — CC: ${docId}`;
                    instSelect.appendChild(opt);
                });
                
                if (window.initTS) {
                    initTS('#instructorSelector', 'Buscar instructor...');
                }
            }
        } catch (e) {
            console.error('Error cargando instructores:', e);
        }
    }

    updateDashboardStats() {
        if (!this.selectedFicha) return;
        
        const progId = this.selectedFicha.programa_prog_codigo || this.selectedFicha.programa_prog_id;
        
        // Competencias asignadas unicas
        const assignedCompIds = [...new Set(this.allAsignaciones.map(a => a.competencia_comp_id))];
        const totalProgramComps = this.allCompetenciasPrograma.length;
        const pendingComps = Math.max(0, totalProgramComps - assignedCompIds.length);
        
        const elPending = document.getElementById('totalCompetenciasPendientes');
        if (elPending) elPending.textContent = pendingComps;

        // Instructores habilitados para este programa (incluye NULL/vacío)
        const instructoresHabilitados = this.allHabilitaciones.filter(h => 
            h.programa_prog_id == progId || h.programa_prog_id === null || h.programa_prog_id === ''
        );
        const uniqueInstructores = new Set(instructoresHabilitados.map(h => h.instructor_inst_id)).size;
        
        const elInst = document.getElementById('totalInstructoresDisp');
        if (elInst) elInst.textContent = uniqueInstructores;
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
            const res = await fetch(`../../routing.php?controller=competencia&action=getByPrograma&prog_id=${progId}`, {
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

        this.calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: async (fetchInfo, successCallback, failureCallback) => {
                let url = '';
                let params = new URLSearchParams({
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr
                });
                
                const midDate = new Date((fetchInfo.start.getTime() + fetchInfo.end.getTime()) / 2);
                params.append('mes', midDate.getMonth() + 1);
                params.append('anio', midDate.getFullYear());

                if (this.activeTab === 'ficha' && this.selectedFicha) {
                    params.append('ficha_id', this.selectedFicha.fich_id);
                    url = `../../routing.php?controller=asignacion&action=getCalendarioFicha&${params.toString()}`;
                } else if (this.activeTab === 'instructor' && this.selectedInstructor) {
                    params.append('instructor_id', this.selectedInstructor);
                    url = `../../routing.php?controller=asignacion&action=getCalendarioInstructor&${params.toString()}`;
                } else if (this.activeTab === 'ambiente' && this.selectedAmbiente) {
                    params.append('ambiente_id', this.selectedAmbiente);
                    url = `../../routing.php?controller=asignacion&action=getCalendarioAmbiente&${params.toString()}`;
                } else {
                    successCallback([]);
                    return;
                }

                try {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    
                    if (this.activeTab === 'instructor' && data.horasMes !== undefined) {
                        this.updateInstructorProgress(data.horasMes);
                    }

                    const rawEvents = data.events || data;
                    this.allDetalles = rawEvents;
                    const events = rawEvents.map((d) => {
                        const asigIdNum = parseInt(d.asig_id, 10) || 0;
                        const horaIni = this.formatTime(d.detasig_hora_ini);
                        const horaFin = this.formatTime(d.detasig_hora_fin);
                        
                        let titleText = '';
                        if (this.activeTab === 'ficha') {
                            titleText = `${horaIni}-${horaFin} | ${d.comp_nombre_corto || 'Comp.'} — ${d.inst_nombres || ''} (Amb. ${d.ambiente_amb_id || ''})`;
                        } else if (this.activeTab === 'instructor') {
                            titleText = `${horaIni}-${horaFin} | ${d.comp_nombre_corto || 'Comp.'} — Ficha: ${d.ficha_num || ''} (Amb. ${d.ambiente_amb_id || ''})`;
                        } else {
                            titleText = `${horaIni}-${horaFin} | ${d.comp_nombre_corto || 'Comp.'} — ${d.inst_nombres || ''} (Ficha: ${d.ficha_num || ''})`;
                        }

                        const eventColor = this.COLORS[asigIdNum % this.COLORS.length];
                        return {
                            id: `det_${d.detasig_id}`,
                            title: titleText,
                            start: d.detasig_fecha,
                            allDay: true,
                            editable: false,
                            backgroundColor: eventColor,
                            borderColor: eventColor,
                            extendedProps: { ...d, asig: d }
                        };
                    });

                    // Add Colombian holidays as background events
                    const currentYear = midDate.getFullYear();
                    const nextYear = currentYear + 1;
                    const prevYear = currentYear - 1;
                    const holidaysSet = new Set([
                        ...getColombianHolidays(prevYear),
                        ...getColombianHolidays(currentYear),
                        ...getColombianHolidays(nextYear)
                    ]);
                    
                    for (const date of holidaysSet) {
                        events.push({
                            title: 'Festivo',
                            start: date,
                            allDay: true,
                            display: 'background',
                            backgroundColor: '#fee2e2' // Light red for holidays
                        });
                    }

                    successCallback(events);
                } catch (e) {
                    console.error('Error fetching events:', e);
                    failureCallback(e);
                }
            },
            dateClick: (info) => this.handleDateClick(info),
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

        if (props.editable === false) {
            const coordNombre = props.coord_descripcion || 'Otra';
            const horaIni = this.formatTime(props.detasig_hora_ini);
            const horaFin = this.formatTime(props.detasig_hora_fin);
            const comp = props.asig.comp_nombre_corto || props.asig.comp_nombre_unidad_competencia || 'Competencia';
            const inst = `${props.asig.inst_nombres || ''} ${props.asig.inst_apellidos || ''}`.trim();
            const amb = props.asig.ambiente_amb_id || 'N/A';
            const ficha = props.asig.ficha_num || props.asig.ficha_fich_id || 'N/A';
            
            const fechaObj = new Date(props.detasig_fecha + 'T00:00:00');
            const fechaFormat = fechaObj.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Asignación Restringida',
                    html: `
                        <div class="text-left space-y-2 text-sm mt-3">
                            <p class="text-red-600 font-semibold mb-2">No tienes permisos para editar esta asignación porque pertenece a la <strong>${coordNombre}</strong>.</p>
                            <p><strong>Fecha:</strong> ${fechaFormat}</p>
                            <p><strong>Horario:</strong> ${horaIni} - ${horaFin}</p>
                            <p><strong>Ficha:</strong> ${ficha}</p>
                            <p><strong>Competencia:</strong> ${comp}</p>
                            <p><strong>Instructor:</strong> ${inst}</p>
                            <p><strong>Ambiente:</strong> ${amb}</p>
                        </div>
                    `,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#3b82f6'
                });
            } else {
                NotificationService.showError(`Pertenece a: ${coordNombre}\nFecha: ${fechaFormat}\nHora: ${horaIni} - ${horaFin}\nFicha: ${ficha}\nInst: ${inst}`);
            }
            return;
        }

        const modal = document.getElementById('dayEditModal');
        if (!modal) return;

        // Fill the quick edit modal with this day's data
        document.getElementById('dayEdit_detasig_id').value = props.detasig_id;
        const asigId = props.asignacion_asig_id || props.asig_id || props.asig.asig_id;
        document.getElementById('dayEdit_asig_id').value = asigId;

        const dateObj = new Date(props.detasig_fecha + 'T00:00:00');
        const dateLabel = dateObj.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('dayEditDateLabel').textContent = dateLabel;
        document.getElementById('dayEditAsigInfo').textContent =
            `${this.formatTime(props.detasig_hora_ini)} - ${this.formatTime(props.detasig_hora_fin)}`;

        // Populate detail info fields
        const fichNum = props.asig.ficha_num || props.asig.ficha_fich_id || props.asig.fich_id || 'N/A';
        const progNombre = props.asig.prog_denominacion || props.asig.titpro_nombre || props.asig.prog_nombre || '';
        document.getElementById('dayEditFichaLabel').textContent = `Ficha ${fichNum}`;
        document.getElementById('dayEditProgramaLabel').textContent = progNombre || 'N/A';
        document.getElementById('dayEditCompetenciaLabel').textContent =
            props.asig.comp_nombre_corto || props.asig.comp_nombre_unidad_competencia || 'N/A';
        document.getElementById('dayEditInstructorLabel').textContent =
            `${props.asig.inst_nombres || ''} ${props.asig.inst_apellidos || ''}`.trim() || 'N/A';
        const ambNombre = props.asig.amb_nombre
            ? `${props.asig.ambiente_amb_id} — ${props.asig.amb_nombre}`
            : (props.asig.ambiente_amb_id || 'N/A');
        document.getElementById('dayEditAmbienteLabel').textContent = ambNombre;

        document.getElementById('dayEditTitle').textContent = 'Detalle de Asignación';
        document.getElementById('dayEditError').classList.add('hidden');

        // Configure the edit button
        const editBtn = document.getElementById('editDayAsigBtn');
        if (editBtn) {
            editBtn.onclick = () => {
                this.closeDayEditModal();
                this.openModal(props.asig);
            };
        }

        modal.classList.add('show');
    }

    handleDateClick(info) {
        const clickedDate = info.dateStr; // YYYY-MM-DD
        // Find details for this date
        const dayDetails = this.allDetalles.filter(d => d.detasig_fecha === clickedDate);
        if (dayDetails.length === 0) return; // No events on this day

        // Open quick edit for the first event on this day
        const d = dayDetails[0];
        // allDetalles stores raw API rows (not calendar extendedProps), so fallback to d itself
        const asig = d._asig || d.asig || d;
        
        if (d.editable === false) {
            const coordNombre = d.coord_descripcion || 'Otra';
            const horaIni = this.formatTime(d.detasig_hora_ini);
            const horaFin = this.formatTime(d.detasig_hora_fin);
            const comp = asig.comp_nombre_corto || asig.comp_nombre_unidad_competencia || 'Competencia';
            const inst = `${asig.inst_nombres || ''} ${asig.inst_apellidos || ''}`.trim();
            const amb = asig.ambiente_amb_id || 'N/A';
            const ficha = asig.ficha_num || asig.ficha_fich_id || 'N/A';
            
            const fechaObj = new Date(d.detasig_fecha + 'T00:00:00');
            const fechaFormat = fechaObj.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Asignación Restringida',
                    html: `
                        <div class="text-left space-y-2 text-sm mt-3">
                            <p class="text-red-600 font-semibold mb-2">No tienes permisos para editar esta asignación porque pertenece a la <strong>${coordNombre}</strong>.</p>
                            <p><strong>Fecha:</strong> ${fechaFormat}</p>
                            <p><strong>Horario:</strong> ${horaIni} - ${horaFin}</p>
                            <p><strong>Ficha:</strong> ${ficha}</p>
                            <p><strong>Competencia:</strong> ${comp}</p>
                            <p><strong>Instructor:</strong> ${inst}</p>
                            <p><strong>Ambiente:</strong> ${amb}</p>
                        </div>
                    `,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#3b82f6'
                });
            } else {
                NotificationService.showError(`Pertenece a: ${coordNombre}\nFecha: ${fechaFormat}\nHora: ${horaIni} - ${horaFin}\nFicha: ${ficha}\nInst: ${inst}`);
            }
            return;
        }

        const modal = document.getElementById('dayEditModal');
        if (!modal) return;

        document.getElementById('dayEdit_detasig_id').value = d.detasig_id;
        const asigId = d.asignacion_asig_id || d.asig_id || asig.asig_id;
        document.getElementById('dayEdit_asig_id').value = asigId;

        const dateObj = new Date(d.detasig_fecha + 'T00:00:00');
        const dateLabel = dateObj.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('dayEditDateLabel').textContent = dateLabel;
        document.getElementById('dayEditAsigInfo').textContent =
            `${this.formatTime(d.detasig_hora_ini)} - ${this.formatTime(d.detasig_hora_fin)}`;

        // Populate detail info fields
        const fichNumD = asig.ficha_num || asig.ficha_fich_id || asig.fich_id || 'N/A';
        const progNombreD = asig.prog_denominacion || asig.titpro_nombre || asig.prog_nombre || '';
        document.getElementById('dayEditFichaLabel').textContent = `Ficha ${fichNumD}`;
        document.getElementById('dayEditProgramaLabel').textContent = progNombreD || 'N/A';
        document.getElementById('dayEditCompetenciaLabel').textContent =
            asig.comp_nombre_corto || asig.comp_nombre_unidad_competencia || 'N/A';
        document.getElementById('dayEditInstructorLabel').textContent =
            `${asig.inst_nombres || ''} ${asig.inst_apellidos || ''}`.trim() || 'N/A';
        const ambNombreD = asig.amb_nombre
            ? `${asig.ambiente_amb_id} — ${asig.amb_nombre}`
            : (asig.ambiente_amb_id || 'N/A');
        document.getElementById('dayEditAmbienteLabel').textContent = ambNombreD;

        document.getElementById('dayEditTitle').textContent = 'Detalle de Asignación';
        document.getElementById('dayEditError').classList.add('hidden');

        // Configure the edit button
        const editBtn = document.getElementById('editDayAsigBtn');
        if (editBtn) {
            editBtn.onclick = () => {
                this.closeDayEditModal();
                this.openModal(asig);
            };
        }

        modal.classList.add('show');
    }

    closeDayEditModal() {
        const modal = document.getElementById('dayEditModal');
        if (modal) modal.classList.remove('show');
    }

    async handleDayEditSubmit(e) {
        e.preventDefault();
        const horaIni = document.getElementById('dayEdit_hora_ini').value;
        const horaFin = document.getElementById('dayEdit_hora_fin').value;
        const observaciones = document.getElementById('dayEdit_observaciones').value;
        const detId = document.getElementById('dayEdit_detasig_id').value;
        const asigId = document.getElementById('dayEdit_asig_id').value;
        const errorDiv = document.getElementById('dayEditError');
        const errorMsg = document.getElementById('dayEditErrorMsg');

        // Validate
        if (horaIni >= horaFin) {
            errorDiv.classList.remove('hidden');
            errorMsg.textContent = 'La hora de inicio debe ser menor a la hora de fin.';
            return;
        }
        if (horaIni < '06:00' || horaFin > '22:00') {
            errorDiv.classList.remove('hidden');
            errorMsg.textContent = 'El horario debe estar dentro de la jornada (06:00 AM - 10:00 PM).';
            return;
        }

        errorDiv.classList.add('hidden');
        const saveBtn = document.getElementById('saveDayEdit');
        if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Guardando...'; }

        try {
            const res = await fetch('../../routing.php?controller=detalle_asignacion&action=update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    detasig_id: detId,
                    asignacion_asig_id: asigId,
                    detasig_hora_ini: horaIni,
                    detasig_hora_fin: horaFin,
                    observaciones: observaciones
                })
            });

            if (res.ok) {
                NotificationService.showSuccess('Horario actualizado');
                this.closeDayEditModal();
                // Reload data from server then re-render calendar
                if (this.calendar) this.calendar.refetchEvents();
                this.updateDashboardStats();
            } else if (res.status === 409) {
                const result = await res.json();
                errorDiv.classList.remove('hidden');
                errorMsg.textContent = 'Cruce de horario detectado. Elija otro horario.';
            } else {
                const result = await res.json();
                errorDiv.classList.remove('hidden');
                errorMsg.textContent = result.error || 'Error al guardar.';
            }
        } catch (err) {
            NotificationService.showError('Error de conexión.');
        } finally {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Guardar';
            }
        }
    }

    async handleModalFichaChange() {
        const fichaId = document.getElementById('modal_ficha_id')?.value;
        const compSelect = document.getElementById('competencia_id');
        const instSelect = document.getElementById('instructor_id');
        
        if (!compSelect || !instSelect) return;

        compSelect.innerHTML = '<option value="">Cargando competencias...</option>';
        instSelect.innerHTML = '<option value="">Primero seleccione competencia...</option>';
        instSelect.disabled = true;

        if (!fichaId) {
            compSelect.innerHTML = '<option value="">Primero seleccione ficha...</option>';
            if (window.refreshTS) {
                refreshTS('#competencia_id', [], 'Primero seleccione ficha...');
            }
            return;
        }

        const ficha = this.fichas.find(f => f.fich_id == fichaId);
        if (!ficha) return;

        const progId = ficha.programa_prog_codigo || ficha.programa_prog_id;
        
        try {
            const [resComp, resHab] = await Promise.all([
                fetch(`../../routing.php?controller=competencia&action=getByPrograma&prog_id=${progId}`, { headers: { 'Accept': 'application/json' } }),
                fetch('../../routing.php?controller=instru_competencia&action=index', { headers: { 'Accept': 'application/json' } })
            ]);
            
            this.allCompetenciasPrograma = await resComp.json();
            this.allHabilitaciones = await resHab.json();
            
            // Load available competencias (not already assigned to this ficha)
            const asigIdInput = document.getElementById('asig_id')?.value;
            // Fetch asignaciones for this specific ficha to filter out already assigned comps
            const resAsig = await fetch('../../routing.php?controller=asignacion&action=index', { headers: { 'Accept': 'application/json' } });
            const dataAsig = await resAsig.json();
            this.allAsignaciones = (Array.isArray(dataAsig) ? dataAsig : []).filter(
                a => a.ficha_fich_id == fichaId || a.fich_id == fichaId
            );

            const assignedCompIds = this.allAsignaciones
                .filter(a => !asigIdInput || a.asig_id != asigIdInput)
                .map(a => a.competencia_comp_id);
                
            let availableComps = (Array.isArray(this.allCompetenciasPrograma) ? this.allCompetenciasPrograma : []).filter(c => !assignedCompIds.includes(c.comp_id));

            // Si estamos en la pestaña Instructor y creando una nueva asignación, filtramos las competencias a SOLO las que este instructor puede dictar para el programa de la ficha
            if (this.activeTab === 'instructor' && this.selectedInstructor && (!asigIdInput || asigIdInput === '')) {
                const habilitacionesInst = this.allHabilitaciones.filter(h => 
                    h.instructor_inst_id == this.selectedInstructor &&
                    (h.programa_prog_id == progId || h.programa_prog_id === null || h.programa_prog_id === '')
                );
                const compIdsHab = habilitacionesInst.map(h => h.competencia_comp_id);
                availableComps = availableComps.filter(c => compIdsHab.includes(c.comp_id));
            }

            const compOpts = availableComps.length === 0
                ? [{ value: '', text: 'No hay competencias pendientes (o habilitadas para el instructor)' }]
                : [{ value: '', text: 'Seleccione competencia...' }, ...availableComps.map(c => ({ value: c.comp_id, text: c.comp_nombre_corto || c.comp_nombre_unidad_competencia }))];
            
            if (window.refreshTS) {
                refreshTS('#competencia_id', compOpts, 'Buscar competencia...');
            }
            
            // Auto-seleccionar si solo hay 1 competencia disponible para el instructor
            if (this.activeTab === 'instructor' && this.selectedInstructor && availableComps.length === 1 && (!asigIdInput || asigIdInput === '')) {
                setTimeout(() => {
                    const compSelect = document.getElementById('competencia_id');
                    if (compSelect) {
                        if (compSelect.tomselect) {
                            compSelect.tomselect.setValue(availableComps[0].comp_id);
                        } else {
                            compSelect.value = availableComps[0].comp_id;
                        }
                        this.handleCompetenciaChange();
                    }
                }, 100);
            }

        } catch(e) {
            console.error('Error cargando data de ficha para modal:', e);
        }
    }

    openModal(asig = null, startDate = null, endDate = null) {
        const modal = document.getElementById('asignacionModal');
        const form = document.getElementById('asignacionForm');
        if (!form) return;
        form.reset();

        const modalTitle = document.getElementById('modalTitle');
        const asigIdInput = document.getElementById('asig_id');
        const fichaSelect = document.getElementById('modal_ficha_id');
        const fechaIni = document.getElementById('asig_fecha_ini');
        const fechaFin = document.getElementById('asig_fecha_fin');
        const competenciaSelect = document.getElementById('competencia_id');
        const instructorSelect = document.getElementById('instructor_id');
        const sedeSelect = document.getElementById('sede_id');
        const ambienteSelect = document.getElementById('ambiente_id');

        // Populate and init Ficha TomSelect
        if (fichaSelect) {
            fichaSelect.innerHTML = '<option value="">Seleccione una ficha...</option>';
            const fichOpts = [{ value: '', text: 'Seleccione una ficha...' }];
            this.fichas.forEach(f => {
                fichOpts.push({
                    value: f.fich_id,
                    text: `Ficha ${f.fich_id} — ${f.prog_denominacion || f.titpro_nombre || ''}`
                });
            });
            if (window.initTS) {
                initTS('#modal_ficha_id', 'Seleccione una ficha...');
                refreshTS('#modal_ficha_id', fichOpts, 'Seleccione una ficha...');
            }
            
            // Add event listener only once
            if (!fichaSelect.dataset.bound) {
                fichaSelect.addEventListener('change', () => this.handleModalFichaChange());
                fichaSelect.dataset.bound = 'true';
            }

            if (asig) {
                fichaSelect.value = asig.ficha_fich_id;
                if(fichaSelect.tomselect) {
                    fichaSelect.tomselect.setValue(asig.ficha_fich_id);
                    fichaSelect.tomselect.disable();
                } else {
                    fichaSelect.disabled = true;
                }
            } else if (this.activeTab === 'ficha' && this.selectedFicha) {
                fichaSelect.value = this.selectedFicha.fich_id;
                if(fichaSelect.tomselect) {
                    fichaSelect.tomselect.setValue(this.selectedFicha.fich_id);
                    fichaSelect.tomselect.enable();
                } else {
                    fichaSelect.disabled = false;
                }
            } else {
                if(fichaSelect.tomselect) {
                    fichaSelect.tomselect.clear();
                    fichaSelect.tomselect.enable();
                } else {
                    fichaSelect.disabled = false;
                }
            }
        }

        if (asig) {
            if (fechaIni && asig.asig_fecha_ini) fechaIni.value = asig.asig_fecha_ini;
            if (fechaFin && asig.asig_fecha_fin) fechaFin.value = asig.asig_fecha_fin;
        } else {
            if (startDate && fechaIni) fechaIni.value = startDate;
            if (endDate && fechaFin) {
                // FullCalendar selection: endStr is exclusive, subtract 1 day
                const endObj = new Date(endDate);
                endObj.setDate(endObj.getDate() - 1);
                const correctedEnd = endObj.toISOString().split('T')[0];
                fechaFin.value = correctedEnd;
            }
        }

        // Generate day fields based on range
        const finalStart = fechaIni?.value;
        const finalEnd = fechaFin?.value;
        if (finalStart && finalEnd && finalStart <= finalEnd) {
            this.generateDaysInputs(finalStart, finalEnd, asig, false);
        } else {
            // Clear days container
            const container = document.getElementById('diasListContainer');
            if (container) {
                container.innerHTML = '<p class="text-sm text-center text-gray-400 italic py-4">Seleccione un rango de fechas para configurar los días</p>';
            }
        }

        if (competenciaSelect && !asig) {
            refreshTS('#competencia_id', [{ value: '', text: 'Primero seleccione ficha...' }], 'Buscar competencia...');
        }

        if (instructorSelect) {
            instructorSelect.innerHTML = '<option value="">Primero seleccione competencia...</option>';
            instructorSelect.disabled = true;
        }
        
        const instGroup = document.getElementById('instructor_form_group');
        if (instGroup) {
            if (this.activeTab === 'instructor') {
                instGroup.style.display = 'none';
            } else {
                instGroup.style.display = 'block';
            }
        }
        
        const fichaGroup = fichaSelect ? fichaSelect.closest('.form-group') : null;
        if (fichaGroup) {
            if (this.activeTab === 'ficha') {
                fichaGroup.style.display = 'none';
            } else {
                fichaGroup.style.display = 'block';
            }
        }

        const sedeGroup = document.getElementById('sede_form_group');
        const ambGroup = document.getElementById('ambiente_form_group');
        if (sedeGroup && ambGroup) {
            if (this.activeTab === 'ambiente') {
                sedeGroup.style.display = 'none';
                ambGroup.style.display = 'none';
            } else {
                sedeGroup.style.display = 'block';
                ambGroup.style.display = 'block';
            }
        }

        if (sedeSelect) {
            sedeSelect.innerHTML = '<option value="">Seleccione sede...</option>';
            this.sedes.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.sede_id;
                opt.textContent = s.sede_nombre;
                sedeSelect.appendChild(opt);
            });
        }

        if (asig) {
            if (modalTitle) modalTitle.textContent = 'Editar Asignación';
            if (asigIdInput) asigIdInput.value = asig.asig_id;
            
            // Pre-seleccionar Sede y Ambiente
            if (sedeSelect && asig.ambiente_amb_id) {
                const amb = this.ambientes.find(a => a.amb_id == asig.ambiente_amb_id);
                if (amb) {
                    sedeSelect.value = amb.sede_sede_id;
                    this.handleSedeChange(asig.ambiente_amb_id);
                }
            }

            // Fire Ficha change to load competencies, then preselect
            this.handleModalFichaChange().then(() => {
                if (competenciaSelect) {
                    const currentCompInList = Array.from(competenciaSelect.options).some(o => o.value == asig.competencia_comp_id);
                    if (!currentCompInList) {
                        if (competenciaSelect.tomselect) {
                             competenciaSelect.tomselect.addOption({value: asig.competencia_comp_id, text: asig.comp_nombre_corto || 'Competencia actual'});
                        } else {
                             const opt = document.createElement('option');
                             opt.value = asig.competencia_comp_id;
                             opt.textContent = asig.comp_nombre_corto || 'Competencia actual';
                             competenciaSelect.appendChild(opt);
                        }
                    }
                    if (competenciaSelect.tomselect) {
                        competenciaSelect.tomselect.setValue(asig.competencia_comp_id);
                    } else {
                        competenciaSelect.value = asig.competencia_comp_id;
                    }
                    this.handleCompetenciaChange();
                    setTimeout(() => {
                        if (instructorSelect) {
                            if (instructorSelect.tomselect) {
                                instructorSelect.tomselect.setValue(asig.instructor_inst_id);
                            } else {
                                instructorSelect.value = asig.instructor_inst_id;
                            }
                        }
                    }, 100);
                }
            });

        } else {
            if (modalTitle) modalTitle.textContent = 'Nueva Asignación';
            if (asigIdInput) asigIdInput.value = '';
            if (ambienteSelect) ambienteSelect.innerHTML = '<option value="">Primero seleccione sede...</option>';
            
            // Pre-fill fields based on active tab
            if (this.activeTab === 'ambiente' && this.selectedAmbiente && sedeSelect) {
                const amb = this.ambientes.find(a => a.amb_id == this.selectedAmbiente);
                if (amb) {
                    sedeSelect.value = amb.sede_sede_id;
                    this.handleSedeChange(this.selectedAmbiente);
                }
            }
            if (this.activeTab === 'ficha' && this.selectedFicha) {
                this.handleModalFichaChange();
            }
        }

        // Clear previous conflict alerts
        const conflictAlert = document.getElementById('modalConflictAlert');
        if (conflictAlert) {
            conflictAlert.classList.add('hidden');
            conflictAlert.innerHTML = '';
        }

        if (modal) modal.classList.add('show');
    }

    closeModal() {
        const modal = document.getElementById('asignacionModal');
        if (modal) modal.classList.remove('show');
    }

    generateDaysInputs(startStr, endStr, existingAsig = null, fromUserInput = false) {
        const container = document.getElementById('diasListContainer');
        if (!container) return;

        container.innerHTML = '';

        let start = new Date(startStr + 'T00:00:00');
        let end = new Date(endStr + 'T00:00:00');

        const defaultIni = document.getElementById('default_hora_ini')?.value || '08:00';
        const defaultFin = document.getElementById('default_hora_fin')?.value || '12:00';

        // Safety: max 90 days to avoid browser hang
        const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        if (diffDays > 90) {
            container.innerHTML = '<p class="text-sm text-center text-red-500 italic py-4">El rango no puede exceder 90 días</p>';
            return;
        }
        if (diffDays < 1) {
            container.innerHTML = '<p class="text-sm text-center text-gray-400 italic py-4">El rango de fechas no es válido</p>';
            return;
        }

        // Calculate holidays for the years covered in the range
        const startYear = start.getFullYear();
        const endYear = end.getFullYear();
        const holidays = new Set();
        for (let y = startYear; y <= endYear; y++) {
            const yearHolidays = getColombianHolidays(y);
            yearHolidays.forEach(h => holidays.add(h));
        }

        // Header
        const header = document.createElement('div');
        header.className = 'flex items-center justify-between mb-2';
        header.innerHTML = `
            <span class="text-xs font-bold text-gray-600">${diffDays} día(s) en el rango</span>
            <button type="button" id="selectAllDays" class="text-xs text-sena-green font-bold hover:underline">Seleccionar todos</button>
        `;
        container.appendChild(header);

        const current = new Date(start);
        const formatOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const today = new Date().toISOString().split('T')[0];

        while (current <= end) {
            const dateISO = current.toISOString().split('T')[0];
            
            // Skip Sundays (Day 0)
            if (current.getDay() === 0) {
                current.setDate(current.getDate() + 1);
                continue;
            }

            const dateLabel = current.toLocaleDateString('es-CO', formatOptions);
            const isPast = dateISO < today;
            const isHoliday = holidays.has(dateISO);
            const isDisabled = isPast || isHoliday;

            const row = document.createElement('div');
            let rowClass = `flex flex-col md:flex-row items-start md:items-center gap-2 md:gap-4 p-3 bg-white border rounded-lg shadow-sm transition-all `;
            if (isPast) {
                rowClass += `border-red-200 bg-red-50/30 opacity-60`;
            } else if (isHoliday) {
                rowClass += `border-amber-200 bg-amber-50/30 opacity-75`;
            } else {
                rowClass += `border-gray-100 hover:border-sena-green/30`;
            }
            row.className = rowClass;

            row.innerHTML = `
                <div class="flex items-center gap-3 w-full md:w-2/5 md:min-w-[140px]">
                    <input type="checkbox" id="chk_${dateISO}" class="day-checkbox w-4 h-4 text-sena-green rounded focus:ring-sena-green accent-[#39a900] flex-shrink-0" ${isDisabled ? 'disabled' : ''}>
                    <label for="chk_${dateISO}" class="text-sm font-medium text-gray-700 capitalize cursor-pointer leading-tight flex-1">
                        ${dateLabel}
                        ${isHoliday ? '<span class="text-[10px] text-amber-500 font-bold ml-2 whitespace-nowrap">(Festivo)</span>' : ''}
                    </label>
                </div>
                <div class="flex-1 flex items-center gap-2 md:gap-3 w-full pl-7 md:pl-0">
                    <div class="flex-1 min-w-[110px]">
                        <input type="time" id="ini_${dateISO}" class="day-time-ini w-full px-2 md:px-3 py-1.5 text-xs md:text-sm border border-gray-200 rounded-md focus:ring-2 focus:ring-sena-green/20 focus:border-sena-green transition-all" value="${defaultIni}" disabled min="06:00" max="22:00">
                    </div>
                    <span class="text-gray-400 font-bold hidden md:inline">—</span>
                    <div class="flex-1 min-w-[110px]">
                        <input type="time" id="fin_${dateISO}" class="day-time-fin w-full px-2 md:px-3 py-1.5 text-xs md:text-sm border border-gray-200 rounded-md focus:ring-2 focus:ring-sena-green/20 focus:border-sena-green transition-all" value="${defaultFin}" disabled min="06:00" max="22:00">
                    </div>
                </div>
                ${isPast ? '<span class="text-[10px] text-red-400 font-bold whitespace-nowrap hidden md:block">Fecha pasada</span>' : ''}
                ${isHoliday ? '<span class="text-[10px] text-amber-500 font-bold whitespace-nowrap hidden md:block">Festivo</span>' : ''}
            `;

            container.appendChild(row);

            // Toggle logic
            const chk = row.querySelector(`#chk_${dateISO}`);
            const ini = row.querySelector(`#ini_${dateISO}`);
            const fin = row.querySelector(`#fin_${dateISO}`);

            if (chk && !isDisabled) {
                chk.addEventListener('change', (e) => {
                    ini.disabled = !e.target.checked;
                    fin.disabled = !e.target.checked;
                    if (e.target.checked) {
                        row.classList.add('border-sena-green/40', 'bg-green-50/30');
                        ini.focus();
                    } else {
                        row.classList.remove('border-sena-green/40', 'bg-green-50/30');
                    }
                });
            }

            current.setDate(current.getDate() + 1);
        }

        // "Select All" button
        const selectAllBtn = container.querySelector('#selectAllDays');
        if (selectAllBtn) {
            selectAllBtn.onclick = () => {
                container.querySelectorAll('.day-checkbox:not(:disabled)').forEach(chk => {
                    chk.checked = true;
                    chk.dispatchEvent(new Event('change'));
                });
            };
        }

        // Load existing details if editing
        if (existingAsig) {
            this.loadExistingDetailsIntoUI(existingAsig.asig_id);
        }
    }

    async loadExistingDetailsIntoUI(asigId) {
        try {
            const res = await fetch(`../../routing.php?controller=detalle_asignacion&action=index&asig_id=${asigId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const franjas = await res.json();

            // Turn off all by default in edit mode first
            document.querySelectorAll('.day-checkbox').forEach(chk => {
                chk.checked = false;
                const dateISO = chk.id.split('chk_')[1];
                const ini = document.getElementById(`ini_${dateISO}`);
                const fin = document.getElementById(`fin_${dateISO}`);
                if (ini) ini.disabled = true;
                if (fin) fin.disabled = true;
            });

            if (Array.isArray(franjas)) {
                franjas.forEach(f => {
                    const dateISO = f.detasig_fecha;
                    const chk = document.getElementById(`chk_${dateISO}`);
                    if (chk) {
                        chk.checked = true;
                        const ini = document.getElementById(`ini_${dateISO}`);
                        const fin = document.getElementById(`fin_${dateISO}`);
                        if (ini) {
                            ini.disabled = false;
                            ini.value = f.detasig_hora_ini.substring(0, 5);
                        }
                        if (fin) {
                            fin.disabled = false;
                            fin.value = f.detasig_hora_fin.substring(0, 5);
                        }
                        // Highlight selected day
                        const row = chk.closest('.flex');
                        if (row) row.classList.add('border-sena-green/40', 'bg-green-50/30');
                    }
                });
            }
        } catch (e) {
            console.error('Error cargando detalles existentes', e);
        }
    }

    handleCompetenciaChange() {
        const competenciaSelect = document.getElementById('competencia_id');
        const instructorSelect = document.getElementById('instructor_id');
        const fichaSelect = document.getElementById('modal_ficha_id');
        const compId = competenciaSelect.value;
        const fichaId = fichaSelect ? fichaSelect.value : null;

        if (!instructorSelect) return;

        if (!compId || !fichaId) {
            instructorSelect.innerHTML = '<option value="">Primero seleccione competencia...</option>';
            instructorSelect.disabled = true;
            return;
        }

        const ficha = this.fichas.find(f => f.fich_id == fichaId);
        if (!ficha) return;

        const progId = ficha.programa_prog_codigo || ficha.programa_prog_id;
        // Filtrar habilitaciones por comp_id Y por programa_prog_id de la ficha.
        // Se acepta también programa_prog_id NULL o vacío (competencias transversales).
        const habilitados = this.allHabilitaciones.filter(h =>
            h.competencia_comp_id == compId &&
            (h.programa_prog_id == progId || h.programa_prog_id === null || h.programa_prog_id === '' || h.programa_prog_id === undefined)
        );

        if (habilitados.length === 0) {
            instructorSelect.disabled = true;
            refreshTS('#instructor_id', [{ value: '', text: 'Sin instructores habilitados' }], 'Buscar instructor...');
            if (instructorSelect.tomselect) instructorSelect.tomselect.disable();
        } else {
            const seen = new Set();
            const instOpts = [{ value: '', text: 'Seleccione instructor...' }];
            habilitados.forEach(h => {
                const name = `${h.inst_nombres} ${h.inst_apellidos}`;
                if (!seen.has(name)) {
                    seen.add(name);
                    instOpts.push({ value: h.instructor_inst_id, text: name });
                }
            });
            instructorSelect.disabled = false;
            refreshTS('#instructor_id', instOpts, 'Buscar instructor...');
            if (instructorSelect.tomselect) instructorSelect.tomselect.enable();
            
            // Si el tab activo es Instructor y este instructor está en la lista de habilitados, pre-seleccionar y bloquear
            if (this.activeTab === 'instructor' && this.selectedInstructor) {
                const isHabilitado = habilitados.some(h => h.instructor_inst_id == this.selectedInstructor);
                if (isHabilitado) {
                    if (instructorSelect.tomselect) {
                        instructorSelect.tomselect.setValue(this.selectedInstructor);
                        instructorSelect.tomselect.enable();
                    } else {
                        instructorSelect.value = this.selectedInstructor;
                        instructorSelect.disabled = false;
                    }
                    
                    const instGroup = document.getElementById('instructor_form_group');
                    if (instGroup) {
                        instGroup.style.display = 'none';
                    }
                }
            }
        }
    }

    validateDaysClient(diasSeleccionados) {
        const errors = [];
        const today = new Date().toISOString().split('T')[0];

        for (const dia of diasSeleccionados) {
            const label = new Date(dia.fecha + 'T00:00:00').toLocaleDateString('es-CO', { day: 'numeric', month: 'short' });

            // Coherencia cronológica
            if (dia.hora_ini >= dia.hora_fin) {
                errors.push(`${label}: La hora de inicio debe ser menor a la hora de fin`);
            }

            // Jornada institucional
            if (dia.hora_ini < '06:00' || dia.hora_fin > '22:00') {
                errors.push(`${label}: Horario fuera de la jornada (06:00 AM - 10:00 PM)`);
            }

            // Fecha no en el pasado
            if (dia.fecha < today) {
                errors.push(`${label}: No se puede programar en una fecha pasada`);
            }
        }

        return errors;
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
            asig_fecha_fin: document.getElementById('asig_fecha_fin').value,
            dias_seleccionados: []
        };
        if (id) data.asig_id = id;

        // Collect selected days
        const checkboxes = document.querySelectorAll('.day-checkbox:checked');
        checkboxes.forEach(chk => {
            const dateISO = chk.id.split('chk_')[1];
            const ini = document.getElementById(`ini_${dateISO}`).value;
            const fin = document.getElementById(`fin_${dateISO}`).value;
            data.dias_seleccionados.push({
                fecha: dateISO,
                hora_ini: ini,
                hora_fin: fin
            });
        });

        if (data.dias_seleccionados.length === 0) {
            NotificationService.showError('Debe seleccionar al menos un día con su respectivo horario.');
            return;
        }

        // Client-side validation
        const clientErrors = this.validateDaysClient(data.dias_seleccionados);
        if (clientErrors.length > 0) {
            NotificationService.showError(clientErrors.join('\n'));
            return;
        }

        const conflictAlert = document.getElementById('modalConflictAlert');
        if (conflictAlert) {
            conflictAlert.classList.add('hidden');
            conflictAlert.innerHTML = '';
        }

        await this.sendSaveRequest(data, action);
    }

    async sendSaveRequest(data, action) {
        const saveBtn = document.getElementById('saveBtn');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Guardando...';
        }

        try {
            while (true) {
                const res = await fetch(`../../routing.php?controller=asignacion&action=${action}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if (res.status === 202 && result.warning) {
                    const confirmed = await new Promise((resolve) => {
                        const originalCancel = NotificationService.cancelBtn.onclick;
                        NotificationService.cancelBtn.onclick = () => {
                            NotificationService.hide();
                            NotificationService.cancelBtn.onclick = originalCancel;
                            resolve(false);
                        };
                        NotificationService.showConfirm(result.message, () => {
                            NotificationService.cancelBtn.onclick = originalCancel;
                            resolve(true);
                        }, {
                            title: result.warning === '80_percent_alert' ? 'Horas menores al 80%' : 'Límite de 160 horas superado',
                            confirmText: 'Sí, guardar',
                            type: 'warning'
                        });
                    });

                    if (confirmed) {
                        if (result.warning === '80_percent_alert') {
                            data.confirm_80_percent = true;
                        } else if (result.warning === '160_hours_alert') {
                            data.confirm_160_hours = true;
                        }
                        continue;
                    } else {
                        break;
                    }
                } else if (res.ok) {
                    const id = data.asig_id;
                    NotificationService.showSuccess(id ? '¡Asignación actualizada!' : '¡Asignación registrada!');
                    this.closeModal();
                    const fichId = this.selectedFicha?.fich_id;
                    if (fichId) await this.loadAsignacionesFicha(fichId);
                    if (this.calendar) this.calendar.refetchEvents();
                    this.updateDashboardStats();
                    break;
                } else if (res.status === 409) {
                    this.showConflictAlert(result.details || [], result.error);
                    break;
                } else {
                    NotificationService.showError(result.error || 'Error al guardar.');
                    break;
                }
            }
        } catch (error) {
            NotificationService.showError('Error de conexión.');
        } finally {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Guardar';
            }
        }
    }

    showConflictAlert(conflicts, errorMsg = '') {
        const conflictAlert = document.getElementById('modalConflictAlert');
        if (!conflictAlert) return;

        let html = '';
        if (conflicts.length > 0) {
            const types = new Set();
            conflicts.forEach(c => {
                if (c.conflict_type) {
                    c.conflict_type.forEach(t => {
                        if (t === 'instructor') types.add('el Instructor');
                        else if (t === 'ambiente') types.add('el Ambiente');
                        else if (t === 'ficha') types.add('la Ficha');
                    });
                }
            });
            const typeMsg = Array.from(types).join(' y ');
            const daysStr = [...new Set(conflicts.map(c => c.dia_conflicto || ''))].filter(Boolean).join(', ');

            html = `
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <ion-icon src="../../assets/ionicons/warning-outline.svg" class="text-red-500 text-xl"></ion-icon>
                        <span class="text-sm font-bold text-red-700">Cruce de Horario Detectado</span>
                    </div>
                    <p class="text-xs text-red-600 leading-relaxed">
                        ${typeMsg} ya tiene(n) cruce en los días: <strong>${daysStr || 'seleccionados'}</strong>.
                        Ajuste los horarios o seleccione otro recurso.
                    </p>
                </div>
            `;
        } else if (errorMsg) {
            html = `
                <div class="p-4 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-amber-500 text-xl"></ion-icon>
                        <span class="text-sm font-bold text-amber-700">Error de Validación</span>
                    </div>
                    <p class="text-xs text-amber-600 leading-relaxed whitespace-pre-line">${errorMsg}</p>
                </div>
            `;
        }

        conflictAlert.classList.remove('hidden');
        conflictAlert.innerHTML = html;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.asignacionManager = new AsignacionManager();
});
