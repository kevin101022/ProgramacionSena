class CompetenciaIndex {
    constructor() {
        this.competencias = [];
        this.filteredCompetencias = [];
        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadCompetencias();
    }

    cacheDOM() {
        this.tbody = document.getElementById('competenciasBody');
        this.searchInput = document.getElementById('searchTerm');
        this.showingCount = document.getElementById('showingCount');
        this.totalCount = document.getElementById('totalCount');
        this.totalStatsCount = document.getElementById('totalCompetencias');
    }

    bindEvents() {
        this.searchInput.addEventListener('input', () => this.handleSearch());
    }

    async loadCompetencias() {
        try {
            const response = await fetch('../../routing.php?controller=competencia&action=index');
            this.competencias = await response.json();
            this.filteredCompetencias = [...this.competencias];
            this.render();
        } catch (error) {
            console.error('Error loading competencias:', error);
            if (window.NotificationService) {
                NotificationService.showError('No pudimos cargar las competencias. Intenta recargar la página.');
            }
        }
    }

    handleSearch() {
        const term = this.searchInput.value.toLowerCase();
        this.filteredCompetencias = this.competencias.filter(c =>
            c.comp_nombre_corto.toLowerCase().includes(term) ||
            (c.comp_nombre_unidad_competencia && c.comp_nombre_unidad_competencia.toLowerCase().includes(term))
        );
        this.render();
    }

    render() {
        this.tbody.innerHTML = '';

        if (this.filteredCompetencias.length === 0) {
            this.tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8">No se encontraron competencias</td></tr>';
            this.showingCount.textContent = '0';
            this.totalCount.textContent = this.competencias.length;
            return;
        }

        this.filteredCompetencias.forEach(c => {
            const tr = document.createElement('tr');
            tr.className = 'cursor-pointer hover:bg-gray-50 transition-colors';
            tr.onclick = () => {
                window.location.href = `ver.php?id=${c.comp_id}`;
            };

            tr.innerHTML = `
                <td><span class="font-mono text-xs text-gray-500">${String(c.comp_id).padStart(3, '0')}</span></td>
                <td><div class="font-semibold text-gray-900">${c.comp_nombre_corto}</div></td>
                <td><span class="status-badge status-active">${c.comp_horas}h</span></td>
                <td><div class="text-sm text-gray-600 truncate max-w-xs">${c.comp_nombre_unidad_competencia || 'N/A'}</div></td>
            `;
            this.tbody.appendChild(tr);
        });

        this.showingCount.textContent = this.filteredCompetencias.length;
        this.totalCount.textContent = this.competencias.length;
        if (this.totalStatsCount) this.totalStatsCount.textContent = this.competencias.length;
    }

    async handleDelete(id) {
        if (typeof NotificationService === 'undefined' && !window.NotificationService) {
            if (!confirm('¿Estás seguro de eliminar esta competencia? Esta acción no se puede deshacer.')) return;
            this.executeDelete(id);
        } else {
            const service = window.NotificationService || NotificationService;
            service.showConfirm(
                '¿Estás seguro de que deseas eliminar esta competencia? Se eliminarán todas sus asociaciones con programas y esta acción no se puede deshacer.',
                () => this.executeDelete(id)
            );
        }
    }

    async executeDelete(id) {
        try {
            const response = await fetch(`../../routing.php?controller=competencia&action=destroy&id=${id}`);
            const result = await response.json();

            if (result.message) {
                if (window.NotificationService) {
                    NotificationService.showSuccess(result.message);
                } else {
                    alert(result.message);
                }
                await this.loadCompetencias();
            } else {
                throw new Error(result.error || 'Error al eliminar');
            }
        } catch (error) {
            if (window.NotificationService) {
                NotificationService.showError('No se pudo eliminar la competencia. Intenta de nuevo.');
            } else {
                alert('Ocurrió un error al intentar eliminar.');
            }
        }
    }
}

let competenciaIndex;
document.addEventListener('DOMContentLoaded', () => {
    competenciaIndex = new CompetenciaIndex();
});
