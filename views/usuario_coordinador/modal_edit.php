<!-- modal_edit.php -->
<div id="coordinadorEditModal" class="modal-overlay fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden"
    onclick="if(event.target === this) usuarioCoordinadorModule.closeEditModal()">
    <div class="modal-content bg-white w-full max-w-md mx-4 rounded-2xl shadow-xl transform transition-all duration-300 scale-95 opacity-0">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b border-gray-100 bg-gray-50/50 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <ion-icon src="../../assets/ionicons/create-outline.svg" class="text-xl text-amber-600"></ion-icon>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Editar Coordinador</h3>
                    <p class="text-sm text-gray-500 font-medium">Actualizar datos de contacto y acceso</p>
                </div>
            </div>
            <button onclick="usuarioCoordinadorModule.closeEditModal()" class="text-gray-400 hover:text-red-500 transition-colors bg-white rounded-full p-2 shadow-sm border border-gray-100 hover:border-red-100 hover:bg-red-50">
                <ion-icon src="../../assets/ionicons/close-outline.svg" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="coordinadorEditForm" onsubmit="usuarioCoordinadorModule.handleEditSubmit(event)" class="p-6 space-y-6">
            <input type="hidden" id="edit_coord_id" name="edit_coord_id">

            <div class="space-y-4">
                <div class="form-group">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="edit_coord_nombre">Nombre Completo del Coordinador</label>
                    <div class="relative">
                        <ion-icon src="../../assets/ionicons/person-outline.svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></ion-icon>
                        <input type="text" id="edit_coord_nombre" name="edit_coord_nombre" required
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all shadow-sm">
                    </div>
                </div>

                <div class="form-group">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="edit_coord_correo">Correo Institucional</label>
                    <div class="relative">
                        <ion-icon src="../../assets/ionicons/mail-outline.svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></ion-icon>
                        <input type="email" id="edit_coord_correo" name="edit_coord_correo" placeholder="usuario@sena.edu.co"
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all shadow-sm">
                    </div>
                </div>

                <div class="form-group">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="edit_coord_password">Nueva Contraseña (Dejar en blanco para no cambiar)</label>
                    <div class="relative">
                        <ion-icon src="../../assets/ionicons/lock-closed-outline.svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></ion-icon>
                        <input type="password" id="edit_coord_password" name="edit_coord_password" placeholder="Escriba solo si desea cambiarla"
                            class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all shadow-sm">
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="usuarioCoordinadorModule.closeEditModal()" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:ring-2 focus:ring-gray-200 transition-all shadow-sm">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-amber-500 rounded-xl hover:bg-amber-600 focus:ring-2 focus:ring-amber-500/50 transition-all shadow-md shadow-amber-500/20 flex items-center gap-2">
                    <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                    Actualizar Datos
                </button>
            </div>
        </form>
    </div>
</div>