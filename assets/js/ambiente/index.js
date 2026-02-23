/**
 * Ambiente Management JavaScript
 * Refactored to Class-based architecture for consistency and maintainability.
 */
class AmbienteManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 5;
        this.ambientes = [];
        this.sedes = [];
        this.filteredAmbientes = [];
        this.ambienteToDeleteId = null;

        const urlParams = new URLSearchParams(window.location.search);
        this.sedeFilterId = urlParams.get('sede_id');

        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadInitialData();
        this.renderTable();
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

        // Global modal triggers
        window.openDeleteModal = (id, nombre) => this.openDeleteModal(id, nombre);
        window.closeDeleteModal = () => this.closeDeleteModal();
        window.confirmDelete = () => this.deleteAmbiente();

        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (confirmBtn) {
            confirmBtn.onclick = () => this.deleteAmbiente();
        }
    }

    async loadInitialData() {
        try {
            const headers = { 'Accept': 'application/json' };
            const [ambData, sedeData] = await Promise.all([
                fetch('../../routing.php?controller=ambiente&action=index', { headers }).then(res => res.json()),
                fetch('../../routing.php?controller=sede&action=index', { headers }).then(res => res.json())
            ]);

            this.ambientes = Array.isArray(ambData) ? ambData : [];
            this.sedes = Array.isArray(sedeData) ? sedeData : [];

            if (this.sedeFilterId) {
                this.ambientes = this.ambientes.filter(a => a.sede_sede_id == this.sedeFilterId);
                this.showFilterBadge();
            }
        } catch (error) {
            console.error('Error loading initial data:', error);
            NotificationService.showError('Error al cargar la información inicial.');
        }
    }

    showFilterBadge() {
        if (!this.sedes.length) return;
        const sede = this.sedes.find(s => s.sede_id == this.sedeFilterId);
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

    getFilteredData() {
        const searchInput = document.getElementById('searchInput');
        const query = (searchInput ? searchInput.value : '').toLowerCase();

        return this.ambientes.filter(a =>
            (a.amb_nombre || '').toLowerCase().includes(query) ||
            (a.sede_nombre && a.sede_nombre.toLowerCase().includes(query))
        );
    }

    renderTable() {
        const tableBody = document.getElementById('ambientesTableBody');
        if (!tableBody) return;

        const filtered = this.getFilteredData();
        const total = filtered.length;

        // Update stats
        const totalAmbientesSpan = document.getElementById('totalAmbientes');
        const totalRecords = document.getElementById('totalRecords');
        if (totalAmbientesSpan) totalAmbientesSpan.textContent = this.ambientes.length;
        if (totalRecords) totalRecords.textContent = total;

        const totalPages = Math.ceil(total / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = Math.min(start + this.itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';

        if (paginated.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center p-8 text-gray-500">No se encontraron ambientes</td></tr>';
        } else {
            paginated.forEach(a => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/30 transition-all cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${a.amb_id}`;
                tr.innerHTML = `
                    <td class="font-semibold text-sena-green">
                        ${String(a.amb_id).padStart(3, '0')}
                    </td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm">
                                <ion-icon src="../../assets/ionicons/cube-outline.svg"></ion-icon>
                            </div>
                            <div class="user-info-sm">
                                <div class="user-name-sm">${a.amb_nombre}</div>
                                <div class="user-meta-sm">Ambiente de Formación</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="contact-cell">
                            <div class="contact-item">
                                <ion-icon src="../../assets/ionicons/business-outline.svg" class="text-sena-green"></ion-icon>
                                <span>${a.sede_nombre || 'N/A'}</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-right">
                        <button class="btn-more-glass" onclick="event.stopPropagation(); window.location.href='ver.php?id=${a.amb_id}'">
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

    openDeleteModal(id, nombre) {
        this.ambienteToDeleteId = id;
        const label = document.getElementById('ambienteToDelete');
        if (label) label.textContent = nombre;
        const modal = document.getElementById('deleteModal');
        if (modal) modal.classList.add('show');
    }

    closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.classList.remove('show');
        this.ambienteToDeleteId = null;
    }

    async deleteAmbiente() {
        if (!this.ambienteToDeleteId) return;

        try {
            const response = await fetch(`../../routing.php?controller=ambiente&action=destroy&id=${this.ambienteToDeleteId}`, {
                method: 'POST',
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.details || data.error || 'Error al eliminar');

            this.ambientes = this.ambientes.filter(a => a.amb_id != this.ambienteToDeleteId);
            this.closeDeleteModal();
            this.renderTable();
            NotificationService.showSuccess('El ambiente fue eliminado correctamente.');
        } catch (error) {
            console.error('Error deleting:', error);
            NotificationService.showError('No se pudo eliminar el ambiente. Es posible que tenga asignaciones asociadas.');
        }
    }
}

// Iniciar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.ambienteManager = new AmbienteManager();
});
