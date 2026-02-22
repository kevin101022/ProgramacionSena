<!-- Create/Edit Modal -->
<div id="fichaModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 id="modalTitle">Nueva Ficha</h3>
            <button class="modal-close" id="closeModal">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="fichaForm">
            <input type="hidden" id="fich_id_old" name="fich_id_old">
            <div class="modal-body p-6 space-y-4">
                <div class="form-group">
                    <label class="form-label">Número de Ficha <span class="text-red-500">*</span></label>
                    <input type="number" id="fich_id" name="fich_id" required class="search-input" style="padding-left: 12px !important;" placeholder="Ej: 2670687">
                </div>
                <div class="form-group">
                    <label class="form-label">Programa <span class="text-red-500">*</span></label>
                    <select id="programa_id" name="programa_prog_id" required class="search-input" style="padding-left: 12px !important;">
                        <option value="">Seleccione programa...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Instructor Líder <span class="text-red-500">*</span></label>
                    <select id="instructor_id" name="instructor_inst_id" required class="search-input" style="padding-left: 12px !important;">
                        <option value="">Seleccione instructor líder...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Coordinación <span class="text-red-500">*</span></label>
                    <select id="coordinacion_id" name="coordinacion_id" required class="search-input" style="padding-left: 12px !important;">
                        <option value="">Seleccione coordinación...</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jornada <span class="text-red-500">*</span></label>
                    <select id="fich_jornada" name="fich_jornada" required class="search-input" style="padding-left: 12px !important;">
                        <option value="">Seleccione jornada...</option>
                        <option value="Mañana">Mañana</option>
                        <option value="Tarde">Tarde</option>
                        <option value="Noche">Noche</option>
                        <option value="Mixta">Mixta</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha Inicio Lectiva <span class="text-red-500">*</span></label>
                    <input type="date" id="fich_fecha_ini_lectiva" name="fich_fecha_ini_lectiva" required class="search-input" style="padding-left: 12px !important;">
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha Fin Lectiva <span class="text-red-500">*</span></label>
                    <input type="date" id="fich_fecha_fin_lectiva" name="fich_fecha_fin_lectiva" required class="search-input" style="padding-left: 12px !important;">
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