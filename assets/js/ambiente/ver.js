// Ambiente View JavaScript
class AmbienteView {
    constructor() {
        this.ambienteId = this.getAmbienteIdFromUrl();
        this.ambienteData = null;
        this.init();
    }

    init() {
        if (!this.ambienteId) {
            window.location.href = 'index.php';
            return;
        }
        this.bindEvents();
        this.loadAmbienteData();
        this.initDeleteModal();
    }

    bindEvents() {
        // Delete button
        const deleteBtn = document.getElementById('deleteBtn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                this.openDeleteModal();
            });
        }
    }

    initDeleteModal() {
        this.modal = document.getElementById('deleteModal');
        this.modalContent = document.getElementById('modalContent');
        this.modalOverlay = document.getElementById('modalOverlay');
        this.cancelBtn = document.getElementById('cancelDeleteBtn');
        this.confirmBtn = document.getElementById('confirmDeleteBtn');
        this.ambienteNameSpan = document.getElementById('ambienteToDeleteName');

        if (this.cancelBtn) {
            this.cancelBtn.addEventListener('click', () => this.closeDeleteModal());
        }

        if (this.modalOverlay) {
            this.modalOverlay.addEventListener('click', () => this.closeDeleteModal());
        }

        if (this.confirmBtn) {
            this.confirmBtn.addEventListener('click', () => this.confirmDelete());
        }

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal && !this.modal.classList.contains('hidden')) {
                this.closeDeleteModal();
            }
        });
    }

    openDeleteModal() {
        if (!this.modal || !this.ambienteData) return;

        if (this.ambienteNameSpan) {
            this.ambienteNameSpan.textContent = this.ambienteData.amb_nombre;
        }

        this.modal.classList.remove('hidden');
        // Small delay for animation
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
        if (!this.ambienteId) return;

        try {
            this.confirmBtn.disabled = true;
            this.confirmBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';

            const formData = new FormData();
            formData.append('controller', 'ambiente');
            formData.append('action', 'destroy');
            formData.append('id', this.ambienteId);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (!response.ok || data.error) {
                throw new Error(data.error || 'Error al eliminar el ambiente');
            }

            this.showSuccessFeedback();

            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);

        } catch (error) {
            console.error('Error deleting ambiente:', error);
            NotificationService.showError(error.message || 'Hubo un error al intentar eliminar el ambiente. Por favor, intente de nuevo.');
            this.confirmBtn.disabled = false;
            this.confirmBtn.textContent = 'Sí, eliminar';
        }
    }

    showSuccessFeedback() {
        const overlay = document.getElementById('successOverlay');
        if (overlay) {
            overlay.classList.remove('hidden');
            this.closeDeleteModal();
        }
    }

    getAmbienteIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    async loadAmbienteData() {
        try {
            const [ambiente, sedes] = await Promise.all([
                fetch(`../../routing.php?controller=ambiente&action=show&id=${this.ambienteId}`, {
                    headers: { 'Accept': 'application/json' }
                }).then(res => res.json()),
                fetch('../../routing.php?controller=sede&action=index', {
                    headers: { 'Accept': 'application/json' }
                }).then(res => res.json())
            ]);

            if (ambiente && !ambiente.error) {
                this.ambienteData = ambiente;
                // Note: sede_nombre is already included by the model's JOIN in readById
                this.populateAmbienteInfo();
                this.showDetails();
            } else {
                this.showError(ambiente.error || 'Ambiente no encontrado');
            }
        } catch (error) {
            console.error('Error loading ambiente:', error);
            this.showError('Error al cargar la información del ambiente');
        }
    }

    populateAmbienteInfo() {
        const elements = {
            'ambienteNombreCard': this.ambienteData.amb_nombre,
            'dispNombre': this.ambienteData.amb_nombre,
            'dispIdAmbiente': String(this.ambienteData.amb_id).padStart(3, '0'),
            'dispSede': this.ambienteData.sede_nombre,
            'editBtn': `editar.php?id=${this.ambienteData.amb_id}`
        };

        for (const [id, value] of Object.entries(elements)) {
            const el = document.getElementById(id);
            if (el) {
                if (id === 'editBtn') {
                    el.href = value;
                } else {
                    el.textContent = value;
                }
            }
        }
    }

    showDetails() {
        const loadingState = document.getElementById('loadingState');
        const ambienteDetails = document.getElementById('ambienteDetails');

        if (loadingState) loadingState.style.display = 'none';
        if (ambienteDetails) {
            ambienteDetails.style.display = 'block';
            ambienteDetails.classList.add('animate-fade-in');
        }
    }

    showError(message) {
        const loadingState = document.getElementById('loadingState');
        const errorCard = document.getElementById('errorCard');
        const errorMessage = document.getElementById('errorMessage');

        if (loadingState) loadingState.style.display = 'none';
        if (errorCard) errorCard.style.display = 'block';
        if (errorMessage) errorMessage.textContent = message;
    }
}

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    window.ambienteView = new AmbienteView();
});
