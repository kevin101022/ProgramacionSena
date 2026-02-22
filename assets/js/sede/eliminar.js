// Sede Delete JavaScript
class SedeDelete {
    constructor() {
        this.sedeId = this.getSedeIdFromUrl();
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadSedeData();
    }

    getSedeIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    bindEvents() {
        const deleteForm = document.getElementById('deleteForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleDelete();
            });
        }
    }

    async loadSedeData() {
        if (!this.sedeId) {
            this.showError('ID de sede no válido');
            return;
        }

        try {
            const sede = await this.fetchSede(this.sedeId);

            if (sede) {
                this.sedeData = sede;
                this.populateDeleteForm();
                this.showDeleteConfirmation();
            } else {
                this.showError('Sede no encontrada');
            }
        } catch (error) {
            console.error('Error loading sede:', error);
            NotificationService.showError('Error al cargar la información de la sede');
        }
    }

    async fetchSede(sedeId) {
        return new Promise((resolve) => {
            setTimeout(() => {
                const mockSedes = {
                    1: { sede_id: 1, sede_nombre: 'Complejo Sur - Tecnologías' },
                    2: { sede_id: 2, sede_nombre: 'Centro de Diseño y Manufactura' },
                    8: { sede_id: 8, sede_nombre: 'Centro de Servicios' }
                };
                resolve(mockSedes[sedeId] || null);
            }, 800);
        });
    }

    populateDeleteForm() {
        const sedeNombre = document.getElementById('sedeNombre');
        const sedeId = document.getElementById('sedeId');

        if (sedeNombre) sedeNombre.textContent = this.sedeData.sede_nombre;
        if (sedeId) sedeId.textContent = String(this.sedeData.sede_id).padStart(3, '0');
    }

    showDeleteConfirmation() {
        const loadingState = document.getElementById('loadingState');
        const deleteConfirmation = document.getElementById('deleteConfirmation');

        if (loadingState) loadingState.style.display = 'none';
        if (deleteConfirmation) deleteConfirmation.style.display = 'block';
    }

    showError(message) {
        NotificationService.showError(message);
    }

    async handleDelete() {
        const result = await this.deleteSede(this.sedeData.sede_id);

        if (result.success) {
            this.showSuccessModal(this.sedeData.sede_nombre);
        }
    }

    async deleteSede(sedeId) {
        const formData = new FormData();
        formData.append('controller', 'sede');
        formData.append('action', 'destroy');
        formData.append('sede_id', sedeId);

        const response = await fetch('../../routing.php', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        });

        const data = await response.json();
        if (!response.ok || data.error) {
            throw new Error(data.error || 'Error al eliminar la sede');
        }
        return { success: true };
    }

    showSuccessModal(sedeName) {
        const modal = document.getElementById('successModal');
        const sedeNameElement = document.getElementById('deletedSedeName');

        if (sedeNameElement) sedeNameElement.textContent = sedeName;
        if (modal) modal.classList.add('show');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new SedeDelete();
});