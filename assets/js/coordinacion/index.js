/**
 * Coordinacion Management JavaScript - Premium Refactor
 */
class CoordinacionManager {
    constructor() {
        this.coordinaciones = [];
        this.centros = [];
        this.filteredCoordinaciones = [];
        this.currentPage = 1;
        this.itemsPerPage = 10; // Aumentado para mejor visualización en tablas premium

        this.init();
    }

    async init() {
        this.bindEvents();
        await Promise.all([
            this.loadCentros(),
            this.loadCoordinaciones()
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

        const addBtn = document.querySelector('[onclick="coordinacionModule.openModal()"]') || document.getElementById('addBtn');
        // No es necesario bindear el onclick si ya está en el HTML, pero por si acaso:
        window.coordinacionModule = {
            openModal: (coord) => this.openModal(coord)
        };

        const closeBtn = document.getElementById('closeModal');
        if (closeBtn) closeBtn.onclick = () => this.closeModal();

        const cancelBtn = document.getElementById('cancelBtn');
        if (cancelBtn) cancelBtn.onclick = () => this.closeModal();

        const form = document.getElementById('coordinacionForm');
        if (form) {
            form.onsubmit = (e) => this.handleFormSubmit(e);
        }

        window.deleteCoordinacion = (id) => this.confirmDelete(id);
        window.editCoordinacion = (id) => {
            const coord = this.coordinaciones.find(c => c.coord_id == id);
            this.openModal(coord);
        };
    }

    async loadCentros() {
        try {
            const response = await fetch('../../routing.php?controller=centro_formacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Error al cargar centros');
            this.centros = await response.json();

            const centroSelect = document.getElementById('centro_id');
            if (centroSelect && centroSelect.tagName === 'SELECT') {
                centroSelect.innerHTML = '<option value="">Seleccione un centro...</option>';
                this.centros.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.cent_id;
                    opt.textContent = c.cent_nombre;
                    centroSelect.appendChild(opt);
                });
            }
        } catch (error) {
            console.error('Error al cargar centros:', error);
        }
    }

