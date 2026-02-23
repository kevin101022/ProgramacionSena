document.addEventListener('DOMContentLoaded', () => {
    const loadingState = document.getElementById('loadingState');
    const centroDetails = document.getElementById('centroDetails');
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');

    const centId = new URLSearchParams(window.location.search).get('id');

    // UI Elements for Modal
    const modal = document.getElementById('centroModal');
    const form = document.getElementById('centroForm');

    const init = async () => {
        if (!centId) {
            showError('ID de centro no proporcionado');
            return;
        }

        try {
            const response = await fetch(`../../routing.php?controller=centro_formacion&action=show&id=${centId}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error('Centro no encontrado');
            }

            const centro = await response.json();

            populateUI(centro);

            // Load related data in parallel
            await Promise.all([
                loadInstructores(),
                loadCoordinaciones()
            ]);

            showDetails();

        } catch (error) {
            console.error('Error:', error);
            showError(error.message);
        }
    };

    const loadInstructores = async () => {
        const container = document.getElementById('instructoresList');
        const countEl = document.getElementById('countInstructores');
        const empty = document.getElementById('noInstructores');
        if (!container) return;

        try {
            const res = await fetch(`../../routing.php?controller=centro_formacion&action=getInstructores&id=${centId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            const instructores = Array.isArray(data) ? data : [];

            if (countEl) countEl.textContent = instructores.length;

            if (instructores.length === 0) {
                container.innerHTML = '';
                if (empty) empty.classList.remove('hidden');
                return;
            }

            if (empty) empty.classList.add('hidden');
            container.innerHTML = '';
            instructores.forEach(ins => {
                const item = document.createElement('div');
                item.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100 group hover:border-sena-green/30 transition-all cursor-pointer';
                item.onclick = () => window.location.href = `../instructor/ver.php?id=${ins.inst_id}`;

                const iniciales = `${ins.inst_nombres[0]}${ins.inst_apellidos[0]}`;

                item.innerHTML = `
                    <div class="w-10 h-10 rounded-full bg-sena-green text-white flex items-center justify-center text-xs font-bold shadow-sm">
                        ${iniciales}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate group-hover:text-sena-green transition-colors">${ins.inst_nombres} ${ins.inst_apellidos}</p>
                        <p class="text-[10px] text-gray-500 truncate">${ins.inst_correo || 'Sin correo'}</p>
                    </div>
                    <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-gray-300 group-hover:text-sena-green transition-colors text-xs"></ion-icon>
                `;
                container.appendChild(item);
            });
        } catch (err) {
            console.error('Error al cargar instructores:', err);
        }
    };

    const loadCoordinaciones = async () => {
        const container = document.getElementById('coordinacionesList');
        const countEl = document.getElementById('countCoordinaciones');
        const empty = document.getElementById('noCoordinaciones');
        if (!container) return;

        try {
            const res = await fetch(`../../routing.php?controller=centro_formacion&action=getCoordinaciones&id=${centId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            const coordinaciones = Array.isArray(data) ? data : [];

            if (countEl) countEl.textContent = coordinaciones.length;

            if (coordinaciones.length === 0) {
                container.innerHTML = '';
                if (empty) empty.classList.remove('hidden');
                return;
            }

            if (empty) empty.classList.add('hidden');
            container.innerHTML = '';
            coordinaciones.forEach(c => {
                const item = document.createElement('div');
                item.className = 'p-3 bg-gray-50 rounded-lg border border-gray-100 flex items-center gap-3';
                item.innerHTML = `
                    <div class="w-8 h-8 rounded bg-white flex items-center justify-center text-sena-green shadow-sm border border-gray-50">
                        <ion-icon src="../../assets/ionicons/git-network-outline.svg"></ion-icon>
                    </div>
                    <p class="text-sm font-medium text-gray-700">${c.coord_descripcion}</p>
                `;
                container.appendChild(item);
            });
        } catch (err) {
            console.error('Error al cargar coordinaciones:', err);
        }
    };

    const populateUI = (c) => {
        document.getElementById('detCentroNombre').textContent = c.cent_nombre;
        document.getElementById('detCentroId').textContent = String(c.cent_id).padStart(3, '0');

        document.getElementById('editBtn').onclick = () => openEditModal(c);
        document.getElementById('deleteBtn').onclick = () => handleDelete(c.cent_id);
    };

    const openEditModal = (c) => {
        document.getElementById('modalTitle').textContent = 'Editar Centro de Formación';
        document.getElementById('cent_id').value = c.cent_id;
        document.getElementById('cent_nombre').value = c.cent_nombre;
        modal.classList.add('show');
    };

    const handleDelete = async (id) => {
        NotificationService.showConfirm('¿Está seguro de eliminar este centro de formación?', async () => {
            try {
                const response = await fetch(`../../routing.php?controller=centro_formacion&action=destroy&id=${id}`);
                if (response.ok) {
                    NotificationService.showSuccess('Centro eliminado');
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    NotificationService.showError('Error al eliminar');
                }
            } catch (err) {
                NotificationService.showError('Error de conexión');
            }
        });
    };

    // Modal Control
    document.getElementById('closeModal').onclick = () => modal.classList.remove('show');
    document.getElementById('cancelBtn').onclick = () => modal.classList.remove('show');

    form.onsubmit = async (e) => {
        e.preventDefault();
        const data = {
            cent_id: document.getElementById('cent_id').value,
            cent_nombre: document.getElementById('cent_nombre').value
        };

        try {
            const response = await fetch(`../../routing.php?controller=centro_formacion&action=update`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                NotificationService.showSuccess('Centro actualizado');
                modal.classList.remove('show');
                init(); // Recargar datos
            } else {
                NotificationService.showError('Error al actualizar');
            }
        } catch (err) {
            NotificationService.showError('Error de servidor');
        }
    };

    const showDetails = () => {
        loadingState.style.display = 'none';
        centroDetails.style.display = 'grid';
        errorState.style.display = 'none';
    };

    const showError = (msg) => {
        loadingState.style.display = 'none';
        centroDetails.style.display = 'none';
        errorState.style.display = 'block';
        errorMessage.textContent = msg;
    };

    init();
});
