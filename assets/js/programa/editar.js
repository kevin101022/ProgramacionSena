class EditarPrograma {
    constructor() {
        const urlParams = new URLSearchParams(window.location.search);
        this.programaId = urlParams.get('id');

        if (!this.programaId) {
            window.location.href = 'index.php';
            return;
        }

        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await Promise.all([
            this.loadTitulos()
        ]);
        await this.loadProgramaData();
    }

    cacheDOM() {
        this.form = document.getElementById('editarProgramaForm');
        this.tituloSelect = document.getElementById('tit_programa_titpro_id');
        this.codigoInput = document.getElementById('prog_codigo');
        this.denominacionInput = document.getElementById('prog_denominacion');
        this.tipoSelect = document.getElementById('prog_tipo');
        this.versionInput = document.getElementById('prog_version');
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async loadTitulos() {
        try {
            const response = await fetch('../../routing.php?controller=programa&action=getTitulos');
            const titulos = await response.json();

            this.tituloSelect.innerHTML = '<option value="" disabled>Seleccione un título...</option>';
            titulos.forEach(t => {
                const option = document.createElement('option');
                option.value = t.titpro_id;
                option.textContent = t.titpro_nombre;
                this.tituloSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading titulos:', error);
        }
    }

    async loadProgramaData() {
        try {
            const response = await fetch(`../../routing.php?controller=programa&action=show&id=${this.programaId}`);
            const data = await response.json();

            if (data.error) throw new Error(data.error);

            this.codigoInput.value = data.prog_codigo;
            this.denominacionInput.value = data.prog_denominacion;
            this.tituloSelect.value = data.tit_programa_titpro_id;
            this.tipoSelect.value = data.prog_tipo || '';
            this.versionInput.value = data.prog_version || '';

        } catch (error) {
            console.error('Error loading programa data:', error);
            if (window.NotificationService) {
                NotificationService.show('Error al cargar datos del programa', 'error');
            }
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);
        formData.append('controller', 'programa');
        formData.append('action', 'update');

        try {
            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.message) {
                NotificationService.showSuccess(result.message || '¡Programa actualizado con éxito!', () => {
                    window.location.href = 'index.php';
                });
            } else {
                throw new Error(result.error || 'Error al actualizar el programa');
            }
        } catch (error) {
            console.error('Error updating programa:', error);
            NotificationService.showError(error.message || 'Error al actualizar el programa');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new EditarPrograma();
});
