<!-- Create/Edit Modal -->
<div id="habilitacionModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 id="modalTitle">Nueva Habilitación</h3>
            <button class="modal-close" id="closeModal">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="habilitacionForm">
            <input type="hidden" id="inscomp_id" name="inscomp_id">
            <div class="modal-body p-6 space-y-4">
                <div class="form-group">
                    <label class="form-label">Instructor <span class="text-red-500">*</span></label>
                    <select id="instructor_id" name="instructor_inst_id" required class="search-input" style="padding-left: 12px !important;">
                        <option value="">Seleccione instructor...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Programa <span class="text-red-500">*</span></label>
                    <select id="programa_id" name="competxprograma_programa_prog_id" required class="search-input" style="padding-left: 12px !important;">
                        <option value="">Seleccione programa...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Competencia <span class="text-red-500">*</span></label>
                    <select id="competencia_id" name="competxprograma_competencia_comp_id" required class="search-input" style="padding-left: 12px !important;" disabled>
                        <option value="">Primero seleccione un programa...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Vigencia <span class="text-red-500">*</span></label>
                    <input type="date" id="inscomp_vigencia" name="inscomp_vigencia" required class="search-input" style="padding-left: 12px !important;">
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