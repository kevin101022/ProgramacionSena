class DetalleCompetencia {
    constructor() {
        this.compId = new URLSearchParams(window.location.search).get('id');
        this.competenciaData = null;
        if (!this.compId) window.location.href = 'index.php';
        this.init();
    }

    init() {
        this.cacheDOM();
        this.bindEvents();
        this.initDeleteModal();
        this.loadData();
    }

    cacheDOM() {
        this.loadingState = document.getElementById('loadingState');
        this.competenciaDetails = document.getElementById('competenciaDetails');
        this.errorCard = document.getElementById('errorCard');
        this.errorMessage = document.getElementById('errorMessage');
        this.deleteButtonWrapper = document.getElementById('deleteButtonWrapper');

        this.breadcrumbCompetencia = document.getElementById('breadcrumbCompetencia');
        this.editLink = document.getElementById('editLink');
        this.deleteBtn = document.getElementById('deleteBtn');

        this.compIdDisplay = document.getElementById('compIdDisplay');
        this.compNombreDisplay = document.getElementById('compNombreDisplay');
        this.compHorasDisplay = document.getElementById('compHorasDisplay');
        this.compUnidadDisplay = document.getElementById('compUnidadDisplay');

        this.programasList = document.getElementById('associatedProgramasList');
        this.programCount = document.getElementById('programCount');
    }

    bindEvents() {
        if (this.deleteBtn) {
            this.deleteBtn.addEventListener('click', () => this.openDeleteModal());
        }
    }

    initDeleteModal() {
        this.modal = document.getElementById('deleteModal');
        this.modalContent = document.getElementById('modalContent');
        this.modalOverlay = document.getElementById('modalOverlay');
        this.cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        this.confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        this.competenciaToDeleteName = document.getElementById('competenciaToDeleteName');

        if (this.cancelDeleteBtn) {
            this.cancelDeleteBtn.addEventListener('click', () => this.closeDeleteModal());
        }

        if (this.modalOverlay) {
            this.modalOverlay.addEventListener('click', () => this.closeDeleteModal());
        }

        if (this.confirmDeleteBtn) {
            this.confirmDeleteBtn.addEventListener('click', () => this.confirmDelete());
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal && !this.modal.classList.contains('hidden')) {
                this.closeDeleteModal();
            }
        });
    }

    openDeleteModal() {
        if (!this.modal || !this.competenciaData) return;

        if (this.competenciaToDeleteName) {
            this.competenciaToDeleteName.textContent = this.competenciaData.comp_nombre_corto;
        }

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

    async loadData() {
        try {
            const response = await fetch(`../../routing.php?controller=competencia&action=show&id=${this.compId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (data.error) throw new Error(data.error);

            this.competenciaData = data;
            this.renderCompetencia(data);
            this.renderProgramas(data.programas || []);

            this.showDetails();
        } catch (error) {
            console.error('Error loading competency info:', error);
            this.showError(error.message);
        }
    }

    showDetails() {
        if (this.loadingState) this.loadingState.style.display = 'none';
        if (this.competenciaDetails) {
            this.competenciaDetails.style.display = 'block';
        }
        if (this.deleteButtonWrapper) this.deleteButtonWrapper.style.display = 'flex';
        if (this.errorCard) this.errorCard.style.display = 'none';
    }

    showError(msg) {
        if (this.loadingState) this.loadingState.style.display = 'none';
        if (this.competenciaDetails) this.competenciaDetails.style.display = 'none';
        if (this.deleteButtonWrapper) this.deleteButtonWrapper.style.display = 'none';
        if (this.errorCard) {
            this.errorCard.style.display = 'block';
            if (this.errorMessage) this.errorMessage.textContent = msg;
        }
    }

    renderCompetencia(data) {
        if (this.breadcrumbCompetencia) this.breadcrumbCompetencia.textContent = data.comp_nombre_corto;
        if (this.editLink) this.editLink.href = `editar.php?id=${this.compId}`;

        if (this.compIdDisplay) this.compIdDisplay.textContent = '#' + String(data.comp_id).padStart(3, '0');
        if (this.compNombreDisplay) this.compNombreDisplay.textContent = data.comp_nombre_corto;
        if (this.compHorasDisplay) this.compHorasDisplay.textContent = data.comp_horas + 'h';
        if (this.compUnidadDisplay) {
            this.compUnidadDisplay.textContent = data.comp_nombre_unidad_competencia || 'Sin descripción detallada.';
        }
    }

    renderProgramas(programas) {
        if (!this.programasList) return;

        this.programasList.innerHTML = '';
        if (this.programCount) this.programCount.textContent = `${programas.length} asociados`;

        if (programas.length === 0) {
            this.programasList.innerHTML = `
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 dark:text-slate-600">
                        <ion-icon src="../../assets/ionicons/school-outline.svg" class="text-3xl"></ion-icon>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">Esta competencia no está asociada a ningún programa todavía.</p>
                </div>
            `;
            return;
        }

        programas.forEach(p => {
            const item = document.createElement('div');
            item.className = 'p-6 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors flex items-center justify-between cursor-pointer';
            item.onclick = () => window.location.href = `../programa/ver.php?id=${p.prog_codigo}`;

            item.innerHTML = `
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 dark:text-green-400">
                        <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white">${p.prog_denominacion || 'Programa'}</h4>
                        <p class="text-sm text-slate-500 dark:text-slate-400">${p.titpro_nombre || ''}</p>
                    </div>
                </div>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-slate-300 dark:text-slate-500"></ion-icon>
            `;
            this.programasList.appendChild(item);
        });
    }

    async confirmDelete() {
        if (!this.compId) return;

        try {
            this.confirmDeleteBtn.disabled = true;
            this.confirmDeleteBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mx-auto"></div>';

            const response = await fetch(`../../routing.php?controller=competencia&action=destroy&id=${this.compId}`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();

            this.closeDeleteModal();

            if (result.message) {
                this.showSuccessOverlay();
                setTimeout(() => window.location.href = 'index.php', 2000);
            } else {
                throw new Error(result.error || 'Error al eliminar');
            }
        } catch (error) {
            this.confirmDeleteBtn.disabled = false;
            this.confirmDeleteBtn.textContent = 'Sí, eliminar';
            if (window.NotificationService) {
                NotificationService.showError(error.message || 'Error al eliminar la competencia');
            } else {
                alert(error.message || 'Error al eliminar');
            }
        }
    }

    showSuccessOverlay() {
        const overlay = document.getElementById('successOverlay');
        if (overlay) overlay.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new DetalleCompetencia();
});
