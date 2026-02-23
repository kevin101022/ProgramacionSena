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
        this.itemsPerPage = 5;

        this.init();
    }

    async init() {
        this.bindEvents();
        await Promise.all([
            this.loadInstructores(),
            this.loadProgramas(),
            this.loadHabilitaciones()
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

        const programaSelect = document.getElementById('programa_id');
        if (programaSelect) {
            programaSelect.addEventListener('change', () => this.loadCompetenciasByPrograma(programaSelect.value));
        }

        // Global delete trigger (if view supports it)
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
                    opt.value = i.inst_id;
                    opt.textContent = `${i.inst_nombres} ${i.inst_apellidos}`;
                    instructorSelect.appendChild(opt);
                });
            }
        } catch (e) {
            console.error('Error:', e);
        }
    }

    async loadProgramas() {
        try {
            const res = await fetch('../../routing.php?controller=programa&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            this.programas = await res.json();
            const programaSelect = document.getElementById('programa_id');
            if (programaSelect) {
                programaSelect.innerHTML = '<option value="">Seleccione programa...</option>';
                this.programas.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.prog_codigo;
                    opt.textContent = `${p.prog_codigo} - ${p.prog_denominacion}`;
                    programaSelect.appendChild(opt);
                });
            }
        } catch (e) {
            console.error('Error:', e);
        }
    }

    async loadCompetenciasByPrograma(progId) {
        const competenciaSelect = document.getElementById('competencia_id');
        if (!competenciaSelect) return;

        competenciaSelect.innerHTML = '<option value="">Cargando competencias...</option>';
        competenciaSelect.disabled = true;

        if (!progId) {
            competenciaSelect.innerHTML = '<option value="">Primero seleccione programa...</option>';
            return;
        }

        try {
            const res = await fetch(`../../routing.php?controller=competencia_programa&action=getByPrograma&prog_id=${progId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const competencias = await res.json();

            competenciaSelect.innerHTML = '<option value="">Seleccione competencia...</option>';
            if (Array.isArray(competencias) && competencias.length > 0) {
                competencias.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.comp_id;
                    opt.textContent = c.comp_nombre_corto;
                    competenciaSelect.appendChild(opt);
                });
                competenciaSelect.disabled = false;
            } else {
                competenciaSelect.innerHTML = '<option value="">No hay competencias</option>';
            }
        } catch (e) {
            console.error('Error:', e);
        }
    }

    async loadHabilitaciones() {
        try {
            const res = await fetch('../../routing.php?controller=instru_competencia&action=index', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!res.ok) throw new Error('Error al cargar habilitaciones');
            this.habilitaciones = Array.isArray(data) ? data : [];
            this.renderTable();
        } catch (e) {
            console.error('Error:', e);
        }
    }

    getFilteredData() {
        const searchInput = document.getElementById('searchInput');
        const term = (searchInput ? searchInput.value : '').toLowerCase();

        return this.habilitaciones.filter(h =>
            `${h.inst_nombres} ${h.inst_apellidos}`.toLowerCase().includes(term) ||
            (h.prog_denominacion || '').toLowerCase().includes(term) ||
            (h.comp_nombre_corto || '').toLowerCase().includes(term)
        );
    }

    renderTable() {
        const tableBody = document.getElementById('habilitacionTableBody');
        if (!tableBody) return;

        const filtered = this.getFilteredData();
        const total = filtered.length;

        // Update stats
        const totalLabel = document.getElementById('totalHabilitaciones');
        if (totalLabel) totalLabel.textContent = this.habilitaciones.length;
        const totalRecords = document.getElementById('totalRecords');
        if (totalRecords) totalRecords.textContent = total;

        const totalPages = Math.ceil(total / this.itemsPerPage);
        if (this.currentPage > totalPages && totalPages > 0) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = Math.min(start + this.itemsPerPage, total);
        const paginated = filtered.slice(start, end);

        tableBody.innerHTML = '';

        if (paginated.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-500">No se encontraron habilitaciones</td></tr>';
        } else {
            paginated.forEach(h => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-green-50/50 transition-colors cursor-pointer group';
                tr.onclick = () => window.location.href = `ver.php?id=${h.inscomp_id}`;

                const vigenciaDate = h.inscomp_vigencia ? new Date(h.inscomp_vigencia).toLocaleDateString('es-CO') : 'N/A';
                const isVigente = h.inscomp_vigencia ? new Date(h.inscomp_vigencia) >= new Date() : false;

                tr.innerHTML = `
                    <td class="px-6 py-4 font-semibold text-sena-green">${String(h.inscomp_id).padStart(3, '0')}</td>
                    <td class="px-6 py-4">
                        <div class="user-cell px-0">
                            <div class="user-info-sm px-0">
                                <div class="user-name-sm">${h.inst_nombres} ${h.inst_apellidos}</div>
                                <div class="user-meta-sm">Instructor</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm italic">${h.prog_denominacion || 'N/A'}</td>
                    <td class="px-6 py-4">
                        <div class="badge-glass">${h.comp_nombre_corto || 'N/A'}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="status-badge ${isVigente ? 'status-active' : 'status-inactive'}">${vigenciaDate}</span>
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

    openModal(hab = null) {
        const modal = document.getElementById('habilitacionModal');
        const form = document.getElementById('habilitacionForm');
        if (!form) return;
        form.reset();

        const modalTitle = document.getElementById('modalTitle');
        const idInput = document.getElementById('inscomp_id');
        const competenciaSelect = document.getElementById('competencia_id');

        if (competenciaSelect) {
            competenciaSelect.innerHTML = '<option value="">Primero seleccione un programa...</option>';
            competenciaSelect.disabled = true;
        }

        if (hab) {
            if (modalTitle) modalTitle.textContent = 'Editar Habilitación';
            if (idInput) idInput.value = hab.inscomp_id;
            const progSelect = document.getElementById('programa_id');
            if (progSelect) {
                progSelect.value = hab.competxprograma_programa_prog_id;
                this.loadCompetenciasByPrograma(hab.competxprograma_programa_prog_id).then(() => {
                    if (competenciaSelect) competenciaSelect.value = hab.competxprograma_competencia_comp_id;
                });
            }
            if (document.getElementById('inscomp_vigencia') && hab.inscomp_vigencia) {
                document.getElementById('inscomp_vigencia').value = hab.inscomp_vigencia.split('T')[0];
            }
        } else {
            if (modalTitle) modalTitle.textContent = 'Nueva Habilitación';
            if (idInput) idInput.value = '';
        }
        if (modal) modal.classList.add('show');
    }

    closeModal() {
        const modal = document.getElementById('habilitacionModal');
        if (modal) modal.classList.remove('show');
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('inscomp_id').value;
        const action = id ? 'update' : 'store';

        const data = {
            instructor_inst_id: document.getElementById('instructor_id').value,
            competxprograma_programa_prog_id: document.getElementById('programa_id').value,
            competxprograma_competencia_comp_id: document.getElementById('competencia_id').value,
            inscomp_vigencia: document.getElementById('inscomp_vigencia').value
        };
        if (id) data.inscomp_id = id;

        try {
            const res = await fetch(`../../routing.php?controller=instru_competencia&action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(data)
            });
            if (res.ok) {
                NotificationService.showSuccess(id ? 'Habilitación actualizada' : 'Habilitación creada');
                this.closeModal();
                await this.loadHabilitaciones();
            } else {
                NotificationService.showError('Error al guardar la habilitación.');
            }
        } catch (e) {
            NotificationService.showError('Error de conexión.');
        }
    }

    confirmDelete(id) {
        NotificationService.showConfirm(
            '¿Estás seguro de que deseas eliminar esta habilitación? Esta acción no se puede deshacer.',
            async () => {
                try {
                    const res = await fetch(`../../routing.php?controller=instru_competencia&action=destroy&id=${id}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (res.ok) {
                        NotificationService.showSuccess('Habilitación eliminada correctamente.');
                        await this.loadHabilitaciones();
                    } else {
                        NotificationService.showError('Error al eliminar.');
                    }
                } catch (e) {
                    NotificationService.showError('Error de conexión.');
                }
            }
        );
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.habilitacionManager = new HabilitacionManager();
});
