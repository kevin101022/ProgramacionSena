class EditarCompetencia {
    constructor() {
        this.compId = new URLSearchParams(window.location.search).get('id');
        this.programas = [];
        this.associatedProgramId = null;
        if (!this.compId) window.location.href = 'index.php';
        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadInitialData();
    }

    cacheDOM() {
        this.form = document.getElementById('editarCompetenciaForm');
        this.programaSelect = document.getElementById('programa_prog_id');
        // Inputs
        this.compIdInput = document.getElementById('comp_id');
        this.nombreInput = document.getElementById('comp_nombre_corto');
        this.horasInput = document.getElementById('comp_horas');
        this.unidadInput = document.getElementById('comp_nombre_unidad_competencia');
        this.requisitosInput = document.getElementById('requisitos_academicos');
        this.experienciaInput = document.getElementById('experiencia_laboral');
    }

    bindEvents() {
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
    }

    async loadInitialData() {
        try {
            // Load programs list first
            const progResponse = await fetch('../../routing.php?controller=programa&action=index');
            const progData = await progResponse.json();
            this.programas = Array.isArray(progData) ? progData : [];

            // Load competency data
            const compResponse = await fetch(`../../routing.php?controller=competencia&action=show&id=${this.compId}`);
            const compData = await compResponse.json();

            if (compData.error) throw new Error(compData.error);

            // Populate inputs
            if (this.compIdInput) this.compIdInput.value = compData.comp_id;
            if (this.nombreInput) this.nombreInput.value = compData.comp_nombre_corto;
            if (this.horasInput) this.horasInput.value = compData.comp_horas;
            if (this.unidadInput) this.unidadInput.value = compData.comp_nombre_unidad_competencia || '';
            if (this.requisitosInput) this.requisitosInput.value = compData.requisitos_academicos || '';
            if (this.experienciaInput) this.experienciaInput.value = compData.experiencia_laboral || '';

            // Associated program ID
            this.associatedProgramId = (compData.programas && compData.programas.length > 0) ? compData.programas[0].prog_codigo : null;

            this.renderProgramas();
        } catch (error) {
            console.error('Error loading data:', error);
            if (window.NotificationService) {
                NotificationService.show('Error al cargar la información', 'error');
            }
        }
    }

    renderProgramas() {
        if (!this.programaSelect) return;
        
        const defaultOption = this.programaSelect.options[0];
        this.programaSelect.innerHTML = '';
        this.programaSelect.appendChild(defaultOption);

        this.programas.forEach(p => {
            const option = document.createElement('option');
            option.value = p.prog_codigo;
            option.textContent = `${p.prog_codigo} — ${p.prog_denominacion}`;
            if (this.associatedProgramId && p.prog_codigo == this.associatedProgramId) {
                option.selected = true;
            }
            this.programaSelect.appendChild(option);
        });
        initTS(this.programaSelect, 'Buscar programa...');
    }

    async handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);

        try {
            const response = await fetch('../../routing.php?controller=competencia&action=update', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const result = await response.json();

            if (result.message) {
                NotificationService.showSuccess(result.message || '¡Competencia actualizada con éxito!', () => {
                    window.location.href = 'index.php';
                });
            } else {
                throw new Error(result.error || 'Error al actualizar');
            }
        } catch (error) {
            NotificationService.showError(error.message || 'Error al actualizar la competencia');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new EditarCompetencia();
});
