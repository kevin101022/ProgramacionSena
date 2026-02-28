class MisCompetencias {
    constructor() {
        this.competenciasInfo = [];
        this.userId = window.USER_ID || null;
        this.init();
    }

    async init() {
        if (!this.userId) {
            this.showError('No se pudo identificar el usuario. Inicie sesión nuevamente.');
            return;
        }
        await this.loadCompetencias();
        this.setupSearch();
    }

    async loadCompetencias() {
        try {
            const tableBody = document.getElementById('competenciasTableBody');
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-8">Cargando competencias...</td></tr>';
            }

            const response = await fetch(`../../routing.php?controller=instructor&action=getCompetencias&id=${this.userId}`);

            if (!response.ok) {
                throw new Error('Error al cargar competencias');
            }

            this.competenciasInfo = await response.json();

            if (this.competenciasInfo.error) {
                throw new Error(this.competenciasInfo.error);
            }

            this.renderTable(this.competenciasInfo);
        } catch (error) {
            console.error('Error:', error);
            this.showError('No se pudieron cargar sus competencias.');
        }
    }

    renderTable(data) {
        const tbody = document.getElementById('competenciasTableBody');
        if (!tbody) return;

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-8">
                        <div class="empty-state">
                            <ion-icon src="../../assets/ionicons/bookmarks-outline.svg" class="empty-icon"></ion-icon>
                            <p>No se encontraron competencias asociadas a tu perfil.</p>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = data.map((comp, index) => `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="text-center font-medium text-slate-500">${index + 1}</td>
                <td>
                    <div class="font-medium text-slate-800">${comp.comp_nombre || 'Sin nombre'}</div>
                    <div class="text-xs text-slate-400">ID: ${comp.comp_id || 'N/A'}</div>
                </td>
                <td>
                    <div class="text-sm text-slate-600 line-clamp-2 max-w-md" title="${comp.comp_descripcion || ''}">
                        ${comp.comp_descripcion || 'Sin descripción'}
                    </div>
                </td>
                <td>
                    <div class="text-sm font-medium text-[#39A900]">${comp.prog_denominacion || 'General'}</div>
                    <div class="text-xs text-slate-400">Cod: ${comp.prog_codigo || 'N/A'}</div>
                </td>
                <td class="text-center">
                    <span class="status-badge ${this.getVigenciaClass(comp.hab_vigencia)}">
                        ${comp.hab_vigencia || 'N/A'}
                    </span>
                </td>
            </tr>
        `).join('');
    }

    getVigenciaClass(fechaStr) {
        if (!fechaStr) return 'bg-slate-100 text-slate-600';
        const vigencia = new Date(fechaStr);
        const hoy = new Date();
        const msgVigencia = vigencia >= hoy ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
        return msgVigencia;
    }

    setupSearch() {
        const strFilter = document.getElementById('searchInput');
        if (strFilter) {
            strFilter.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                const filtered = this.competenciasInfo.filter(c =>
                    (c.comp_nombre && c.comp_nombre.toLowerCase().includes(term)) ||
                    (c.comp_descripcion && c.comp_descripcion.toLowerCase().includes(term)) ||
                    (c.prog_denominacion && c.prog_denominacion.toLowerCase().includes(term))
                );
                this.renderTable(filtered);
            });
        }
    }

    showError(msg) {
        const tbody = document.getElementById('competenciasTableBody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-red-500">${msg}</td></tr>`;
        }
        if (typeof NotificationSystem !== 'undefined') {
            NotificationSystem.show('error', msg);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new MisCompetencias();
});
