/**
 * Titulo Programa Management JavaScript
 * Refactored to Class-based manager for consistency.
 */
class TituloProgramaManager {
    constructor() {
        this.titulos = [];
        this.filteredTitulos = [];
        this.currentPage = 1;
        this.itemsPerPage = 5;
        this.tituloIdToDelete = null;

        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadTitulos();
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

        // Global modal functions
        window.openDeleteModal = (id, nombre) => this.openDeleteModal(id, nombre);
        window.closeDeleteModal = () => this.closeDeleteModal();
        window.confirmDelete = () => this.deleteTitulo();
    }

    async loadTitulos() {
        try {
            const response = await fetch('../../routing.php?controller=titulo_programa&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.details || data.error || 'Error del servidor');

            this.titulos = Array.isArray(data) ? data : [];
            this.renderTable();
        } catch (error) {
            console.error('Error loading titulos:', error);
            NotificationService.showError('No pudimos cargar los títulos.');
        }
    }

    getFilteredData() {
        const searchInput = document.getElementById('searchInput');
        const query = (searchInput ? searchInput.value : '').toLowerCase();

        return this.titulos.filter(t =>
            t.titpro_nombre.toLowerCase().includes(query) ||
            (t.titpro_id && t.titpro_id.toString().includes(query))
        );
    }

    renderTable() {
        const tableBody = document.getElementById('titulosTableBody');
        if (!tableBody) return;

        const filtered = this.getFilteredData();
        const total = filtered.length;

        // Update stats
        const totalTitulosEl = document.getElementById('totalTitulos');
        const totalRecords = document.getElementById('totalRecords');
        if (totalTitulosEl) totalTitulosEl.textContent = this.titulos.length;
        if (totalRecords) totalRecords.textContent = total;

        const totalPages = Math.ceil(total / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = Math.min(start + this.itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';

        if (paginated.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-8 text-gray-500">No se encontraron títulos</td></tr>`;
        } else {
            paginated.forEach((t, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/30 transition-all cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${t.titpro_id}`;
                tr.innerHTML = `
                    <td class="text-xs font-semibold text-gray-400">
                        ${String(start + index + 1).padStart(2, '0')}
                    </td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm">
                                <ion-icon src="../../assets/ionicons/ribbon-outline.svg"></ion-icon>
                            </div>
                            <div class="user-info-sm">
                                <div class="user-name-sm">${t.titpro_nombre}</div>
                                <div class="user-meta-sm">Título de Formación</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-right">
                        <button class="btn-more-glass" onclick="event.stopPropagation(); window.location.href='ver.php?id=${t.titpro_id}'">
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
        this.tituloIdToDelete = id;
        const label = document.getElementById('tituloToDelete');
        if (label) label.textContent = nombre;
        const modal = document.getElementById('deleteModal');
        if (modal) modal.classList.add('show');
    }

    closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.classList.remove('show');
        this.tituloIdToDelete = null;
    }

    async deleteTitulo() {
        if (!this.tituloIdToDelete) return;

        try {
            const formData = new FormData();
            formData.append('controller', 'titulo_programa');
            formData.append('action', 'destroy');
            formData.append('id', this.tituloIdToDelete);

            const response = await fetch(`../../routing.php`, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (response.ok) {
                NotificationService.showSuccess('Título eliminado correctamente.');
                this.closeDeleteModal();
                await this.loadTitulos();
            } else {
                throw new Error(data.error || 'Error al eliminar');
            }
        } catch (error) {
            console.error('Error deleting titulo:', error);
            NotificationService.showError('No se pudo eliminar el título.');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.tituloProgramaManager = new TituloProgramaManager();
});
