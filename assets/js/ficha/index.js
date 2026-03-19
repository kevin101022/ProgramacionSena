/**
 * Ficha Management JavaScript
 * Refactored to Class-based manager with Pagination support.
 */
class FichaManager {
    constructor() {
        this.fichas = [];
        this.allCoordinaciones = [];
        this.currentPage = 1;
        this.itemsPerPage = 5;

        this.init();
    }

    async init() {
        this.bindEvents();
        await this.loadInitialData();
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

        const addBtn = document.getElementById('addBtn');
        if (addBtn) addBtn.onclick = () => this.openModal();

        const closeBtn = document.getElementById('closeModal');
        if (closeBtn) closeBtn.onclick = () => this.closeModal();

        const cancelBtn = document.getElementById('cancelBtn');
        if (cancelBtn) cancelBtn.onclick = () => this.closeModal();

        const form = document.getElementById('fichaForm');
        if (form) {
            form.onsubmit = (e) => this.handleFormSubmit(e);
        }

        const sedeSelect = document.getElementById('sede_id');
        if (sedeSelect) {
            sedeSelect.addEventListener('change', () => this.filterCoordinacionesBySede(sedeSelect.value));
        }

        // Global delete trigger
        window.deleteFicha = (id) => this.confirmDelete(id);
    }

    async loadInitialData() {
        try {
            const headers = { 'Accept': 'application/json' };

            // Promise.all to load select data and main list
            const [sedeRes, progRes, instRes, coordRes, fichaRes] = await Promise.all([
                fetch('../../routing.php?controller=sede&action=index', { headers }),
                fetch('../../routing.php?controller=programa&action=index', { headers }),
                fetch('../../routing.php?controller=instructor&action=index', { headers }),
                fetch('../../routing.php?controller=coordinacion&action=index', { headers }),
                fetch('../../routing.php?controller=ficha&action=index', { headers })
            ]);

            // Populate Sedes
            if (sedeRes.ok) {
                const sedes = await sedeRes.json();
                const sedeSelect = document.getElementById('sede_id');
                if (sedeSelect) {
                    sedeSelect.innerHTML = '<option value="">Seleccione sede...</option>';
                    sedes.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.sede_id;
                        opt.textContent = s.sede_nombre;
                        sedeSelect.appendChild(opt);
                    });
                }
            }

