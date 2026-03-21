document.addEventListener('DOMContentLoaded', async () => {
    const form = document.getElementById('instructorForm');
    const programaSelect = document.getElementById('programa_id');
    const compSearch = document.getElementById('compSearch');
    const competenciasContainer = document.getElementById('competenciasContainer');
    const instIdInput = document.getElementById('inst_id');
    const submitBtn = document.getElementById('submitBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const selectionSummary = document.getElementById('selectionSummary');

    if (!instIdInput) return;
    const instId = instIdInput.value;

    let allCompetencias = []; // Competencias del programa seleccionado
    let selectedCompetencyIds = new Set();

    const fetchAPI = async (url) => {
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
        return await response.json();
    };

    // 1. Cargar Catálogo de Programas
    const loadProgramas = async () => {
        try {
            const programas = await fetchAPI('../../routing.php?controller=competencia&action=getProgramas');
            programaSelect.innerHTML = '<option value="">-- Seleccione un Programa --</option>';
            programas.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.prog_codigo;
                opt.textContent = `${p.prog_codigo} - ${p.prog_denominacion}`;
                programaSelect.appendChild(opt);
            });
        } catch (error) {
            console.error('Error cargando programas:', error);
        }
    };

    // 2. Cargar Competencias por Programa
    const loadCompetenciasByPrograma = async (progId) => {
        if (!progId) {
            allCompetencias = [];
            renderCompetencias();
            return;
        }
        competenciasContainer.innerHTML = '<p class="text-gray-400 text-sm italic text-center py-4">Cargando competencias...</p>';
        try {
            allCompetencias = await fetchAPI(`../../routing.php?controller=competencia&action=getByPrograma&prog_id=${progId}`);
            renderCompetencias();
        } catch (error) {
            console.error('Error cargando competencias:', error);
            competenciasContainer.innerHTML = '<p class="text-red-500 text-sm italic text-center py-4">Error al cargar datos.</p>';
        }
    };

    const renderCompetencias = (filter = '') => {
        if (!competenciasContainer) return;

        if (allCompetencias.length === 0) {
            competenciasContainer.innerHTML = '<p class="text-gray-400 text-sm italic text-center py-4">' + 
                (programaSelect.value ? 'No hay competencias asociadas.' : 'Primero seleccione un programa...') + '</p>';
            return;
        }

        const term = filter.toLowerCase().trim();
        const filtered = allCompetencias.filter(c => {
            const nombre = c.comp_nombre_corto ? c.comp_nombre_corto.toLowerCase() : '';
            const desc = c.comp_nombre_unidad_competencia ? c.comp_nombre_unidad_competencia.toLowerCase() : '';
            return nombre.includes(term) || desc.includes(term);
        });

        if (filtered.length === 0) {
            competenciasContainer.innerHTML = '<p class="text-gray-400 text-sm italic text-center py-4">No se encontraron coincidencias.</p>';
            return;
        }

        let html = '';
        filtered.forEach(comp => {
            const isChecked = selectedCompetencyIds.has(String(comp.comp_id));
            html += `
                <label class="flex items-center w-full cursor-pointer hover:bg-gray-100 p-2 rounded transition-colors border border-transparent hover:border-gray-200">
                    <input type="checkbox" data-compId="${comp.comp_id}" ${isChecked ? 'checked' : ''} class="comp-checkbox w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                    <div class="ml-3 text-sm flex-1">
                        <span class="font-medium text-gray-900">${comp.comp_nombre_corto}</span>
                        <span class="block text-gray-500 text-xs">${comp.comp_nombre_unidad_competencia || ''}</span>
                    </div>
                </label>
            `;
        });

        competenciasContainer.innerHTML = html;
        bindCheckboxEvents();
    };

    const bindCheckboxEvents = () => {
        const checkboxes = competenciasContainer.querySelectorAll('.comp-checkbox');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', (e) => {
                const id = String(e.target.dataset.compid);
                if (e.target.checked) selectedCompetencyIds.add(id);
                else selectedCompetencyIds.delete(id);
                updateSummary();
            });
        });
    };

    const updateSummary = () => {
        const count = selectedCompetencyIds.size;
        selectedCountSpan.textContent = count;
        if (count > 0) selectionSummary.classList.remove('hidden');
        else selectionSummary.classList.add('hidden');
    };

    const init = async () => {
        try {
            await loadProgramas();

            const [instructor, currentHabs] = await Promise.all([
                fetchAPI(`../../routing.php?controller=instructor&action=show&id=${instId}`),
                fetchAPI(`../../routing.php?controller=instructor&action=getCompetencias&id=${instId}`)
            ]);

            // Poblado básico
            document.getElementById('numero_documento').value = instructor.inst_id || instructor.numero_documento || '';
            document.getElementById('inst_nombres').value = instructor.inst_nombres || '';
            document.getElementById('inst_apellidos').value = instructor.inst_apellidos || '';
            document.getElementById('inst_correo').value = instructor.inst_correo || '';
            document.getElementById('inst_telefono').value = instructor.inst_telefono || '';
            document.getElementById('inst_password').value = instructor.inst_password || '';
            if (document.getElementById('profesion')) document.getElementById('profesion').value = instructor.profesion || '';
            if (document.getElementById('especializacion')) document.getElementById('especializacion').value = instructor.especializacion || '';

            // Cargar habilitaciones actuales al Set
            if (Array.isArray(currentHabs)) {
                currentHabs.forEach(h => {
                    selectedCompetencyIds.add(String(h.competencia_comp_id || h.comp_id));
                });
            }
            updateSummary();

        } catch (error) {
            console.error('Error initializing:', error);
            if (window.NotificationService) NotificationService.showError('Error al cargar datos del instructor');
        }
    };

    // Eventos
    programaSelect.addEventListener('change', (e) => loadCompetenciasByPrograma(e.target.value));
    compSearch.addEventListener('input', (e) => renderCompetencias(e.target.value));

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitBtn.disabled = true;

            const formData = new FormData(form);
            formData.append('controller', 'instructor');
            formData.append('action', 'update');
            
            // Añadir competencias del Set
            selectedCompetencyIds.forEach(id => {
                formData.append('competencias[]', id);
            });

            try {
                const response = await fetch('../../routing.php', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });

                const result = await response.json();
                if (response.ok && !result.error) {
                    if (window.NotificationService) NotificationService.showSuccess('Instructor actualizado con éxito');
                    setTimeout(() => window.location.href = `ver.php?id=${instId}`, 1500);
                } else {
                    if (window.NotificationService) NotificationService.showError(result.error || 'Error al actualizar');
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error(error);
                if (window.NotificationService) NotificationService.showError('Error de servidor');
                submitBtn.disabled = false;
            }
        });
    }

    init();
});