    async loadCoordinaciones() {
        try {
            const response = await fetch('../../routing.php?controller=coordinacion&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.details || data.error || 'Error del servidor');

            this.coordinaciones = Array.isArray(data) ? data : [];
            this.renderTable();
        } catch (error) {
            console.error('Error:', error);
            if (window.NotificationService) {
                NotificationService.showError('No pudimos cargar las coordinaciones.');
            }
        }
    }

    getFilteredData() {
        const searchInput = document.getElementById('searchInput');
        const term = (searchInput ? searchInput.value : '').toLowerCase().trim();

        return this.coordinaciones.filter(c =>
            (c.coord_descripcion || '').toLowerCase().includes(term) ||
            (c.cent_nombre || '').toLowerCase().includes(term) ||
            (c.coord_nombre_coordinador || '').toLowerCase().includes(term)
        );
    }

    renderTable() {
        const tableBody = document.getElementById('coordinacionTableBody');
        if (!tableBody) return;

        const filtered = this.getFilteredData();
        const totalFiltered = filtered.length;

        // Metadata counters
        const totalLabel = document.getElementById('totalCoordinaciones');
        const activeCount = document.getElementById('totalRecords');

        if (totalLabel) totalLabel.textContent = this.coordinaciones.length;
        if (activeCount) activeCount.textContent = totalFiltered;

        const totalPages = Math.ceil(totalFiltered / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = Math.min(start + this.itemsPerPage, totalFiltered);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';

        if (paginated.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center py-20">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mb-4">
                                <ion-icon src="../../assets/ionicons/search-outline.svg" class="text-3xl"></ion-icon>
                            </div>
                            <p class="text-slate-400 font-medium italic">No se encontraron coordinaciones con esos criterios.</p>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            paginated.forEach((c, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${c.coord_id}`;
                tr.innerHTML = `
                    <td class="text-xs font-semibold text-gray-400 pl-6">
                        ${String(start + index + 1).padStart(2, '0')}
                    </td>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm">
                                <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                            </div>
                            <div class="user-info-sm">
                                <div class="user-name-sm">${c.coord_descripcion}</div>
                                <div class="user-meta-sm">ID: ${c.coord_id}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="contact-cell">
                            <div class="contact-item">
                                <ion-icon src="../../assets/ionicons/location-outline.svg"></ion-icon>
                                <span>${c.cent_nombre || 'N/A'}</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-right pr-6">
                        <button class="btn-more-glass" onclick="event.stopPropagation(); window.location.href='ver.php?id=${c.coord_id}'">
                            <span>Ver más</span>
                            <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                        </button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        }

        this.updatePagination(totalPages, start, end, totalFiltered);
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
                btn.className = `w-7 h-7 rounded-lg text-[10px] font-black transition-all ${i === this.currentPage ? 'bg-sena-green text-white shadow-md' : 'bg-white text-slate-400 border border-slate-100 hover:border-sena-green hover:text-sena-green'}`;
                btn.textContent = i;
                btn.onclick = (e) => {
                    e.stopPropagation();
                    this.currentPage = i;
                    this.renderTable();
                };
                paginationNumbers.appendChild(btn);
            }
        }
    }

    async loadDisponibles() {
        try {
            const select = document.getElementById('coordinador_actual');
            if (!select) return;

            const response = await fetch('../../routing.php?controller=coordinacion&action=get_coordinadores_disponibles');
            if (response.ok) {
                const data = await response.json();
                this.coordinadoresDisponibles = data;
            }
        } catch (error) {
            console.error('Error cargando coordinadores disponibles', error);
        }
    }

    async openModal(coord = null) {
        const modal = document.getElementById('coordinacionModal');
        const form = document.getElementById('coordinacionForm');
        const modalTitle = document.getElementById('modalTitle');
        const coordIdInput = document.getElementById('coord_id');
        const coordNombreInput = document.getElementById('coord_nombre');
        const centroIdInput = document.getElementById('centro_id');
        const coordinadorSelect = document.getElementById('coordinador_actual');

        if (form) form.reset();

        await this.loadDisponibles();

        if (coordinadorSelect) {
            coordinadorSelect.innerHTML = '<option value="">Nadie (Vacante)</option>';
            if (this.coordinadoresDisponibles) {
                this.coordinadoresDisponibles.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.numero_documento;
                    opt.textContent = `${c.coord_nombre_coordinador} (${c.numero_documento})`;
                    coordinadorSelect.appendChild(opt);
                });
            }
        }

        if (coord) {
            if (modalTitle) modalTitle.textContent = 'Editar Coordinación';
            if (coordIdInput) coordIdInput.value = coord.coord_id;
            if (coordNombreInput) coordNombreInput.value = coord.coord_descripcion;
            if (centroIdInput && centroIdInput.tagName === 'SELECT') {
                centroIdInput.value = coord.cent_id || coord.centro_formacion_cent_id;
            }
            if (coordinadorSelect) {
                if (coord.numero_documento && coord.numero_documento !== 'Vacante') {
                    const existe = Array.from(coordinadorSelect.options).some(opt => opt.value == coord.numero_documento);
                    if (!existe) {
                        const opt = document.createElement('option');
                        opt.value = coord.numero_documento;
                        opt.textContent = `${coord.coord_nombre_coordinador} (${coord.numero_documento}) - Actualmente asignado`;
                        coordinadorSelect.appendChild(opt);
                    }
                    coordinadorSelect.value = coord.numero_documento;
                } else {
                    coordinadorSelect.value = '';
                }
            }
        } else {
            if (modalTitle) modalTitle.textContent = 'Nueva Coordinación';
            if (coordIdInput) coordIdInput.value = '';
            if (centroIdInput && centroIdInput.tagName === 'SELECT') centroIdInput.value = '';
            if (coordinadorSelect) coordinadorSelect.value = '';
        }
        if (modal) modal.classList.add('show');
    }

    closeModal() {
        const modal = document.getElementById('coordinacionModal');
        if (modal) modal.classList.remove('show');
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('coord_id').value;
        const modalTitle = document.getElementById('modalTitle');
        const isEdit = modalTitle && modalTitle.textContent.includes('Editar');
        const action = isEdit ? 'update' : 'store';

        const data = {
            coord_descripcion: document.getElementById('coord_nombre').value,
            centro_formacion_cent_id: document.getElementById('centro_id').value,
            coordinador_actual: document.getElementById('coordinador_actual')?.value || ''
        };
        if (isEdit) data.coord_id = id;

        try {
            const response = await fetch(`../../routing.php?controller=coordinacion&action=${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                if (window.NotificationService) {
                    NotificationService.showSuccess(isEdit ? '¡Coordinación actualizada!' : '¡Coordinación creada!');
                }
                this.closeModal();
                await this.loadCoordinaciones();
            } else {
                if (window.NotificationService) {
                    NotificationService.showError('No se pudo guardar la coordinación.');
                }
            }
        } catch (error) {
            console.error(error);
            if (window.NotificationService) {
                NotificationService.showError('Error de conexión.');
            }
        }
    }

    confirmDelete(id) {
        if (!window.NotificationService) return;
        NotificationService.showConfirm(
            '¿Estás seguro de que deseas eliminar esta coordinación? Esta acción no se puede deshacer.',
            async () => {
                try {
                    const response = await fetch(`../../routing.php?controller=coordinacion&action=destroy&id=${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (response.ok) {
                        NotificationService.showSuccess('Coordinación eliminada correctamente.');
                        await this.loadCoordinaciones();
                    } else {
                        NotificationService.showError('No se pudo eliminar. Es posible que tenga dependencias.');
                    }
                } catch (error) {
                    NotificationService.showError('Error de conexión.');
                }
            }
        );
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.coordinacionManager = new CoordinacionManager();
});
