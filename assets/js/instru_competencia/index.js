document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('habilitacionTableBody');
    const searchInput = document.getElementById('searchInput');
    const totalLabel = document.getElementById('totalHabilitaciones');
    const addBtn = document.getElementById('addBtn');
    const modal = document.getElementById('habilitacionModal');
    const form = document.getElementById('habilitacionForm');
    const closeBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const instructorSelect = document.getElementById('instructor_id');
    const programaSelect = document.getElementById('programa_id');
    const competenciaSelect = document.getElementById('competencia_id');

    let habilitaciones = [];

    // ── Carga de selects ──────────────────────────────────────
    const loadInstructores = async () => {
        try {
            const res = await fetch('../../routing.php?controller=instructor&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (instructorSelect) {
                instructorSelect.innerHTML = '<option value="">Seleccione instructor...</option>';
                data.forEach(i => {
                    const opt = document.createElement('option');
                    opt.value = i.inst_id;
                    opt.textContent = `${i.inst_nombres} ${i.inst_apellidos}`;
                    instructorSelect.appendChild(opt);
                });
            }
        } catch (e) { console.error('Error cargando instructores:', e); }
    };

    const loadProgramas = async () => {
        try {
            const res = await fetch('../../routing.php?controller=programa&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (programaSelect) {
                programaSelect.innerHTML = '<option value="">Seleccione programa...</option>';
                data.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.prog_codigo;
                    opt.textContent = `${p.prog_codigo} - ${p.prog_denominacion}`;
                    programaSelect.appendChild(opt);
                });
            }
        } catch (e) { console.error('Error cargando programas:', e); }
    };

    // ── Carga en cascada: Programa → Competencias ────────────
    const loadCompetenciasByPrograma = async (progId) => {
        if (!competenciaSelect) return;
        competenciaSelect.innerHTML = '<option value="">Cargando competencias...</option>';
        competenciaSelect.disabled = true;

        if (!progId) {
            competenciaSelect.innerHTML = '<option value="">Primero seleccione un programa...</option>';
            return;
        }

        try {
            const res = await fetch(`../../routing.php?controller=competencia_programa&action=getByPrograma&prog_id=${progId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const competencias = await res.json();

            competenciaSelect.innerHTML = '<option value="">Seleccione competencia...</option>';
            if (Array.isArray(competencias) && competencias.length > 0) {
                competencias.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.comp_id;
                    opt.textContent = c.comp_nombre_corto;
                    competenciaSelect.appendChild(opt);
                });
                competenciaSelect.disabled = false;
            } else {
                competenciaSelect.innerHTML = '<option value="">No hay competencias para este programa</option>';
            }
        } catch (e) {
            console.error('Error cargando competencias:', e);
            competenciaSelect.innerHTML = '<option value="">Error al cargar</option>';
        }
    };

    if (programaSelect) {
        programaSelect.addEventListener('change', () => {
            loadCompetenciasByPrograma(programaSelect.value);
        });
    }

    // ── CRUD ──────────────────────────────────────────────────
    const loadHabilitaciones = async () => {
        try {
            const res = await fetch('../../routing.php?controller=instru_competencia&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.details || data.error || 'Error del servidor');

            habilitaciones = Array.isArray(data) ? data : [];
            renderHabilitaciones(habilitaciones);
            if (totalLabel) totalLabel.textContent = habilitaciones.length;
        } catch (e) {
            console.error('Error:', e);
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-500">Error: ${e.message}</td></tr>`;
            }
        }
    };

    const renderHabilitaciones = (data) => {
        if (!tableBody) return;
        tableBody.innerHTML = '';
        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-500">No se encontraron habilitaciones</td></tr>';
            return;
        }

        data.forEach(h => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
            row.onclick = () => window.location.href = `ver.php?id=${h.inscomp_id}`;

            const vigenciaDate = h.inscomp_vigencia ? new Date(h.inscomp_vigencia).toLocaleDateString('es-CO') : 'N/A';
            const isVigente = h.inscomp_vigencia ? new Date(h.inscomp_vigencia) >= new Date() : false;

            row.innerHTML = `
                <td class="px-6 py-4 font-semibold text-sena-green">${String(h.inscomp_id).padStart(3, '0')}</td>
                <td class="px-6 py-4 font-bold text-gray-900">${h.inst_nombres || ''} ${h.inst_apellidos || ''}</td>
                <td class="px-6 py-4 text-gray-600">${h.prog_denominacion || 'N/A'}</td>
                <td class="px-6 py-4 text-gray-600">${h.comp_nombre_corto || 'N/A'}</td>
                <td class="px-6 py-4">
                    <span class="status-badge ${isVigente ? 'status-active' : 'status-inactive'}">${vigenciaDate}</span>
                </td>
            `;
            tableBody.appendChild(row);
        });
    };

    // ── Modal ─────────────────────────────────────────────────
    const openModal = (hab = null) => {
        if (!form) return;
        form.reset();
        competenciaSelect.innerHTML = '<option value="">Primero seleccione un programa...</option>';
        competenciaSelect.disabled = true;

        const modalTitle = document.getElementById('modalTitle');
        const idInput = document.getElementById('inscomp_id');

        if (hab) {
            if (modalTitle) modalTitle.textContent = 'Editar Habilitación';
            if (idInput) idInput.value = hab.inscomp_id;
            if (instructorSelect) instructorSelect.value = hab.instructor_inst_id;
            if (programaSelect) {
                programaSelect.value = hab.competxprograma_programa_prog_id;
                loadCompetenciasByPrograma(hab.competxprograma_programa_prog_id).then(() => {
                    if (competenciaSelect) competenciaSelect.value = hab.competxprograma_competencia_comp_id;
                });
            }
            const vigInput = document.getElementById('inscomp_vigencia');
            if (vigInput && hab.inscomp_vigencia) vigInput.value = hab.inscomp_vigencia.split('T')[0];
        } else {
            if (modalTitle) modalTitle.textContent = 'Nueva Habilitación';
            if (idInput) idInput.value = '';
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
            const id = document.getElementById('inscomp_id').value;
            const action = id ? 'update' : 'store';
            const data = {
                instructor_inst_id: instructorSelect.value,
                competxprograma_programa_prog_id: programaSelect.value,
                competxprograma_competencia_comp_id: competenciaSelect.value,
                inscomp_vigencia: document.getElementById('inscomp_vigencia').value
            };
            if (id) data.inscomp_id = id;

            try {
                const res = await fetch(`../../routing.php?controller=instru_competencia&action=${action}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if (res.ok) {
                    NotificationService.showSuccess(id ? 'Habilitación actualizada' : 'Habilitación creada');
                    closeModal();
                    loadHabilitaciones();
                } else {
                    NotificationService.showError(result.details || result.error || 'Error al guardar');
                }
            } catch (e) {
                NotificationService.showError('Error de conexión');
            }
        };
    }

    // ── Búsqueda ──────────────────────────────────────────────
    if (searchInput) {
        searchInput.oninput = () => {
            const term = searchInput.value.toLowerCase();
            const filtered = habilitaciones.filter(h =>
                `${h.inst_nombres} ${h.inst_apellidos}`.toLowerCase().includes(term) ||
                (h.prog_denominacion || '').toLowerCase().includes(term) ||
                (h.comp_nombre_corto || '').toLowerCase().includes(term)
            );
            renderHabilitaciones(filtered);
        };
    }

    // ── Init ──────────────────────────────────────────────────
    loadInstructores();
    loadProgramas();
    loadHabilitaciones();
});
