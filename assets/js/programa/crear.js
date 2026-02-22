class CrearPrograma {
    constructor() {
        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await Promise.all([
            this.loadSedes(),
            this.loadTitulos()
        ]);
    }

    cacheDOM() {
        this.form = document.getElementById('crearProgramaForm');
        this.tituloSelect = document.getElementById('tit_programa_titpro_id');
        this.sedeSelect = document.getElementById('sede_sede_id');
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async loadTitulos() {
        try {
            const response = await fetch('../../routing.php?controller=programa&action=getTitulos');
            const titulos = await response.json();

            this.tituloSelect.innerHTML = '<option value="" disabled selected>Seleccione un título...</option>';
            titulos.forEach(t => {
                const option = document.createElement('option');
                option.value = t.titpro_id;
                option.textContent = t.titpro_nombre;
                this.tituloSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading titulos:', error);
            if (window.NotificationService) {
                NotificationService.show('Error al cargar los títulos', 'error');
            }
        }
    }

    async loadSedes() {
        try {
            const response = await fetch('../../routing.php?controller=sede&action=index');
            const sedes = await response.json();

            this.sedeSelect.innerHTML = '<option value="" disabled selected>Seleccione una sede...</option>';
            sedes.forEach(s => {
                const option = document.createElement('option');
                option.value = s.sede_id;
                option.textContent = s.sede_nombre;
                this.sedeSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading sedes:', error);
            if (window.NotificationService) {
                NotificationService.show('Error al cargar las sedes', 'error');
            }
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);
        formData.append('controller', 'programa');
        formData.append('action', 'store');

        try {
            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.message) {
                if (window.NotificationService) {
                    NotificationService.show(result.message, 'success');
                }
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            } else {
                throw new Error(result.error || 'Error al crear el programa');
            }
        } catch (error) {
            console.error('Error creating programa:', error);
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CrearPrograma();
});
