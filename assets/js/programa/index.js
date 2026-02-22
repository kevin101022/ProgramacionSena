class ProgramaView {
    constructor() {
        this.programas = [];
        this.filteredProgramas = [];
        this.currentPage = 1;
        this.recordsPerPage = 5;
        this.programaIdToDelete = null;

        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadProgramas();
    }

    cacheDOM() {
        this.tableBody = document.getElementById('programasTableBody');
        this.searchInput = document.getElementById('searchInput');
        this.totalProgramasEl = document.getElementById('totalProgramas');
        this.showingFrom = document.getElementById('showingFrom');
        this.showingTo = document.getElementById('showingTo');
        this.totalRecords = document.getElementById('totalRecords');
        this.paginationNumbers = document.getElementById('paginationNumbers');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.deleteModal = document.getElementById('deleteModal');
        this.programaToDeleteLabel = document.getElementById('programaToDelete');
        this.confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    }

    bindEvents() {
        if (this.searchInput) this.searchInput.addEventListener('input', () => this.handleSearch());
        if (this.prevBtn) this.prevBtn.addEventListener('click', () => this.changePage(this.currentPage - 1));
        if (this.nextBtn) this.nextBtn.addEventListener('click', () => this.changePage(this.currentPage + 1));
        if (this.confirmDeleteBtn) this.confirmDeleteBtn.addEventListener('click', () => this.confirmDelete());
    }

    async loadProgramas() {
        try {
            const response = await fetch('../../routing.php?controller=programa&action=index', {
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.details || data.error || 'Error del servidor');
            }

            this.programas = Array.isArray(data) ? data : [];
            this.filteredProgramas = [...this.programas];
            this.render();
        } catch (error) {
            console.error('Error loading programas:', error);
            if (this.tableBody) {
                this.tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-12 text-gray-500">No pudimos cargar los programas. Intenta recargar la página.</td></tr>`;
            }
            if (window.NotificationService) {
                NotificationService.showError('No pudimos cargar los programas. Intenta recargar la página.');
            }
        }
    }

    handleSearch() {
        const query = this.searchInput.value.toLowerCase();
        this.filteredProgramas = this.programas.filter(p =>
            (p.prog_codigo || '').toString().includes(query) ||
            (p.prog_denominacion || '').toLowerCase().includes(query) ||
            (p.titpro_nombre || '').toLowerCase().includes(query)
        );
        this.currentPage = 1;
        this.render();
    }

    render() {
        this.renderTable();
        this.renderStats();
        this.renderPagination();
    }

    renderStats() {
        if (this.totalProgramasEl) this.totalProgramasEl.textContent = this.programas.length;
        if (this.totalRecords) this.totalRecords.textContent = this.filteredProgramas.length;

        const start = this.filteredProgramas.length === 0 ? 0 : (this.currentPage - 1) * this.recordsPerPage + 1;
        const end = Math.min(this.currentPage * this.recordsPerPage, this.filteredProgramas.length);

        if (this.showingFrom) this.showingFrom.textContent = start;
        if (this.showingTo) this.showingTo.textContent = end;
    }

    renderTable() {
        if (!this.tableBody) return;
        const start = (this.currentPage - 1) * this.recordsPerPage;
        const end = start + this.recordsPerPage;
        const paginatedProgramas = this.filteredProgramas.slice(start, end);

        if (paginatedProgramas.length === 0) {
            this.tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-12 text-gray-500">
                <div class="flex flex-col items-center gap-2">
                    <ion-icon src="../../assets/ionicons/search-outline.svg" class="text-4xl text-gray-300"></ion-icon>
                    <p>No se encontraron programas</p>
                </div>
            </td></tr>`;
            return;
        }

        this.tableBody.innerHTML = '';
        paginatedProgramas.forEach(p => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-green-50/50 dark:hover:bg-emerald-900/10 transition-colors cursor-pointer group';
            row.onclick = () => {
                window.location.href = `ver.php?id=${p.prog_codigo}`;
            };

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-sena-green">
                    ${String(p.prog_codigo).padStart(3, '0')}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-sena-green/10 text-sena-green border border-sena-green/20">
                        ${p.prog_codigo}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-sena-green transition-colors leading-tight">
                        ${p.prog_denominacion || 'N/A'}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                        <ion-icon src="../../assets/ionicons/ribbon-outline.svg" class="text-sena-green"></ion-icon>
                        <span class="truncate max-w-[200px]" title="${p.titpro_nombre || ''}">${p.titpro_nombre || 'Sin título'}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        ${p.prog_tipo || 'N/A'}
                    </span>
                </td>
            `;
            this.tableBody.appendChild(row);
        });
    }

    renderPagination() {
        if (!this.paginationNumbers) return;
        const totalPages = Math.ceil(this.filteredProgramas.length / this.recordsPerPage);
        this.paginationNumbers.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = `pagination-btn ${i === this.currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.onclick = () => this.changePage(i);
            this.paginationNumbers.appendChild(btn);
        }

        if (this.prevBtn) this.prevBtn.disabled = this.currentPage === 1;
        if (this.nextBtn) this.nextBtn.disabled = this.currentPage === totalPages || totalPages === 0;
    }

    changePage(page) {
        const totalPages = Math.ceil(this.filteredProgramas.length / this.recordsPerPage);
        if (page < 1 || page > totalPages) return;
        this.currentPage = page;
        this.render();
    }

    openDeleteModal(id, nombre) {
        this.programaIdToDelete = id;
        if (this.programaToDeleteLabel) this.programaToDeleteLabel.textContent = nombre;
        if (this.deleteModal) this.deleteModal.classList.add('active');
    }

    closeDeleteModal() {
        if (this.deleteModal) this.deleteModal.classList.remove('active');
        this.programaIdToDelete = null;
    }

    async confirmDelete() {
        if (!this.programaIdToDelete) return;

        try {
            const response = await fetch(`../../routing.php?controller=programa&action=destroy&id=${this.programaIdToDelete}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();

            if (response.ok) {
                if (window.NotificationService) {
                    NotificationService.showSuccess('El programa fue eliminado correctamente.');
                }
                this.closeDeleteModal();
                await this.loadProgramas();
            } else {
                throw new Error(result.details || result.error || 'Error desconocido');
            }
        } catch (error) {
            console.error('Error deleting programa:', error);
            if (window.NotificationService) {
                NotificationService.showError('No se pudo eliminar el programa. Es posible que tenga fichas o competencias asociadas.');
            }
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.programaView = new ProgramaView();
});

// Helper functions for global scope (onclick attributes)
window.closeDeleteModal = () => window.programaView ? window.programaView.closeDeleteModal() : null;
window.confirmDelete = () => window.programaView ? window.programaView.confirmDelete() : null;
