// Instructor Management JavaScript
document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('instructorTableBody');
    const mobileGrid = document.getElementById('instructorMobileGrid');
    const searchInput = document.getElementById('searchInput');
    const sedeFilter = document.getElementById('sedeFilter');
    const totalInstructoresLabel = document.getElementById('totalInstructores');
    const refreshBtn = document.getElementById('refreshBtn');

    let instructores = [];

    const loadCentros = async () => {
        try {
            const response = await fetch('../../routing.php?controller=instructor&action=getCentros', {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Error al cargar centros');
            const centros = await response.json();

            sedeFilter.innerHTML = '<option value="">Todos los Centros</option>';
            centros.forEach(centro => {
                const option = document.createElement('option');
                option.value = centro.cent_id;
                option.textContent = centro.cent_nombre;
                sedeFilter.appendChild(option);
            });
        } catch (error) {
            console.error('Error al cargar centros:', error);
        }
    };

    const loadInstructores = async () => {
        try {
            const response = await fetch('../../routing.php?controller=instructor&action=index', {
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.details || data.error || 'Error desconocido');
            }

            instructores = data;
            renderInstructores(instructores);
            updateStats(instructores);
        } catch (error) {
            console.error('Error:', error);
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-gray-500">No pudimos cargar la lista de instructores. Intenta recargar la página.</td></tr>`;
            }
        }
    };

    const renderInstructores = (data) => {
        if (!tableBody) return;
        tableBody.innerHTML = '';

        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">No se encontraron instructores</td></tr>';
            return;
        }

        data.forEach(inst => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
            row.onclick = () => window.location.href = `ver.php?id=${inst.inst_id}`;

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-sena-green">
                    ${String(inst.inst_id).padStart(3, '0')}
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600 font-bold">
                            ${inst.inst_nombres[0]}${inst.inst_apellidos[0]}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">${inst.inst_nombres} ${inst.inst_apellidos}</div>
                            <div class="text-xs text-gray-500">Instructor SENA</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-600">${inst.inst_correo}</div>
                    <div class="text-xs text-gray-400">${inst.inst_telefono || 'Sin teléfono'}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="status-badge status-active">
                        ${inst.cent_nombre || 'Sin centro'}
                    </span>
                </td>
            `;
            tableBody.appendChild(row);
        });
    };

    const updateStats = (data) => {
        if (totalInstructoresLabel) totalInstructoresLabel.textContent = data.length;
    };

    const filterData = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const sedeId = sedeFilter.value;

        const filtered = instructores.filter(inst => {
            const names = (inst.inst_nombres + ' ' + inst.inst_apellidos).toLowerCase();
            const matchesSearch = names.includes(searchTerm) || inst.inst_correo.toLowerCase().includes(searchTerm);
            const matchesSede = !sedeId || inst.centro_formacion_cent_id == sedeId;
            return matchesSearch && matchesSede;
        });

        renderInstructores(filtered);
    };

    if (searchInput) searchInput.addEventListener('input', filterData);
    if (sedeFilter) sedeFilter.addEventListener('change', filterData);
    if (refreshBtn) refreshBtn.onclick = loadInstructores;

    window.deleteInstructor = async (id) => {
        NotificationService.showConfirm(
            '¿Estás seguro de que deseas eliminar este instructor? Esta acción no se puede deshacer.',
            async () => {
                try {
                    const response = await fetch(`../../routing.php?controller=instructor&action=destroy&id=${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (response.ok) {
                        NotificationService.showSuccess('El instructor fue eliminado correctamente.');
                        loadInstructores();
                    } else {
                        const data = await response.json();
                        NotificationService.showError(data.error ? 'No se pudo eliminar el instructor. Es posible que tenga asignaciones activas.' : 'Ocurrió un error al eliminar.');
                    }
                } catch (e) {
                    NotificationService.showError('No pudimos conectar con el servidor. Intenta de nuevo.');
                }
            }
        );
    };

    loadCentros();
    loadInstructores();
});
