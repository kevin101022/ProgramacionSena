document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('instructorForm');
    const compSearch = document.getElementById('compSearch');
    const competenciasContainer = document.getElementById('competenciasContainer');
    const submitBtn = document.getElementById('submitBtn');

    let allCompetencias = [];

    const loadAllCompetencias = async () => {
        try {
            const response = await fetch('../../routing.php?controller=competencia&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Error al obtener competencias');

            allCompetencias = await response.json();
            renderCompetencias();
        } catch (error) {
            console.error('Error al cargar competencias:', error);
            if (competenciasContainer) {
                competenciasContainer.innerHTML = '<p class="text-red-500 text-sm italic text-center py-4">Error al conectar con el servidor.</p>';
            }
        }
    };

    const renderCompetencias = (filter = '') => {
        if (!competenciasContainer) return;

        const term = filter.toLowerCase().trim();
        const filtered = allCompetencias.filter(c => {
            const nombre = c.comp_nombre_corto ? c.comp_nombre_corto.toLowerCase() : '';
            const desc = c.comp_nombre_unidad_competencia ? c.comp_nombre_unidad_competencia.toLowerCase() : '';
            return nombre.includes(term) || desc.includes(term);
        });

        if (filtered.length === 0) {
            competenciasContainer.innerHTML = '<p class="text-gray-400 text-sm italic text-center py-4">No se encontraron competencias.</p>';
            return;
        }

        let html = '';
        filtered.forEach(comp => {
            html += `
                <label class="flex items-center w-full cursor-pointer hover:bg-gray-100 p-2 rounded transition-colors border border-transparent hover:border-gray-200">
                    <input type="checkbox" name="competencias[]" value="${comp.comp_id}" class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                    <div class="ml-3 text-sm flex-1">
                        <span class="font-medium text-gray-900">${comp.comp_nombre_corto}</span>
                        <span class="block text-gray-500 text-xs">${comp.comp_nombre_unidad_competencia || ''}</span>
                    </div>
                </label>
            `;
        });

        competenciasContainer.innerHTML = html;
    };

    if (compSearch) {
        compSearch.addEventListener('input', (e) => renderCompetencias(e.target.value));
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
            formData.append('controller', 'instructor');
            formData.append('action', 'store');

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
                    else alert(result.error || 'Error al guardar');
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error(error);
                if (window.NotificationService) NotificationService.showError('Error de red o de servidor');
                submitBtn.disabled = false;
            }
        });
    }

    loadAllCompetencias();
});
