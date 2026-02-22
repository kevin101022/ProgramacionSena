class VerPrograma {
    constructor() {
        const urlParams = new URLSearchParams(window.location.search);
        this.programaId = urlParams.get('id');
        this.programaData = null;

        if (!this.programaId) {
            window.location.href = 'index.php';
            return;
        }

        this.init();
    }

    init() {
        this.cacheDOM();
        this.bindEvents();
        this.loadProgramaData();
    }

    cacheDOM() {
        // Main states
        this.loadingState = document.getElementById('loadingState');
        this.programaDetails = document.getElementById('programaDetails');
        this.errorCard = document.getElementById('errorCard');

        // Data elements
        this.denominacionEl = document.getElementById('viewProgDenominacion');
        this.idEl = document.getElementById('viewProgId');
        this.codigoValEl = document.getElementById('viewProgCodigoVal');
        this.tituloEl = document.getElementById('viewProgTitulo');
        this.tipoEl = document.getElementById('viewProgTipo');

        // Delete modal elements
        this.modal = document.getElementById('deleteModal');
        this.modalContent = document.getElementById('modalContent');
        this.modalOverlay = document.getElementById('modalOverlay');
        this.programaToDeleteName = document.getElementById('programaToDeleteName');
        this.confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    }

    bindEvents() {
        const deleteBtn = document.getElementById('deleteProgramaBtn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => this.openDeleteModal());
        }

        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', () => this.closeDeleteModal());
        }

        if (this.modalOverlay) {
            this.modalOverlay.addEventListener('click', () => this.closeDeleteModal());
        }

        if (this.confirmDeleteBtn) {
            this.confirmDeleteBtn.addEventListener('click', () => this.confirmDelete());
        }
    }

    async loadProgramaData() {
        try {
            const response = await fetch(`../../routing.php?controller=programa&action=show&id=${this.programaId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (data.error) throw new Error(data.error);

            this.programaData = data;
            this.populateUI();
            this.showDetails();

        } catch (error) {
            console.error('Error loading programa data:', error);
            this.showError(error.message);
        }
    }

    populateUI() {
        if (this.denominacionEl) this.denominacionEl.textContent = this.programaData.prog_denominacion;
        if (this.idEl) this.idEl.textContent = String(this.programaData.prog_codigo).padStart(3, '0');
        if (this.codigoValEl) this.codigoValEl.textContent = this.programaData.prog_codigo;
        if (this.tituloEl) this.tituloEl.textContent = this.programaData.titpro_nombre || 'No asignado';
        if (this.tipoEl) {
            this.tipoEl.textContent = this.programaData.prog_tipo || 'N/A';
        }

        this.renderCompetencias();
    }

    renderCompetencias() {
        const list = document.getElementById('competenciasList');
        const empty = document.getElementById('noCompetencias');
        const comps = this.programaData.competencias || [];

        if (comps.length > 0) {
            if (empty) empty.style.display = 'none';
            if (list) {
                list.innerHTML = '';
                comps.forEach(c => {
                    const item = document.createElement('div');
                    item.className = 'p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700/50 flex items-center justify-between group hover:border-sena-green/30 transition-all cursor-pointer';
                    item.onclick = () => window.location.href = `../competencia/ver.php?id=${c.comp_id}`;

                    item.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-lg flex items-center justify-center text-sena-green shadow-sm border border-slate-100 dark:border-slate-700">
                                <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">${c.comp_nombre_corto}</h4>
                                <p class="text-xs text-slate-500">${c.comp_horas} horas totales</p>
                            </div>
                        </div>
                        <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-slate-300 group-hover:text-sena-green transition-colors"></ion-icon>
                    `;
                    list.appendChild(item);
                });
            }
        } else {
            if (empty) empty.style.display = 'block';
            if (list) list.innerHTML = '';
        }
    }

    showDetails() {
        if (this.loadingState) this.loadingState.style.display = 'none';
        if (this.programaDetails) this.programaDetails.style.display = 'block';
    }

    showError(msg) {
        if (this.loadingState) this.loadingState.style.display = 'none';
        if (this.errorCard) {
            this.errorCard.style.display = 'block';
            document.getElementById('errorMessage').textContent = msg || 'No se pudo cargar la información del programa.';
        }
    }

    openDeleteModal() {
        if (!this.modal || !this.programaData) return;
        if (this.programaToDeleteName) this.programaToDeleteName.textContent = this.programaData.prog_denominacion;

        this.modal.classList.remove('hidden');
        setTimeout(() => {
            if (this.modalContent) {
                this.modalContent.classList.remove('scale-95', 'opacity-0');
                this.modalContent.classList.add('scale-100', 'opacity-100');
            }
        }, 10);
    }

    closeDeleteModal() {
        if (!this.modal || !this.modalContent) return;
        this.modalContent.classList.remove('scale-100', 'opacity-100');
        this.modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            this.modal.classList.add('hidden');
        }, 300);
    }

    async confirmDelete() {
        if (!this.programaId) return;

        try {
            this.confirmDeleteBtn.disabled = true;
            this.confirmDeleteBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';

            const formData = new FormData();
            formData.append('controller', 'programa');
            formData.append('action', 'destroy');
            formData.append('id', this.programaId);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const result = await response.json();

            if (result.message) {
                if (window.NotificationService) {
                    NotificationService.show(result.message, 'success');
                }
                this.closeDeleteModal();
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            } else {
                throw new Error(result.error || 'Error al eliminar el programa');
            }
        } catch (error) {
            console.error('Error deleting programa:', error);
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            }
            this.confirmDeleteBtn.disabled = false;
            this.confirmDeleteBtn.textContent = 'Sí, eliminar';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new VerPrograma();
});
