/**
 * Competencia Management JavaScript
 * Refactored to Class-based manager with Pagination support.
 */
class CompetenciaManager {
    constructor() {
        this.competencias = [];
        this.filteredCompetencias = [];
        this.currentPage = 1;
        this.itemsPerPage = 5;

        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadCompetencias();
    }

    bindEvents() {
        const searchInput = document.getElementById('searchTerm');
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

        // Global delete trigger
        window.deleteCompetencia = (id) => this.confirmDelete(id);
    }

    async loadCompetencias() {
        try {
            const response = await fetch('../../routing.php?controller=competencia&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.details || data.error || 'Error al cargar');

            this.competencias = Array.isArray(data) ? data : [];
            this.renderTable();
        } catch (error) {
            console.error('Error loading competencias:', error);
            NotificationService.showError('No pudimos cargar las competencias.');
        }
    }

    getFilteredData() {
        const searchInput = document.getElementById('searchTerm');
        const term = (searchInput ? searchInput.value : '').toLowerCase();

        return this.competencias.filter(c =>
            c.comp_nombre_corto.toLowerCase().includes(term) ||
            (c.comp_nombre_unidad_competencia && c.comp_nombre_unidad_competencia.toLowerCase().includes(term))
        );
    }

    renderTable() {
        const tableBody = document.getElementById('competenciasBody');
        if (!tableBody) return;

        const filtered = this.getFilteredData();
        const total = filtered.length;

        // Update stats
        const totalStatsCount = document.getElementById('totalCompetencias');
        const totalCountLabel = document.getElementById('totalCount');
        const showingFromLabel = document.getElementById('showingFrom');
        const showingToLabel = document.getElementById('showingTo');
        if (totalStatsCount) totalStatsCount.textContent = this.competencias.length;
        if (totalCountLabel) totalCountLabel.textContent = total;

        const totalPages = Math.ceil(total / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = Math.min(start + this.itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';

        if (paginated.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-8">No se encontraron competencias</td></tr>';
        } else {
            paginated.forEach((c, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/30 transition-all cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${c.comp_id}`;
                tr.innerHTML = `
                    <td class="text-xs font-semibold text-gray-400">
                        ${String(start + index + 1).padStart(2, '0')}
                    </td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm">
                                <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                            </div>
                            <div class="user-info-sm">
                                <div class="user-name-sm">${c.comp_nombre_corto}</div>
                                <div class="user-meta-sm">Competencia Académica</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="badge-glass">
                            ${c.comp_horas} Horas
                        </div>
                    </td>
                    <td>
                        <div class="contact-cell">
                            <div class="contact-item">
                                <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                                <span class="truncate max-w-xs" title="${c.comp_nombre_unidad_competencia || ''}">
                                    ${c.comp_nombre_unidad_competencia || 'General'}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="text-right">
                        <button class="btn-more-glass" onclick="event.stopPropagation(); window.location.href='ver.php?id=${c.comp_id}'">
                            <span>Ver más</span>
                            <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                        </button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        }

        if (showingFromLabel) showingFromLabel.textContent = total > 0 ? start + 1 : 0;
        if (showingToLabel) showingToLabel.textContent = end;
        this.updatePagination(totalPages);
    }

    updatePagination(totalPages) {
        const paginationNumbers = document.getElementById('paginationNumbers');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

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

    confirmDelete(id) {
        NotificationService.showConfirm(
            '¿Estás seguro de que deseas eliminar esta competencia? Se eliminarán todas sus asociaciones con programas.',
            async () => {
                try {
                    const response = await fetch(`../../routing.php?controller=competencia&action=destroy&id=${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const result = await response.json();
                    if (response.ok) {
                        NotificationService.showSuccess('Competencia eliminada correctamente.');
                        await this.loadCompetencias();
                    } else {
                        throw new Error(result.error || 'Error al eliminar');
                    }
                } catch (error) {
                    NotificationService.showError('No se pudo eliminar la competencia.');
                }
            }
        );
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.competenciaManager = new CompetenciaManager();
});
