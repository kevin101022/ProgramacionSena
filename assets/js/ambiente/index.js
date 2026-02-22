document.addEventListener('DOMContentLoaded', function () {
    let ambientes = [];
    let sedes = [];
    let currentPage = 1;
    const itemsPerPage = 5;
    const urlParams = new URLSearchParams(window.location.search);
    const sedeFilterId = urlParams.get('sede_id');

    const tableBody = document.getElementById('ambientesTableBody');
    const totalAmbientesSpan = document.getElementById('totalAmbientes');
    const searchInput = document.getElementById('searchInput');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const paginationNumbers = document.getElementById('paginationNumbers');
    const showingFrom = document.getElementById('showingFrom');
    const showingTo = document.getElementById('showingTo');
    const totalRecords = document.getElementById('totalRecords');

    // Fetch initial data
    const headers = { 'Accept': 'application/json' };

    Promise.all([
        fetch('../../routing.php?controller=ambiente&action=index', { headers })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.details || data.error || 'Error al cargar ambientes');
                return data;
            }),
        fetch('../../routing.php?controller=sede&action=index', { headers })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw new Error(data.details || data.error || 'Error al cargar sedes');
                return data;
            })
    ]).then(([ambData, sedeData]) => {
        ambientes = Array.isArray(ambData) ? ambData : [];
        sedes = Array.isArray(sedeData) ? sedeData : [];

        // Apply URL filter if present
        if (sedeFilterId && ambientes.length > 0) {
            ambientes = ambientes.filter(a => a.sede_sede_id == sedeFilterId);
            showFilterBadge();
        }

        renderTable();
    }).catch(err => {
        console.error('Error loading data:', err);
        if (tableBody) {
            tableBody.innerHTML = `<tr><td colspan="3" class="text-center p-8 text-gray-500">No pudimos cargar los ambientes. Intenta recargar la página.</td></tr>`;
        }
    });

    function showFilterBadge() {
        if (!sedes.length) return;
        const sede = sedes.find(s => s.sede_id == sedeFilterId);
        const sedeName = sede ? sede.sede_nombre : 'Sede seleccionada';

        if (document.getElementById('sedeFilterBadge')) return;

        const badge = document.createElement('div');
        badge.id = 'sedeFilterBadge';
        badge.className = 'inline-flex items-center gap-2 px-3 py-1 bg-sena-green/10 text-sena-green text-xs font-medium rounded-full border border-sena-green/20 mb-4';
        badge.innerHTML = `
            Filtrando por: ${sedeName}
            <button onclick="window.location.href='index.php'" class="hover:text-green-700 transition-colors">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        `;

        const container = document.querySelector('.main-content .p-8');
        if (container) {
            container.insertBefore(badge, container.firstChild);
        }
    }

    function renderTable() {
        if (!tableBody) return;
        const query = (searchInput ? searchInput.value : '').toLowerCase();
        const filtered = ambientes.filter(a =>
            (a.amb_nombre || '').toLowerCase().includes(query) ||
            (a.sede_nombre && a.sede_nombre.toLowerCase().includes(query))
        );

        const total = filtered.length;
        if (totalAmbientesSpan) totalAmbientesSpan.textContent = ambientes.length;
        if (totalRecords) totalRecords.textContent = total;

        const totalPages = Math.ceil(total / itemsPerPage);
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

        const start = (currentPage - 1) * itemsPerPage;
        const end = Math.min(start + itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';
        if (paginated.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center p-8 text-gray-500">No se encontraron ambientes</td></tr>';
        } else {
            paginated.forEach(a => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${a.amb_id}`;
                tr.title = 'Haga clic para ver detalles';

                tr.innerHTML = `
                    <td class="font-semibold text-sena-green">${String(a.amb_id).padStart(3, '0')}</td>
                    <td class="font-medium group-hover:text-sena-green transition-colors">${a.amb_nombre}</td>
                    <td>${a.sede_nombre || 'N/A'}</td>
                `;
                tableBody.appendChild(tr);
            });
        }

        if (showingFrom) showingFrom.textContent = total > 0 ? start + 1 : 0;
        if (showingTo) showingTo.textContent = end;

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (!paginationNumbers) return;
        paginationNumbers.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = `pagination-number ${i === currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.onclick = () => {
                currentPage = i;
                renderTable();
            };
            paginationNumbers.appendChild(btn);
        }
        if (prevBtn) prevBtn.disabled = currentPage === 1;
        if (nextBtn) nextBtn.disabled = currentPage === totalPages || totalPages === 0;
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            currentPage = 1;
            renderTable();
        });
    }

    if (prevBtn) {
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        };
    }

    if (nextBtn) {
        nextBtn.onclick = () => {
            const query = searchInput.value.toLowerCase();
            const filtered = ambientes.filter(a =>
                (a.amb_nombre || '').toLowerCase().includes(query) ||
                (a.sede_nombre && a.sede_nombre.toLowerCase().includes(query))
            );
            const totalPages = Math.ceil(filtered.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        };
    }

    // Global delete modal functions
    let ambienteToDeleteId = null;
    window.openDeleteModal = (id, nombre) => {
        ambienteToDeleteId = id;
        const label = document.getElementById('ambienteToDelete');
        if (label) label.textContent = nombre;
        const modal = document.getElementById('deleteModal');
        if (modal) modal.classList.add('show');
    };

    window.closeDeleteModal = () => {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.classList.remove('show');
    };

    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.onclick = () => {
            if (ambienteToDeleteId) {
                fetch(`../../routing.php?controller=ambiente&action=destroy&id=${ambienteToDeleteId}`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' }
                }).then(async res => {
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.details || data.error || 'Error al eliminar');
                    return data;
                })
                    .then(data => {
                        ambientes = ambientes.filter(a => a.amb_id != ambienteToDeleteId);
                        window.closeDeleteModal();
                        renderTable();
                        NotificationService.showSuccess('El ambiente fue eliminado correctamente.');
                    })
                    .catch(err => {
                        console.error('Error deleting:', err);
                        NotificationService.showError('No se pudo eliminar el ambiente. Es posible que tenga asignaciones asociadas.');
                    });
            }
        };
    }
});
