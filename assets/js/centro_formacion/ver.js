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
            const response = await fetch(`../../routing.php?controller=centro_formacion&action=index`, {
                headers: { 'Accept': 'application/json' }
            });
            const centros = await response.json();
            const centro = centros.find(c => c.cent_id == centId);

            if (!centro) {
                throw new Error('Centro no encontrado');
            }

            populateUI(centro);
            showDetails();

        } catch (error) {
            console.error('Error:', error);
            showError(error.message);
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
