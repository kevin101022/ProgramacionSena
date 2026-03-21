document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('instructorForm');
    const programaSelect = document.getElementById('programa_id');
    const compSearch = document.getElementById('compSearch');
    const competenciasContainer = document.getElementById('competenciasContainer');
    const submitBtn = document.getElementById('submitBtn');
    const selectedCountSpan = document.getElementById('selectedCount');
    const selectionSummary = document.getElementById('selectionSummary');

    let allCompetencias = []; // Competencias del programa seleccionado
    let selectedCompetencyIds = new Set();

    // 1. Cargar Programas al iniciar
    const loadProgramas = async () => {
        try {
            const res = await fetch('../../routing.php?controller=competencia&action=getProgramas', {
                headers: { 'Accept': 'application/json' }
            });
            const programas = await res.json();
            
            programaSelect.innerHTML = '<option value="">-- Seleccione un Programa --</option>';
            programas.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.prog_codigo;
                opt.textContent = `${p.prog_codigo} - ${p.prog_denominacion}`;
                programaSelect.appendChild(opt);
            });
        } catch (error) {
            console.error('Error cargando programas:', error);
            programaSelect.innerHTML = '<option value="">Error al cargar</option>';
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
            const res = await fetch(`../../routing.php?controller=competencia&action=getByPrograma&prog_id=${progId}`, {
                headers: { 'Accept': 'application/json' }
            });
            allCompetencias = await res.json();
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

    // Eventos
    programaSelect.addEventListener('change', (e) => loadCompetenciasByPrograma(e.target.value));
    compSearch.addEventListener('input', (e) => renderCompetencias(e.target.value));

    // Form Submit
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            submitBtn.disabled = true;
            const formData = new FormData(form);
            formData.append('controller', 'instructor');
            formData.append('action', 'store');

            // Añadir las competencias del Set
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
                    if (window.NotificationService) NotificationService.showSuccess('Instructor guardado con éxito');
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    if (window.NotificationService) NotificationService.showError(result.error || 'Error al guardar el instructor');
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error(error);
                if (window.NotificationService) NotificationService.showError('Error de red o de servidor');
                submitBtn.disabled = false;
            }
        });
    }

    loadProgramas();
});
