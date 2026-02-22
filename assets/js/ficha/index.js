document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('fichaTableBody');
    const searchInput = document.getElementById('searchInput');
    const totalLabel = document.getElementById('totalFichas');
    const addBtn = document.getElementById('addBtn');
    const modal = document.getElementById('fichaModal');
    const form = document.getElementById('fichaForm');
    const closeBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const programaSelect = document.getElementById('programa_id');
    const instructorSelect = document.getElementById('instructor_id');
    const coordinacionSelect = document.getElementById('coordinacion_id');

    let fichas = [];

    const sedeSelect = document.getElementById('sede_id');
    let allCoordinaciones = [];

    const loadData = async () => {
        try {
            const headers = { 'Accept': 'application/json' };

            // Load Sedes
            const sedeRes = await fetch('../../routing.php?controller=sede&action=index', { headers });
            if (sedeRes.ok) {
                const sedes = await sedeRes.json();
                if (sedeSelect) {
                    sedeSelect.innerHTML = '<option value="">Seleccione sede...</option>';
                    sedes.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.sede_id;
                        opt.textContent = s.sede_nombre;
                        sedeSelect.appendChild(opt);
                    });
                }
            }

            // Load Programas
            const progRes = await fetch('../../routing.php?controller=programa&action=index', { headers });
            if (progRes.ok) {
                const programas = await progRes.json();
                if (programaSelect) {
                    programaSelect.innerHTML = '<option value="">Seleccione programa...</option>';
                    programas.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.prog_codigo;
                        opt.textContent = `${p.prog_codigo} - ${p.prog_denominacion}`;
                        programaSelect.appendChild(opt);
                    });
                }
            }

            // Load Instructores
            const instRes = await fetch('../../routing.php?controller=instructor&action=index', { headers });
            if (instRes.ok) {
                const instructores = await instRes.json();
                if (instructorSelect) {
                    instructorSelect.innerHTML = '<option value="">Seleccione instructor líder...</option>';
                    instructores.forEach(i => {
                        const opt = document.createElement('option');
                        opt.value = i.inst_id;
                        opt.textContent = `${i.inst_nombres} ${i.inst_apellidos}`;
                        instructorSelect.appendChild(opt);
                    });
                }
            }

            // Load Coordinaciones (All for filtering)
            const coordRes = await fetch('../../routing.php?controller=coordinacion&action=index', { headers });
            if (coordRes.ok) {
                allCoordinaciones = await coordRes.json();
                // Populate coordinacion select immediately
                if (coordinacionSelect) {
                    coordinacionSelect.innerHTML = '<option value="">Seleccione coordinación...</option>';
                    allCoordinaciones.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.coord_id;
                        opt.textContent = c.cent_nombre ? `${c.coord_descripcion} - ${c.cent_nombre}` : c.coord_descripcion;
                        coordinacionSelect.appendChild(opt);
                    });
                }
            }

            // Load Fichas
            const fichaRes = await fetch('../../routing.php?controller=ficha&action=index', { headers });
            const data = await fichaRes.json();

            if (!fichaRes.ok) {
                throw new Error(data.details || data.error || 'Error al cargar fichas');
            }

            fichas = Array.isArray(data) ? data : [];
            renderFichas(fichas);
            if (totalLabel) totalLabel.textContent = fichas.length;
        } catch (error) {
            console.error('Error:', error);
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-gray-500">No pudimos cargar las fichas. Intenta recargar la página.</td></tr>`;
            }
        }
    };

    // Filter coordinaciones based on selected sede
    if (sedeSelect) {
        sedeSelect.addEventListener('change', () => {
            const sedeId = sedeSelect.value;
            if (coordinacionSelect) {
                coordinacionSelect.innerHTML = '<option value="">Seleccione coordinación...</option>';
                const filtered = allCoordinaciones.filter(c => !sedeId || c.sede_id == sedeId);
                filtered.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.coord_id;
                    opt.textContent = c.coord_descripcion;
                    coordinacionSelect.appendChild(opt);
                });
            }
        });
    }

    const renderFichas = (data) => {
        if (!tableBody) return;
        tableBody.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">No se encontraron fichas</td></tr>';
            return;
        }

        data.forEach(f => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
            row.onclick = () => window.location.href = `ver.php?id=${f.fich_id}`;
            row.innerHTML = `
                <td class="px-6 py-4">
                    <span class="text-sena-green font-bold text-sm tracking-wider">
                        ${f.fich_id}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-gray-900 font-semibold text-sm">${f.titpro_nombre || 'N/A'}</div>
                    <div class="text-xs text-gray-500">${f.prog_denominacion || ''}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-700">${f.sede_nombre || 'N/A'}</div>
                    <div class="text-xs text-gray-500">${f.coord_nombre || 'No asignada'}</div>
                </td>
                <td class="px-6 py-4 text-gray-600 text-sm">${f.inst_nombres || ''} ${f.inst_apellidos || ''}</td>
                <td class="px-6 py-4">
                    <span class="status-badge status-active">${f.fich_jornada || 'N/A'}</span>
                </td>
            `;
            tableBody.appendChild(row);
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.onclick = () => {
                const id = btn.dataset.id;
                const ficha = fichas.find(f => f.fich_id == id);
                if (ficha) openModal(ficha);
            };
        });
    };

    const openModal = (ficha = null) => {
        if (!form) return;
        form.reset();

        const modalTitle = document.getElementById('modalTitle');
        const fichIdInput = document.getElementById('fich_id');
        const fichIdOldInput = document.getElementById('fich_id_old');

        if (ficha) {
            if (modalTitle) modalTitle.textContent = 'Editar Ficha';
            if (fichIdInput) {
                fichIdInput.value = ficha.fich_id;
                fichIdInput.readOnly = true;
            }
            if (fichIdOldInput) fichIdOldInput.value = ficha.fich_id;

            if (document.getElementById('programa_id')) document.getElementById('programa_id').value = ficha.programa_prog_id || ficha.programa_prog_codigo || '';
            if (document.getElementById('instructor_id')) document.getElementById('instructor_id').value = ficha.instructor_inst_id_lider || ficha.instructor_inst_id || '';
            if (document.getElementById('coordinacion_id')) document.getElementById('coordinacion_id').value = ficha.coordinacion_coord_id || '';
            if (document.getElementById('fich_jornada')) document.getElementById('fich_jornada').value = ficha.fich_jornada || '';
            if (document.getElementById('fich_fecha_ini_lectiva') && ficha.fich_fecha_ini_lectiva) {
                document.getElementById('fich_fecha_ini_lectiva').value = ficha.fich_fecha_ini_lectiva.split('T')[0];
            }
            if (document.getElementById('fich_fecha_fin_lectiva') && ficha.fich_fecha_fin_lectiva) {
                document.getElementById('fich_fecha_fin_lectiva').value = ficha.fich_fecha_fin_lectiva.split('T')[0];
            }
        } else {
            if (modalTitle) modalTitle.textContent = 'Nueva Ficha';
            if (fichIdInput) {
                fichIdInput.value = '';
                fichIdInput.readOnly = false;
            }
            if (fichIdOldInput) fichIdOldInput.value = '';
        }

        if (modal) modal.classList.add('show');
    };

    const closeModal = () => {
        if (modal) modal.classList.remove('show');
    };

    if (addBtn) addBtn.onclick = () => openModal();
    if (closeBtn) closeBtn.onclick = closeModal;
    if (cancelBtn) cancelBtn.onclick = closeModal;

    if (form) {
        form.onsubmit = async (e) => {
            e.preventDefault();
            const idOld = document.getElementById('fich_id_old').value;
            const action = idOld ? 'update' : 'store';

            const formData = new FormData(form);
            // Asegurarse de que los nombres de los campos coincidan con lo que el controlador espera si hay diferencias
            // En este caso, el controlador espera programa_prog_id, instructor_inst_id, etc.
            // El formulario ya tiene esos nombres en los atributos 'name'.

            try {
                const response = await fetch(`../../routing.php?controller=ficha&action=${action}`, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });

                const result = await response.json();

                if (response.ok) {
                    NotificationService.showSuccess(idOld ? '¡Ficha actualizada correctamente!' : '¡Ficha registrada correctamente!');
                    closeModal();
                    loadData();
                } else {
                    NotificationService.showError('No se pudo guardar la ficha. Verifica que todos los campos estén completos e intenta de nuevo.');
                }
            } catch (error) {
                console.error('Error al guardar:', error);
                NotificationService.showError('No pudimos conectar con el servidor. Intenta de nuevo.');
            }
        };
    }

    window.deleteFicha = async (id) => {
        if (window.NotificationService) {
            NotificationService.showConfirm('¿Estás seguro de que deseas eliminar esta ficha? Esta acción no se puede deshacer.', async () => {
                try {
                    const response = await fetch(`../../routing.php?controller=ficha&action=destroy&id=${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (response.ok) {
                        NotificationService.showSuccess('La ficha fue eliminada correctamente.');
                        loadData();
                    } else {
                        NotificationService.showError('No se pudo eliminar la ficha. Es posible que tenga asignaciones asociadas.');
                    }
                } catch (error) {
                    NotificationService.showError('No pudimos conectar con el servidor. Intenta de nuevo.');
                }
            });
        }
    };

    if (searchInput) {
        searchInput.oninput = () => {
            const term = searchInput.value.toLowerCase();
            const filtered = fichas.filter(f =>
                String(f.fich_id).toLowerCase().includes(term) ||
                (f.titpro_nombre || '').toLowerCase().includes(term) ||
                (f.inst_nombres || '').toLowerCase().includes(term) ||
                (f.inst_apellidos || '').toLowerCase().includes(term)
            );
            renderFichas(filtered);
        };
    }

    loadData();
});
