// Sede Edit JavaScript
class SedeEdit {
    constructor() {
        this.form = document.getElementById('sedeEditForm');
        this.sedeId = this.getSedeIdFromUrl();
        this.originalSedeData = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupValidation();
        this.loadSedeData();
    }

    getSedeIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    bindEvents() {
        if (this.form) {
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSubmit();
            });
        }

        // Real-time validation
        const sedeNombreInput = document.getElementById('sede_nombre');
        if (sedeNombreInput) {
            sedeNombreInput.addEventListener('blur', () => {
                this.validateSedeNombre();
            });

            sedeNombreInput.addEventListener('input', () => {
                this.clearError('sede_nombre');
            });
        }

        // Modal events
        window.closeSuccessModal = () => {
            this.closeSuccessModal();
        };
    }

    setupValidation() {
        this.validationRules = {
            sede_nombre: {
                required: true,
                minLength: 3,
                maxLength: 100,
                pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\-\.]+$/
            }
        };
    }

    async loadSedeData() {
        if (!this.sedeId) {
            this.showError('ID de sede no válido');
            return;
        }

        try {
            const sede = await this.fetchSede(this.sedeId);

            if (sede) {
                this.originalSedeData = sede;
                this.populateForm(sede);
                this.showForm();
            } else {
                this.showError('Sede no encontrada');
            }
        } catch (error) {
            console.error('Error loading sede:', error);
            this.showError('Error al cargar la información de la sede');
        }
    }

    async fetchSede(sedeId) {
        const response = await fetch(`../../routing.php?controller=sede&action=show&id=${sedeId}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!response.ok) {
            throw new Error('No se pudo obtener la información de la sede');
        }
        return await response.json();
    }

    populateForm(sede) {
        const sedeIdInput = document.getElementById('sede_id');
        const sedeNombreInput = document.getElementById('sede_nombre');

        if (sedeIdInput) {
            sedeIdInput.value = sede.sede_id;
        }

        if (sedeNombreInput) {
            sedeNombreInput.value = sede.sede_nombre;
        }
    }

    showForm() {
        const loadingState = document.getElementById('loadingState');
        const formCard = document.getElementById('formCard');
        const infoCard = document.getElementById('infoCard');

        if (loadingState) {
            loadingState.style.display = 'none';
        }

        if (formCard) {
            formCard.style.display = 'block';
        }

        if (infoCard) {
            infoCard.style.display = 'block';
        }
    }

    showError(message) {
        const loadingState = document.getElementById('loadingState');
        const errorCard = document.getElementById('errorCard');
        const errorMessage = document.getElementById('errorMessage');

        if (loadingState) {
            loadingState.style.display = 'none';
        }

        if (errorMessage) {
            errorMessage.textContent = message;
        }

        if (errorCard) {
            errorCard.style.display = 'block';
        }
    }

    async handleSubmit() {
        if (!this.validateForm()) {
            return;
        }

        const submitBtn = this.form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        try {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <div class="loading-spinner" style="width: 16px; height: 16px; margin-right: 8px;"></div>
                Actualizando...
            `;

            const formData = new FormData(this.form);
            const sedeData = {
                sede_id: formData.get('sede_id'),
                sede_nombre: formData.get('sede_nombre').trim()
            };

            // Check if name changed and if new name already exists
            if (sedeData.sede_nombre !== this.originalSedeData.sede_nombre) {
                const exists = await this.checkSedeExists(sedeData.sede_nombre);
                if (exists) {
                    this.showFieldError('sede_nombre', 'Ya existe una sede con este nombre');
                    return;
                }
            }

            // Submit to API
            const result = await this.updateSede(sedeData);

            if (result.success) {
                this.originalSedeData.sede_nombre = sedeData.sede_nombre;
                this.showSuccessModal(sedeData.sede_nombre);
            } else {
                throw new Error(result.message || 'Error al actualizar la sede');
            }

        } catch (error) {
            console.error('Error updating sede:', error);
            NotificationService.showError(error.message || 'Error al actualizar la sede');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    validateForm() {
        let isValid = true;

        if (!this.validateSedeNombre()) {
            isValid = false;
        }

        return isValid;
    }

    validateSedeNombre() {
        const input = document.getElementById('sede_nombre');
        const value = input.value.trim();
        const rules = this.validationRules.sede_nombre;

        if (rules.required && !value) {
            this.showFieldError('sede_nombre', 'El nombre de la sede es obligatorio');
            return false;
        }

        if (value && value.length < rules.minLength) {
            this.showFieldError('sede_nombre', `El nombre debe tener al menos ${rules.minLength} caracteres`);
            return false;
        }

        if (value && value.length > rules.maxLength) {
            this.showFieldError('sede_nombre', `El nombre no puede exceder ${rules.maxLength} caracteres`);
            return false;
        }

        if (value && !rules.pattern.test(value)) {
            this.showFieldError('sede_nombre', 'El nombre contiene caracteres no válidos');
            return false;
        }

        this.clearError('sede_nombre');
        return true;
    }

    showFieldError(fieldName, message) {
        const errorElement = document.getElementById(`${fieldName}_error`);
        const inputElement = document.getElementById(fieldName);

        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }

        if (inputElement) {
            inputElement.style.borderColor = 'var(--red-500)';
            inputElement.focus();
        }
    }

    clearError(fieldName) {
        const errorElement = document.getElementById(`${fieldName}_error`);
        const inputElement = document.getElementById(fieldName);

        if (errorElement) {
            errorElement.textContent = '';
            errorElement.classList.remove('show');
        }

        if (inputElement) {
            inputElement.style.borderColor = '';
        }
    }

    async checkSedeExists(sedeName) {
        try {
            const response = await fetch(`../../routing.php?controller=sede&action=index`, {
                headers: { 'Accept': 'application/json' }
            });
            const sedes = await response.json();
            return sedes.some(s => s.sede_nombre.toLowerCase() === sedeName.toLowerCase() && s.sede_id != this.sedeId);
        } catch (error) {
            console.error('Error checking sede name:', error);
            return false;
        }
    }

    async updateSede(sedeData) {
        const formData = new FormData();
        formData.append('controller', 'sede');
        formData.append('action', 'update');
        formData.append('sede_id', sedeData.sede_id);
        formData.append('sede_nombre', sedeData.sede_nombre);

        // Handle photo if present
        const photoInput = document.getElementById('sede_foto');
        if (photoInput && photoInput.files[0]) {
            formData.append('sede_foto', photoInput.files[0]);
        }

        const response = await fetch('../../routing.php', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        });

        const data = await response.json();
        if (!response.ok || data.error) {
            return { success: false, message: data.error || 'Error al actualizar la sede' };
        }

        return { success: true, message: data.message };
    }

    showSuccessModal(sedeName) {
        const modal = document.getElementById('successModal');
        const sedeNameElement = document.getElementById('updatedSedeName');

        if (sedeNameElement) {
            sedeNameElement.textContent = sedeName;
        }

        if (modal) {
            modal.classList.add('show');
        }
    }

    closeSuccessModal() {
        const modal = document.getElementById('successModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new SedeEdit();
});