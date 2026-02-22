document.addEventListener('DOMContentLoaded', () => {
    const loadingState = document.getElementById('loadingState');
    const instructorDetails = document.getElementById('instructorDetails');
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');

    const instId = new URLSearchParams(window.location.search).get('id');

    const init = async () => {
        if (!instId) {
            showError('ID de instructor no proporcionado');
            return;
        }

        try {
            const response = await fetch(`../../routing.php?controller=instructor&action=show&id=${instId}`, {
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Error al cargar los datos');
            }

            populateBasicInfo(data);

            // Load related data in parallel
            await Promise.all([
                loadFichasLider(),
                loadAsignaciones(),
                loadCompetencias()
            ]);

            showDetails();
        } catch (error) {
            console.error('Error:', error);
            showError(error.message);
        }
    };

    const populateBasicInfo = (inst) => {
        document.getElementById('instNombreCompleto').textContent = `${inst.inst_nombres} ${inst.inst_apellidos}`;
        document.getElementById('instIniciales').textContent = `${inst.inst_nombres[0]}${inst.inst_apellidos[0]}`;
        document.getElementById('instCorreo').textContent = inst.inst_correo || 'Sin correo';
        document.getElementById('instTelefono').textContent = inst.inst_telefono || 'Sin teléfono';
        document.getElementById('instCentro').textContent = inst.cent_nombre || 'Sin centro asignado';

        document.getElementById('editBtn').href = `editar.php?id=${inst.inst_id}`;
        document.getElementById('deleteBtn').onclick = () => deleteInstructor(inst.inst_id);
    };

    // ── Fichas donde el instructor es líder ───────────────────
    const loadFichasLider = async () => {
        try {
            const res = await fetch(`../../routing.php?controller=ficha&action=index`, {
                headers: { 'Accept': 'application/json' }
            });
            const fichas = await res.json();
            const misFichas = Array.isArray(fichas) ? fichas.filter(f => f.instructor_inst_id == instId) : [];

            const listContainer = document.getElementById('fichasList');
            const countLabel = document.getElementById('countFichas');
            if (countLabel) countLabel.textContent = misFichas.length;

            if (!listContainer) return;

            if (misFichas.length === 0) {
                listContainer.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">No tiene fichas asignadas como líder.</p>';
                return;
            }

            listContainer.innerHTML = '';
            misFichas.forEach(f => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors cursor-pointer group';
                item.onclick = () => window.location.href = `../ficha/ver.php?id=${f.fich_id}`;
                item.innerHTML = `
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center text-sena-green font-bold shadow-sm">
                            ${f.fich_id.toString().slice(-2)}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Ficha ${f.fich_id}</p>
                            <p class="text-xs text-gray-500">${f.titpro_nombre || f.prog_denominacion || 'Programa no definido'}</p>
                        </div>
                    </div>
                    <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-gray-400 group-hover:translate-x-1 transition-transform"></ion-icon>
                `;
                listContainer.appendChild(item);
            });
        } catch (error) {
            console.error('Error cargando fichas lider:', error);
        }
    };

    // ── Asignaciones del instructor ───────────────────────────
    const loadAsignaciones = async () => {
        const container = document.getElementById('asignacionesList');
        const countEl = document.getElementById('countAsignaciones');
        if (!container) return;

        try {
            const res = await fetch(`../../routing.php?controller=instructor&action=getAsignaciones&id=${instId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            const asignaciones = Array.isArray(data) ? data : [];

            if (countEl) countEl.textContent = asignaciones.length;

            if (asignaciones.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">No tiene asignaciones registradas.</p>';
                return;
            }

            container.innerHTML = '';
            asignaciones.forEach(a => {
                const item = document.createElement('div');
                item.className = 'p-4 bg-gray-50 rounded-xl border border-gray-100';
                item.innerHTML = `
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-sm font-bold text-gray-900">Ficha ${a.fich_id} — ${a.prog_denominacion || 'Programa'}</p>
                            <p class="text-xs text-gray-600 mt-1"><span class="font-semibold">Competencia:</span> ${a.comp_nombre || 'N/A'}</p>
                            <p class="text-xs text-gray-500 mt-0.5"><span class="font-semibold">Ambiente:</span> ${a.amb_nombre || 'N/A'} · ${a.sede_nombre || ''}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-gray-400">${a.asig_fecha_ini || ''}</p>
                            <p class="text-xs text-gray-400">${a.asig_fecha_fin || ''}</p>
                        </div>
                    </div>
                `;
                container.appendChild(item);
            });
        } catch (error) {
            console.error('Error cargando asignaciones:', error);
            container.innerHTML = '<p class="text-sm text-red-400 text-center">Error al cargar asignaciones.</p>';
        }
    };

    // ── Competencias habilitadas del instructor ────────────────
    const loadCompetencias = async () => {
        const container = document.getElementById('competenciasList');
        const countEl = document.getElementById('countCompetencias');
        if (!container) return;

        try {
            const res = await fetch(`../../routing.php?controller=instructor&action=getCompetencias&id=${instId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            const competencias = Array.isArray(data) ? data : [];

            if (countEl) countEl.textContent = competencias.length;

            if (competencias.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">No tiene competencias habilitadas.</p>';
                return;
            }

            container.innerHTML = '';
            competencias.forEach(c => {
                const item = document.createElement('div');
                item.className = 'p-3 bg-green-50 rounded-xl border border-green-100 flex items-start gap-3';
                item.innerHTML = `
                    <div class="w-8 h-8 rounded-lg bg-sena-green flex items-center justify-center flex-shrink-0">
                        <ion-icon src="../../assets/ionicons/ribbon-outline.svg" class="text-white text-sm"></ion-icon>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">${c.comp_nombre || 'Competencia'}</p>
                        <p class="text-xs text-gray-500">${c.prog_denominacion || 'Programa'} · Vigencia: ${c.hab_vigencia || 'N/A'}</p>
                    </div>
                `;
                container.appendChild(item);
            });
        } catch (error) {
            console.error('Error cargando competencias:', error);
            container.innerHTML = '<p class="text-sm text-red-400 text-center">Error al cargar competencias.</p>';
        }
    };

    const deleteInstructor = async (id) => {
        if (!confirm('¿Está seguro de eliminar a este instructor? Esta acción no se puede deshacer.')) return;

        try {
            const response = await fetch(`../../routing.php?controller=instructor&action=destroy&id=${id}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (response.ok) {
                NotificationService.showSuccess('Instructor eliminado correctamente');
                setTimeout(() => window.location.href = 'index.php', 1500);
            } else {
                const data = await response.json();
                NotificationService.showError(data.error || 'Error al eliminar');
            }
        } catch (error) {
            NotificationService.showError('Error de conexión');
        }
    };

    const showDetails = () => {
        loadingState.style.display = 'none';
        instructorDetails.style.display = 'grid';
        errorState.style.display = 'none';
    };

    const showError = (msg) => {
        loadingState.style.display = 'none';
        instructorDetails.style.display = 'none';
        errorState.style.display = 'block';
        errorMessage.textContent = msg;
    };

    init();
});
