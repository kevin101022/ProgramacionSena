document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');

    const loadingState = document.getElementById('loadingState');
    const detailsSection = document.getElementById('habilitacionDetails');
    const errorState = document.getElementById('errorState');
    const deleteBtn = document.getElementById('deleteBtn');

    if (!id) {
        showError('No se proporcionó un ID de habilitación.');
        return;
    }

    const loadHabilitacion = async () => {
        try {
            const res = await fetch(`../../routing.php?controller=instru_competencia&action=show&id=${id}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (!res.ok || data.error) {
                showError(data.error || 'Habilitación no encontrada');
                return;
            }

            // Datos principales y de contacto
            document.getElementById('detInstructor').textContent = `${data.inst_nombres || ''} ${data.inst_apellidos || ''}`;
            document.getElementById('detDocumento').textContent = data.instructor_inst_id || 'N/A';
            document.getElementById('detInstructorFull').textContent = `${data.inst_nombres || ''} ${data.inst_apellidos || ''}`;
            document.getElementById('detCorreo').textContent = data.inst_correo || 'N/A';
            document.getElementById('detTelefono').textContent = data.inst_telefono || 'N/A';
            document.getElementById('detProfesion').textContent = data.profesion || 'No registrada';
            document.getElementById('detEspecializacion').textContent = data.especializacion || 'No registrada';
            document.getElementById('detCentro').textContent = data.cent_nombre || 'N/A';
            document.getElementById('detCompetencia').textContent = data.comp_nombre_corto || 'N/A';

            // Cargar asignaciones (horario)
            loadAssignments(data.instructor_inst_id);
            // Cargar fichas lideradas
            loadFichasLider(data.instructor_inst_id);

            if (loadingState) loadingState.style.display = 'none';
            if (detailsSection) detailsSection.style.display = '';
        } catch (e) {
            console.error(e);
            showError('Error de conexión al cargar la habilitación.');
        }
    };

    const loadFichasLider = async (instructorId) => {
        const section = document.getElementById('fichasLiderSection');
        const container = document.getElementById('fichasLiderContainer');
        if (!section || !container) return;

        try {
            const response = await fetch(`../../routing.php?controller=instructor&action=getFichasLider&id=${instructorId}`);
            let fichas = await response.json();

            // Filtrar solo fichas que tengan datos reales y válidos para evitar "ghost cards"
            const validFichas = (Array.isArray(fichas) ? fichas : []).filter(f =>
                f && f.fich_id && f.prog_denominacion
            );

            if (validFichas.length === 0) {
                section.style.display = 'none';
                return;
            }

            section.style.display = 'block';
            container.innerHTML = validFichas.map(f => `
                <div class="p-5 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-sena-green transition-all group">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-xs font-black text-sena-green uppercase tracking-wider mb-1">FICHA ${f.fich_id}</p>
                            <h4 class="font-bold text-gray-900 leading-tight">${f.prog_denominacion}</h4>
                        </div>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase">${f.fich_jornada || 'N/A'}</span>
                    </div>
                    <div class="space-y-2 pt-3 border-t border-gray-50">
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                            <span>${f.sede_nombre || 'Sede no asignada'}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                            <span>Lectiva: ${f.fich_fecha_ini_lectiva || '?'} al ${f.fich_fecha_fin_lectiva || '?'}</span>
                        </div>
                    </div>
                </div>
            `).join('');

        } catch (error) {
            console.error('Error loading lead fichas:', error);
            section.style.display = 'none';
        }
    };

    const loadAssignments = async (instructorId) => {
        const tableBody = document.getElementById('assignmentsTableBody');
        if (!tableBody) return;

        try {
            const response = await fetch(`../../routing.php?controller=instructor&action=getAsignaciones&id=${instructorId}`);
            const assignments = await response.json();

            if (!assignments || assignments.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">
                            El instructor no tiene asignaciones programadas actualmente.
                        </td>
                    </tr>`;
                return;
            }

            tableBody.innerHTML = assignments.map(asig => `
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900 text-sm">${asig.prog_denominacion}</p>
                        <p class="text-xs text-gray-500 font-medium">Ficha: ${asig.fich_id} (${asig.fich_jornada})</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-700 font-medium">${asig.comp_nombre}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900 font-bold">${asig.amb_nombre}</p>
                        <p class="text-xs text-gray-500">${asig.sede_nombre}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-100">
                                Del: ${asig.asig_fecha_ini}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                Al: ${asig.asig_fecha_fin}
                            </span>
                        </div>
                    </td>
                </tr>
            `).join('');

        } catch (error) {
            console.error('Error loading assignments:', error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-red-500 text-sm italic">
                        No se pudieron cargar las asignaciones en este momento.
                    </td>
                </tr>`;
        }
    };

    const showError = (msg) => {
        if (loadingState) loadingState.style.display = 'none';
        if (errorState) {
            errorState.style.display = '';
            const errorMsg = document.getElementById('errorMessage');
            if (errorMsg) errorMsg.textContent = msg;
        }
    };

    if (deleteBtn) {
        deleteBtn.onclick = async () => {
            if (!confirm('¿Está seguro de eliminar esta habilitación?')) return;
            try {
                const res = await fetch(`../../routing.php?controller=instru_competencia&action=destroy&id=${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await res.json();

                if (res.ok) {
                    NotificationService.showSuccess('Habilitación eliminada correctamente');
                    setTimeout(() => window.location.href = 'index.php', 1000);
                } else {
                    NotificationService.showError(result.error || 'Error al eliminar');
                }
            } catch (e) {
                NotificationService.showError('Error de conexión');
            }
        };
    }

    loadHabilitacion();
});
