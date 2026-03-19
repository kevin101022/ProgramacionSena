class AuditoriaManager {
    constructor() {
        this.allData = [];
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadAuditoria();
    }

    bindEvents() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) searchInput.addEventListener('input', () => { this.currentPage = 1; this.renderTable(); });

        const coordFilter = document.getElementById('coordFilter');
        if (coordFilter) coordFilter.addEventListener('change', () => { this.currentPage = 1; this.renderTable(); });

        const actionFilter = document.getElementById('actionFilter');
        if (actionFilter) actionFilter.addEventListener('change', () => { this.currentPage = 1; this.renderTable(); });

        const prevBtn = document.getElementById('prevBtn');
        if (prevBtn) prevBtn.addEventListener('click', () => { if (this.currentPage > 1) { this.currentPage--; this.renderTable(); } });

        const nextBtn = document.getElementById('nextBtn');
        if (nextBtn) nextBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(this.getFilteredData().length / this.itemsPerPage);
            if (this.currentPage < totalPages) { this.currentPage++; this.renderTable(); }
        });
    }

    async loadAuditoria() {
        const tableBody = document.getElementById('auditoriaTableBody');
        try {
            const response = await fetch('../../routing.php?controller=auditoria_asignacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.allData = await response.json();
            const totalEl = document.getElementById('totalAuditorias');
            if (totalEl) totalEl.textContent = this.allData.length;
            this.renderTable();
        } catch (error) {
            console.error('Error cargando auditoría:', error);
            if (tableBody) tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-red-500 py-8 text-sm">Error al cargar los datos.</td></tr>`;
        }
    }

    getFilteredData() {
        const searchTerm = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
        const coordFilter = document.getElementById('coordFilter');
        const coordValue = coordFilter ? coordFilter.value : '';
        const actionTerm = document.getElementById('actionFilter')?.value || '';

        return this.allData.filter(item => {
            const fullName = `${item.inst_nombres || ''} ${item.inst_apellidos || ''}`.toLowerCase();
            const matchesSearch = !searchTerm ||
                fullName.includes(searchTerm) ||
                (item.comp_nombre_corto || '').toLowerCase().includes(searchTerm) ||
                (item.area_nombre || '').toLowerCase().includes(searchTerm) ||
                item.tipo_accion.toLowerCase().includes(searchTerm);
            const matchesCoord = !coordValue || String(item.area_id) === String(coordValue);
            const matchesAction = !actionTerm || item.tipo_accion === actionTerm;
            return matchesSearch && matchesCoord && matchesAction;
        });
    }

    renderTable() {
        const tableBody = document.getElementById('auditoriaTableBody');
        if (!tableBody) return;

        const filtered = this.getFilteredData();
        const total = filtered.length;
        const totalPages = Math.ceil(total / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = Math.min(start + this.itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        if (paginated.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-12 text-gray-400 italic text-sm">No se encontraron registros de auditoría.</td></tr>`;
            this.updatePagination(0, 0, 0, 0);
            return;
        }

        tableBody.innerHTML = paginated.map(item => {
            const badgeClass = this.getBadgeClass(item.tipo_accion);
            const actionBadge = this.getActionBadgeLabel(item.tipo_accion);
            const actionPhrase = this.getActionPhrase(item.tipo_accion);
            const instName = (item.inst_nombres || item.inst_apellidos)
                ? `${item.inst_nombres} ${item.inst_apellidos}` : 'Instructor no encontrado';
            const readableDate = this.formatDate(item.fecha_hora);
            let authorName = item.nombre_responsable || item.correo_usuario || 'Desconocido';
            if (item.documento_usuario_accion && item.documento_usuario_accion != 0)
                authorName += ` - CC: ${item.documento_usuario_accion}`;
            const areaTag = item.area_nombre
                ? `<span class="text-[9px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-md mt-1 w-fit uppercase font-bold tracking-wider">${item.area_nombre}</span>` : '';

            return `
                <tr class="hover:bg-slate-50 cursor-pointer transition-colors group" data-id="${item.id_auditoria}">
                    <td class="whitespace-nowrap text-[11px] font-bold text-slate-500">${readableDate}</td>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-bold text-sm text-slate-700">${authorName}</span>
                            <span class="text-[10px] text-slate-400">${item.correo_usuario || ''}</span>
                        </div>
                    </td>
                    <td>
                        <div class="flex flex-col">
                            <span class="badge ${badgeClass} text-[10px] px-2 py-0.5 mb-1 w-fit">${actionBadge}</span>
                            <span class="text-[11px] font-medium text-slate-500">${actionPhrase}</span>
                        </div>
                    </td>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-bold text-sm text-sena-green">${instName}</span>
                            ${areaTag}
                        </div>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-2 pr-2">
                            <span class="text-[10px] font-bold text-sena-green opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">VER DETALLE</span>
                            <div class="btn-icon text-sena-green bg-green-50 rounded-full p-2 flex items-center justify-center">
                                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                            </div>
                        </div>
                    </td>
                </tr>`;
        }).join('');

        tableBody.addEventListener('click', (e) => {
            const row = e.target.closest('tr');
            if (row && row.dataset.id) window.location.href = `ver.php?id=${row.dataset.id}`;
        }, { once: true });

        this.updatePagination(totalPages, start, end, total);
    }

    updatePagination(totalPages, start, end, total) {
        const showingFrom = document.getElementById('showingFrom');
        const showingTo = document.getElementById('showingTo');
        const totalRecords = document.getElementById('totalRecords');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const paginationNumbers = document.getElementById('paginationNumbers');

        if (showingFrom) showingFrom.textContent = total > 0 ? start + 1 : 0;
        if (showingTo) showingTo.textContent = end;
        if (totalRecords) totalRecords.textContent = total;
        if (prevBtn) prevBtn.disabled = this.currentPage === 1;
        if (nextBtn) nextBtn.disabled = this.currentPage === totalPages || totalPages === 0;

        if (paginationNumbers) {
            paginationNumbers.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.className = `w-7 h-7 rounded-lg text-[10px] font-black transition-all ${i === this.currentPage ? 'bg-sena-green text-white shadow-md' : 'bg-white text-slate-400 border border-slate-100 hover:border-sena-green hover:text-sena-green'}`;
                btn.textContent = i;
                btn.onclick = () => { this.currentPage = i; this.renderTable(); };
                paginationNumbers.appendChild(btn);
            }
        }
    }

    formatDate(dateString) {
        if (!dateString) return '--';
        const date = new Date(dateString);
        const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        const day = date.getDate().toString().padStart(2, '0');
        const month = months[date.getMonth()];
        let hours = date.getHours();
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        return `${day} ${month}, ${hours}:${minutes} ${ampm}`;
    }

    getActionPhrase(action) {
        const map = { INSERT: 'Creación de Asignación', UPDATE: 'Modificación de Asignación', DELETE: 'Eliminación de Asignación' };
        return map[action] || action;
    }

    getActionBadgeLabel(action) {
        const map = { INSERT: 'NUEVO', UPDATE: 'MODIFICADO', DELETE: 'ELIMINADO' };
        return map[action] || action;
    }

    getBadgeClass(action) {
        const map = { INSERT: 'badge-green', UPDATE: 'badge-blue', DELETE: 'badge-red' };
        return map[action] || 'badge-gray';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.auditoriaManager = new AuditoriaManager();
});
