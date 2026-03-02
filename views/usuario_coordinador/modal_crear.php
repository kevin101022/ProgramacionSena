<!-- modal_crear.php -->
<div id="coordinadorModal" class="modal-overlay fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden"
    onclick="if(event.target === this) usuarioCoordinadorModule.closeModal()">
    <div class="modal-content bg-white w-full max-w-md mx-4 rounded-2xl shadow-xl transform transition-all duration-300 scale-95 opacity-0">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sena-green/10 flex items-center justify-center">
                    <ion-icon src="../../assets/ionicons/person-add-outline.svg" class="text-xl text-sena-green"></ion-icon>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Nuevo Coordinador</h3>
                    <p class="text-sm text-gray-500 font-medium">Registrar persona física en el sistema</p>
                </div>
            </div>
            <button onclick="usuarioCoordinadorModule.closeModal()" class="text-gray-400 hover:text-red-500 transition-colors bg-white rounded-full p-2 shadow-sm border border-gray-100 hover:border-red-100 hover:bg-red-50">
                <ion-icon src="../../assets/ionicons/close-outline.svg" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="coordinadorForm" onsubmit="usuarioCoordinadorModule.handleFormSubmit(event)" class="p-6 space-y-6">
            <!-- Datos del Coordinador -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 mb-2">
                    <ion-icon src="../../assets/ionicons/person-circle-outline.svg" class="text-sena-green text-lg"></ion-icon>
                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Información Personal</h4>
                </div>

                <div class="space-y-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                    <div class="form-group">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="coord_id">Documento de Identidad (Cédula)</label>
                        <div class="relative">
                            <ion-icon src="../../assets/ionicons/id-card-outline.svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></ion-icon>
                            <input type="number" id="coord_id" name="coord_id" required placeholder="Ej. 1094..."
                                class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sena-green/20 focus:border-sena-green outline-none transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="coord_nombre">Nombre Completo del Coordinador</label>
                        <div class="relative">
                            <ion-icon src="../../assets/ionicons/person-outline.svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></ion-icon>
                            <input type="text" id="coord_nombre" name="coord_nombre" required placeholder="Nombres y Apellidos"
                                class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sena-green/20 focus:border-sena-green outline-none transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="coord_correo">Correo Institucional (Opcional)</label>
                        <div class="relative">
                            <ion-icon src="../../assets/ionicons/mail-outline.svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></ion-icon>
                            <input type="email" id="coord_correo" name="coord_correo" placeholder="usuario@sena.edu.co"
                                class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sena-green/20 focus:border-sena-green outline-none transition-all shadow-sm">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="coord_password">Contraseña (Por defecto: 123456 si queda vacío)</label>
                        <div class="relative">
                            <ion-icon src="../../assets/ionicons/lock-closed-outline.svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></ion-icon>
                            <input type="password" id="coord_password" name="coord_password" placeholder="Crear contraseña temporal"
                                class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-sena-green/20 focus:border-sena-green outline-none transition-all shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="usuarioCoordinadorModule.closeModal()" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-2 focus:ring-gray-200 transition-all shadow-sm">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-sena-green rounded-xl hover:bg-sena-green-dark focus:ring-2 focus:ring-sena-green/50 transition-all shadow-md shadow-sena-green/20 flex items-center gap-2">
                    <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                    Guardar Coordinador
                </button>
            </div>
        </form>
    </div>
</div>