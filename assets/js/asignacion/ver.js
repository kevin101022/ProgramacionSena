document.addEventListener('DOMContentLoaded', () => {
    const loadingState = document.getElementById('loadingState');
    const detailsContainer = document.getElementById('asignacionDetails');
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');

    const asigId = new URLSearchParams(window.location.search).get('id');

    // UI Elements for Edit Modal (same as index.js)
    const modal = document.getElementById('asignacionModal');
    const form = document.getElementById('asignacionForm');

    const init = async () => {
        if (!asigId) {
            showError('ID de asignación no proporcionado');
            return;
        }

        try {
            // Cargar opciones para los selects (para el modal)
            await loadSelectOptions();

            // Cargar datos de la asignación
            await loadAsignacionData();

        } catch (error) {
            console.error('Error:', error);
            showError('Error al recuperar los datos del servidor');
        }
    };

    const loadSelectOptions = async () => {
        const headers = { 'Accept': 'application/json' };
        try {
            const [instRes, fichRes, ambRes, compRes] = await Promise.all([
                fetch('../../routing.php?controller=instructor&action=index', { headers }),
                fetch('../../routing.php?controller=ficha&action=index', { headers }),
                fetch('../../routing.php?controller=ambiente&action=index', { headers }),
                fetch('../../routing.php?controller=competencia&action=index', { headers })
            ]);

            const [instructores, fichas, ambientes, competencias] = await Promise.all([
                instRes.json(), fichRes.json(), ambRes.json(), compRes.json()
            ]);

            fillSelect('instructor_id', instructores, i => `${i.inst_nombres} ${i.inst_apellidos}`, 'inst_id');
            fillSelect('ficha_id', fichas, f => `Ficha: ${f.fich_id}`, 'fich_id');
            fillSelect('ambiente_id', ambientes, a => a.amb_nombre, 'amb_id');
            fillSelect('competencia_id', competencias, c => c.comp_nombre_corto || c.comp_nombre, 'comp_id');
        } catch (err) {
            console.warn('Error cargando opciones de selects', err);
        }
    };

    const fillSelect = (id, data, labelFn, valueKey) => {
        const select = document.getElementById(id);
        if (!select) return;
        select.innerHTML = '<option value="">Seleccione...</option>';
        if (Array.isArray(data)) {
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item[valueKey];
                opt.textContent = labelFn(item);
                select.appendChild(opt);
            });
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

        populateUI(asig);
        showDetails();
    };

    const populateUI = (asig) => {
        document.getElementById('detAsigId').textContent = String(asig.asig_id).padStart(3, '0');
        document.getElementById('detInstructor').textContent = `${asig.inst_nombres} ${asig.inst_apellidos}`;
        document.getElementById('detInstInic').textContent = `${asig.inst_nombres[0]}${asig.inst_apellidos[0]}`;
        document.getElementById('detFicha').textContent = `Ficha ${asig.fich_id || asig.ficha_fich_id}`;
        document.getElementById('detAmbiente').textContent = asig.amb_nombre || 'N/A';
        document.getElementById('detCompetencia').textContent = asig.comp_nombre_corto || asig.comp_nombre || 'N/A';
        document.getElementById('detFechaIni').textContent = formatDate(asig.asig_fecha_ini);
        document.getElementById('detFechaFin').textContent = formatDate(asig.asig_fecha_fin);

        // Links
        document.getElementById('instLink').href = `../instructor/ver.php?id=${asig.instructor_inst_id}`;
        document.getElementById('fichaLink').href = `../ficha/ver.php?id=${asig.ficha_fich_id || asig.fich_id}`;
        document.getElementById('manageHorariosBtn').href = `detalles.php?id=${asig.asig_id}`;

        // Buttons
        document.getElementById('deleteBtn').onclick = () => handleDelete(asig.asig_id);
        document.getElementById('editBtn').onclick = () => openEditModal(asig);

        // Load time slots (franjas)
        loadFranjas(asig.asig_id);
        checkConflicts(asig.asig_id);
    };

    const formatTime = (timeStr) => {
        if (!timeStr) return '--:--';
        // Handle HH:MM:SS or YYYY-MM-DD HH:MM:SS
        const timePart = timeStr.includes(' ') ? timeStr.split(' ')[1] : timeStr;
        const parts = timePart.split(':');
        if (parts.length >= 2) {
            return `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}`;
        }
        return timeStr;
    };

    const formatDate = (dateStr) => {
        if (!dateStr) return '--/--/--';
        // Take only the YYYY-MM-DD part
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
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100';
                    div.innerHTML = `
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-sena-green shadow-sm text-sm">
                            <ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon>
                        </div>
                        <span class="text-sm font-bold text-gray-700">${formatTime(f.detasig_hora_ini)} - ${formatTime(f.detasig_hora_fin)}</span>
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

    const checkConflicts = async (id) => {
        const container = document.getElementById('conflictContainer');
        if (!container) return;

        try {
            const response = await fetch(`../../routing.php?controller=asignacion&action=conflicts&id=${id}`);
            const conflicts = await response.json();

            if (conflicts.length > 0) {
                container.innerHTML = '';
                conflicts.forEach(c => {
                    const typeText = c.conflict_type.includes('instructor') && c.conflict_type.includes('ambiente')
                        ? 'Instructor y Ambiente Ocupados'
                        : (c.conflict_type.includes('instructor') ? 'Instructor Ocupado' : 'Ambiente Ocupado');

                    const typeIcon = c.conflict_type.includes('instructor') && c.conflict_type.includes('ambiente')
                        ? 'alert-circle'
                        : (c.conflict_type.includes('instructor') ? 'person' : 'business');

                    const div = document.createElement('div');
                    div.className = 'p-3 bg-red-50 border-l-4 border-red-500 rounded-r-lg mb-2';
                    div.innerHTML = `
                        <div class="flex items-center gap-2">
                            <ion-icon name="${typeIcon}" class="text-red-500"></ion-icon>
                            <span class="text-sm font-bold text-red-700">${typeText}</span>
                        </div>
                        <p class="text-xs text-red-600 mt-1">
                            Cruza con <strong>Ficha ${c.fich_id}</strong> (${c.inst_nombres} ${c.inst_apellidos}) 
                            en <strong>${c.amb_nombre}</strong>.
                        </p>
                    `;
                    container.appendChild(div);
                });
            } else {
                container.innerHTML = `
                    <div class="p-3 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
                        <div class="flex items-center gap-2">
                            <ion-icon name="checkmark-circle-outline" class="text-green-500"></ion-icon>
                            <span class="text-sm font-bold text-green-700">Sin cruces detectados</span>
                        </div>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error al verificar conflictos:', error);
        }
    };

    const openEditModal = (asig) => {
        document.getElementById('modalTitle').textContent = 'Editar Asignación';
        document.getElementById('asig_id').value = asig.asig_id;
        // Fix for hidden input for ficha_id
        const modalFichaId = document.getElementById('modal_ficha_id');
        if (modalFichaId) modalFichaId.value = asig.ficha_fich_id || asig.fich_id || '';

        // Ficha display for readonly input in modal
        const fichaDisplay = document.getElementById('fichaDisplay');
        if (fichaDisplay) fichaDisplay.value = `Ficha ${asig.fich_id || asig.ficha_fich_id}`;

        document.getElementById('instructor_id').value = asig.instructor_inst_id || '';
        document.getElementById('ambiente_id').value = asig.ambiente_amb_id || '';
        document.getElementById('competencia_id').value = asig.competencia_comp_id || '';
        document.getElementById('asig_fecha_ini').value = asig.asig_fecha_ini || '';
        document.getElementById('asig_fecha_fin').value = asig.asig_fecha_fin || '';

        modal.classList.add('show');
    };

    const handleDelete = async (id) => {
        NotificationService.showConfirm('¿Está seguro de eliminar esta asignación académica?', async () => {
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

    // Modal Events
    document.getElementById('closeModal').onclick = () => modal.classList.remove('show');
    document.getElementById('cancelBtn').onclick = () => modal.classList.remove('show');

    form.onsubmit = async (e) => {
        e.preventDefault();
        const data = {
            asig_id: document.getElementById('asig_id').value,
            instructor_inst_id: document.getElementById('instructor_id').value,
            ficha_fich_id: document.getElementById('ficha_id').value,
            ambiente_amb_id: document.getElementById('ambiente_id').value,
            competencia_comp_id: document.getElementById('competencia_id').value,
            asig_fecha_ini: document.getElementById('asig_fecha_ini').value,
            asig_fecha_fin: document.getElementById('asig_fecha_fin').value
        };

        try {
            const response = await fetch(`../../routing.php?controller=asignacion&action=update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                NotificationService.showSuccess('Asignación actualizada');
                modal.classList.remove('show');
                await loadAsignacionData();
            } else {
                const res = await response.json();
                NotificationService.showError(res.error || 'Error al actualizar');
            }
        } catch (err) {
            NotificationService.showError('Error de servidor');
        }
    };

    const showDetails = () => {
        loadingState.style.display = 'none';
        detailsContainer.style.display = 'grid';
        errorState.style.display = 'none';
    };

    const showError = (msg) => {
        loadingState.style.display = 'none';
        detailsContainer.style.display = 'none';
        errorState.style.display = 'block';
        errorMessage.textContent = msg;
    };

    init();
});
