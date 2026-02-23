// Instructor Edit JavaScript - Version: 2.1 (Ultra Robust)
document.addEventListener('DOMContentLoaded', async () => {
    console.log('=== Instructor Edit JS Start ===');

    // Selectores
    const form = document.getElementById('instructorForm');
    const centroSelect = document.getElementById('centro_id');
    const programaSelect = document.getElementById('programa_id');
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

    const loadCentros = async () => {
        try {
            const centros = await fetchAPI('../../routing.php?controller=instructor&action=getCentros');
            console.log('Centros data:', centros);

            centroSelect.innerHTML = '<option value="">Seleccione un centro de formación...</option>';
            if (Array.isArray(centros)) {
                centros.forEach(centro => {
                    const option = document.createElement('option');
                    option.value = centro.cent_id;
                    option.textContent = centro.cent_nombre;
                    centroSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('loadCentros Error:', error);
        }
    };

    const loadProgramas = async () => {
        try {
            const programas = await fetchAPI('../../routing.php?controller=programa&action=index');
            console.log('Programas data:', programas);

            programaSelect.innerHTML = '<option value="">Seleccione un programa...</option>';
            if (Array.isArray(programas)) {
                programas.forEach(prog => {
                    const opt = document.createElement('option');
                    opt.value = prog.prog_codigo;
                    opt.textContent = `${prog.prog_denominacion} - ${prog.titpro_nombre}`;
                    programaSelect.appendChild(opt);
                });
            }
        } catch (error) {
            console.error('loadProgramas Error:', error);
        }
    };

    const loadCompetencias = async (progId) => {
        if (!progId) {
            competenciasContainer.innerHTML = '<p class="text-gray-400 text-sm italic text-center py-4">Seleccione un programa para cargar competencias</p>';
            return;
        }

        try {
            competenciasContainer.innerHTML = '<p class="text-blue-500 text-sm text-center py-4">Cargando competencias...</p>';
            const competencias = await fetchAPI(`../../routing.php?controller=competencia_programa&action=getByPrograma&prog_id=${progId}`);

            competenciasContainer.innerHTML = '';
            if (!Array.isArray(competencias) || competencias.length === 0) {
                competenciasContainer.innerHTML = '<p class="text-orange-500 text-sm text-center py-4">Este programa no tiene competencias asociadas</p>';
                return;
            }

            competencias.forEach(comp => {
                const isSelected = currentCompetencias.some(cc => cc.comp_id == comp.comp_id);
                const div = document.createElement('div');
                div.className = 'flex items-center p-2 hover:bg-white rounded mb-1';
                div.innerHTML = `
                    <label class="flex items-center w-full cursor-pointer">
                        <input type="checkbox" name="competencias[]" value="${comp.comp_id}" ${isSelected ? 'checked' : ''} class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        <div class="ml-3 text-sm">
                            <span class="font-medium text-gray-900">${comp.comp_nombre_corto}</span>
                            <span class="block text-gray-500 text-xs">${comp.comp_nombre_unidad_competencia || ''}</span>
                        </div>
                    </label>
                `;
                competenciasContainer.appendChild(div);
            });
        } catch (error) {
            console.error('loadCompetencias Error:', error);
            competenciasContainer.innerHTML = '<p class="text-red-500 text-sm text-center py-4">Error al cargar competencias</p>';
        }
    };

    const init = async () => {
        try {
            console.log('Starting Initialization...');

            // 1. Cargar Catálogos
            await Promise.all([loadCentros(), loadProgramas()]);
            console.log('Catalogs loaded successfully');

            // 2. Cargar datos del Instructor
            const [instructor, competenciasData] = await Promise.all([
                fetchAPI(`../../routing.php?controller=instructor&action=show&id=${instId}`),
                fetchAPI(`../../routing.php?controller=instructor&action=getCompetencias&id=${instId}`)
            ]);

            currentCompetencias = Array.isArray(competenciasData) ? competenciasData : [];
            console.log('Instructor loaded:', instructor);

            // 3. Poblar formulario básico
            document.getElementById('inst_nombres').value = instructor.inst_nombres || '';
            document.getElementById('inst_apellidos').value = instructor.inst_apellidos || '';
            document.getElementById('inst_correo').value = instructor.inst_correo || '';
            document.getElementById('inst_telefono').value = instructor.inst_telefono || '';
            document.getElementById('inst_password').value = instructor.inst_password || '';

            // 4. Mapear Centro ID (Soportando mayúsculas/minúsculas de DB)
            const centroId = instructor.cent_id || instructor.CENTRO_FORMACION_cent_id || instructor.centro_formacion_cent_id;
            if (centroId) {
                console.log('Auto-selecting Centro:', centroId);
                centroSelect.value = centroId;
            }

            // 5. Mapear Programa y Habilitaciones actuales
            if (currentCompetencias.length > 0) {
                const progId = currentCompetencias[0].prog_codigo || currentCompetencias[0].PROGRAMA_prog_id;
                console.log('Auto-selecting Programa:', progId);
                if (progId) {
                    programaSelect.value = progId;
                    await loadCompetencias(progId);
                }
            }

            console.log('Initialization Complete');

        } catch (error) {
            console.error('Initialization Failed:', error);
            if (typeof NotificationService !== 'undefined') {
                NotificationService.showError('No se pudieron cargar los datos del instructor');
            }
        }
    };

    // Eventos
    if (programaSelect) {
        programaSelect.addEventListener('change', (e) => loadCompetencias(e.target.value));
    }

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitBtn.disabled = true;

            const formData = new FormData(form);
            formData.append('programa_id', programaSelect.value);

            try {
                const response = await fetch('../../routing.php?controller=instructor&action=update', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });

                const result = await response.json();
                if (response.ok) {
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
