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

document.addEventListener('DOMContentLoaded', () => {
    const loadingState = document.getElementById('loadingState');
    const detailsContainer = document.getElementById('asignacionDetails');
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');

    const asigId = new URLSearchParams(window.location.search).get('id');

    // UI Elements for Edit Modal
    const modal = document.getElementById('asignacionModal');
    const form = document.getElementById('asignacionForm');

    let currentAsig = null;
    let allAmbientes = [];
    let allSedes = [];
    let allCompetencias = [];
    let allHabilitaciones = [];

    const init = async () => {
        if (!asigId) {
            showError('ID de asignación no proporcionado');
            return;
        }

        try {
            await Promise.all([
                loadAsignacionData(),
                loadSedes()
            ]);

            // Auto-trigger edit modal if edit=true parameter is present
            if (new URLSearchParams(window.location.search).get('edit') === 'true') {
                setTimeout(async () => {
                    await loadEditDependencies();
                    openEditModal(currentAsig);
                }, 400);
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Error al recuperar los datos del servidor');
        }
    };

    const loadSedes = async () => {
        try {
            const res = await fetch('../../routing.php?controller=sede&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            allSedes = await res.json();
        } catch (e) {
            console.error('Error cargando sedes:', e);
        }
    };

    const loadEditDependencies = async () => {
        const headers = { 'Accept': 'application/json' };
        try {
            const [ambRes, habRes] = await Promise.all([
                fetch('../../routing.php?controller=ambiente&action=index', { headers }),
                fetch('../../routing.php?controller=instru_competencia&action=index', { headers })
            ]);
            allAmbientes = await ambRes.json();
            allHabilitaciones = await habRes.json();

            // Load competencias for the program of this ficha
            if (currentAsig) {
                const fichaRes = await fetch(`../../routing.php?controller=ficha&action=index`, { headers });
                const fichas = await fichaRes.json();
                const ficha = fichas.find(f => f.fich_id == (currentAsig.ficha_fich_id || currentAsig.fich_id));
                if (ficha) {
                    const progId = ficha.programa_prog_codigo || ficha.programa_prog_id;
                    const compRes = await fetch(`../../routing.php?controller=competencia&action=getByPrograma&prog_id=${progId}`, { headers });
                    allCompetencias = await compRes.json();
                }
            }
        } catch (err) {
            console.warn('Error cargando dependencias de edición', err);
        }
    };

    const loadAsignacionData = async () => {
        const response = await fetch('../../routing.php?controller=asignacion&action=index', {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        const asig = Array.isArray(data) ? data.find(a => a.asig_id == asigId) : null;

        if (!asig) {
            throw new Error('Asignación no encontrada');
        }

        currentAsig = asig;
        populateUI(asig);
        showDetails();
    };

    const populateUI = (asig) => {
        document.getElementById('detAsigId').textContent = String(asig.asig_id).padStart(3, '0');
        document.getElementById('detInstructor').textContent = `${asig.inst_nombres} ${asig.inst_apellidos}`;
        document.getElementById('detInstInic').textContent = `${asig.inst_nombres[0]}${asig.inst_apellidos[0]}`;
        document.getElementById('detFicha').textContent = `Ficha ${asig.fich_id || asig.ficha_fich_id}`;
        document.getElementById('detAmbiente').textContent = `Ambiente: ${asig.ambiente_amb_id || 'ID'} - ${asig.amb_nombre || 'N/A'}`;
        document.getElementById('detCompetencia').textContent = asig.comp_nombre_corto || asig.comp_nombre || 'N/A';
        document.getElementById('detFechaIni').textContent = formatDate(asig.asig_fecha_ini);
        document.getElementById('detFechaFin').textContent = formatDate(asig.asig_fecha_fin);
        const detSede = document.getElementById('detSede');
        if (detSede) detSede.textContent = `Sede: ${asig.sede_nombre || 'Sin Sede'}`;

        // Links
        const instLink = document.getElementById('instLink');
        const fichaLink = document.getElementById('fichaLink');
        const manageHorariosBtn = document.getElementById('manageHorariosBtn');

        if (instLink) instLink.href = `../instructor/ver.php?id=${asig.instructor_inst_id}`;
        if (fichaLink) fichaLink.href = `../ficha/ver.php?id=${asig.ficha_fich_id || asig.fich_id}`;
        if (manageHorariosBtn) manageHorariosBtn.href = `detalles.php?id=${asig.asig_id}`;

        // Buttons
        const deleteBtn = document.getElementById('deleteBtn');
        const editBtn = document.getElementById('editBtn');

        if (deleteBtn) deleteBtn.onclick = () => handleDelete(asig.asig_id);
        if (editBtn) {
            editBtn.onclick = async () => {
                await loadEditDependencies();
                openEditModal(asig);
            };
        }

        // Load time slots (franjas)
        loadFranjas(asig.asig_id);
    };

    const formatTime = (timeStr) => {
        if (!timeStr) return '--:--';
        const timePart = timeStr.includes(' ') ? timeStr.split(' ')[1] : timeStr;
        const parts = timePart.split(':');
        if (parts.length >= 2) {
            return `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}`;
        }
        return timeStr;
    };

    const formatDate = (dateStr) => {
        if (!dateStr) return '--/--/--';
        return dateStr.split(' ')[0];
    };

    const loadFranjas = async (id) => {
        const container = document.getElementById('franjasContainer');
        if (!container) return;

        try {
            const res = await fetch(`../../routing.php?controller=detalle_asignacion&action=index&asig_id=${id}`, {
                headers: { 'Accept': 'application/json' }
            });
            const franjas = await res.json();

            if (Array.isArray(franjas) && franjas.length > 0) {
                container.innerHTML = '';
                franjas.forEach(f => {
                    const dateFormatted = new Date(f.detasig_fecha + 'T00:00:00').toLocaleDateString('es-CO', {
                        weekday: 'short', day: 'numeric', month: 'short', year: 'numeric'
                    });
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100';
                    div.innerHTML = `
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-sena-green shadow-sm text-sm">
                            <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                        </div>
                        <div class="flex-1">
                            <span class="text-sm font-bold text-gray-700 capitalize">${dateFormatted}</span>
                            <span class="text-sm text-gray-500 ml-2">${formatTime(f.detasig_hora_ini)} - ${formatTime(f.detasig_hora_fin)}</span>
                        </div>
                    `;
                    container.appendChild(div);
                });
            } else {
                container.innerHTML = '<p class="text-xs text-gray-500 italic">No hay horarios específicos registrados.</p>';
            }
        } catch (e) {
            console.error('Error loading franjas:', e);
            container.innerHTML = 'Error al cargar horarios';
        }
    };

    const generateDaysInputs = (startStr, endStr, existingAsig = null) => {
        const container = document.getElementById('diasListContainer');
        if (!container) return;
        container.innerHTML = '';

        let start = new Date(startStr + 'T00:00:00');
        let end = new Date(endStr + 'T00:00:00');

        const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        if (diffDays > 90 || diffDays < 1) {
            container.innerHTML = '<p class="text-sm text-center text-gray-400 italic py-4">Rango no válido</p>';
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

        const current = new Date(start);
        const formatOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const today = new Date().toISOString().split('T')[0];
        const defaultIni = document.getElementById('default_hora_ini')?.value || '08:00';
        const defaultFin = document.getElementById('default_hora_fin')?.value || '12:00';

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
            row.className = `flex items-center gap-4 p-3 bg-white border rounded-lg shadow-sm transition-all ${
                isPast ? 'border-red-200 bg-red-50/10 opacity-60' : 
                isHoliday ? 'border-amber-200 bg-amber-50/10 opacity-75' : 'border-gray-100'
            }`;

            row.innerHTML = `
                <div class="flex items-center gap-3 w-2/5">
                    <input type="checkbox" id="chk_${dateISO}" class="day-checkbox w-4 h-4 accent-[#39a900]" ${isDisabled ? 'disabled' : ''}>
                    <label for="chk_${dateISO}" class="text-sm font-medium text-gray-700 capitalize cursor-pointer leading-tight">
                        ${dateLabel}
                        ${isHoliday ? '<span class="text-[10px] text-amber-500 font-bold ml-2 whitespace-nowrap">(Festivo)</span>' : ''}
                    </label>
                </div>
                <div class="flex-1 flex items-center gap-3">
                    <input type="time" id="ini_${dateISO}" class="day-time-ini w-full px-3 py-1.5 text-sm border border-gray-200 rounded-md" value="${defaultIni}" disabled>
                    <span class="text-gray-400 font-bold">—</span>
                    <input type="time" id="fin_${dateISO}" class="day-time-fin w-full px-3 py-1.5 text-sm border border-gray-200 rounded-md" value="${defaultFin}" disabled>
                </div>
            `;

            container.appendChild(row);

            const chk = row.querySelector(`#chk_${dateISO}`);
            const ini = row.querySelector(`#ini_${dateISO}`);
            const fin = row.querySelector(`#fin_${dateISO}`);

            if (chk && !isDisabled) {
                chk.addEventListener('change', (e) => {
                    ini.disabled = !e.target.checked;
                    fin.disabled = !e.target.checked;
                });
            }

            current.setDate(current.getDate() + 1);
        }

        if (existingAsig) {
            loadExistingDetails(existingAsig.asig_id);
        }
    };

    const loadExistingDetails = async (asigId) => {
        try {
            const res = await fetch(`../../routing.php?controller=detalle_asignacion&action=index&asig_id=${asigId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const franjas = await res.json();

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
                        if (ini) { ini.disabled = false; ini.value = f.detasig_hora_ini.substring(0, 5); }
                        if (fin) { fin.disabled = false; fin.value = f.detasig_hora_fin.substring(0, 5); }
                    }
                });
            }
        } catch (e) {
            console.error('Error loading existing details', e);
        }
    };

    const handleSedeChange = (targetAmbienteId = null) => {
        const sedeId = document.getElementById('sede_id')?.value;
        const ambSelect = document.getElementById('ambiente_id');
        if (!ambSelect) return;

        ambSelect.innerHTML = '<option value="">Seleccione ambiente...</option>';
        if (!sedeId) {
            ambSelect.innerHTML = '<option value="">Primero seleccione sede...</option>';
            return;
        }

        const filtered = allAmbientes.filter(a => a.sede_sede_id == sedeId);
        if (filtered.length === 0) {
            ambSelect.innerHTML = '<option value="">No hay ambientes en esta sede</option>';
        } else {
            filtered.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.amb_id;
                opt.textContent = `${a.amb_id} - ${a.amb_nombre || 'Sin nombre'} (${a.tipo_ambiente || 'Convencional'})`;
                ambSelect.appendChild(opt);
            });
            if (targetAmbienteId) {
                ambSelect.value = targetAmbienteId;
            }
        }
    };

    const openEditModal = (asig) => {
        if (!form) return;
        form.reset();

        const modalTitle = document.getElementById('modalTitle');
        const asigIdInput = document.getElementById('asig_id');
        const modalFichaId = document.getElementById('modal_ficha_id');
        const fichaDisplay = document.getElementById('fichaDisplay');
        const ambSelect = document.getElementById('ambiente_id');
        const compSelect = document.getElementById('competencia_id');
        const instSelect = document.getElementById('instructor_id');
        const startInput = document.getElementById('asig_fecha_ini');
        const endInput = document.getElementById('asig_fecha_fin');

        if (modalTitle) modalTitle.textContent = 'Editar Asignación';
        if (asigIdInput) asigIdInput.value = asig.asig_id;
        if (modalFichaId) modalFichaId.value = asig.ficha_fich_id || asig.fich_id || '';
        if (fichaDisplay) fichaDisplay.value = `Ficha ${asig.fich_id || asig.ficha_fich_id}`;

        // Populate sedes
        const sedeSelect = document.getElementById('sede_id');
        if (sedeSelect) {
            sedeSelect.innerHTML = '<option value="">Seleccione sede...</option>';
            allSedes.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.sede_id;
                opt.textContent = s.sede_nombre;
                sedeSelect.appendChild(opt);
            });

            // Bind change event if not already bound (ver.js uses a different pattern)
            sedeSelect.onchange = () => handleSedeChange();
        }

        // Pre-seleccionar Sede si existe ambiente
        if (sedeSelect && asig.ambiente_amb_id) {
            const amb = allAmbientes.find(a => a.amb_id == asig.ambiente_amb_id);
            if (amb) {
                sedeSelect.value = amb.sede_sede_id;
                handleSedeChange(asig.ambiente_amb_id);
            }
        } else if (ambSelect) {
            ambSelect.innerHTML = '<option value="">Primero seleccione sede...</option>';
        }

        // Populate competencias
        if (compSelect) {
            compSelect.innerHTML = '<option value="">Seleccione competencia...</option>';
            allCompetencias.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.comp_id;
                opt.textContent = c.comp_nombre_corto || c.comp_nombre_unidad_competencia;
                compSelect.appendChild(opt);
            });
            compSelect.value = asig.competencia_comp_id || '';

            // Load instructors for current competencia
            if (asig.competencia_comp_id && instSelect) {
                const fichaObj = { programa_prog_codigo: asig.programa_prog_id };
                loadInstructorsForComp(asig.competencia_comp_id, asig.ficha_fich_id || asig.fich_id);
                setTimeout(() => {
                    if (instSelect.tomselect) {
                        instSelect.tomselect.setValue(asig.instructor_inst_id || '');
                    } else {
                        instSelect.value = asig.instructor_inst_id || '';
                    }
                }, 150);
            }

            compSelect.addEventListener('change', () => {
                loadInstructorsForComp(compSelect.value, asig.ficha_fich_id || asig.fich_id);
            });
        }

        if (startInput) startInput.value = asig.asig_fecha_ini || '';
        if (endInput) endInput.value = asig.asig_fecha_fin || '';

        // Generate days
        if (asig.asig_fecha_ini && asig.asig_fecha_fin) {
            generateDaysInputs(asig.asig_fecha_ini, asig.asig_fecha_fin, asig);
        }

        // Date change listeners  
        if (startInput) startInput.addEventListener('change', onDateChange);
        if (endInput) endInput.addEventListener('change', onDateChange);

        modal.classList.add('show');
    };

    const onDateChange = () => {
        const s = document.getElementById('asig_fecha_ini')?.value;
        const e = document.getElementById('asig_fecha_fin')?.value;
        if (s && e && s <= e) {
            generateDaysInputs(s, e, null);
        }
    };

    const loadInstructorsForComp = async (compId, fichId) => {
        const instSelect = document.getElementById('instructor_id');
        if (!instSelect || !compId) return;

        // Get programa from ficha
        try {
            const fichaRes = await fetch('../../routing.php?controller=ficha&action=index', { headers: { 'Accept': 'application/json' } });
            const fichas = await fichaRes.json();
            const ficha = fichas.find(f => f.fich_id == fichId);
            const progId = ficha?.programa_prog_codigo || ficha?.programa_prog_id;

            const habilitados = allHabilitaciones.filter(h =>
                h.competencia_comp_id == compId &&
                (h.programa_prog_id == progId || h.programa_prog_id === null || h.programa_prog_id === '')
            );

            instSelect.innerHTML = '<option value="">Seleccione instructor...</option>';
            if (habilitados.length === 0) {
                instSelect.innerHTML = '<option value="">Sin instructores habilitados</option>';
                instSelect.disabled = true;
            } else {
                const seen = new Set();
                habilitados.forEach(h => {
                    if (!seen.has(h.instructor_inst_id)) {
                        seen.add(h.instructor_inst_id);
                        const opt = document.createElement('option');
                        opt.value = h.instructor_inst_id;
                        opt.textContent = `${h.inst_nombres} ${h.inst_apellidos}`;
                        instSelect.appendChild(opt);
                    }
                });
                instSelect.disabled = false;
            }
        } catch (e) {
            console.warn('Error loading instructors', e);
        }
    };

    const handleDelete = async (id) => {
        NotificationService.showConfirm('¿Está seguro de eliminar esta asignación académica? Se eliminarán también todos los horarios asociados.', async () => {
            try {
                const response = await fetch(`../../routing.php?controller=asignacion&action=destroy&id=${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (response.ok) {
                    NotificationService.showSuccess('Asignación eliminada');
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    const res = await response.json();
                    NotificationService.showError(res.error || 'Error al eliminar');
                }
            } catch (err) {
                NotificationService.showError('Error de red');
            }
        });
    };

    const handleApplyDefaultHours = () => {
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
            NotificationService.showError('Seleccione un rango de fechas válido primero.');
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
    };

    // Modal Events
    const closeModalBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const applyDefaultHoursBtn = document.getElementById('applyDefaultHours');

    if (closeModalBtn) closeModalBtn.onclick = () => modal.classList.remove('show');
    if (cancelBtn) cancelBtn.onclick = () => modal.classList.remove('show');
    if (applyDefaultHoursBtn) {
        applyDefaultHoursBtn.onclick = () => handleApplyDefaultHours();
    }

    if (form) {
        form.onsubmit = async (e) => {
            e.preventDefault();
            const data = {
                asig_id: document.getElementById('asig_id').value,
                instructor_inst_id: document.getElementById('instructor_id').value,
                ficha_fich_id: document.getElementById('modal_ficha_id').value,
                ambiente_amb_id: document.getElementById('ambiente_id').value,
                competencia_comp_id: document.getElementById('competencia_id').value,
                asig_fecha_ini: document.getElementById('asig_fecha_ini').value,
                asig_fecha_fin: document.getElementById('asig_fecha_fin').value,
                dias_seleccionados: []
            };

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
            const today = new Date().toISOString().split('T')[0];
            for (const dia of data.dias_seleccionados) {
                if (dia.hora_ini >= dia.hora_fin) {
                    NotificationService.showError(`Hora de inicio debe ser menor a hora de fin en ${dia.fecha}`);
                    return;
                }
                if (dia.hora_ini < '06:00' || dia.hora_fin > '22:00') {
                    NotificationService.showError(`Horario fuera de jornada (06:00-22:00) en ${dia.fecha}`);
                    return;
                }
                if (dia.fecha < today) {
                    NotificationService.showError(`No se puede programar en fecha pasada: ${dia.fecha}`);
                    return;
                }
            }

            await sendSaveRequest(data);
        };
    }

    const sendSaveRequest = async (data) => {
        const saveBtn = document.getElementById('saveBtn');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Guardando...';
        }

        try {
            while (true) {
                const response = await fetch(`../../routing.php?controller=asignacion&action=update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (response.status === 202 && result.warning) {
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
                } else if (response.ok) {
                    NotificationService.showSuccess('Asignación actualizada');
                    modal.classList.remove('show');
                    await loadAsignacionData();
                    break;
                } else {
                    if (response.status === 409) {
                        NotificationService.showError('Cruce de horario detectado. Revise los días y horarios.');
                    } else {
                        NotificationService.showError(result.error || 'Error al actualizar');
                    }
                    break;
                }
            }
        } catch (err) {
            NotificationService.showError('Error de servidor');
        } finally {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Guardar';
            }
        }
    };

    const showDetails = () => {
        if (loadingState) loadingState.style.display = 'none';
        if (detailsContainer) detailsContainer.style.display = 'grid';
        if (errorState) errorState.style.display = 'none';
    };

    const showError = (msg) => {
        if (loadingState) loadingState.style.display = 'none';
        if (detailsContainer) detailsContainer.style.display = 'none';
        if (errorState) errorState.style.display = 'block';
        if (errorMessage) errorMessage.textContent = msg;
    };

    init();
});
