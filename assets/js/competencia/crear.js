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
        this.programasList = document.getElementById('programasList');
        this.progSearch = document.getElementById('progSearch');
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        this.progSearch.addEventListener('input', () => this.filterProgramas());
    }

    async loadProgramas() {
        try {
            const response = await fetch('../../routing.php?controller=programa&action=index');
            this.programas = await response.json();
            this.renderProgramas();
        } catch (error) {
            console.error('Error loading programas:', error);
        }
    }

    renderProgramas() {
        this.programasList.innerHTML = '';
        this.programas.forEach(p => {
            const div = document.createElement('div');
            div.className = 'program-item flex items-center p-3 bg-white rounded-lg border border-gray-100 hover:border-green-200 transition-colors cursor-pointer group';
            div.dataset.name = `${p.prog_denominacion} ${p.titpro_nombre}`.toLowerCase();

            div.innerHTML = `
                <label class="flex items-center gap-3 w-full cursor-pointer">
                    <input type="checkbox" name="programas[]" value="${p.prog_codigo}" class="w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500 transition-all">
                    <div class="flex-1 min-width-0">
                        <div class="text-sm font-semibold text-gray-800 truncate">${p.prog_denominacion}</div>
                        <div class="text-xs text-gray-500 truncate">${p.titpro_nombre}</div>
                    </div>
                </label>
            `;

            // Add click listener to the div as well for better UX
            div.onclick = (e) => {
                if (e.target.tagName !== 'INPUT') {
                    const cb = div.querySelector('input');
                    cb.checked = !cb.checked;
                }
            };

            this.programasList.appendChild(div);
        });
    }

    filterProgramas() {
        const term = this.progSearch.value.toLowerCase();
        const items = this.programasList.querySelectorAll('.program-item');
        items.forEach(item => {
            const visible = item.dataset.name.includes(term);
            item.style.display = visible ? 'flex' : 'none';
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
