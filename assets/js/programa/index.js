/**
 * Programa Management JavaScript
 * Refactored to Class-based manager for consistency.
 */
class ProgramaManager {
    constructor() {
        this.programas = [];
        this.filteredProgramas = [];
        this.currentPage = 1;
        this.itemsPerPage = 5;
        this.programaIdToDelete = null;

        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadProgramas();
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

        // Global functions for modals
        window.openDeleteModal = (id, nombre) => this.openDeleteModal(id, nombre);
        window.closeDeleteModal = () => this.closeDeleteModal();
        window.confirmDelete = () => this.deletePrograma();
    }

    async loadProgramas() {
        try {
            const response = await fetch('../../routing.php?controller=programa&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.details || data.error || 'Error del servidor');

            this.programas = Array.isArray(data) ? data : [];
            this.renderTable();
        } catch (error) {
            console.error('Error loading programas:', error);
            NotificationService.showError('No pudimos cargar los programas.');
        }
    }

    getFilteredData() {
        const searchInput = document.getElementById('searchInput');
        const query = (searchInput ? searchInput.value : '').toLowerCase();

        return this.programas.filter(p =>
            (p.prog_codigo || '').toString().includes(query) ||
            (p.prog_denominacion || '').toLowerCase().includes(query) ||
            (p.titpro_nombre || '').toLowerCase().includes(query)
        );
    }

    renderTable() {
        const tableBody = document.getElementById('programasTableBody');
        if (!tableBody) return;

        const filtered = this.getFilteredData();
        const total = filtered.length;

        // Update stats
        const totalProgramasEl = document.getElementById('totalProgramas');
        const totalRecords = document.getElementById('totalRecords');
        if (totalProgramasEl) totalProgramasEl.textContent = this.programas.length;
        if (totalRecords) totalRecords.textContent = total;

        const totalPages = Math.ceil(total / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = Math.min(start + this.itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';

        if (paginated.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-12 text-gray-500">No se encontraron programas</td></tr>`;
        } else {
            paginated.forEach((p, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${p.prog_codigo}`;
                tr.innerHTML = `
                    <td class="text-xs font-semibold text-gray-400">
                        ${String(start + index + 1).padStart(2, '0')}
                    </td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm">
                                <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                            </div>
                            <div class="user-info-sm">
                                <div class="user-name-sm">${p.prog_denominacion}</div>
                                <div class="user-meta-sm">Código: ${p.prog_codigo}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="contact-cell">
                            <div class="contact-item">
                                <ion-icon src="../../assets/ionicons/ribbon-outline.svg"></ion-icon>
                                <span>${p.titpro_nombre || 'Sin título'}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="badge-glass">
                            ${p.prog_tipo || 'N/A'}
                        </div>
                    </td>
                    <td class="text-right">
                        <button class="btn-more-glass" onclick="event.stopPropagation(); window.location.href='ver.php?id=${p.prog_codigo}'">
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
        this.programaIdToDelete = id;
        const label = document.getElementById('programaToDelete');
        if (label) label.textContent = nombre;
        const modal = document.getElementById('deleteModal');
        if (modal) modal.classList.add('show');
    }

    closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.classList.remove('show');
        this.programaIdToDelete = null;
    }

    async deletePrograma() {
        if (!this.programaIdToDelete) return;

        try {
            const response = await fetch(`../../routing.php?controller=programa&action=destroy&id=${this.programaIdToDelete}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (response.ok) {
                NotificationService.showSuccess('El programa fue eliminado correctamente.');
                this.closeDeleteModal();
                await this.loadProgramas();
            } else {
                throw new Error(data.details || data.error || 'Error desconocido');
            }
        } catch (error) {
            console.error('Error deleting programa:', error);
            NotificationService.showError('No se pudo eliminar el programa. Es posible que tenga fichas o competencias asociadas.');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.programaManager = new ProgramaManager();
});
