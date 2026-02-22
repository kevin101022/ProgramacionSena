class TituloProgramaView {
    constructor() {
        this.titulos = [];
        this.filteredTitulos = [];
        this.currentPage = 1;
        this.recordsPerPage = 5;
        this.tituloIdToDelete = null;

        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadTitulos();
    }

    cacheDOM() {
        this.tableBody = document.getElementById('titulosTableBody');
        this.searchInput = document.getElementById('searchInput');
        this.totalTitulosEl = document.getElementById('totalTitulos');
        this.showingFrom = document.getElementById('showingFrom');
        this.showingTo = document.getElementById('showingTo');
        this.totalRecords = document.getElementById('totalRecords');
        this.paginationNumbers = document.getElementById('paginationNumbers');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.deleteModal = document.getElementById('deleteModal');
        this.tituloToDeleteLabel = document.getElementById('tituloToDelete');
        this.confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    }

    bindEvents() {
        this.searchInput.addEventListener('input', () => this.handleSearch());
        this.prevBtn.addEventListener('click', () => this.changePage(this.currentPage - 1));
        this.nextBtn.addEventListener('click', () => this.changePage(this.currentPage + 1));
        this.confirmDeleteBtn.addEventListener('click', () => this.confirmDelete());
    }

    async loadTitulos() {
        try {
            const response = await fetch('../../routing.php?controller=titulo_programa&action=index');
            this.titulos = await response.json();
            this.filteredTitulos = [...this.titulos];
            this.render();
        } catch (error) {
            console.error('Error loading titulos:', error);
            if (window.NotificationService) {
                NotificationService.show('Error al cargar los títulos', 'error');
            }
        }
    }

    handleSearch() {
        const query = this.searchInput.value.toLowerCase();
        this.filteredTitulos = this.titulos.filter(t =>
            t.titpro_nombre.toLowerCase().includes(query) ||
            t.titpro_id.toString().includes(query)
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
        this.totalTitulosEl.textContent = this.titulos.length;
        this.totalRecords.textContent = this.filteredTitulos.length;

        const start = this.filteredTitulos.length === 0 ? 0 : (this.currentPage - 1) * this.recordsPerPage + 1;
        const end = Math.min(this.currentPage * this.recordsPerPage, this.filteredTitulos.length);

        this.showingFrom.textContent = start;
        this.showingTo.textContent = end;
    }

    renderTable() {
        const start = (this.currentPage - 1) * this.recordsPerPage;
        const end = start + this.recordsPerPage;
        const paginatedTitulos = this.filteredTitulos.slice(start, end);

        this.tableBody.innerHTML = '';

        if (paginatedTitulos.length === 0) {
            this.tableBody.innerHTML = `<tr><td colspan="2" class="text-center py-8 text-gray-500">No se encontraron títulos</td></tr>`;
            return;
        }

        paginatedTitulos.forEach(t => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
            row.title = 'Haga clic para ver detalles';

            row.onclick = () => {
                window.location.href = `ver.php?id=${t.titpro_id}`;
            };

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-sena-green">
                    ${String(t.titpro_id).padStart(3, '0')}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    <div class="font-medium group-hover:text-sena-green transition-colors">${t.titpro_nombre}</div>
                </td>
            `;
            this.tableBody.appendChild(row);
        });
    }

    renderPagination() {
        const totalPages = Math.ceil(this.filteredTitulos.length / this.recordsPerPage);
        this.paginationNumbers.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.className = `pagination-btn ${i === this.currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.onclick = () => this.changePage(i);
            this.paginationNumbers.appendChild(btn);
        }

        this.prevBtn.disabled = this.currentPage === 1;
        this.nextBtn.disabled = this.currentPage === totalPages || totalPages === 0;
    }

    changePage(page) {
        if (page < 1 || page > Math.ceil(this.filteredTitulos.length / this.recordsPerPage)) return;
        this.currentPage = page;
        this.render();
    }

    openDeleteModal(id, nombre) {
        this.tituloIdToDelete = id;
        this.tituloToDeleteLabel.textContent = nombre;
        this.deleteModal.classList.add('active');
    }

    closeDeleteModal() {
        this.deleteModal.classList.remove('active');
        this.tituloIdToDelete = null;
    }

    async confirmDelete() {
        if (!this.tituloIdToDelete) return;

        try {
            const formData = new FormData();
            formData.append('controller', 'titulo_programa');
            formData.append('action', 'destroy');
            formData.append('id', this.tituloIdToDelete);

            const response = await fetch(`../../routing.php`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.message) {
                if (window.NotificationService) {
                    NotificationService.show(result.message, 'success');
                }
                this.closeDeleteModal();
                await this.loadTitulos();
            } else {
                throw new Error(result.error || 'Error al eliminar el título');
            }
        } catch (error) {
            console.error('Error deleting titulo:', error);
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            }
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.tituloProgramaView = new TituloProgramaView();
});

// Helper functions for global scope
window.closeDeleteModal = () => window.tituloProgramaView.closeDeleteModal();
window.confirmDelete = () => window.tituloProgramaView.confirmDelete();
