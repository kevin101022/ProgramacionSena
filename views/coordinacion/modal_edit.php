<!-- Create/Edit Modal -->
<div id="coordinacionModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 id="modalTitle">Nueva Coordinación</h3>
            <button class="modal-close" id="closeModal">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="coordinacionForm">
            <div class="modal-body p-6 space-y-4">
                <input type="hidden" id="coord_id" name="coord_id">
                <div class="form-group">
                    <label class="form-label">Nombre de la Coordinación <span class="text-red-500">*</span></label>
                    <input type="text" id="coord_nombre" name="coord_nombre" required class="search-input" style="padding-left: 12px !important;" placeholder="Ej: Coordinación de Teleinformática">
                </div>
                <div class="form-group">
                    <label class="form-label">Centro de Formación <span class="text-red-500">*</span></label>
                    <select id="centro_id" name="centro_formacion_cent_id" required class="search-input" style="padding-left: 12px !important;">
                        <option value="">Seleccione un centro...</option>
                    </select>
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