            // Populate Programas
            if (progRes.ok) {
                const programas = await progRes.json();
                const programaSelect = document.getElementById('programa_id');
                if (programaSelect) {
                    programaSelect.innerHTML = '<option value="">Seleccione programa...</option>';
                    programas.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.prog_codigo;
                        opt.textContent = `${p.prog_codigo} - ${p.prog_denominacion}`;
                        programaSelect.appendChild(opt);
                    });
                }
            }

            // Populate Instructores
            if (instRes.ok) {
                const instructores = await instRes.json();
                const instructorSelect = document.getElementById('instructor_id');
                if (instructorSelect) {
                    instructorSelect.innerHTML = '<option value="">Seleccione instructor líder...</option>';
                    instructores.forEach(i => {
                        const opt = document.createElement('option');
                        opt.value = i.inst_id;
                        opt.textContent = `${i.inst_nombres} ${i.inst_apellidos}`;
                        instructorSelect.appendChild(opt);
                    });
                }
            }

            // Populate Coordinaciones
            if (coordRes.ok) {
                this.allCoordinaciones = await coordRes.json();
                const coordinacionSelect = document.getElementById('coordinacion_id');
                if (coordinacionSelect) {
                    coordinacionSelect.innerHTML = '<option value="">Seleccione coordinación...</option>';
                    this.allCoordinaciones.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.coord_id;
                        opt.textContent = c.cent_nombre ? `${c.coord_descripcion} - ${c.cent_nombre}` : c.coord_descripcion;
                        coordinacionSelect.appendChild(opt);
                    });
                }
            }

            // Load Fichas
            if (fichaRes.ok) {
                const data = await fichaRes.json();
                this.fichas = Array.isArray(data) ? data : [];
                this.renderTable();
            } else {
                throw new Error('Error al cargar fichas');
            }

        } catch (error) {
            console.error('Error loading initial data:', error);
            NotificationService.showError('No pudimos cargar la información de las fichas.');
        }
    }

    filterCoordinacionesBySede(sedeId) {
        const coordinacionSelect = document.getElementById('coordinacion_id');
        if (coordinacionSelect) {
            coordinacionSelect.innerHTML = '<option value="">Seleccione coordinación...</option>';
            const filtered = this.allCoordinaciones.filter(c => !sedeId || c.sede_id == sedeId);
            filtered.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.coord_id;
                opt.textContent = c.coord_descripcion;
                coordinacionSelect.appendChild(opt);
            });
        }
    }

    getFilteredData() {
        const searchInput = document.getElementById('searchInput');
        const term = (searchInput ? searchInput.value : '').toLowerCase();

        return this.fichas.filter(f =>
            String(f.fich_id).toLowerCase().includes(term) ||
            (f.titpro_nombre || '').toLowerCase().includes(term) ||
            (f.prog_denominacion || '').toLowerCase().includes(term) ||
            (f.inst_nombres || '').toLowerCase().includes(term) ||
            (f.inst_apellidos || '').toLowerCase().includes(term)
        );
    }

    renderTable() {
        const tableBody = document.getElementById('fichaTableBody');
        if (!tableBody) return;

        const filtered = this.getFilteredData();
        const total = filtered.length;

        // Update stats
        const totalLabel = document.getElementById('totalFichas');
        const totalRecords = document.getElementById('totalRecords');
        if (totalLabel) totalLabel.textContent = this.fichas.length;
        if (totalRecords) totalRecords.textContent = total;

        const totalPages = Math.ceil(total / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = Math.min(start + this.itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';

        if (paginated.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-500">No se encontraron fichas</td></tr>';
        } else {
            paginated.forEach(f => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${f.fich_id}`;
                
                const totalComps = parseInt(f.total_comps) || 0;
                const assignedComps = parseInt(f.assigned_comps) || 0;
                const progress = totalComps > 0 ? Math.round((assignedComps / totalComps) * 100) : 0;
                const historyHtml = f.instructores_historial ? `
                    <div class="mt-2 text-xs border-t pt-2 border-gray-100">
                        <span class="text-gray-400 block mb-1 font-semibold">Histórico:</span>
                        <div class="text-gray-500 truncate max-w-xs" title="${f.instructores_historial}">
                            ${f.instructores_historial}
                        </div>
                    </div>
                ` : '';

                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <span class="text-sena-green font-bold text-sm tracking-wider flex items-center gap-1">
                            <ion-icon src="../../assets/ionicons/folder-open-outline.svg"></ion-icon>
                            ${f.fich_id}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="user-cell">
                            <div class="user-info-sm px-0">
                                <div class="user-name-sm text-gray-800">${f.titpro_nombre || 'N/A'}</div>
                                <div class="user-meta-sm text-gray-500">${f.prog_denominacion || ''}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 w-fit">
                            <div class="w-8 h-8 rounded-full bg-sena-green/10 text-sena-green flex items-center justify-center font-bold text-xs">
                                ${f.inst_nombres ? f.inst_nombres[0] + (f.inst_apellidos ? f.inst_apellidos[0] : '') : '?'}
                            </div>
                            <span class="text-sm font-semibold text-gray-700">${f.inst_nombres ? (f.inst_nombres + ' ' + f.inst_apellidos) : 'Sin Líder'}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 pb-1.5 rounded-full text-xs font-semibold whitespace-nowrap
                            ${f.fich_jornada === 'Mañana' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 
                              f.fich_jornada === 'Tarde' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 
                              'bg-purple-50 text-purple-700 border border-purple-200'}">
                            <ion-icon src="../../assets/ionicons/${f.fich_jornada === 'Mañana' ? 'sunny' : f.fich_jornada === 'Tarde' ? 'partly-sunny' : 'moon'}-outline.svg"></ion-icon>
                            ${f.fich_jornada || 'Sin jornada'}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex flex-col items-end w-full max-w-[120px] ml-auto" title="${assignedComps} de ${totalComps} competencias asignadas">
                            <div class="flex justify-between w-full text-[10px] mb-1 font-semibold
                                ${progress === 100 ? 'text-sena-green' : 'text-amber-600'}">
                                <span>Cobertura</span>
                                <span>${progress}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 relative overflow-hidden">
                                <div class="h-1.5 rounded-full transition-all duration-500
                                    ${progress === 100 ? 'bg-sena-green' : 'bg-amber-400'}"
                                    style="width: ${progress}%">
                                </div>
                            </div>
                        </div>
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

    openModal(ficha = null) {
        const modal = document.getElementById('fichaModal');
        const form = document.getElementById('fichaForm');
        const modalTitle = document.getElementById('modalTitle');
        const fichIdInput = document.getElementById('fich_id');
        const fichIdOldInput = document.getElementById('fich_id_old');

        if (form) form.reset();

        if (ficha) {
            if (modalTitle) modalTitle.textContent = 'Editar Ficha';
            if (fichIdInput) {
                fichIdInput.value = ficha.fich_id;
                fichIdInput.readOnly = true;
            }
            if (fichIdOldInput) fichIdOldInput.value = ficha.fich_id;

            const fields = {
                'programa_id': ficha.programa_prog_id || ficha.programa_prog_codigo,
                'instructor_id': ficha.instructor_inst_id_lider || ficha.instructor_inst_id,
                'coordinacion_id': ficha.coordinacion_coord_id,
                'fich_jornada': ficha.fich_jornada
            };

            for (const [id, value] of Object.entries(fields)) {
                const el = document.getElementById(id);
                if (el) el.value = value || '';
            }

            if (document.getElementById('fich_fecha_ini_lectiva') && ficha.fich_fecha_ini_lectiva) {
                document.getElementById('fich_fecha_ini_lectiva').value = ficha.fich_fecha_ini_lectiva.split('T')[0];
            }
            if (document.getElementById('fich_fecha_fin_lectiva') && ficha.fich_fecha_fin_lectiva) {
                document.getElementById('fich_fecha_fin_lectiva').value = ficha.fich_fecha_fin_lectiva.split('T')[0];
            }
        } else {
            if (modalTitle) modalTitle.textContent = 'Nueva Ficha';
            if (fichIdInput) {
                fichIdInput.value = '';
                fichIdInput.readOnly = false;
            }
            if (fichIdOldInput) fichIdOldInput.value = '';
        }

        if (modal) modal.classList.add('show');
    }

    closeModal() {
        const modal = document.getElementById('fichaModal');
        if (modal) modal.classList.remove('show');
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        const idOld = document.getElementById('fich_id_old').value;
        const action = idOld ? 'update' : 'store';
        const form = e.target;
        const formData = new FormData(form);

        try {
            const response = await fetch(`../../routing.php?controller=ficha&action=${action}`, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            if (response.ok) {
                NotificationService.showSuccess(idOld ? '¡Ficha actualizada!' : '¡Ficha registrada!');
                this.closeModal();
                await this.loadInitialData();
            } else {
                NotificationService.showError('No se pudo guardar la ficha. Revisa los datos.');
            }
        } catch (error) {
            NotificationService.showError('Error de conexión.');
        }
    }

    confirmDelete(id) {
        NotificationService.showConfirm(
            '¿Estás seguro de que deseas eliminar esta ficha? Esta acción no se puede deshacer.',
            async () => {
                try {
                    const response = await fetch(`../../routing.php?controller=ficha&action=destroy&id=${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (response.ok) {
                        NotificationService.showSuccess('Ficha eliminada correctamente.');
                        await this.loadInitialData();
                    } else {
                        NotificationService.showError('No se pudo eliminar la ficha. Es posible que tenga asignaciones.');
                    }
                } catch (error) {
                    NotificationService.showError('Error de conexión.');
                }
            }
        );
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.fichaManager = new FichaManager();
});
