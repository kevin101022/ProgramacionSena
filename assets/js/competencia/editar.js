class EditarCompetencia {
    constructor() {
        this.compId = new URLSearchParams(window.location.search).get('id');
        this.programas = [];
        this.associatedProgramIds = [];
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
        this.programasList = document.getElementById('programasList');
        this.progSearch = document.getElementById('progSearch');
        // Inputs
        this.compIdInput = document.getElementById('comp_id');
        this.nombreInput = document.getElementById('comp_nombre_corto');
        this.horasInput = document.getElementById('comp_horas');
        this.unidadInput = document.getElementById('comp_nombre_unidad_competencia');
    }

    bindEvents() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        this.progSearch.addEventListener('input', () => this.filterProgramas());
    }

    async loadInitialData() {
        try {
            // Load programs list first
            const progResponse = await fetch('../../routing.php?controller=programa&action=index');
            this.programas = await progResponse.json();

            // Load competency data
            const compResponse = await fetch(`../../routing.php?controller=competencia&action=show&id=${this.compId}`);
            const compData = await compResponse.json();

            if (compData.error) throw new Error(compData.error);

            // Populate inputs
            this.compIdInput.value = compData.comp_id;
            this.nombreInput.value = compData.comp_nombre_corto;
            this.horasInput.value = compData.comp_horas;
            this.unidadInput.value = compData.comp_nombre_unidad_competencia || '';

            // Associated programs
            this.associatedProgramIds = compData.programas.map(p => p.prog_codigo);

            this.renderProgramas();
        } catch (error) {
            console.error('Error loading data:', error);
            if (window.NotificationService) {
                NotificationService.show('Error al cargar la información', 'error');
            }
        }
    }

    renderProgramas() {
        this.programasList.innerHTML = '';
        this.programas.forEach(p => {
            const isChecked = this.associatedProgramIds.includes(p.prog_codigo);
            const div = document.createElement('div');
            div.className = 'program-item flex items-center p-3 bg-white rounded-lg border border-gray-100 hover:border-green-200 transition-colors cursor-pointer group';
            div.dataset.name = `${p.prog_denominacion} ${p.titpro_nombre}`.toLowerCase();

            div.innerHTML = `
                <label class="flex items-center gap-3 w-full cursor-pointer">
                    <input type="checkbox" name="programas[]" value="${p.prog_codigo}" ${isChecked ? 'checked' : ''} class="w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500 transition-all">
                    <div class="flex-1 min-width-0">
                        <div class="text-sm font-semibold text-gray-800 truncate">${p.prog_denominacion}</div>
                        <div class="text-xs text-gray-500 truncate">${p.titpro_nombre}</div>
                    </div>
                </label>
            `;

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
            const response = await fetch('../../routing.php?controller=competencia&action=update', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const result = await response.json();

            if (result.message) {
                if (window.NotificationService) {
                    NotificationService.show(result.message, 'success');
                }
                setTimeout(() => window.location.href = 'index.php', 1500);
            } else {
                throw new Error(result.error || 'Error al actualizar');
            }
        } catch (error) {
            if (window.NotificationService) {
                NotificationService.show(error.message, 'error');
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new EditarCompetencia();
});
