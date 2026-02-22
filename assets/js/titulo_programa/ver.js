class VerTitulo {
    constructor() {
        this.tituloId = this.getTituloIdFromUrl();
        this.tituloData = null;

        if (!this.tituloId) {
            window.location.href = 'index.php';
            return;
        }

        this.init();
    }

    init() {
        this.cacheDOM();
        this.bindEvents();
        this.loadTituloData();
    }

    cacheDOM() {
        // Main elements
        this.loadingState = document.getElementById('loadingState');
        this.tituloDetails = document.getElementById('tituloDetails');
        this.errorCard = document.getElementById('errorCard');

        // Data elements
        this.nombreEl = document.getElementById('viewTitNombre');
        this.idEl = document.getElementById('viewTitId');
        this.programasList = document.getElementById('programasList');
        this.noProgramas = document.getElementById('noProgramas');

        // Delete elements
        this.modal = document.getElementById('deleteModal');
        this.modalContent = document.getElementById('modalContent');
        this.modalOverlay = document.getElementById('modalOverlay');
        this.tituloToDeleteName = document.getElementById('tituloToDeleteName');
    }

    bindEvents() {
        const deleteBtn = document.getElementById('deleteTituloBtn');
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

        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', () => this.confirmDelete());
        }
    }

    getTituloIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    async loadTituloData() {
        try {
            const response = await fetch(`../../routing.php?controller=titulo_programa&action=show&id=${this.tituloId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (data.error) throw new Error(data.error);

            this.tituloData = data;
            this.populateUI();
            this.showDetails();

            // Logic to fetch programs could go here, for now empty
            this.loadRelatedPrograms();

        } catch (error) {
            console.error('Error loading titulo data:', error);
            this.showError();
        }
    }

    async loadRelatedPrograms() {
        // Ideally we fetch from a controller. For now, showing empty state
        if (this.noProgramas) this.noProgramas.style.display = 'block';
    }

    populateUI() {
        if (this.nombreEl) this.nombreEl.textContent = this.tituloData.titpro_nombre;
        if (this.idEl) this.idEl.textContent = String(this.tituloData.titpro_id).padStart(3, '0');
    }

    showDetails() {
        if (this.loadingState) this.loadingState.style.display = 'none';
        if (this.tituloDetails) this.tituloDetails.style.display = 'block';
    }

    showError() {
        if (this.loadingState) this.loadingState.style.display = 'none';
        if (this.errorCard) this.errorCard.style.display = 'block';
    }

    openDeleteModal() {
        if (!this.modal || !this.tituloData) return;
        if (this.tituloToDeleteName) this.tituloToDeleteName.textContent = this.tituloData.titpro_nombre;

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
        if (!this.tituloId) return;

        try {
            const formData = new FormData();
            formData.append('controller', 'titulo_programa');
            formData.append('action', 'destroy');
            formData.append('id', this.tituloId);

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
                throw new Error(result.error || 'Error al eliminar el título');
            }
        } catch (error) {
            console.error('Error deleting titulo:', error);
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new VerTitulo();
});
