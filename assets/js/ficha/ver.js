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
            // Solo cargar opciones si NO es instructor
            if (!window.isInstructor) {
                await loadOptions();
            }

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
        const response = await fetch(`../../routing.php?controller=ficha&action=show&id=${fichId}`, {
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) {
            throw new Error('Ficha no encontrada');
        }

        const ficha = await response.json();
        currentFicha = ficha;
        populateFichaInfo(ficha);
        await loadCompetencias();
        showDetails();
    };

    const loadCompetencias = async () => {
        const vistasList = document.getElementById('compVistasList');
        const noVistas = document.getElementById('noCompVistas');
        const faltantesList = document.getElementById('compFaltantesList');
        const noFaltantes = document.getElementById('noCompFaltantes');

        if (!vistasList || !faltantesList) return;

        try {
            const res = await fetch(`../../routing.php?controller=ficha&action=getDetalleCompetencias&id=${fichId}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            
            if (!res.ok) throw new Error(data.error || 'Error al cargar competencias');

            const vistas = data.vistas || [];
            const faltantes = data.faltantes || [];

            // ---------- Render Vistas ----------
            vistasList.innerHTML = '';
            if (vistas.length === 0) {
                if (noVistas) noVistas.style.display = 'block';
            } else {
                if (noVistas) noVistas.style.display = 'none';
                vistas.forEach(c => {
                    const card = document.createElement('div');
                    card.className = 'flex flex-col p-4 bg-white rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:border-sena-green transition-colors select-none';
                    const prog = Math.round((c.horas_asignadas / c.horas_totales) * 100) || 0;
                    
                    const headerRow = document.createElement('div');
                    headerRow.className = 'flex flex-col sm:flex-row items-start sm:items-center justify-between w-full pointer-events-none';
                    headerRow.innerHTML = `
                        <div class="flex items-center gap-4 mb-3 sm:mb-0">
                            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-sena-green font-bold text-[10px] tracking-tighter">
                                ${c.horas_totales}h
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm font-bold text-gray-900 leading-tight mb-1" title="${c.comp_nombre_corto}">${c.comp_nombre_corto}</p>
                                <p class="text-[10px] sm:text-xs text-gray-400 flex items-center gap-1">
                                    <ion-icon src="../../assets/ionicons/person-outline.svg"></ion-icon>
                                    <span class="font-medium text-gray-600">${c.inst_nombres} ${c.inst_apellidos}</span>
                                </p>
                            </div>
                        </div>
                        <div class="w-full sm:w-1/3 min-w-[150px]">
                            <div class="flex justify-between text-[10px] sm:text-xs mb-1 font-semibold
                                ${prog >= 100 ? 'text-sena-green' : 'text-amber-600'}">
                                <span>Cobertura Horas</span>
                                <span>${Math.round(c.horas_asignadas)} / ${c.horas_totales} h</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 relative overflow-hidden">
                                <div class="h-1.5 rounded-full transition-all duration-500
                                    ${prog >= 100 ? 'bg-sena-green' : 'bg-amber-400'}"
                                    style="width: ${Math.min(prog, 100)}%">
                                </div>
                            </div>
                        </div>
                    `;
                    card.appendChild(headerRow);

                    const detailDiv = document.createElement('div');
                    detailDiv.className = 'w-full mt-3 pt-3 border-t border-gray-100 hidden flex-col gap-2';
                    card.appendChild(detailDiv);

                    card.onclick = async () => {
                        if (detailDiv.classList.contains('hidden')) {
                            detailDiv.classList.remove('hidden');
                            detailDiv.classList.add('flex');
                            if (detailDiv.innerHTML === '') {
                                detailDiv.innerHTML = '<p class="text-xs text-gray-400 italic">Cargando planeación...</p>';
                                try {
                                    const asigRes = await fetch('../../routing.php?controller=asignacion&action=index', { headers: { 'Accept': 'application/json' } });
                                    const allAsig = await asigRes.json();
                                    const myAsig = Array.isArray(allAsig) ? allAsig.filter(a => String(a.ficha_fich_id) === String(fichId) && String(a.competencia_comp_id) === String(c.comp_id)) : [];
                                    
                                    detailDiv.innerHTML = '<p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-1">Sesiones de la Competencia:</p>';
                                    if (myAsig.length === 0) {
                                        detailDiv.innerHTML += '<p class="text-xs text-gray-500">No hay sesiones en el calendario.</p>';
                                    } else {
                                        myAsig.forEach(a => {
                                            const aDiv = document.createElement('div');
                                            aDiv.className = 'flex justify-between items-center bg-gray-50/80 p-2.5 rounded border border-gray-100 hover:bg-green-50/50 hover:border-green-100 transition-colors pointer-events-auto';
                                            aDiv.onclick = (e) => {
                                                e.stopPropagation();
                                                window.location.href = `../asignacion/ver.php?id=${a.asig_id}`;
                                            };
                                            aDiv.innerHTML = `
                                                <div class="flex flex-col">
                                                    <span class="font-semibold text-gray-800 text-xs">${a.inst_nombres} ${a.inst_apellidos}</span>
                                                    <span class="text-[10px] text-gray-500 flex items-center gap-1 mt-0.5"><ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon> ${formatFecha(a.asig_fecha_ini)} — ${formatFecha(a.asig_fecha_fin)}</span>
                                                </div>
                                                <div class="text-sena-green flex items-center text-sm p-1">
                                                    <ion-icon src="../../assets/ionicons/arrow-forward-circle-outline.svg"></ion-icon>
                                                </div>
                                            `;
                                            detailDiv.appendChild(aDiv);
                                        });
                                    }
                                } catch (e) {
                                    detailDiv.innerHTML = '<p class="text-xs text-red-500">Error al cargar detalles.</p>';
                                }
                            }
                        } else {
                            // Collapse
                            detailDiv.classList.add('hidden');
                            detailDiv.classList.remove('flex');
                        }
                    };

                    vistasList.appendChild(card);
                });
            }

            // ---------- Render Faltantes ----------
            faltantesList.innerHTML = '';
            if (faltantes.length === 0) {
                if (noFaltantes) noFaltantes.style.display = 'block';
            } else {
                if (noFaltantes) noFaltantes.style.display = 'none';
                faltantes.forEach(c => {
                    const card = document.createElement('div');
                    card.className = 'flex flex-col p-4 bg-amber-50/30 rounded-xl border border-amber-100 hover:border-amber-400 cursor-pointer transition-colors shadow-sm';
                    card.onclick = () => {
                        window.location.href = `../instru_competencia/index.php?comp_id=${c.comp_id}`;
                    };
                    card.innerHTML = `
                        <div class="flex items-start gap-3 pointer-events-none">
                            <div class="w-8 h-8 rounded-full bg-amber-100 flex-shrink-0 flex items-center justify-center text-amber-600 font-bold text-[9px]">
                                ${c.comp_num_horas}h
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800 leading-snug mb-1" title="${c.comp_nombre}">${c.comp_nombre_corto}</p>
                                <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-700 uppercase tracking-wide">
                                    Buscar Instructores
                                </span>
                            </div>
                        </div>
                    `;
                    faltantesList.appendChild(card);
                });
            }

        } catch (err) {
            console.error(err);
            vistasList.innerHTML = '<p class="text-sm text-red-500">Error al cargar las competencias</p>';
            faltantesList.innerHTML = '<p class="text-sm text-red-500">Error al cargar las competencias</p>';
        }
    };



    const formatFecha = (str) => {
        if (!str) return '--';
        const d = new Date(str);
        return d.toLocaleDateString('es-CO', { day: '2-digit', month: 'short', year: 'numeric' });
    };

    const populateFichaInfo = (f) => {
        document.getElementById('detFichaId').textContent = f.fich_id;
        document.getElementById('detPrograma').textContent = f.prog_denominacion || 'N/A';
        document.getElementById('detJornada').textContent = `Jornada ${f.fich_jornada}`;
        document.getElementById('detInstructor').textContent = f.inst_nombres ? `${f.inst_nombres} ${f.inst_apellidos}` : 'No asignado';
        document.getElementById('detCoordinacion').textContent = f.coord_nombre || 'No asignada';

        const iniciales = (f.inst_nombres && f.inst_apellidos) ? `${f.inst_nombres[0]}${f.inst_apellidos[0]}` : '--';
        document.getElementById('detInstInic').textContent = iniciales;

        // Botón de eliminar (solo si existe)
        const deleteBtn = document.getElementById('deleteBtn');
        if (deleteBtn) {
            deleteBtn.onclick = () => handleDelete(f.fich_id);
        }

        // Botón de editar (solo si existe y el modal está disponible)
        const editBtn = document.getElementById('editBtn');
        if (editBtn) {
            editBtn.onclick = () => openEditModal(f);
        }
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
                    setTimeout(() => window.location.href = window.isInstructor ? '../instructor/mi_ficha.php' : '../../routing.php?controller=ficha&action=index', 1500);
                } else {
                    const data = await response.json();
                    NotificationService.showError(data.error || 'No se pudo eliminar');
                }
            } catch (err) {
                NotificationService.showError('Error de red');
            }
        });
    };

    // Modal Control & Form (Suelen no existir para instructores)
    if (!window.isInstructor) {
        const closeModal = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        
        if (closeModal) closeModal.onclick = () => { if (modal) modal.classList.remove('show'); };
        if (cancelBtn) cancelBtn.onclick = () => { if (modal) modal.classList.remove('show'); };

        if (form) {
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
                        if (modal) modal.classList.remove('show');
                        await loadFichaData(); // Recargar datos en la vista
                    } else {
                        const result = await response.json();
                        NotificationService.showError(result.error || 'Error al actualizar');
                    }
                } catch (err) {
                    NotificationService.showError('Error de conexión');
                }
            };
        }
    }

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
