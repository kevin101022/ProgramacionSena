document.addEventListener('DOMContentLoaded', () => {
    // ── DOM Elements ──────────────────────────────────────────
    const fichaSelector = document.getElementById('fichaSelector');
    const fichaSearch = document.getElementById('fichaSearch');
    const fichaDropdown = document.getElementById('fichaDropdown');
    const addBtn = document.getElementById('addBtn');
    const calendarEl = document.getElementById('calendar');
    const placeholder = document.getElementById('calendarPlaceholder');
    const totalLabel = document.getElementById('totalAsignaciones');
    const modal = document.getElementById('asignacionModal');
    const form = document.getElementById('asignacionForm');
    const closeBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const competenciaSelect = document.getElementById('competencia_id');
    const instructorSelect = document.getElementById('instructor_id');
    const ambienteSelect = document.getElementById('ambiente_id');

    let calendar = null;






    let fichas = [];
    let selectedFicha = null;
    let allAsignaciones = [];
    let allCompetenciasPrograma = []; // competencias del programa de la ficha
    let allHabilitaciones = []; // instru_competencia

    // ── Color palette for events ──────────────────────────────
    const COLORS = [
        '#39a900', '#3b82f6', '#8b5cf6', '#ef4444',
        '#f59e0b', '#06b6d4', '#ec4899', '#14b8a6'
    ];
    const getColor = (index) => COLORS[index % COLORS.length];

    // ── Render custom searchable dropdown ─────────────────────
    const renderFichas = (filter = '') => {
        if (!fichaDropdown) return;

        fichaDropdown.innerHTML = '';
        const searchTerm = filter.toLowerCase().trim();

        const filtered = fichas.filter(f => {
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
                    <div class="prog-name">${f.prog_denominacion || f.titpro_nombre || 'Sin nombre de programa'}</div>
                `;
                item.onclick = (e) => {
                    e.stopPropagation();
                    selectFicha(f);
                };
                fichaDropdown.appendChild(item);
            });
        }

        fichaDropdown.style.display = 'block';
    };

    const selectFicha = async (f) => {
        if (!f) return;

        // Update UI
        fichaSearch.value = `Ficha ${f.fich_id} — ${f.prog_denominacion || f.titpro_nombre || ''}`;
        fichaDropdown.style.display = 'none';

        // Update hidden select for compatibility
        if (fichaSelector) {
            fichaSelector.innerHTML = `<option value="${f.fich_id}" selected>Ficha ${f.fich_id}</option>`;
            // Trigger change event manually
            const event = new Event('change');
            fichaSelector.dispatchEvent(event);
        }
    };

    // ── Load fichas ───────────────────────────────────────────
    const loadFichas = async () => {
        try {
            const res = await fetch('../../routing.php?controller=ficha&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            fichas = await res.json();
        } catch (e) { console.error('Error cargando fichas:', e); }
    };

    // ── Ficha search logic ────────────────────────────────────
    if (fichaSearch) {
        fichaSearch.addEventListener('focus', () => {
            renderFichas(fichaSearch.value);
        });

        fichaSearch.addEventListener('input', (e) => {
            renderFichas(e.target.value);
        });

        fichaSearch.addEventListener('click', (e) => {
            e.stopPropagation();
            renderFichas(e.target.value);
        });
    }

    // Close dropdown on click outside
    document.addEventListener('click', (e) => {
        if (fichaDropdown && !fichaSearch.contains(e.target) && !fichaDropdown.contains(e.target)) {
            fichaDropdown.style.display = 'none';
        }
    });

    // ── Load ambientes ────────────────────────────────────────
    const loadAmbientes = async () => {
        try {
            const res = await fetch('../../routing.php?controller=ambiente&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const ambientes = await res.json();
            if (ambienteSelect) {
                ambienteSelect.innerHTML = '<option value="">Seleccione ambiente...</option>';
                ambientes.forEach(a => {
                    const opt = document.createElement('option');
                    opt.value = a.amb_id;
                    opt.textContent = `${a.amb_id} - ${a.amb_nombre || 'Sin nombre'}`;
                    ambienteSelect.appendChild(opt);
                });
            }
        } catch (e) { console.error('Error cargando ambientes:', e); }
    };

    // ── When ficha is selected ────────────────────────────────
    fichaSelector.addEventListener('change', async () => {
        const fichId = fichaSelector.value;
        if (!fichId) {
            selectedFicha = null;
            if (addBtn) addBtn.disabled = true;
            if (calendarEl) calendarEl.style.display = 'none';
            if (placeholder) placeholder.style.display = '';
            return;
        }

        selectedFicha = fichas.find(f => f.fich_id == fichId);
        if (addBtn) addBtn.disabled = false;
        if (placeholder) placeholder.style.display = 'none';
        if (calendarEl) calendarEl.style.display = '';

        // Load assignments for this ficha
        await loadAsignacionesFicha(fichId);

        // Load competencias del programa de la ficha
        const progId = selectedFicha.programa_prog_codigo || selectedFicha.programa_prog_id;
        if (progId) {
            await loadCompetenciasPrograma(progId);
        }

        // Load habilitaciones for filtering instructors
        await loadHabilitaciones();

        initCalendar();
    });

    // ── Load assignments for a specific ficha ─────────────────
    const loadAsignacionesFicha = async (fichId) => {
        try {
            const res = await fetch('../../routing.php?controller=asignacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            // Filter by ficha
            allAsignaciones = (Array.isArray(data) ? data : []).filter(
                a => a.ficha_fich_id == fichId || a.fich_id == fichId
            );
            if (totalLabel) totalLabel.textContent = allAsignaciones.length;
        } catch (e) {
            console.error('Error cargando asignaciones:', e);
            allAsignaciones = [];
        }
    };

    // ── Load competencias del programa ────────────────────────
    const loadCompetenciasPrograma = async (progId) => {
        try {
            const res = await fetch(`../../routing.php?controller=competencia_programa&action=getByPrograma&prog_id=${progId}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Error fetching competencies');
            const data = await res.json();
            allCompetenciasPrograma = Array.isArray(data) ? data : [];
        } catch (e) {
            console.error('Error cargando competencias:', e);
            allCompetenciasPrograma = [];
        }
    };

    // ── Load habilitaciones ───────────────────────────────────
    const loadHabilitaciones = async () => {
        try {
            const res = await fetch('../../routing.php?controller=instru_competencia&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Error fetching habilitaciones');
            const data = await res.json();
            allHabilitaciones = Array.isArray(data) ? data : [];
        } catch (e) {
            console.error('Error cargando habilitaciones:', e);
            allHabilitaciones = [];
        }
    };

    // ── Initialize FullCalendar ───────────────────────────────
    const initCalendar = () => {
        if (calendar) calendar.destroy();

        const events = allAsignaciones.map((a, i) => ({
            id: a.asig_id,
            title: `${a.comp_nombre_corto || 'Comp.'} — ${a.inst_nombres || ''} ${a.inst_apellidos || ''}`,
            start: a.asig_fecha_ini,
            end: a.asig_fecha_fin,
            backgroundColor: getColor(i),
            extendedProps: a
        }));

        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: events,
            selectable: true,
            select: (info) => {
                // When clicking a date range, prefill the dates in the modal
                openModal(null, info.startStr, info.endStr);
            },
            eventClick: (info) => {
                // Navigate to detail view
                window.location.href = `ver.php?id=${info.event.id}`;
            },
            height: 'auto',
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                list: 'Lista'
            }
        });

        calendar.render();
    };

    // ── Open modal with smart filtering ───────────────────────
    const openModal = (asig = null, startDate = null, endDate = null) => {
        if (!form || !selectedFicha) return;
        form.reset();

        const modalTitle = document.getElementById('modalTitle');
        const asigIdInput = document.getElementById('asig_id');
        const fichaInput = document.getElementById('modal_ficha_id');
        const fichaDisplay = document.getElementById('fichaDisplay');
        const fechaIni = document.getElementById('asig_fecha_ini');
        const fechaFin = document.getElementById('asig_fecha_fin');

        // Pre-fill ficha
        if (fichaInput) fichaInput.value = selectedFicha.fich_id;
        if (fichaDisplay) fichaDisplay.value = `Ficha ${selectedFicha.fich_id} — ${selectedFicha.prog_denominacion || selectedFicha.titpro_nombre || ''}`;

        // Pre-fill dates from calendar selection
        if (startDate && fechaIni) fechaIni.value = startDate;
        if (endDate && fechaFin) fechaFin.value = endDate;

        // Filter competencias: only those NOT already assigned to this ficha
        const assignedCompIds = allAsignaciones.map(a => a.competencia_comp_id);
        const availableComps = allCompetenciasPrograma.filter(c => !assignedCompIds.includes(c.comp_id));

        if (competenciaSelect) {
            competenciaSelect.innerHTML = '<option value="">Seleccione competencia...</option>';
            if (availableComps.length === 0) {
                competenciaSelect.innerHTML = '<option value="">Todas las competencias ya están asignadas</option>';
            } else {
                availableComps.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.comp_id;
                    opt.textContent = c.comp_nombre_corto || c.comp_nombre_unidad_competencia;
                    competenciaSelect.appendChild(opt);
                });
            }
        }

        // Reset instructor select
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
    };

    // ── Cascade: Competencia → Instructores habilitados ───────
    if (competenciaSelect) {
        competenciaSelect.addEventListener('change', () => {
            const compId = competenciaSelect.value;
            if (!instructorSelect) return;

            if (!compId) {
                instructorSelect.innerHTML = '<option value="">Primero seleccione competencia...</option>';
                instructorSelect.disabled = true;
                return;
            }

            const progId = selectedFicha.programa_prog_codigo || selectedFicha.programa_prog_id;

            // Filter habilitaciones: instructors who are habilitated for this competencia+programa
            const habilitados = allHabilitaciones.filter(h =>
                h.competxprograma_competencia_comp_id == compId &&
                h.competxprograma_programa_prog_id == progId
            );

            instructorSelect.innerHTML = '<option value="">Seleccione instructor...</option>';
            if (habilitados.length === 0) {
                instructorSelect.innerHTML = '<option value="">No hay instructores habilitados para esta competencia</option>';
                instructorSelect.disabled = true;
            } else {
                // Remove duplicates (same instructor could have multiple habilitaciones)
                const seen = new Set();
                habilitados.forEach(h => {
                    if (!seen.has(h.instructor_inst_id)) {
                        seen.add(h.instructor_inst_id);
                        const opt = document.createElement('option');
                        opt.value = h.instructor_inst_id;
                        opt.textContent = `${h.inst_nombres || ''} ${h.inst_apellidos || ''}`;
                        instructorSelect.appendChild(opt);
                    }
                });
                instructorSelect.disabled = false;
            }
        });
    }

    // ── Modal controls ────────────────────────────────────────
    const closeModal = () => { if (modal) modal.classList.remove('show'); };
    if (addBtn) addBtn.onclick = () => openModal();
    if (closeBtn) closeBtn.onclick = closeModal;
    if (cancelBtn) cancelBtn.onclick = closeModal;

    // ── Form submit ───────────────────────────────────────────
    if (form) {
        form.onsubmit = async (e) => {
            e.preventDefault();
            const id = document.getElementById('asig_id').value;
            const action = id ? 'update' : 'store';

            const data = {
                ficha_fich_id: document.getElementById('modal_ficha_id').value,
                competencia_comp_id: competenciaSelect.value,
                instructor_inst_id: instructorSelect.value,
                ambiente_amb_id: ambienteSelect.value,
                asig_fecha_ini: document.getElementById('asig_fecha_ini').value,
                asig_fecha_fin: document.getElementById('asig_fecha_fin').value
            };
            if (id) data.asig_id = id;

            // Clear previous alerts
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
                    NotificationService.showSuccess(id ? '¡Asignación actualizada correctamente!' : '¡Asignación registrada correctamente!');
                    closeModal();
                    // Reload ficha data
                    await loadAsignacionesFicha(selectedFicha.fich_id);
                    initCalendar();
                } else if (res.status === 409) {
                    const conflicts = result.details || [];
                    const types = new Set();
                    conflicts.forEach(c => c.conflict_type.forEach(t => types.add(t === 'instructor' ? 'el Instructor' : 'el Ambiente')));
                    const typeMsg = Array.from(types).join(' y ');
                    const fichas = conflicts.map(c => `Ficha ${c.fich_id}`).join(', ');

                    if (conflictAlert) {
                        conflictAlert.classList.remove('hidden');
                        conflictAlert.innerHTML = `
                            <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                                <div class="flex items-center gap-2 mb-1">
                                    <ion-icon name="warning" class="text-red-500"></ion-icon>
                                    <span class="text-sm font-bold text-red-700">Cruce Detectado</span>
                                </div>
                                <p class="text-xs text-red-600">
                                    ${typeMsg} ya están asignados a <strong>${fichas}</strong> en esas fechas.
                                </p>
                            </div>
                        `;
                    }
                } else if (res.status === 400) {
                    if (conflictAlert) {
                        conflictAlert.classList.remove('hidden');
                        conflictAlert.innerHTML = `
                            <div class="p-3 bg-amber-50 border-l-4 border-amber-500 rounded-r-lg">
                                <div class="flex items-center gap-2 mb-1">
                                    <ion-icon name="alert-circle" class="text-amber-500"></ion-icon>
                                    <span class="text-sm font-bold text-amber-700">Campo Requerido</span>
                                </div>
                                <p class="text-xs text-amber-600">${result.error}</p>
                            </div>
                        `;
                    }
                } else {
                    NotificationService.showError('No se pudo guardar la asignación. Revisa los campos e intenta de nuevo.');
                }
            } catch (error) {
                console.error('Error al guardar:', error);
                NotificationService.showError('No pudimos conectar con el servidor. Intenta de nuevo.');
            }
        };
    }

    // ── Init ──────────────────────────────────────────────────
    loadFichas();
    loadAmbientes();
});
