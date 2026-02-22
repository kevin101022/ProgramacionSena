document.addEventListener('DOMContentLoaded', () => {
    const loadingState = document.getElementById('loadingState');
    const fichaDetails = document.getElementById('fichaDetails');
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');

    const fichId = new URLSearchParams(window.location.search).get('id');

    // UI Elements
    const programaSelect = document.getElementById('programa_id');
    const instructorSelect = document.getElementById('instructor_id');
    const coordinacionSelect = document.getElementById('coordinacion_id');
    const modal = document.getElementById('fichaModal');
    const form = document.getElementById('fichaForm');

    let currentFicha = null;

    const init = async () => {
        if (!fichId) {
            showError('ID de ficha no proporcionado');
            return;
        }

        try {
            // Cargar datos para los selectores primero (para cuando se abra el modal)
            await loadOptions();

            // Cargar los datos de la ficha
            await loadFichaData();

        } catch (error) {
            console.error('Error:', error);
            showError('Error de conexión o datos inválidos');
        }
    };

    const loadOptions = async () => {
        const headers = { 'Accept': 'application/json' };

        const [progRes, instRes, coordRes] = await Promise.all([
            fetch('../../routing.php?controller=programa&action=index', { headers }),
            fetch('../../routing.php?controller=instructor&action=index', { headers }),
            fetch('../../routing.php?controller=coordinacion&action=index', { headers })
        ]);

        if (progRes.ok) {
            const programas = await progRes.json();
            programaSelect.innerHTML = '<option value="">Seleccione programa...</option>';
            programas.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.prog_codigo;
                opt.textContent = p.prog_nombre || p.prog_denominacion;
                programaSelect.appendChild(opt);
            });
        }

        if (instRes.ok) {
            const instructores = await instRes.json();
            instructorSelect.innerHTML = '<option value="">Seleccione instructor líder...</option>';
            instructores.forEach(i => {
                const opt = document.createElement('option');
                opt.value = i.inst_id;
                opt.textContent = `${i.inst_nombres} ${i.inst_apellidos}`;
                instructorSelect.appendChild(opt);
            });
        }

        if (coordRes.ok) {
            const coordinaciones = await coordRes.json();
            coordinacionSelect.innerHTML = '<option value="">Seleccione coordinación...</option>';
            coordinaciones.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.coord_id;
                opt.textContent = c.coord_descripcion;
                coordinacionSelect.appendChild(opt);
            });
        }
    };

    const loadFichaData = async () => {
        const response = await fetch(`../../routing.php?controller=ficha&action=index`, {
            headers: { 'Accept': 'application/json' }
        });
        const fichas = await response.json();
        const ficha = fichas.find(f => f.fich_id == fichId);

        if (!ficha) {
            throw new Error('Ficha no encontrada');
        }

        currentFicha = ficha;
        populateFichaInfo(ficha);
        await loadAsignaciones();
        showDetails();
    };

    const loadAsignaciones = async () => {
        const container = document.getElementById('asignacionesList');
        const noAsignaciones = document.getElementById('noAsignaciones');
        if (!container) return;

        try {
            const res = await fetch('../../routing.php?controller=asignacion&action=index', { headers: { 'Accept': 'application/json' } });
            const asignaciones = await res.json();
            const misAsig = Array.isArray(asignaciones) ? asignaciones.filter(a => a.ficha_fich_id == fichId) : [];

            if (misAsig.length === 0) {
                container.innerHTML = '';
                if (noAsignaciones) noAsignaciones.style.display = 'block';
                return;
            }

            if (noAsignaciones) noAsignaciones.style.display = 'none';
            container.innerHTML = '';

            misAsig.forEach(a => {
                const card = document.createElement('div');
                card.className = 'flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 hover:bg-gray-100/80 transition-colors cursor-pointer';
                card.onclick = () => window.location.href = `../asignacion/ver.php?id=${a.asig_id}`;

                const fechas = formatFecha(a.asig_fecha_ini) + ' — ' + formatFecha(a.asig_fecha_fin);
                card.innerHTML = `
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-sena-green/10 flex items-center justify-center text-sena-green">
                            <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-xl"></ion-icon>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">${a.inst_nombres} ${a.inst_apellidos} · ${a.comp_nombre_corto || 'Competencia'}</p>
                            <p class="text-xs text-gray-500">${a.amb_nombre || 'Ambiente'} · ${fechas}</p>
                        </div>
                    </div>
                    <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-gray-400"></ion-icon>
                `;
                container.appendChild(card);
            });
        } catch (err) {
            container.innerHTML = '<p class="text-sm text-gray-500">Error al cargar asignaciones</p>';
            if (noAsignaciones) noAsignaciones.style.display = 'none';
        }
    };

    const formatFecha = (str) => {
        if (!str) return '--';
        const d = new Date(str);
        return d.toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' });
    };

    const populateFichaInfo = (f) => {
        document.getElementById('detFichaId').textContent = f.fich_id;
        document.getElementById('detPrograma').textContent = f.titpro_nombre || 'N/A';
        document.getElementById('detJornada').textContent = `Jornada ${f.fich_jornada}`;
        document.getElementById('detInstructor').textContent = `${f.inst_nombres} ${f.inst_apellidos}`;
        document.getElementById('detCoordinacion').textContent = f.coord_descripcion || 'No asignada';

        const iniciales = `${f.inst_nombres[0]}${f.inst_apellidos[0]}`;
        document.getElementById('detInstInic').textContent = iniciales;

        // Botón de eliminar
        document.getElementById('deleteBtn').onclick = () => handleDelete(f.fich_id);

        // Botón de editar (abrir modal)
        document.getElementById('editBtn').onclick = () => openEditModal(f);
    };

    const openEditModal = (f) => {
        document.getElementById('modalTitle').textContent = 'Editar Ficha';
        document.getElementById('fich_id').value = f.fich_id;
        document.getElementById('fich_id').readOnly = true;
        document.getElementById('fich_id_old').value = f.fich_id;

        document.getElementById('programa_id').value = f.programa_prog_id || '';
        document.getElementById('instructor_id').value = f.instructor_inst_id || f.instructor_inst_id_lider || '';
        document.getElementById('coordinacion_id').value = f.coordinacion_coord_id || '';
        document.getElementById('fich_jornada').value = f.fich_jornada || '';

        // Populate dates
        if (f.fich_fecha_ini_lectiva) {
            document.getElementById('fich_fecha_ini_lectiva').value = f.fich_fecha_ini_lectiva.split('T')[0];
        }
        if (f.fich_fecha_fin_lectiva) {
            document.getElementById('fich_fecha_fin_lectiva').value = f.fich_fecha_fin_lectiva.split('T')[0];
        }

        modal.classList.add('show');
    };

    const handleDelete = async (id) => {
        NotificationService.showConfirm('¿Realmente desea eliminar esta ficha?', async () => {
            try {
                const response = await fetch(`../../routing.php?controller=ficha&action=destroy&id=${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (response.ok) {
                    NotificationService.showSuccess('Ficha eliminada');
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    const data = await response.json();
                    NotificationService.showError(data.error || 'No se pudo eliminar');
                }
            } catch (err) {
                NotificationService.showError('Error de red');
            }
        });
    };

    // Modal Control
    document.getElementById('closeModal').onclick = () => modal.classList.remove('show');
    document.getElementById('cancelBtn').onclick = () => modal.classList.remove('show');

    form.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        // Ensure action is set (although controller defaults to update if ID exists, good to be explicit or match index.js logic) but here we call action=update directly

        try {
            const response = await fetch(`../../routing.php?controller=ficha&action=update`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (response.ok) {
                NotificationService.showSuccess('Ficha actualizada correctamente');
                modal.classList.remove('show');
                await loadFichaData(); // Recargar datos en la vista
            } else {
                const result = await response.json();
                NotificationService.showError(result.error || 'Error al actualizar');
            }
        } catch (err) {
            NotificationService.showError('Error de conexión');
        }
    };

    const showDetails = () => {
        loadingState.style.display = 'none';
        fichaDetails.style.display = 'grid';
        errorState.style.display = 'none';
    };

    const showError = (msg) => {
        loadingState.style.display = 'none';
        fichaDetails.style.display = 'none';
        errorState.style.display = 'block';
        errorMessage.textContent = msg;
    };

    init();
});
