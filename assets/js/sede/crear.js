// Sede Form JavaScript
class SedeForm {
    constructor() {
        this.form = document.getElementById('sedeForm');
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupValidation();
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
        // Add validation rules
        this.validationRules = {
            sede_nombre: {
                required: true,
                minLength: 3,
                maxLength: 100,
                pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\-\.]+$/
            }
        };
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
                Guardando...
            `;

            const formData = new FormData(this.form);
            const sedeData = {
                sede_nombre: formData.get('sede_nombre').trim()
            };

            // Check if sede name already exists
            const exists = await this.checkSedeExists(sedeData.sede_nombre);
            if (exists) {
                this.showError('sede_nombre', 'Ya existe una sede con este nombre');
                return;
            }

            // Submit to API
            const result = await this.createSede(sedeData);

            if (result.success) {
                this.showSuccessModal(sedeData.sede_nombre);
                this.form.reset();
            } else {
                throw new Error(result.message || 'Error al crear la sede');
            }

        } catch (error) {
            console.error('Error creating sede:', error);
            NotificationService.showError(error.message || 'Error al crear la sede');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    validateForm() {
        return this.validateSedeNombre();
    }

    validateSedeNombre() {
        const input = document.getElementById('sede_nombre');
        const value = input.value.trim();
        const rules = this.validationRules.sede_nombre;

        // Required validation
        if (rules.required && !value) {
            this.showError('sede_nombre', 'El nombre de la sede es obligatorio');
            return false;
        }

        // Length validation
        if (value && value.length < rules.minLength) {
            this.showError('sede_nombre', `El nombre debe tener al menos ${rules.minLength} caracteres`);
            return false;
        }

        if (value && value.length > rules.maxLength) {
            this.showError('sede_nombre', `El nombre no puede exceder ${rules.maxLength} caracteres`);
            return false;
        }

        // Pattern validation
        if (value && !rules.pattern.test(value)) {
            this.showError('sede_nombre', 'El nombre contiene caracteres no válidos');
            return false;
        }

        this.clearError('sede_nombre');
        return true;
    }

    showError(fieldName, message) {
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
            return sedes.some(s => s.sede_nombre.toLowerCase() === sedeName.toLowerCase());
        } catch (error) {
            console.error('Error checking sede name:', error);
            return false;
        }
    }

    async createSede(sedeData) {
        const formData = new FormData();
        formData.append('controller', 'sede');
        formData.append('action', 'store');
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
            const detailMsg = data.details ? ` (${data.details})` : '';
            return { success: false, message: (data.error || 'Error al crear la sede') + detailMsg };
        }

        return { success: true, message: data.message };
    }

    showSuccessModal(sedeName) {
        const modal = document.getElementById('successModal');
        const sedeNameElement = document.getElementById('createdSedeName');

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
    new SedeForm();
});