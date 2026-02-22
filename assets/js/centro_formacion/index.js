document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('centroTableBody');
    const searchInput = document.getElementById('searchInput');
    const totalLabel = document.getElementById('totalCentros');
    const addBtn = document.getElementById('addBtn');
    const modal = document.getElementById('centroModal');
    const form = document.getElementById('centroForm');
    const closeBtn = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');

    let centros = [];

    const loadCentros = async () => {
        try {
            const response = await fetch('../../routing.php?controller=centro_formacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.details || data.error || 'Error al cargar centros');
            }

            centros = Array.isArray(data) ? data : [];
            renderCentros(centros);
            if (totalLabel) totalLabel.textContent = centros.length;
        } catch (error) {
            console.error('Error:', error);
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-8 text-gray-500">No pudimos cargar los centros de formación. Intenta recargar la página.</td></tr>`;
            }
        }
    };

    const renderCentros = (data) => {
        if (!tableBody) return;
        tableBody.innerHTML = '';
        if (!Array.isArray(data) || data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center py-8 text-gray-500">No se encontraron centros</td></tr>';
            return;
        }
        // ... (rest of renderCentros code is the same)

        data.forEach(c => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
            row.onclick = () => window.location.href = `ver.php?id=${c.cent_id}`;
            row.innerHTML = `
                <td class="px-6 py-4 font-semibold text-sena-green">${String(c.cent_id).padStart(3, '0')}</td>
                <td class="px-6 py-4 font-bold text-gray-900">${c.cent_nombre}</td>
            `;
            tableBody.appendChild(row);
        });

        // Add event listeners to edit buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.onclick = () => {
                const id = btn.dataset.id;
                const centro = centros.find(c => c.cent_id == id);
                if (centro) openModal(centro);
            };
        });
    };

    const openModal = (centro = null) => {
        form.reset();
        const centIdInput = document.getElementById('cent_id');
        if (centro) {
            document.getElementById('modalTitle').textContent = 'Editar Centro de Formación';
            centIdInput.value = centro.cent_id;
            centIdInput.readOnly = true; // No permitir editar ID en actualización
            centIdInput.style.backgroundColor = '#f3f4f6';
            document.getElementById('cent_nombre').value = centro.cent_nombre;
        } else {
            document.getElementById('modalTitle').textContent = 'Nuevo Centro de Formación';
            centIdInput.value = '';
            centIdInput.readOnly = false;
            centIdInput.style.backgroundColor = 'white';
        }
        modal.classList.add('show');
    };

    const closeModal = () => modal.classList.remove('show');

    addBtn.onclick = () => openModal();
    closeBtn.onclick = closeModal;
    cancelBtn.onclick = closeModal;

    form.onsubmit = async (e) => {
        e.preventDefault();
        const id = document.getElementById('cent_id').value;
        const isEdit = document.getElementById('modalTitle').textContent.includes('Editar');
        const action = isEdit ? 'update' : 'store';

        const data = {
            cent_nombre: document.getElementById('cent_nombre').value
        };
        if (isEdit) data.cent_id = id;

        try {
            const response = await fetch(`../../routing.php?controller=centro_formacion&action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                NotificationService.showSuccess(id ? '¡Centro de formación actualizado correctamente!' : '¡Centro de formación creado correctamente!');
                closeModal();
                loadCentros();
            } else {
                NotificationService.showError('No se pudo guardar el centro de formación. Verifica los datos e intenta de nuevo.');
            }
        } catch (error) {
            NotificationService.showError('No pudimos conectar con el servidor. Intenta de nuevo.');
        }
    };

    window.deleteCentro = async (id) => {
        NotificationService.showConfirm(
            '¿Estás seguro de que deseas eliminar este centro de formación? Esta acción no se puede deshacer.',
            async () => {
                try {
                    const response = await fetch(`../../routing.php?controller=centro_formacion&action=destroy&id=${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (response.ok) {
                        NotificationService.showSuccess('El centro de formación fue eliminado correctamente.');
                        loadCentros();
                    } else {
                        NotificationService.showError('No se pudo eliminar el centro. Es posible que tenga instructores o coordinaciones asociadas.');
                    }
                } catch (error) {
                    NotificationService.showError('No pudimos conectar con el servidor. Intenta de nuevo.');
                }
            }
        );
    };

    searchInput.oninput = () => {
        const term = searchInput.value.toLowerCase();
        const filtered = centros.filter(c => c.cent_nombre.toLowerCase().includes(term));
        renderCentros(filtered);
    };

    loadCentros();
});
