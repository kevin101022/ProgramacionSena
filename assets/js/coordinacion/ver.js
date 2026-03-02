/**
 * Detail View Manager - Coordinacion Premium
 */
document.addEventListener('DOMContentLoaded', () => {
    const loadingState = document.getElementById('loadingState');
    const coordinacionDetails = document.getElementById('coordinacionDetails');
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');

    const urlParams = new URLSearchParams(window.location.search);
    const coordId = urlParams.get('id');

    if (!coordId) {
        showError('No se proporcionó el ID de la coordinación.');
        return;
    }

    async function loadDetail() {
        try {
            const response = await fetch(`../../routing.php?controller=coordinacion&action=show&id=${coordId}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Error al cargar datos del servidor');

            const data = await response.json();
            if (data.error) throw new Error(data.error);

            renderDetail(data);
            await loadProgramas();
        } catch (error) {
            console.error(error);
            showError(error.message);
        }
    }

    function renderDetail(data) {
        const detCoordNombre = document.getElementById('detCoordNombre');
        const detCentroPertenece = document.getElementById('detCentroPertenece');
        const detCoordNombreCoordinador = document.getElementById('detCoordNombreCoordinador');
        const detCoordDoc = document.getElementById('detCoordDoc');
        const detCoordCorreo = document.getElementById('detCoordCorreo');
        const vacanteState = document.getElementById('vacanteState');
        const coordinadorInfo = document.getElementById('coordinadorInfo');

        detCoordNombre.textContent = data.coord_descripcion;
        detCentroPertenece.lastElementChild.textContent = data.cent_nombre || 'Centro no especificado';

        if (data.numero_documento) {
            detCoordNombreCoordinador.textContent = data.coord_nombre_coordinador;
            detCoordDoc.textContent = data.numero_documento;
            detCoordCorreo.textContent = data.coord_correo;

            vacanteState.style.display = 'none';
            coordinadorInfo.style.display = 'grid';
        } else {
            vacanteState.style.display = 'block';
            coordinadorInfo.style.display = 'none';
        }

        const editBtn = document.getElementById('editBtn');
        if (editBtn) {
            editBtn.onclick = () => {
                if (window.coordinacionManager) {
                    window.coordinacionManager.openModal(data);
                }
            };
        }

        const deleteBtn = document.getElementById('deleteBtn');
        if (deleteBtn) {
            deleteBtn.onclick = () => {
                if (window.NotificationService) {
                    NotificationService.showConfirm('¿Estás seguro de que deseas desvincular a este coordinador de esta área?', async () => {
                        try {
                            const response = await fetch(`../../routing.php?controller=coordinacion&action=desvincular&id=${coordId}`);
                            if (response.ok) {
                                NotificationService.showSuccess('Coordinador desvinculado.');
                                location.reload();
                            } else {
                                NotificationService.showError('Error al desvincular.');
                            }
                        } catch (e) {
                            NotificationService.showError('Error de red.');
                        }
                    });
                }
            };
            if (!data.numero_documento) deleteBtn.style.display = 'none';
        }

        loadingState.style.display = 'none';
        coordinacionDetails.style.display = 'grid';
    }

    async function loadProgramas() {
        try {
            const response = await fetch(`../../routing.php?controller=coordinacion&action=getProgramas&id=${coordId}`);
            if (response.ok) {
                const programas = await response.json();
                renderProgramas(programas);
            }
        } catch (e) {
            console.error('Error cargando programas', e);
        }
    }

    function renderProgramas(programas) {
        const countEl = document.getElementById('countProgramas');
        const listEl = document.getElementById('programasList');
        const emptyEl = document.getElementById('noProgramas');

        if (countEl) countEl.textContent = programas.length;

        if (!listEl) return;
        listEl.innerHTML = '';

        if (programas.length === 0) {
            emptyEl.classList.remove('hidden');
        } else {
            emptyEl.classList.add('hidden');
            programas.forEach(p => {
                const card = document.createElement('div');
                card.className = 'p-4 bg-slate-50 border border-slate-100 rounded-2xl flex items-start gap-3 hover:bg-white hover:shadow-md transition-all group';
                card.innerHTML = `
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-black text-slate-800 leading-tight">${p.prog_denominacion}</p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">CÓDIGO: ${p.prog_codigo}</p>
                    </div>
                `;
                listEl.appendChild(card);
            });
        }
    }

    function showError(msg) {
        loadingState.style.display = 'none';
        errorState.style.display = 'block';
        errorMessage.textContent = msg;
    }

    loadDetail();
});
