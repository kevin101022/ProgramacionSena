// Instructor Create JavaScript
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('instructorForm');
    const centroSelect = document.getElementById('centro_id');
    const programaSelect = document.getElementById('programa_id');
    const competenciasContainer = document.getElementById('competenciasContainer');
    const submitBtn = document.getElementById('submitBtn');

    if (!centroSelect || !programaSelect) {
        console.error('Error: Elementos select requeridos no encontrados en el DOM.');
        return;
    }

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
            const programas = await response.json();

            programaSelect.innerHTML = '<option value="">Seleccione un programa...</option>';
            programas.forEach(prog => {
                const opt = document.createElement('option');
                opt.value = prog.prog_codigo;
                opt.textContent = `${prog.prog_denominacion} - ${prog.titpro_nombre}`;
                programaSelect.appendChild(opt);
            });
        } catch (error) {
            console.error('Error al cargar programas:', error);
        }
    };

    const loadCompetencias = async (progId) => {
        if (!progId) {
            competenciasContainer.innerHTML = '<p class="text-gray-400 text-sm italic text-center py-4">Seleccione un programa para cargar competencias</p>';
            return;
        }

        try {
            competenciasContainer.innerHTML = '<p class="text-blue-500 text-sm text-center py-4">Cargando competencias...</p>';
            const response = await fetch(`../../routing.php?controller=competencia_programa&action=getByPrograma&prog_id=${progId}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Error al obtener competencias');
            const competencias = await response.json();

            competenciasContainer.innerHTML = '';
            if (competencias.length === 0) {
                competenciasContainer.innerHTML = '<p class="text-orange-500 text-sm text-center py-4">Este programa no tiene competencias asociadas</p>';
                return;
            }

            competencias.forEach(comp => {
                const div = document.createElement('div');
                div.className = 'flex items-center p-2 hover:bg-white rounded mb-1';
                div.innerHTML = `
                    <label class="flex items-center w-full cursor-pointer">
                        <input type="checkbox" name="competencias[]" value="${comp.comp_id}" class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        <div class="ml-3 text-sm">
                            <span class="font-medium text-gray-900">${comp.comp_nombre_corto}</span>
                            <span class="block text-gray-500 text-xs">${comp.comp_nombre_unidad_competencia || ''}</span>
                        </div>
                    </label>
                `;
                competenciasContainer.appendChild(div);
            });

        } catch (error) {
            console.error('Error al cargar competencias:', error);
            competenciasContainer.innerHTML = '<p class="text-red-500 text-sm text-center py-4">Error al cargar competencias</p>';
        }
    };

    // ── Events ────────────────────────────────────────────────

    programaSelect.addEventListener('change', (e) => {
        loadCompetencias(e.target.value);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        submitBtn.disabled = true;
        const formData = new FormData(form);

        // Append program ID if validation of competency selection is needed logic here
        // Current logic: basic form data covers it, but we need to ensure competencies[] and programa_id are handled by controller
        formData.append('programa_id', programaSelect.value); // Add selected program ID explicitly

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

    // ── Init ──────────────────────────────────────────────────
    loadCentros();
    loadProgramas();
});
