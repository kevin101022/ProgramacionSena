document.addEventListener('DOMContentLoaded', () => {
    const loadingState = document.getElementById('loadingState');
    const detailsContainer = document.getElementById('coordinacionDetails');
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');

    const coordId = new URLSearchParams(window.location.search).get('id');

    // UI Elements for Modal
    const modal = document.getElementById('coordinacionModal');
    const form = document.getElementById('coordinacionForm');
    const centroSelect = document.getElementById('centro_id');

    const init = async () => {
        if (!coordId) {
            showError('ID de coordinación no proporcionado');
            return;
        }

        try {
            // Cargar centros para el modal primero
            await loadCentros();

            // Cargar datos de la coordinación
            await loadCoordinacionData();

            // Cargar programas vinculados
            await loadProgramas();

        } catch (error) {
            console.error('Error:', error);
            showError(error.message);
        }
    };

    const loadCentros = async () => {
        try {
            const response = await fetch('../../routing.php?controller=centro_formacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const centros = await response.json();
            centroSelect.innerHTML = '<option value="">Seleccione un centro...</option>';
            centros.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.cent_id;
                opt.textContent = c.cent_nombre;
                centroSelect.appendChild(opt);
            });
        } catch (err) {
            console.warn('No se pudieron cargar los centros');
        }
    };

    const loadCoordinacionData = async () => {
        const response = await fetch(`../../routing.php?controller=coordinacion&action=index`, {
            headers: { 'Accept': 'application/json' }
        });
        const coordinaciones = await response.json();
        const coord = coordinaciones.find(c => c.coord_id == coordId);

        if (!coord) {
            throw new Error('Coordinación no encontrada');
        }

        populateUI(coord);
        showDetails();
    };

    const loadProgramas = async () => {
        try {
            const response = await fetch(`../../routing.php?controller=coordinacion&action=getProgramas&id=${coordId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const programas = await response.json();

            const list = document.getElementById('programasList');
            const noData = document.getElementById('noProgramas');
            const countEl = document.getElementById('countProgramas');

            countEl.textContent = programas.length;

            if (programas.length === 0) {
                list.innerHTML = '';
                noData.classList.remove('hidden');
                return;
            }

            noData.classList.add('hidden');
            list.innerHTML = '';

            programas.forEach(p => {
                const item = document.createElement('div');
                item.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer group';
                item.onclick = () => window.location.href = `../programa/ver.php?id=${p.prog_codigo}`;

                item.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white rounded-lg shadow-sm group-hover:bg-sena-green/10 transition-colors">
                            <ion-icon src="../../assets/ionicons/journal-outline.svg" class="text-sena-green text-lg"></ion-icon>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">${p.prog_denominacion}</p>
                            <p class="text-[10px] text-gray-400 uppercase tracking-tighter">Código: ${p.prog_codigo}</p>
                        </div>
                    </div>
                    <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-gray-300 group-hover:text-sena-green transition-all"></ion-icon>
                `;
                list.appendChild(item);
            });
        } catch (err) {
            console.error('Error loading programs:', err);
        }
    };

    const populateUI = (c) => {
        document.getElementById('detCoordNombre').textContent = c.coord_descripcion;
        document.getElementById('detCentroPertenece').textContent = c.cent_nombre || 'Centro no asignado';

        document.getElementById('editBtn').onclick = () => openEditModal(c);
        document.getElementById('deleteBtn').onclick = () => handleDelete(c.coord_id);
    };

    const openEditModal = (c) => {
        document.getElementById('modalTitle').textContent = 'Editar Coordinación';
        document.getElementById('coord_id').value = c.coord_id;
        document.getElementById('coord_nombre').value = c.coord_descripcion;
        document.getElementById('centro_id').value = c.centro_formacion_cent_id || '';
        modal.classList.add('show');
    };

    const handleDelete = async (id) => {
        NotificationService.showConfirm('¿Realmente desea eliminar esta coordinación académica?', async () => {
            try {
                const response = await fetch(`../../routing.php?controller=coordinacion&action=destroy&id=${id}`);
                if (response.ok) {
                    NotificationService.showSuccess('Coordinación eliminada');
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    NotificationService.showError('No se pudo eliminar el registro');
                }
            } catch (err) {
                NotificationService.showError('Error de red');
            }
        });
    };

    // Modal Control
    document.getElementById('closeModal').onclick = () => modal.classList.remove('show');
    document.getElementById('cancelBtn').onclick = () => modal.classList.remove('show');

    form.onsubmit = async (e) => {
        e.preventDefault();
        const data = {
            coord_id: document.getElementById('coord_id').value,
            coord_descripcion: document.getElementById('coord_nombre').value,
            centro_formacion_cent_id: document.getElementById('centro_id').value
        };

        try {
            const response = await fetch(`../../routing.php?controller=coordinacion&action=update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                NotificationService.showSuccess('Coordinación actualizada');
                modal.classList.remove('show');
                loadCoordinacionData(); // Refrescar vista
            } else {
                NotificationService.showError('Error al guardar cambios');
            }
        } catch (err) {
            NotificationService.showError('Error de conexión');
        }
    };

    const showDetails = () => {
        loadingState.style.display = 'none';
        detailsContainer.style.display = 'grid';
        errorState.style.display = 'none';
    };

    const showError = (msg) => {
        loadingState.style.display = 'none';
        detailsContainer.style.display = 'none';
        errorState.style.display = 'block';
        errorMessage.textContent = msg;
    };

    init();
});
