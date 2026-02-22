class CrearTitulo {
    constructor() {
        this.init();
    }

    init() {
        this.cacheDOM();
        this.bindEvents();
    }

    cacheDOM() {
        this.form = document.getElementById('crearTituloForm');
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async handleSubmit(e) {
        e.preventDefault();

        const formData = new FormData(this.form);
        formData.append('controller', 'titulo_programa');
        formData.append('action', 'store');

        try {
            const response = await fetch('../../routing.php', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Error en el servidor');
            }

            const result = await response.json();

            if (result.message) {
                if (window.NotificationService) {
                    NotificationService.showSuccess('Título creado correctamente', () => {
                        document.getElementById('successModal').classList.add('show');
                    });
                } else {
                    document.getElementById('successModal').classList.add('show');
                }
            } else {
                throw new Error(result.error || 'Error al crear el título');
            }
        } catch (error) {
            console.error('Error creating titulo:', error);
            if (window.NotificationService) {
                NotificationService.showError('Error al guardar: ' + error.message);
            } else {
                alert('Error al guardar: ' + error.message);
            }
        }
    }
}

function closeSuccessModal() {
    document.getElementById('successModal').classList.remove('show');
    document.getElementById('crearTituloForm').reset();
}

document.addEventListener('DOMContentLoaded', () => {
    new CrearTitulo();
});
