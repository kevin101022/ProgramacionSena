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
        this.setupModal();
        await this.loadCompetencias();
        this.setupSearch();
    }

    setupModal() {
        this.modal = document.getElementById('compModal');
        this.closeButtons = [
            document.getElementById('closeModal'),
            document.getElementById('closeModalBtn')
        ];

        this.closeButtons.forEach(btn => {
            if (btn) btn.onclick = () => this.modal.classList.remove('show');
        });

        window.onclick = (event) => {
            if (event.target == this.modal) {
                this.modal.classList.remove('show');
            }
        };
    }

    async loadCompetencias() {
        try {
            const tableBody = document.getElementById('competenciasTableBody');
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center py-8">Cargando competencias...</td></tr>';
            }

            const response = await fetch(`../../routing.php?controller=instructor&action=getCompetencias&id=${this.userId}`);

            if (!response.ok) {
                throw new Error('Error al cargar competencias');
            }

            const rawData = await response.json();

            if (rawData.error) {
                throw new Error(rawData.error);
            }

            // Agrupar por competencia
            const grouped = {};
            rawData.forEach(item => {
                const id = item.comp_id;
                if (!grouped[id]) {
                    grouped[id] = {
                        comp_id: item.comp_id,
                        comp_nombre: item.comp_nombre,
                        comp_descripcion: item.comp_descripcion,
                        programas: []
                    };
                }
                if (item.prog_denominacion) {
                    grouped[id].programas.push({
                        codigo: item.prog_codigo,
                        nombre: item.prog_denominacion
                    });
                }
            });

            this.competenciasInfo = Object.values(grouped);
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
                    <td colspan="4" class="text-center py-8">
                        <div class="empty-state">
                            <ion-icon name="bookmarks-outline" class="text-4xl text-slate-200 mb-2"></ion-icon>
                            <p class="text-slate-400">No se encontraron competencias asociadas.</p>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = data.map((comp, index) => {
            const numProgs = comp.programas.length;
            return `
                <tr class="hover:bg-green-50/50 transition-all cursor-pointer group" onclick="misComp.showCompDetails('${comp.comp_id}')">
                    <td class="text-center font-medium text-slate-400">${index + 1}</td>
                    <td class="py-4">
                        <div class="font-bold text-slate-800 group-hover:text-sena-green transition-colors">${comp.comp_nombre || 'Sin nombre'}</div>
                        <div class="text-[10px] text-slate-400 font-mono mt-1">ID: ${comp.comp_id || 'N/A'}</div>
                    </td>
                    <td class="py-4">
                        <div class="text-xs text-slate-500 line-clamp-2 max-w-sm">
                            ${comp.comp_descripcion || 'Sin descripción'}
                        </div>
                    </td>
                    <td class="py-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-full text-[10px] font-bold text-slate-600 group-hover:bg-sena-green group-hover:text-white transition-all">
                            <ion-icon name="school-outline"></ion-icon>
                            ${numProgs} Programa${numProgs !== 1 ? 's' : ''} habilitado${numProgs !== 1 ? 's' : ''}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    showCompDetails(compId) {
        const comp = this.competenciasInfo.find(c => c.comp_id == compId);
        if (!comp) return;

        document.getElementById('modalCompNombre').textContent = comp.comp_nombre;
        document.getElementById('modalCompId').textContent = `ID: ${comp.comp_id}`;
        document.getElementById('modalCompDesc').textContent = comp.comp_descripcion || 'Sin descripción detallada disponible.';

        const progsList = document.getElementById('modalProgsList');
        progsList.innerHTML = comp.programas.map(p => `
            <div class="p-3 bg-white border border-slate-100 rounded-xl shadow-sm hover:border-sena-green/30 transition-all group">
                <div class="text-xs font-bold text-slate-800 group-hover:text-sena-green transition-colors">${p.nombre}</div>
                <div class="text-[10px] text-slate-400 font-mono mt-1">CÓDIGO: ${p.codigo}</div>
            </div>
        `).join('') || '<p class="col-span-2 text-center text-slate-400 italic py-4">No hay programas registrados.</p>';

        this.modal.classList.add('show');
    }

    setupSearch() {
        const strFilter = document.getElementById('searchInput');
        if (strFilter) {
            strFilter.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                const filtered = this.competenciasInfo.filter(c =>
                    (c.comp_nombre && c.comp_nombre.toLowerCase().includes(term)) ||
                    (c.comp_descripcion && c.comp_descripcion.toLowerCase().includes(term)) ||
                    (c.programas && c.programas.some(p => p.nombre.toLowerCase().includes(term) || String(p.codigo).includes(term)))
                );
                this.renderTable(filtered);
            });
        }
    }

    showError(msg) {
        const tbody = document.getElementById('competenciasTableBody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-8 text-red-500 font-medium">${msg}</td></tr>`;
        }
    }
}

let misComp;
document.addEventListener('DOMContentLoaded', () => {
    misComp = new MisCompetencias();
});
