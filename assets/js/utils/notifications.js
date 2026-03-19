/**
 * NotificationService - Handlers for premium custom alerts
 */
const NotificationService = {
    overlay: null,
    modal: null,
    errorAlert: null,
    successAlert: null,
    errorText: null,
    successText: null,
    confirmAlert: null,
    confirmText: null,
    confirmBtn: null,

    init() {
        this.overlay = document.getElementById('notification-overlay');
        this.modal = document.getElementById('notification-modal');
        this.errorAlert = document.getElementById('error-alert');
        this.successAlert = document.getElementById('success-alert');
        this.confirmAlert = document.getElementById('confirm-alert');

        this.errorText = document.getElementById('error-message-text');
        this.successText = document.getElementById('success-message-text');
        this.confirmText = document.getElementById('confirm-message-text');

        this.errorBtn = document.getElementById('error-close-btn');
        this.successBtn = document.getElementById('success-close-btn');
        this.confirmBtn = document.getElementById('confirm-btn-primary');
        this.cancelBtn = document.getElementById('confirm-cancel-btn');

        // Default handlers for static buttons
        if (this.errorBtn) this.errorBtn.onclick = () => this.hide();
        if (this.cancelBtn) this.cancelBtn.onclick = () => this.hide();
    },

    showConfirm(message, callback, options = {}) {
        if (!this.overlay) this.init();

        const title = options.title || '¿Confirmar Acción?';
        const confirmText = options.confirmText || 'Sí, confirmar';
        const cancelText = options.cancelText || 'No, cancelar';
        const type = options.type || 'danger'; // danger, warning, success, info

        // Elements
        const titleEl = document.getElementById('confirm-title');
        const iconBg = document.getElementById('confirm-icon-bg');
        const icon = document.getElementById('confirm-icon');
        
        if (titleEl) titleEl.textContent = title;
        if (this.confirmBtn) this.confirmBtn.textContent = confirmText;
        if (this.cancelBtn) this.cancelBtn.textContent = cancelText;

        // Apply theme colors
        if (iconBg && icon) {
            // Reset
            iconBg.className = iconBg.className.replace(/bg-\w+-\d+/g, '');
            icon.className = icon.className.replace(/text-\w+-\d+/g, '');
            this.confirmBtn.className = this.confirmBtn.className.replace(/bg-\w+-\d+/g, '').replace(/hover:bg-\w+-\d+/g, '').replace(/shadow-\w+-\d+/g, '');

            if (type === 'warning') {
                iconBg.classList.add('bg-amber-100');
                icon.classList.add('text-amber-600');
                icon.setAttribute('src', '../../assets/ionicons/alert-circle-outline.svg');
                this.confirmBtn.classList.add('bg-amber-600', 'hover:bg-amber-700', 'shadow-amber-200');
            } else if (type === 'success') {
                iconBg.classList.add('bg-green-100');
                icon.classList.add('text-green-600');
                icon.setAttribute('src', '../../assets/ionicons/checkmark-circle-outline.svg');
                this.confirmBtn.classList.add('bg-sena-green', 'hover:bg-dark-green', 'shadow-sena-green/20');
            } else {
                // Default: danger (red)
                iconBg.classList.add('bg-red-100');
                icon.classList.add('text-red-600');
                icon.setAttribute('src', '../../assets/ionicons/alert-circle-outline.svg');
                this.confirmBtn.classList.add('bg-red-600', 'hover:bg-red-700', 'shadow-red-200');
            }
        }

        this.confirmText.textContent = message;
        this.confirmAlert.classList.remove('hidden');
        this.errorAlert.classList.add('hidden');
        this.successAlert.classList.add('hidden');

        this.confirmBtn.onclick = () => {
            this.hide();
            setTimeout(() => callback(), 100);
        };

        this.show();
    },

    show(message, type = 'success') {
        if (type === 'error') {
            this.showError(message);
        } else {
            this.showSuccess(message);
        }
    },

    showError(message) {
        if (!this.overlay) this.init();

        this.errorText.textContent = message;
        this.errorAlert.classList.remove('hidden');
        this.successAlert.classList.add('hidden');

        this.show();
    },

    showSuccess(message, callback = null) {
        if (!this.overlay) this.init();

        this.successText.textContent = message;
        this.successAlert.classList.remove('hidden');
        this.errorAlert.classList.add('hidden');
        this.confirmAlert.classList.add('hidden');

        if (callback) {
            this.successBtn.onclick = () => {
                this.hide();
                setTimeout(() => callback(), 100);
            };
        } else {
            this.successBtn.onclick = () => this.hide();
        }

        this.show();
    },

    show() {
        this.overlay.classList.remove('hidden');
        this.overlay.classList.add('show'); // Uses styles.css .modal.show

        setTimeout(() => {
            this.modal.classList.remove('scale-95', 'opacity-0');
            this.modal.classList.add('scale-100', 'opacity-100');
        }, 10);
    },

    hide() {
        this.modal.classList.remove('scale-100', 'opacity-100');
        this.modal.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            this.overlay.classList.remove('show');
            this.overlay.classList.add('hidden');
        }, 300);
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    NotificationService.init();
});

// Explicitly expose to window object
window.NotificationService = NotificationService;
