class EditarTitulo {
    constructor() {
        const urlParams = new URLSearchParams(window.location.search);
        this.tituloId = urlParams.get('id');

        if (!this.tituloId) {
            window.location.href = 'index.php';
            return;
        }

        this.init();
    }

    async init() {
        this.cacheDOM();
        this.bindEvents();
        await this.loadTituloData();
    }

    cacheDOM() {
        this.form = document.getElementById('editarTituloForm');
        this.nombreInput = document.getElementById('titpro_nombre');
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async loadTituloData() {
        try {
            const response = await fetch(`../../routing.php?controller=titulo_programa&action=show&id=${this.tituloId}`);
            const data = await response.json();

            if (data.error) throw new Error(data.error);

            this.nombreInput.value = data.titpro_nombre;

        } catch (error) {
            console.error('Error loading titulo data:', error);
            if (window.NotificationService) {
                NotificationService.show('Error al cargar datos del título', 'error');
            }
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);
        formData.append('controller', 'titulo_programa');
        formData.append('action', 'update');

        try {
            const response = await fetch('../../routing.php', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
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
                throw new Error(result.error || 'Error al actualizar el título');
            }
        } catch (error) {
            console.error('Error updating titulo:', error);
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new EditarTitulo();
});
