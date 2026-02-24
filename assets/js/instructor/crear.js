// Instructor Create JavaScript
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('instructorForm');
    const centroSelect = document.getElementById('centro_id');
    const programaSelect = document.getElementById('programa_id');
    const programasList = document.getElementById('programasList');
    const progSearch = document.getElementById('progSearch');
    const checklistContainer = document.getElementById('programasChecklistContainer');
    const competenciasContainer = document.getElementById('competenciasContainer');
    const submitBtn = document.getElementById('submitBtn');

    if (!centroSelect || !programaSelect || !programasList) {
        console.error('Error: Elementos del DOM requeridos no encontrados.');
        return;
    }

    let allProgramas = [];
    let loadedPrograms = new Set();

    const loadCentros = async () => {
        try {
            const response = await fetch('../../routing.php?controller=instructor&action=getCentros', {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Error al obtener centros');
            const centros = await response.json();

            centroSelect.innerHTML = '<option value="">Seleccione un centro de formación...</option>';
            centros.forEach(centro => {
                const option = document.createElement('option');
                option.value = centro.cent_id;
                option.textContent = centro.cent_nombre;
                centroSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error al cargar centros:', error);
            if (typeof NotificationService !== 'undefined') NotificationService.showError('No se pudieron cargar los centros');
        }
    };

    const loadProgramas = async () => {
        try {
            const response = await fetch('../../routing.php?controller=programa&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Error al obtener programas');
            allProgramas = await response.json();

            // Poblar SELECT tradicional
            if (programaSelect) {
                programaSelect.innerHTML = '<option value="">Seleccione un programa...</option>';
                allProgramas.forEach(prog => {
                    const opt = document.createElement('option');
                    opt.value = prog.prog_codigo;
                    opt.textContent = `${prog.prog_denominacion} - (${prog.titpro_nombre})`;
                    programaSelect.appendChild(opt);
                });
            }

            // Mostrar buscador si hay más de 5 programas
            if (allProgramas.length > 5 && checklistContainer) {
                checklistContainer.style.display = 'block';
            }

            renderProgramasList();
        } catch (error) {
            console.error('Error al cargar programas:', error);
            if (programasList) programasList.innerHTML = '<p class="text-red-500 text-sm italic py-2">Error al cargar programas.</p>';
        }
    };

    const renderProgramasList = (filter = '') => {
        if (!programasList) return;
        programasList.innerHTML = '';
        const term = filter.toLowerCase().trim();

        const filtered = allProgramas.filter(p => {
            const denom = p.prog_denominacion ? p.prog_denominacion.toLowerCase() : '';
            const titulo = p.titpro_nombre ? p.titpro_nombre.toLowerCase() : '';
            return denom.includes(term) || titulo.includes(term);
        });

        if (filtered.length === 0) {
            programasList.innerHTML = '<p class="text-gray-400 text-sm italic py-2">No se encontraron programas.</p>';
            return;
        }

        filtered.forEach(prog => {
            const isChecked = loadedPrograms.has(prog.prog_codigo);
            const div = document.createElement('div');
            div.className = 'flex items-center p-2 rounded hover:bg-gray-100 transition-colors cursor-pointer';

            div.innerHTML = `
                <label class="flex items-center gap-3 w-full cursor-pointer">
                    <input type="checkbox" value="${prog.prog_codigo}" ${isChecked ? 'checked' : ''} class="prog-checkbox w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                    <div class="flex-1 min-w-0">
                        <div class="text-[11px] font-medium text-gray-800 truncate" title="${prog.prog_denominacion}">${prog.prog_denominacion}</div>
                        <div class="text-[9px] text-gray-500 truncate" title="${prog.titpro_nombre}">${prog.titpro_nombre}</div>
                    </div>
                </label>
            `;

            const checkbox = div.querySelector('input');
            checkbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    loadCompetencias(prog.prog_codigo, prog.prog_denominacion);
                } else {
                    removeCompetencias(prog.prog_codigo);
                }
            });

            programasList.appendChild(div);
        });
    };

    const removeCompetencias = (progId) => {
        const card = document.getElementById(`prog_card_${progId}`);
        if (card) card.remove();
        loadedPrograms.delete(progId);

        // Sincronizar Checkbox
        const cb = document.querySelector(`.prog-checkbox[value="${progId}"]`);
        if (cb) cb.checked = false;

        if (loadedPrograms.size === 0) {
            competenciasContainer.innerHTML = '<p id="emptyAviso" class="text-gray-400 text-sm italic text-center py-4">Seleccione un programa arriba para cargar competencias</p>';
        }
    };

    const loadCompetencias = async (progId, progName) => {
        if (!progId) return;

        if (loadedPrograms.has(progId)) {
            // Scroll to it if already loaded
            const existingHeader = document.getElementById(`prog_header_${progId}`);
            if (existingHeader) existingHeader.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        try {
            const emptyAviso = document.getElementById('emptyAviso');
            if (emptyAviso) emptyAviso.remove();

            const loadingId = `loading_${progId}`;
            competenciasContainer.insertAdjacentHTML('beforeend', `<p id="${loadingId}" class="text-blue-500 text-sm text-center py-4">Cargando competencias para ${progName}...</p>`);

            const response = await fetch(`../../routing.php?controller=competencia_programa&action=getByPrograma&prog_id=${progId}`, {
                headers: { 'Accept': 'application/json' }
            });

            document.getElementById(loadingId)?.remove();

            if (!response.ok) throw new Error('Error al obtener competencias');
            const competencias = await response.json();

            if (competencias.length === 0) {
                NotificationService.showError(`El programa ${progName} no tiene competencias asociadas`);
                return;
            }

            loadedPrograms.add(progId);

            const div = document.createElement('div');
            div.id = `prog_card_${progId}`;
            div.className = 'mb-6 p-4 border rounded-lg bg-white border-green-100 shadow-sm';

            let checkboxesHTML = competencias.map(comp => `
                <label class="flex items-center w-full cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                    <input type="checkbox" name="competencias[]" value="${progId}|${comp.comp_id}" class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                    <div class="ml-3 text-sm">
                        <span class="font-medium text-gray-900">${comp.comp_nombre_corto}</span>
                        <span class="block text-gray-500 text-xs">${comp.comp_nombre_unidad_competencia || ''}</span>
                    </div>
                </label>
            `).join('');

            div.innerHTML = `
                <div class="flex justify-between items-center mb-3 border-b pb-2">
                    <h5 class="font-bold text-green-700" id="prog_header_${progId}">${progName}</h5>
                    <button type="button" class="text-xs text-red-500 hover:bg-red-50 px-2 py-1 rounded border border-transparent hover:border-red-200 transition-colors" onclick="removeCompetencias('${progId}')">
                        <ion-icon src="../../assets/ionicons/trash-outline.svg" class="align-middle"></ion-icon> Quitar
                    </button>
                </div>
                <div class="space-y-1">
                    ${checkboxesHTML}
                </div>
            `;

            competenciasContainer.appendChild(div);

        } catch (error) {
            console.error('Error al cargar competencias:', error);
            NotificationService.showError('Error al cargar competencias');
        }
    };

    // ── Events ────────────────────────────────────────────────

    if (programaSelect) {
        programaSelect.addEventListener('change', (e) => {
            if (e.target.value) {
                const progName = e.target.options[e.target.selectedIndex].text;
                loadCompetencias(e.target.value, progName);
                e.target.value = ''; // Reset select
            }
        });
    }

    if (progSearch) {
        progSearch.addEventListener('input', (e) => renderProgramasList(e.target.value));
    }

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            submitBtn.disabled = true;
            const formData = new FormData(form);

            try {
                const response = await fetch('../../routing.php?controller=instructor&action=store', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });

                const result = await response.json();

                if (response.ok) {
                    NotificationService.showSuccess('Instructor y habilitaciones registradas con éxito');
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    NotificationService.showError(result.error || 'Error al registrar instructor');
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                NotificationService.showError('Error de conexión con el servidor');
                submitBtn.disabled = false;
            }
        });
    }

    // ── Init ──────────────────────────────────────────────────
    loadCentros();
    loadProgramas();
});
