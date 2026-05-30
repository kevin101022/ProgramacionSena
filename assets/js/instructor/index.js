/**
 * Instructor Management JavaScript
 * Refactored to Class-based architecture for consistency and maintainability.
 * Added Pagination support (5 items per page).
 */
class InstructorManager {
    constructor() {
        this.currentPage = 1;
        this.itemsPerPage = 5;
        this.instructores = [];
        this.filteredInstructores = [];
        this.competencias = [];
        this.habilitaciones = [];

        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadInstructores();
    }

    bindEvents() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                this.currentPage = 1;
                this.renderTable();
            });
        }

        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.loadInstructores());
        }

        const compFilter = document.getElementById('competenciaFilter');
        if (compFilter) {
            compFilter.addEventListener('change', () => {
                this.currentPage = 1;
                this.renderTable();
            });
        }

        // Pagination buttons
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

        // Global functions for delete triggers if needed
        window.deleteInstructor = (id) => this.confirmDelete(id);
    }

    async loadInstructores() {
        try {
            const [resInst, resComp, resHab] = await Promise.all([
                fetch('../../routing.php?controller=instructor&action=index', { headers: { 'Accept': 'application/json' } }),
                fetch('../../routing.php?controller=competencia&action=index', { headers: { 'Accept': 'application/json' } }),
                fetch('../../routing.php?controller=instru_competencia&action=index', { headers: { 'Accept': 'application/json' } })
            ]);

            if (!resInst.ok) throw new Error('Error al cargar instructores');
            
            this.instructores = await resInst.json();
            this.competencias = await resComp.json();
            this.habilitaciones = await resHab.json();

            this.populateCompetenciaFilter();
            this.renderTable();
        } catch (error) {
            console.error('Error loading data:', error);
            const tableBody = document.getElementById('instructorTableBody');
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-gray-500">No pudimos cargar la información.</td></tr>`;
            }
        }
    }

    normalizeString(str) {
        return (str || '')
            .trim()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toUpperCase();
    }

    populateCompetenciaFilter() {
        const select = document.getElementById('competenciaFilter');
        if (!select) return;
        
        const uniqueNames = new Set();
        const options = [];
        
        const sortedComps = [...this.competencias];
        sortedComps.sort((a, b) => (a.comp_nombre_corto || '').localeCompare(b.comp_nombre_corto || ''));
        
        sortedComps.forEach(comp => {
            const name = (comp.comp_nombre_corto || '').trim();
            const normalized = this.normalizeString(name);
            if (name && !uniqueNames.has(normalized)) {
                uniqueNames.add(normalized);
                options.push({
                    value: normalized,
                    text: name
                });
            }
        });
        
        if (window.refreshTS) {
            window.refreshTS(select, options, 'Todas las competencias...');
        } else {
            select.innerHTML = '<option value="">Todas las competencias...</option>';
            options.forEach(optData => {
                const opt = document.createElement('option');
                opt.value = optData.value;
                opt.textContent = optData.text;
                select.appendChild(opt);
            });
        }
    }

    getFilteredData() {
        const searchInput = document.getElementById('searchInput');
        const searchTerm = (searchInput ? searchInput.value : '').toLowerCase();
        const compFilter = document.getElementById('competenciaFilter')?.value;

        return this.instructores.filter(inst => {
            const names = (inst.inst_nombres + ' ' + inst.inst_apellidos).toLowerCase();
            const matchesSearch = names.includes(searchTerm) || inst.inst_correo.toLowerCase().includes(searchTerm);
            
            let matchesComp = true;
            if (compFilter) {
                // Find all comp_ids that have the same normalized comp_nombre_corto
                const matchingCompIds = this.competencias
                    .filter(c => this.normalizeString(c.comp_nombre_corto) === compFilter)
                    .map(c => Number(c.comp_id));
                
                matchesComp = this.habilitaciones.some(h => 
                    h.instructor_inst_id == inst.inst_id && matchingCompIds.includes(Number(h.competencia_comp_id))
                );
            }
            
            return matchesSearch && matchesComp;
        });
    }

    getCompetenciasPills(instId) {
        const habs = this.habilitaciones.filter(h => h.instructor_inst_id == instId);
        if (habs.length === 0) return '<span class="text-[10px] text-gray-400 italic">Sin competencias</span>';
        
        // Show max 2 pills, then +X
        let html = '';
        const limit = 2;
        for (let i = 0; i < Math.min(habs.length, limit); i++) {
            const comp = this.competencias.find(c => c.comp_id == habs[i].competencia_comp_id);
            const name = comp ? comp.comp_nombre_corto : 'Comp. ' + habs[i].competencia_comp_id;
            html += `<span class="inline-block bg-green-50 text-green-700 border border-green-200 text-[10px] font-medium px-2 py-0.5 rounded">${name}</span>`;
        }
        if (habs.length > limit) {
            html += `<span class="inline-block bg-gray-100 text-gray-600 text-[10px] font-medium px-2 py-0.5 rounded" title="Ver perfil para más">+${habs.length - limit}</span>`;
        }
        return html;
    }

    renderTable() {
        const tableBody = document.getElementById('instructorTableBody');
        if (!tableBody) return;

        const filtered = this.getFilteredData();
        const total = filtered.length;

        // Update stats
        const totalLabel = document.getElementById('totalInstructores');
        const totalRecords = document.getElementById('totalRecords');
        if (totalLabel) totalLabel.textContent = this.instructores.length;
        if (totalRecords) totalRecords.textContent = total;

        const totalPages = Math.ceil(total / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = Math.min(start + this.itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';

        if (paginated.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-500">No se encontraron instructores</td></tr>';
        } else {
            paginated.forEach((inst, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/30 transition-all cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${inst.inst_id}`;
                tr.innerHTML = `
                    <td class="text-xs font-medium text-gray-400">
                        ${String(start + index + 1).padStart(2, '0')}
                    </td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm">
                                ${inst.inst_nombres[0]}${inst.inst_apellidos[0]}
                            </div>
                            <div class="user-info-sm">
                                <div class="user-name-sm">${inst.inst_nombres} ${inst.inst_apellidos}</div>
                                <div class="user-meta-sm">Instructor SENA</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="contact-cell">
                            <div class="contact-item">
                                <ion-icon src="../../assets/ionicons/mail-outline.svg"></ion-icon>
                                <span>${inst.inst_correo}</span>
                            </div>
                            <div class="contact-item">
                                <ion-icon src="../../assets/ionicons/call-outline.svg"></ion-icon>
                                <span>${inst.inst_telefono || 'N/A'}</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1 mt-2">
                            ${this.getCompetenciasPills(inst.inst_id)}
                        </div>
                    </td>
                    <td>
                        <div class="badge-glass">
                            ${inst.cent_nombre || 'Sin centro'}
                        </div>
                    </td>
                    <td class="text-right">
                        <button class="btn-more-glass" onclick="event.stopPropagation(); window.location.href='ver.php?id=${inst.inst_id}'">
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
            renderPaginationCarousel(paginationNumbers, this.currentPage, totalPages, (page) => {
                this.currentPage = page;
                this.renderTable();
            });
        }
    }

    confirmDelete(id) {
        NotificationService.showConfirm(
            '¿Estás seguro de que deseas eliminar este instructor? Esta acción no se puede deshacer.',
            async () => {
                try {
                    const response = await fetch(`../../routing.php?controller=instructor&action=destroy&id=${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (response.ok) {
                        NotificationService.showSuccess('El instructor fue eliminado correctamente.');
                        await this.loadInstructores();
                    } else {
                        const data = await response.json();
                        NotificationService.showError(data.error ? 'No se pudo eliminar el instructor. Es posible que tenga asignaciones activas.' : 'Ocurrió un error al eliminar.');
                    }
                } catch (e) {
                    NotificationService.showError('No pudimos conectar con el servidor.');
                }
            }
        );
    }
}

// Iniciar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.instructorManager = new InstructorManager();
});
