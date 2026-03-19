/**
 * Asignacion Management JavaScript
 * Full calendar with per-day events, inline day editing, and conflict detection.
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
            this.loadSedes(),
            this.loadAmbientes()
        ]);
    }

    bindEvents() {
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

        const dayEditForm = document.getElementById('dayEditForm');
        if (dayEditForm) dayEditForm.onsubmit = (e) => this.handleDayEditSubmit(e);

        const deleteAsigBtn = document.getElementById('deleteDayAsig');
        if (deleteAsigBtn) deleteAsigBtn.onclick = () => this.handleDeleteAsig();

        const deleteDayOnlyBtn = document.getElementById('deleteDayOnly');
        if (deleteDayOnlyBtn) deleteDayOnlyBtn.onclick = () => this.handleDeleteDayOnly();

        const applyDefaultHoursBtn = document.getElementById('applyDefaultHours');
        if (applyDefaultHoursBtn) applyDefaultHoursBtn.onclick = () => this.handleApplyDefaultHours();
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
                    await this.loadAllDetalles();
                    this.initCalendar();
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
                    await this.loadAllDetalles();
                    this.initCalendar();
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
                
                // Initialize TomSelect if loaded
                if (window.TomSelect) {
                    new TomSelect('#fichaSelector', {
                        create: false,
                        maxOptions: null,
                        placeholder: 'Buscar ficha o programa...',
                    });
                }
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

        if (fichId) {
            this.selectedFicha = this.fichas.find(f => f.fich_id == fichId) || null;
        } else {
            this.selectedFicha = null;
        }

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

        // Load all detail events for the calendar
        await this.loadAllDetalles();
        this.initCalendar();
        this.updateDashboardStats();
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

        // Instructores habilitados para este programa
        const instructoresHabilitados = this.allHabilitaciones.filter(h => h.competxprograma_programa_prog_id == progId);
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
                        d._asig = asig; // attach parent assignment info
                    });
                    this.allDetalles.push(...detalles);
                }
            }
        } catch (e) {
            console.error('Error cargando detalles:', e);
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

        // Build events from DETAILS (individual days) instead of just assignment ranges
        const events = this.allDetalles.map((d, i) => {
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

        const modal = document.getElementById('dayEditModal');
        if (!modal) return;

        // Fill the quick edit modal with this day's data
        document.getElementById('dayEdit_detasig_id').value = props.detasig_id;
        document.getElementById('dayEdit_asig_id').value = props.asignacion_asig_id || props.asig.asig_id;

        const dateObj = new Date(props.detasig_fecha + 'T00:00:00');
        const dateLabel = dateObj.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('dayEditDateLabel').textContent = dateLabel;
        document.getElementById('dayEditAsigInfo').textContent = `${props.asig.comp_nombre_corto || 'Competencia'} — ${props.asig.inst_nombres || ''} ${props.asig.inst_apellidos || ''} | Amb. ${props.asig.ambiente_amb_id || ''}`;

        document.getElementById('dayEdit_hora_ini').value = this.formatTime(props.detasig_hora_ini);
        document.getElementById('dayEdit_hora_fin').value = this.formatTime(props.detasig_hora_fin);
        document.getElementById('dayEdit_observaciones').value = props.observaciones || '';

        document.getElementById('dayEditTitle').textContent = 'Editar Horario del Día';
        document.getElementById('dayEditError').classList.add('hidden');

        modal.classList.add('show');
    }

    handleDateClick(info) {
        const clickedDate = info.dateStr; // YYYY-MM-DD
        // Find details for this date
        const dayDetails = this.allDetalles.filter(d => d.detasig_fecha === clickedDate);
        if (dayDetails.length === 0) return; // No events on this day

        // Open quick edit for the first event on this day
        const d = dayDetails[0];
        const asig = d._asig;
        if (!asig) return;

        const modal = document.getElementById('dayEditModal');
        if (!modal) return;

        document.getElementById('dayEdit_detasig_id').value = d.detasig_id;
        document.getElementById('dayEdit_asig_id').value = d.asignacion_asig_id || asig.asig_id;

        const dateObj = new Date(d.detasig_fecha + 'T00:00:00');
        const dateLabel = dateObj.toLocaleDateString('es-CO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('dayEditDateLabel').textContent = dateLabel;
        document.getElementById('dayEditAsigInfo').textContent = `${asig.comp_nombre_corto || 'Competencia'} — ${asig.inst_nombres || ''} ${asig.inst_apellidos || ''} | Amb. ${asig.ambiente_amb_id || ''}`;

        document.getElementById('dayEdit_hora_ini').value = this.formatTime(d.detasig_hora_ini);
        document.getElementById('dayEdit_hora_fin').value = this.formatTime(d.detasig_hora_fin);
        document.getElementById('dayEdit_observaciones').value = d.observaciones || '';

        document.getElementById('dayEditTitle').textContent = 'Editar Horario del Día';
        document.getElementById('dayEditError').classList.add('hidden');

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
                await this.loadAllDetalles();
                this.initCalendar();
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
        if (endDate && fechaFin) {
            // FullCalendar selection: endStr is exclusive, subtract 1 day
            const endObj = new Date(endDate);
            endObj.setDate(endObj.getDate() - 1);
            const correctedEnd = endObj.toISOString().split('T')[0];
            fechaFin.value = correctedEnd;
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

        // Load available competencias (not already assigned to this ficha)
        const assignedCompIds = this.allAsignaciones
            .filter(a => !asig || a.asig_id != asig?.asig_id)
            .map(a => a.competencia_comp_id);
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

        const sedeSelect = document.getElementById('sede_id');
        const ambienteSelect = document.getElementById('ambiente_id');

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

            // Pre-select competencia and instructor for edit
            if (competenciaSelect) {
                // Add current competencia if not already in the list
                const currentCompInList = Array.from(competenciaSelect.options).some(o => o.value == asig.competencia_comp_id);
                if (!currentCompInList) {
                    const opt = document.createElement('option');
                    opt.value = asig.competencia_comp_id;
                    opt.textContent = asig.comp_nombre_corto || 'Competencia actual';
                    competenciaSelect.appendChild(opt);
                }
                competenciaSelect.value = asig.competencia_comp_id;
                this.handleCompetenciaChange();
                setTimeout(() => {
                    if (instructorSelect) instructorSelect.value = asig.instructor_inst_id;
                }, 100);
            }
        } else {
            if (modalTitle) modalTitle.textContent = 'Nueva Asignación';
            if (asigIdInput) asigIdInput.value = '';
            if (ambienteSelect) ambienteSelect.innerHTML = '<option value="">Primero seleccione sede...</option>';
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

            const row = document.createElement('div');
            row.className = `flex flex-col md:flex-row items-start md:items-center gap-2 md:gap-4 p-3 bg-white border rounded-lg shadow-sm transition-all ${isPast ? 'border-red-200 bg-red-50/30 opacity-60' : 'border-gray-100 hover:border-sena-green/30'}`;

            row.innerHTML = `
                <div class="flex items-center gap-3 w-full md:w-2/5 md:min-w-[140px]">
                    <input type="checkbox" id="chk_${dateISO}" class="day-checkbox w-4 h-4 text-sena-green rounded focus:ring-sena-green accent-[#39a900] flex-shrink-0" ${isPast ? 'disabled' : ''}>
                    <label for="chk_${dateISO}" class="text-sm font-medium text-gray-700 capitalize cursor-pointer leading-tight flex-1">${dateLabel}</label>
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
            `;

            container.appendChild(row);

            // Toggle logic
            const chk = row.querySelector(`#chk_${dateISO}`);
            const ini = row.querySelector(`#ini_${dateISO}`);
            const fin = row.querySelector(`#fin_${dateISO}`);

            if (chk && !isPast) {
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

        // Disable save button while submitting
        const saveBtn = document.getElementById('saveBtn');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Guardando...';
        }

        try {
            const res = await fetch(`../../routing.php?controller=asignacion&action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();

            if (res.status === 202 && result.warning === '80_percent_alert') {
                NotificationService.showConfirm(result.message, async () => {
                    data.confirm_80_percent = true;
                    
                    // Reenviar con confirmación
                    const saveBtnRetry = document.getElementById('saveBtn');
                    if (saveBtnRetry) {
                        saveBtnRetry.disabled = true;
                        saveBtnRetry.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Guardando...';
                    }
                    try {
                        const resRetry = await fetch(`../../routing.php?controller=asignacion&action=${action}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(data)
                        });
                        const resultRetry = await resRetry.json();
                        
                        if (resRetry.ok) {
                            NotificationService.showSuccess(id ? '¡Asignación actualizada!' : '¡Asignación registrada!');
                            this.closeModal();
                            await this.loadAsignacionesFicha(this.selectedFicha.fich_id);
                            await this.loadAllDetalles();
                            this.initCalendar();
                            this.updateDashboardStats();
                        } else {
                            NotificationService.showError(resultRetry.error || 'Error al guardar.');
                        }
                    } catch (e) {
                        NotificationService.showError('Error de conexión.');
                    } finally {
                        if (saveBtnRetry) {
                            saveBtnRetry.disabled = false;
                            saveBtnRetry.innerHTML = '<ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Guardar';
                        }
                    }
                }, {
                    title: 'Horas menores al 80%',
                    confirmText: 'Sí, guardar',
                    type: 'warning'
                });
            } else if (res.ok) {
                NotificationService.showSuccess(id ? '¡Asignación actualizada!' : '¡Asignación registrada!');
                this.closeModal();
                await this.loadAsignacionesFicha(this.selectedFicha.fich_id);
                await this.loadAllDetalles();
                this.initCalendar();
                this.updateDashboardStats();
            } else if (res.status === 409) {
                this.showConflictAlert(result.details || [], result.error);
            } else {
                NotificationService.showError(result.error || 'Error al guardar.');
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
