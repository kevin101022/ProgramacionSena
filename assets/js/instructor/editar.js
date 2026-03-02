document.addEventListener('DOMContentLoaded', async () => {
    console.log('=== Instructor Edit JS Start ===');

    // Selectores
    const form = document.getElementById('instructorForm');
    const numeroDocInput = document.getElementById('numero_documento');
    const compSearch = document.getElementById('compSearch');
    const competenciasContainer = document.getElementById('competenciasContainer');
    const instIdInput = document.getElementById('inst_id');
    const submitBtn = document.getElementById('submitBtn');

    if (!instIdInput) {
        console.error('CRITICAL: inst_id input not found');
        return;
    }
    const instId = instIdInput.value;
    const timeStamp = new Date().getTime();

    let currentCompetencias = [];
    let allCompetencias = [];

    // Helper para Fetch con Cache Busting
    const fetchAPI = async (url) => {
        const separator = url.includes('?') ? '&' : '?';
        const fullUrl = `${url}${separator}cb=${timeStamp}`;
        console.log('Fetching:', fullUrl);
        const response = await fetch(fullUrl, {
            headers: { 'Accept': 'application/json' }
        });
        if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
        return await response.json();
    };

    const loadAllCompetencias = async () => {
        try {
            allCompetencias = await fetchAPI('../../routing.php?controller=competencia&action=index');
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
            const isSelected = currentCompetencias.some(cc =>
                cc.competxprograma_competencia_comp_id == comp.comp_id || cc.comp_id == comp.comp_id
            );

            html += `
                <label class="flex items-center w-full cursor-pointer hover:bg-gray-100 p-2 rounded transition-colors border border-transparent hover:border-gray-200">
                    <input type="checkbox" name="competencias[]" value="${comp.comp_id}" ${isSelected ? 'checked' : ''} class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                    <div class="ml-3 text-sm flex-1">
                        <span class="font-medium text-gray-900">${comp.comp_nombre_corto}</span>
                        <span class="block text-gray-500 text-xs">${comp.comp_nombre_unidad_competencia || ''}</span>
                    </div>
                </label>
            `;
        });

        competenciasContainer.innerHTML = html;
    };

    const init = async () => {
        try {
            console.log('Starting Initialization...');

            // 1. Cargar Catálogo de Competencias
            await loadAllCompetencias();
            console.log('Competencies catalog loaded');

            // 2. Cargar datos del Instructor y sus competencias actuales
            const [instructor, competenciasData] = await Promise.all([
                fetchAPI(`../../routing.php?controller=instructor&action=show&id=${instId}`),
                fetchAPI(`../../routing.php?controller=instructor&action=getCompetencias&id=${instId}`)
            ]);

            currentCompetencias = Array.isArray(competenciasData) ? competenciasData : [];
            console.log('Instructor loaded:', instructor);

            // 3. Poblar formulario básico
            if (numeroDocInput) numeroDocInput.value = instructor.inst_id || '';
            document.getElementById('inst_nombres').value = instructor.inst_nombres || '';
            document.getElementById('inst_apellidos').value = instructor.inst_apellidos || '';
            document.getElementById('inst_correo').value = instructor.inst_correo || '';
            document.getElementById('inst_telefono').value = instructor.inst_telefono || '';
            document.getElementById('inst_password').value = instructor.inst_password || '';

            // 4. Renderizar competencias
            renderCompetencias();

            console.log('Initialization Complete');

        } catch (error) {
            console.error('Initialization Failed:', error);
            if (typeof NotificationService !== 'undefined') {
                NotificationService.showError('No se pudieron cargar los datos del instructor');
            }
        }
    };

    // Eventos
    if (compSearch) {
        compSearch.addEventListener('input', (e) => renderCompetencias(e.target.value));
    }

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitBtn.disabled = true;

            const formData = new FormData(form);

            try {
                const response = await fetch('../../routing.php?controller=instructor&action=update', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });

                const result = await response.json();
                if (response.ok && !result.error) {
                    NotificationService.showSuccess('Instructor actualizado con éxito');
                    setTimeout(() => window.location.href = `ver.php?id=${instId}`, 1500);
                } else {
                    NotificationService.showError(result.error || 'Error al actualizar');
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error('Submit Error:', error);
                NotificationService.showError('Error de conexión con el servidor');
                submitBtn.disabled = false;
            }
        });
    }

    // Iniciar
    init();
});
