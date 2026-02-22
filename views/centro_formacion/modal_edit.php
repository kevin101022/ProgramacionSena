<!-- Create/Edit Modal -->
<div id="centroModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 id="modalTitle">Nuevo Centro de Formación</h3>
            <button class="modal-close" id="closeModal">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="centroForm">
            <div class="modal-body p-6 space-y-4">
                <input type="hidden" id="cent_id" name="cent_id">
                <div class="form-group">
                    <label class="form-label">Nombre del Centro <span class="text-red-500">*</span></label>
                    <input type="text" id="cent_nombre" name="cent_nombre" required class="search-input" style="padding-left: 12px !important;" placeholder="Ej: Centro de Teleinformática">
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