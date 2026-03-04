<!-- Create/Edit Modal -->
<div id="asignacionModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3 id="modalTitle">Nueva Asignación</h3>
            <button class="modal-close" id="closeModal">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="asignacionForm">
            <input type="hidden" id="asig_id" name="asig_id">
            <input type="hidden" id="modal_ficha_id" name="ficha_fich_id">
            <div class="modal-body p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group md:col-span-2">
                        <label class="form-label">Ficha seleccionada</label>
                        <input type="text" id="fichaDisplay" class="search-input" style="padding-left: 12px !important;" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Competencia <span class="text-red-500">*</span></label>
                        <select id="competencia_id" name="competencia_comp_id" required class="search-input" style="padding-left: 12px !important;">
                            <option value="">Cargando competencias...</option>
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1 italic">Solo competencias no asignadas a esta ficha</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Instructor <span class="text-red-500">*</span></label>
                        <select id="instructor_id" name="instructor_inst_id" required class="search-input" style="padding-left: 12px !important;" disabled>
                            <option value="">Primero seleccione competencia...</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Solo instructores habilitados</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ambiente <span class="text-red-500">*</span></label>
                        <select id="ambiente_id" name="ambiente_amb_id" required class="search-input" style="padding-left: 12px !important;"></select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha Inicio (Rango) <span class="text-red-500">*</span></label>
                        <input type="date" id="asig_fecha_ini" name="asig_fecha_ini" required class="search-input" style="padding-left: 12px !important;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fecha Final (Rango) <span class="text-red-500">*</span></label>
                        <input type="date" id="asig_fecha_fin" name="asig_fecha_fin" required class="search-input" style="padding-left: 12px !important;">
                    </div>

                </div>

                <!-- Detalle por Días -->
                <div class="mt-6">
                    <h4 class="text-sm font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2">Asignación de Horas por Día</h4>
                    <p class="text-xs text-gray-500 mb-4">Seleccione los días específicos y asigne el horario correspondiente para el centro de formación. Tenga en cuenta que no todos los días deben tener horas asignadas o las mismas franjas horarias.</p>
                    <div id="diasListContainer" class="space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                        <p class="text-sm text-center text-gray-400 italic py-4">Seleccione fechas en el calendario para configurar los días</p>
                    </div>
                </div>

                <!-- Inline Conflict Alert Container -->
                <div id="modalConflictAlert" class="mt-4 hidden animate-in fade-in duration-300">
                    <!-- JS will populate this -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="cancelBtn">Cancelar</button>
                <button type="submit" class="btn-primary" id="saveBtn">
                    <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>