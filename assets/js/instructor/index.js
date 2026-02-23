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
        this.centros = [];
        this.filteredInstructores = [];

        this.init();
    }

    async init() {
        this.bindEvents();
        await Promise.all([
            this.loadCentros(),
            this.loadInstructores()
        ]);
    }

    bindEvents() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                this.currentPage = 1;
                this.renderTable();
            });
        }

        const sedeFilter = document.getElementById('sedeFilter');
        if (sedeFilter) {
            sedeFilter.addEventListener('change', () => {
                this.currentPage = 1;
                this.renderTable();
            });
        }

        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.loadInstructores());
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

    async loadCentros() {
        try {
            const response = await fetch('../../routing.php?controller=instructor&action=getCentros', {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Error al cargar centros');
            this.centros = await response.json();

            const sedeFilter = document.getElementById('sedeFilter');
            if (sedeFilter) {
                sedeFilter.innerHTML = '<option value="">Todos los Centros</option>';
                this.centros.forEach(centro => {
                    const option = document.createElement('option');
                    option.value = centro.cent_id;
                    option.textContent = centro.cent_nombre;
                    sedeFilter.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading centros:', error);
        }
    }

    async loadInstructores() {
        try {
            const response = await fetch('../../routing.php?controller=instructor&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.details || data.error || 'Error desconocido');

            this.instructores = data;
            this.renderTable();
        } catch (error) {
            console.error('Error loading instructores:', error);
            const tableBody = document.getElementById('instructorTableBody');
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-gray-500">No pudimos cargar la lista de instructores.</td></tr>`;
            }
        }
    }

    getFilteredData() {
        const searchInput = document.getElementById('searchInput');
        const sedeFilter = document.getElementById('sedeFilter');
        const searchTerm = (searchInput ? searchInput.value : '').toLowerCase();
        const sedeId = sedeFilter ? sedeFilter.value : '';

        return this.instructores.filter(inst => {
            const names = (inst.inst_nombres + ' ' + inst.inst_apellidos).toLowerCase();
            const matchesSearch = names.includes(searchTerm) || inst.inst_correo.toLowerCase().includes(searchTerm);
            const matchesSede = !sedeId || inst.centro_formacion_cent_id == sedeId;
            return matchesSearch && matchesSede;
        });
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
