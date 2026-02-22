<!-- Global Notifications Container -->
<div id="notification-overlay" class="modal hidden">
    <div id="notification-modal" class="modal-content scale-95 opacity-0 transition-all duration-300">
        <!-- Error Alert -->
        <div id="error-alert" class="hidden">
            <div class="modal-header bg-red-50 border-b border-red-100 p-6 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                    <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-3xl"></ion-icon>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Atención detectada</h3>
                    <p class="text-sm text-gray-500">Ha ocurrido un problema con la solicitud.</p>
                </div>
            </div>
            <div class="modal-body p-6">
                <div class="bg-red-50/50 border border-red-100 rounded-xl p-4 mb-4">
                    <p id="error-message-text" class="text-red-800 font-medium"></p>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Si el problema persiste, por favor contacte al administrador del sistema.
                </p>
            </div>
            <div class="modal-footer bg-gray-50 p-4 flex justify-end">
                <button id="error-close-btn" class="px-6 py-2.5 bg-gray-900 text-white rounded-xl font-semibold hover:bg-gray-800 transition-all active:scale-95 shadow-lg shadow-gray-200">
                    Entendido
                </button>
            </div>
        </div>

        <!-- Success Alert -->
        <div id="success-alert" class="hidden">
            <div class="modal-header bg-green-50 border-b border-green-100 p-6 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                    <ion-icon src="../../assets/ionicons/checkmark-circle-outline.svg" class="text-3xl"></ion-icon>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Operación exitosa</h3>
                    <p class="text-sm text-gray-500">Todo se ha procesado correctamente.</p>
                </div>
            </div>
            <div class="modal-body p-6 text-center">
                <p id="success-message-text" class="text-gray-700 font-medium text-lg"></p>
            </div>
            <div class="modal-footer bg-gray-50 p-4 flex justify-end">
                <button id="success-close-btn" class="px-6 py-2.5 bg-sena-green text-white rounded-xl font-semibold hover:bg-dark-green transition-all active:scale-95 shadow-lg shadow-sena-green/20">
                    Continuar
                </button>
            </div>
        </div>

        <!-- Confirm Alert (Danger Style like Sede) -->
        <div id="confirm-alert" class="hidden">
            <div class="px-6 py-8">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-4xl text-red-600 dark:text-red-400 animate-pulse"></ion-icon>
                </div>

                <h3 class="text-2xl font-bold text-slate-900 dark:text-white text-center mb-3">¿Confirmar Acción?</h3>

                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 mb-6">
                    <p id="confirm-message-text" class="text-slate-600 dark:text-slate-400 text-center text-base leading-relaxed"></p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button id="confirm-cancel-btn" class="flex-1 px-6 py-3.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-sm hover:bg-slate-100 dark:hover:bg-slate-800 transition-all active:scale-95">
                        No, cancelar
                    </button>
                    <button id="confirm-btn-primary" class="flex-1 px-6 py-3.5 rounded-xl bg-red-600 text-white font-bold text-sm hover:bg-red-700 shadow-lg shadow-red-200 dark:shadow-none transition-all active:scale-95">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>