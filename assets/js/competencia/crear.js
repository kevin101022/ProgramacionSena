class CrearCompetencia {
    constructor() {
        this.programas = [];
        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadProgramas();
    }

    cacheDOM() {
        this.form = document.getElementById('crearCompetenciaForm');
        this.programaSelect = document.getElementById('programa_prog_id');
    }

    bindEvents() {
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
    }

    async loadProgramas() {
        try {
            const response = await fetch('../../routing.php?controller=programa&action=index');
            const data = await response.json();
            this.programas = Array.isArray(data) ? data : [];
            this.renderProgramas();
        } catch (error) {
            console.error('Error loading programas:', error);
        }
    }

    renderProgramas() {
        if (!this.programaSelect) return;
        
        // Mantener la opción por defecto
        const defaultOption = this.programaSelect.options[0];
        this.programaSelect.innerHTML = '';
        this.programaSelect.appendChild(defaultOption);

        this.programas.forEach(p => {
            const option = document.createElement('option');
            option.value = p.prog_codigo;
            option.textContent = `${p.prog_denominacion} (${p.titpro_nombre})`;
            this.programaSelect.appendChild(option);
        });
    }

    async handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);

        try {
            const response = await fetch('../../routing.php?controller=competencia&action=store', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const result = await response.json();

            if (result.id) {
                if (window.NotificationService) {
                    NotificationService.show('Competencia creada con éxito', 'success');
                }
                setTimeout(() => window.location.href = 'index.php', 1500);
            } else {
                throw new Error(result.error || 'Error al guardar');
            }
        } catch (error) {
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CrearCompetencia();
});
