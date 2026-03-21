/**
 * Habilitacion (Instru Competencia) Management JavaScript
 * Refactored to Class-based manager with Pagination support.
 */
class HabilitacionManager {
    constructor() {
        this.habilitaciones = [];
        this.instructores = [];
        this.programas = [];
        this.currentPage = 1;
        this.itemsPerPage = 10;

        this.init();
    }

    async init() {
        this.bindEvents();
        await Promise.all([
            this.loadInstructores(),
            this.loadProgramas(),
            this.loadHabilitaciones(),
            this.loadCompetenciasForFilter()
        ]);
    }

    async loadCompetenciasForFilter() {
        const filter = document.getElementById('competenciaFilter');
        if (!filter) return;

        try {
            const res = await fetch('../../routing.php?controller=competencia&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const competencias = await res.json();

            if (Array.isArray(competencias)) {
                filter.innerHTML = '<option value="">Todas las competencias</option>';
                competencias.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.comp_id;
                    opt.textContent = c.comp_nombre_corto;
                    filter.appendChild(opt);
                });
            }
        } catch (e) {
            console.error('Error loading filters:', e);
        }
    }

    bindEvents() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                this.currentPage = 1;
                this.renderTable();
            });
        }

        const compFilter = document.getElementById('competenciaFilter');
        if (compFilter) {
            compFilter.addEventListener('change', () => {
                this.currentPage = 1;
                this.renderTable();
            });
        }

        const progSelect = document.getElementById('programa_id');
        if (progSelect) {
            progSelect.addEventListener('change', (e) => this.loadCompetenciasByPrograma(e.target.value));
        }

        const prevBtn = document.getElementById('prevBtn');
        if (prevBtn) prevBtn.onclick = () => { if (this.currentPage > 1) { this.currentPage--; this.renderTable(); } };

        const nextBtn = document.getElementById('nextBtn');
        if (nextBtn) nextBtn.onclick = () => {
            const totalPages = Math.ceil(this.getFilteredData().length / this.itemsPerPage);
            if (this.currentPage < totalPages) { this.currentPage++; this.renderTable(); }
        };

        const addBtn = document.getElementById('addBtn');
        if (addBtn) addBtn.onclick = () => this.openModal();

        const closeBtn = document.getElementById('closeModal');
        if (closeBtn) closeBtn.onclick = () => this.closeModal();

        const cancelBtn = document.getElementById('cancelBtn');
        if (cancelBtn) cancelBtn.onclick = () => this.closeModal();

        const form = document.getElementById('habilitacionForm');
        if (form) {
            form.onsubmit = (e) => this.handleFormSubmit(e);
        }

        window.deleteHabilitacion = (id) => this.confirmDelete(id);
    }

    async loadInstructores() {
        try {
            const res = await fetch('../../routing.php?controller=instructor&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.instructores = await res.json();
            const instructorSelect = document.getElementById('instructor_id');
            if (instructorSelect) {
                instructorSelect.innerHTML = '<option value="">Seleccione instructor...</option>';
                this.instructores.forEach(i => {
                    const opt = document.createElement('option');
                    opt.value = i.numero_documento;
                    opt.textContent = `${i.inst_nombres} ${i.inst_apellidos} (${i.numero_documento})`;
                    instructorSelect.appendChild(opt);
                });
            }
        } catch (e) { console.error('Error loading instructors:', e); }
    }

    async loadProgramas() {
        try {
            const res = await fetch('../../routing.php?controller=programa&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.programas = await res.json();
            const progSelect = document.getElementById('programa_id');
            if (progSelect) {
                progSelect.innerHTML = '<option value="">Seleccione programa...</option>';
                this.programas.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.prog_codigo;
                    opt.textContent = p.prog_denominacion;
                    progSelect.appendChild(opt);
                });
            }
        } catch (e) { console.error('Error loading programs:', e); }
    }

    async loadCompetenciasByPrograma(progId, selectedCompId = null) {
        const container = document.getElementById('competencias_container');
        if (!container) return;

        if (!progId) {
            container.innerHTML = '<p class="text-xs text-slate-400">Primero seleccione un programa para ver sus competencias...</p>';
            return;
        }

        container.innerHTML = '<p class="text-xs text-slate-400">Cargando competencias...</p>';

        try {
            const res = await fetch(`../../routing.php?controller=competencia&action=getByPrograma&prog_id=${progId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const competencias = await res.json();

            container.innerHTML = '';
            if (Array.isArray(competencias) && competencias.length > 0) {
                competencias.forEach(c => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-3 p-2 hover:bg-white rounded-lg transition-colors border border-transparent hover:border-slate-100';
                    div.innerHTML = `
                        <input type="checkbox" name="competencia_ids[]" value="${c.comp_id}" id="comp_${c.comp_id}" 
                               ${selectedCompId && String(c.comp_id) === String(selectedCompId) ? 'checked' : ''}
                               class="w-4 h-4 text-sena-green border-slate-300 rounded focus:ring-sena-green">
                        <label for="comp_${c.comp_id}" class="text-sm text-slate-700 cursor-pointer flex-1">
                            <span class="font-bold text-sena-green">${c.comp_nombre_corto}</span>
                            <span class="block text-[10px] text-slate-400 uppercase truncate">${c.comp_nombre_unidad_competencia || ''}</span>
                        </label>
                    `;
                    container.appendChild(div);
                });
            } else {
                container.innerHTML = '<p class="text-xs text-red-400">Este programa no tiene competencias registradas.</p>';
            }
        } catch (e) {
            container.innerHTML = '<p class="text-xs text-red-400">Error al cargar competencias.</p>';
        }
    }

    async loadHabilitaciones() {
        try {
            const res = await fetch('../../routing.php?controller=instru_competencia&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            this.habilitaciones = Array.isArray(data) ? data : [];
            this.renderTable();
        } catch (e) { console.error('Error loading habilitaciones:', e); }
    }

    normalizeText(text) {
        if (!text) return '';
        return text.trim().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    getFilteredData() {
        const searchInput = document.getElementById('searchInput');
        const term = this.normalizeText(searchInput ? searchInput.value : '');
        const compFilter = document.getElementById('competenciaFilter');
        const selectedComp = compFilter ? compFilter.value : '';

        return this.habilitaciones.filter(h => {
            const matchesComp = selectedComp === '' || String(h.competencia_comp_id) === String(selectedComp);
            const fullName = this.normalizeText(`${h.inst_nombres} ${h.inst_apellidos}`);
            const doc = this.normalizeText(String(h.instructor_inst_id));
            const compName = this.normalizeText(h.comp_nombre_corto || '');

            return matchesComp && (term === '' || fullName.includes(term) || doc.includes(term) || compName.includes(term));
        });
    }

    renderTable() {
        const tableBody = document.getElementById('habilitacionTableBody');
        if (!tableBody) return;

        const filtered = this.getFilteredData();
        const total = filtered.length;

        document.getElementById('totalHabilitaciones').textContent = this.habilitaciones.length;
        document.getElementById('totalRecords').textContent = total;

        const totalPages = Math.ceil(total / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = Math.min(start + this.itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';
        if (paginated.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center py-8 text-gray-500 font-medium">No se encontraron habilitaciones</td></tr>';
        } else {
            paginated.forEach(h => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50 transition-colors cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${h.inscomp_id}`;
                tr.innerHTML = `
                    <td class="px-6 py-4 font-mono text-xs text-slate-400">#${String(h.inscomp_id).padStart(3, '0')}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700">${h.inst_nombres} ${h.inst_apellidos}</span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-tighter">${h.instructor_inst_id}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="inline-flex flex-col p-2 bg-white border border-slate-100 rounded-lg shadow-sm">
                            <span class="text-xs font-black text-sena-green">${h.comp_nombre_corto || 'N/A'}</span>
                            <span class="text-[9px] text-slate-400 uppercase truncate max-w-[200px]">${h.prog_denominacion || ''}</span>
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
        if (paginationNumbers) {
            paginationNumbers.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-number ${i === this.currentPage ? 'active' : ''}`;
                btn.textContent = i;
                btn.onclick = (e) => { e.stopPropagation(); this.currentPage = i; this.renderTable(); };
                paginationNumbers.appendChild(btn);
            }
        }
        document.getElementById('showingFrom').textContent = total > 0 ? start + 1 : 0;
        document.getElementById('showingTo').textContent = end;
        document.getElementById('prevBtn').disabled = this.currentPage === 1;
        document.getElementById('nextBtn').disabled = this.currentPage === totalPages || totalPages === 0;
    }

    async openModal(hab = null) {
        const modal = document.getElementById('habilitacionModal');
        const form = document.getElementById('habilitacionForm');
        if (!form || !modal) return;
        form.reset();
        document.getElementById('competencias_container').innerHTML = '<p class="text-xs text-slate-400">Primero seleccione un programa para ver sus competencias...</p>';

        if (hab) {
            document.getElementById('modalTitle').textContent = 'Editar Habilitación';
            document.getElementById('inscomp_id').value = hab.inscomp_id;
            document.getElementById('instructor_id').value = hab.instructor_inst_id;
            document.getElementById('programa_id').value = hab.programa_prog_id;
            await this.loadCompetenciasByPrograma(hab.programa_prog_id, hab.competencia_comp_id);
        } else {
            document.getElementById('modalTitle').textContent = 'Nueva Habilitación';
            document.getElementById('inscomp_id').value = '';
        }
        modal.classList.add('show');
    }

    closeModal() {
        const modal = document.getElementById('habilitacionModal');
        if (modal) modal.classList.remove('show');
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        const saveBtn = document.getElementById('saveBtn');
        const id = document.getElementById('inscomp_id').value;
        const instructor_id = document.getElementById('instructor_id').value;
        const programa_id = document.getElementById('programa_id').value;
        
        const checkboxes = document.querySelectorAll('input[name="competencia_ids[]"]:checked');
        if (checkboxes.length === 0) {
            NotificationService.showError('Debe seleccionar al menos una competencia.');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<ion-icon name="sync-outline" class="animate-spin"></ion-icon> Guardando...';

        try {
            if (id) {
                // Modo edición: solo actualizamos una (la primera seleccionada)
                const data = {
                    inscomp_id: id,
                    instructor_inst_id: instructor_id,
                    competencia_comp_id: checkboxes[0].value
                };
                await this.sendRequest('update', data);
            } else {
                // Modo creación: puede ser múltiple
                const promises = Array.from(checkboxes).map(cb => {
                    return this.sendRequest('store', {
                        instructor_inst_id: instructor_id,
                        competencia_comp_id: cb.value
                    });
                });
                await Promise.all(promises);
            }

            NotificationService.showSuccess(id ? 'Habilitación actualizada' : 'Habilitaciones creadas correctamente');
            this.closeModal();
            await this.loadHabilitaciones();
        } catch (e) {
            NotificationService.showError('Error al procesar la solicitud.');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Guardar';
        }
    }

    async sendRequest(action, data) {
        const res = await fetch(`../../routing.php?controller=instru_competencia&action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (!res.ok) throw new Error(result.error || 'Error en servidor');
        return result;
    }

    confirmDelete(id) {
        NotificationService.showConfirm('¿Estás seguro de eliminar esta habilitación?', async () => {
            try {
                const res = await fetch(`../../routing.php?controller=instru_competencia&action=destroy&id=${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    NotificationService.showSuccess('Eliminado correctamente.');
                    await this.loadHabilitaciones();
                } else { NotificationService.showError('Error al eliminar.'); }
            } catch (e) { NotificationService.showError('Error de conexión.'); }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => { window.habilitacionManager = new HabilitacionManager(); });
