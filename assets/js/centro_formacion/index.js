/**
 * Centro Formacion Management JavaScript
 * Refactored to Class-based manager with Pagination support.
 */
class CentroFormacionManager {
    constructor() {
        this.centros = [];
        this.filteredCentros = [];
        this.currentPage = 1;
        this.itemsPerPage = 5;

        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadCentros();
    }

    bindEvents() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                this.currentPage = 1;
                this.renderTable();
            });
        }

        const prevBtn = document.getElementById('prevBtn');
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.renderTable();
                }
            });
        }

        const nextBtn = document.getElementById('nextBtn');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const totalPages = Math.ceil(this.getFilteredData().length / this.itemsPerPage);
                if (this.currentPage < totalPages) {
                    this.currentPage++;
                    this.renderTable();
                }
            });
        }

        const addBtn = document.getElementById('addBtn');
        if (addBtn) addBtn.onclick = () => this.openModal();

        const closeBtn = document.getElementById('closeModal');
        if (closeBtn) closeBtn.onclick = () => this.closeModal();

        const cancelBtn = document.getElementById('cancelBtn');
        if (cancelBtn) cancelBtn.onclick = () => this.closeModal();

        const form = document.getElementById('centroForm');
        if (form) {
            form.onsubmit = (e) => this.handleFormSubmit(e);
        }

        // Global delete trigger
        window.deleteCentro = (id) => this.confirmDelete(id);
    }

    async loadCentros() {
        try {
            const response = await fetch('../../routing.php?controller=centro_formacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.details || data.error || 'Error al cargar centros');

            this.centros = Array.isArray(data) ? data : [];
            this.renderTable();
        } catch (error) {
            console.error('Error:', error);
            NotificationService.showError('No pudimos cargar los centros de formación.');
        }
    }

    getFilteredData() {
        const searchInput = document.getElementById('searchInput');
        const term = (searchInput ? searchInput.value : '').toLowerCase();
        return this.centros.filter(c => (c.cent_nombre || '').toLowerCase().includes(term));
    }

    renderTable() {
        const tableBody = document.getElementById('centroTableBody');
        if (!tableBody) return;

        const filtered = this.getFilteredData();
        const total = filtered.length;

        // Update stats
        const totalLabel = document.getElementById('totalCentros');
        const totalRecords = document.getElementById('totalRecords');
        if (totalLabel) totalLabel.textContent = this.centros.length;
        if (totalRecords) totalRecords.textContent = total;

        const totalPages = Math.ceil(total / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = Math.min(start + this.itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';

        if (paginated.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center py-8 text-gray-500">No se encontraron centros</td></tr>';
        } else {
            paginated.forEach((c, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/30 transition-all cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${c.cent_id}`;
                tr.innerHTML = `
                    <td class="text-xs font-semibold text-gray-400">
                        ${String(start + index + 1).padStart(2, '0')}
                    </td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm">
                                <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                            </div>
                            <div class="user-info-sm">
                                <div class="user-name-sm">${c.cent_nombre}</div>
                                <div class="user-meta-sm">Centro de Formación Profesional</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-right">
                        <button class="btn-more-glass" onclick="event.stopPropagation(); window.location.href='ver.php?id=${c.cent_id}'">
                            <span>Ver más</span>
                            <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                        </button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        }

        this.updatePagination(totalPages, start, end, total);
    }

    updatePagination(totalPages, start, end, total) {
        const paginationNumbers = document.getElementById('paginationNumbers');
        const showingFrom = document.getElementById('showingFrom');
        const showingTo = document.getElementById('showingTo');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        if (showingFrom) showingFrom.textContent = total > 0 ? start + 1 : 0;
        if (showingTo) showingTo.textContent = end;
        if (prevBtn) prevBtn.disabled = this.currentPage === 1;
        if (nextBtn) nextBtn.disabled = this.currentPage === totalPages || totalPages === 0;

        if (paginationNumbers) {
            paginationNumbers.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-number ${i === this.currentPage ? 'active' : ''}`;
                btn.textContent = i;
                btn.onclick = () => {
                    this.currentPage = i;
                    this.renderTable();
                };
                paginationNumbers.appendChild(btn);
            }
        }
    }

    openModal(centro = null) {
        const modal = document.getElementById('centroModal');
        const form = document.getElementById('centroForm');
        const modalTitle = document.getElementById('modalTitle');
        const centIdInput = document.getElementById('cent_id');
        const centNombreInput = document.getElementById('cent_nombre');

        if (form) form.reset();

        if (centro) {
            if (modalTitle) modalTitle.textContent = 'Editar Centro de Formación';
            if (centIdInput) {
                centIdInput.value = centro.cent_id;
                centIdInput.readOnly = true;
                centIdInput.style.backgroundColor = '#f3f4f6';
            }
            if (centNombreInput) centNombreInput.value = centro.cent_nombre;
        } else {
            if (modalTitle) modalTitle.textContent = 'Nuevo Centro de Formación';
            if (centIdInput) {
                centIdInput.value = '';
                centIdInput.readOnly = false;
                centIdInput.style.backgroundColor = 'white';
            }
        }
        if (modal) modal.classList.add('show');
    }

    closeModal() {
        const modal = document.getElementById('centroModal');
        if (modal) modal.classList.remove('show');
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('cent_id').value;
        const modalTitle = document.getElementById('modalTitle');
        const isEdit = modalTitle && modalTitle.textContent.includes('Editar');
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
                NotificationService.showSuccess(isEdit ? '¡Centro actualizado!' : '¡Centro creado!');
                this.closeModal();
                await this.loadCentros();
            } else {
                NotificationService.showError('No se pudo guardar el centro.');
            }
        } catch (error) {
            NotificationService.showError('Error de conexión.');
        }
    }

    confirmDelete(id) {
        NotificationService.showConfirm(
            '¿Estás seguro de que deseas eliminar este centro de formación? Esta acción no se puede deshacer.',
            async () => {
                try {
                    const response = await fetch(`../../routing.php?controller=centro_formacion&action=destroy&id=${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (response.ok) {
                        NotificationService.showSuccess('Centro eliminado correctamente.');
                        await this.loadCentros();
                    } else {
                        NotificationService.showError('No se pudo eliminar el centro. Es posible que tenga dependencias.');
                    }
                } catch (error) {
                    NotificationService.showError('Error de conexión.');
                }
            }
        );
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.centroFormacionManager = new CentroFormacionManager();
});
