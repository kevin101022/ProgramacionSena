// Ambiente View JavaScript
class AmbienteView {
    constructor() {
        this.ambienteId = this.getAmbienteIdFromUrl();
        this.ambienteData = null;
        this.init();
    }

    init() {
        if (!this.ambienteId) {
            window.location.href = 'index.php';
            return;
        }
        this.bindEvents();
        this.loadAmbienteData();
        this.initDeleteModal();
    }

    bindEvents() {
        // Delete button
        const deleteBtn = document.getElementById('deleteBtn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                this.openDeleteModal();
            });
        }
    }

    initDeleteModal() {
        this.modal = document.getElementById('deleteModal');
        this.modalContent = document.getElementById('modalContent');
        this.modalOverlay = document.getElementById('modalOverlay');
        this.cancelBtn = document.getElementById('cancelDeleteBtn');
        this.confirmBtn = document.getElementById('confirmDeleteBtn');
        this.ambienteNameSpan = document.getElementById('ambienteToDeleteName');

        if (this.cancelBtn) {
            this.cancelBtn.addEventListener('click', () => this.closeDeleteModal());
        }

        if (this.modalOverlay) {
            this.modalOverlay.addEventListener('click', () => this.closeDeleteModal());
        }

        if (this.confirmBtn) {
            this.confirmBtn.addEventListener('click', () => this.confirmDelete());
        }

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal && !this.modal.classList.contains('hidden')) {
                this.closeDeleteModal();
            }
        });
    }

    openDeleteModal() {
        if (!this.modal || !this.ambienteData) return;

        if (this.ambienteNameSpan) {
            this.ambienteNameSpan.textContent = this.ambienteData.amb_nombre;
        }

        this.modal.classList.remove('hidden');
        // Small delay for animation
        setTimeout(() => {
            if (this.modalContent) {
                this.modalContent.classList.remove('scale-95', 'opacity-0');
                this.modalContent.classList.add('scale-100', 'opacity-100');
            }
        }, 10);
    }

    closeDeleteModal() {
        if (!this.modal || !this.modalContent) return;

        this.modalContent.classList.remove('scale-100', 'opacity-100');
        this.modalContent.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            this.modal.classList.add('hidden');
        }, 300);
    }

    async confirmDelete() {
        if (!this.ambienteId) return;

        try {
            this.confirmBtn.disabled = true;
            this.confirmBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';

            const formData = new FormData();
            formData.append('controller', 'ambiente');
            formData.append('action', 'destroy');
            formData.append('id', this.ambienteId);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (!response.ok || data.error) {
                throw new Error(data.error || 'Error al eliminar el ambiente');
            }

            this.showSuccessFeedback();

            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);

        } catch (error) {
            console.error('Error deleting ambiente:', error);
            NotificationService.showError(error.message || 'Hubo un error al intentar eliminar el ambiente. Por favor, intente de nuevo.');
            this.confirmBtn.disabled = false;
            this.confirmBtn.textContent = 'Sí, eliminar';
        }
    }

    showSuccessFeedback() {
        const overlay = document.getElementById('successOverlay');
        if (overlay) {
            overlay.classList.remove('hidden');
            this.closeDeleteModal();
        }
    }

    getAmbienteIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    async loadAmbienteData() {
        try {
            const [ambiente, sedes] = await Promise.all([
                fetch(`../../routing.php?controller=ambiente&action=show&id=${this.ambienteId}`, {
                    headers: { 'Accept': 'application/json' }
                }).then(res => res.json()),
                fetch('../../routing.php?controller=sede&action=index', {
                    headers: { 'Accept': 'application/json' }
                }).then(res => res.json())
            ]);

            if (ambiente && !ambiente.error) {
                this.ambienteData = ambiente;
                // Note: sede_nombre is already included by the model's JOIN in readById
                this.populateAmbienteInfo();

                // Load and render programming
                await this.loadProgramacion();

                this.showDetails();
            } else {
                this.showError(ambiente.error || 'Ambiente no encontrado');
            }
        } catch (error) {
            console.error('Error loading ambiente:', error);
            this.showError('Error al cargar la información del ambiente');
        }
    }

    populateAmbienteInfo() {
        const elements = {
            'ambienteNombreCard': this.ambienteData.amb_nombre,
            'dispNombre': this.ambienteData.amb_nombre,
            'dispIdAmbiente': String(this.ambienteData.amb_id).padStart(3, '0'),
            'dispSede': this.ambienteData.sede_nombre,
            'editBtn': `editar.php?id=${this.ambienteData.amb_id}`
        };

        for (const [id, value] of Object.entries(elements)) {
            const el = document.getElementById(id);
            if (el) {
                if (id === 'editBtn') {
                    el.href = value;
                } else {
                    el.textContent = value;
                }
            }
        }
    }

    async loadProgramacion() {
        try {
            const response = await fetch(`../../routing.php?controller=ambiente&action=getProgramacion&id=${this.ambienteId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            const list = document.getElementById('programacionList');
            const noData = document.getElementById('noProgramacion');
            const countFichas = document.getElementById('totalFichas');
            const countInstructores = document.getElementById('totalInstructores');

            // Unique fichas and instructors counts
            const uniqueFichas = new Set(data.map(p => p.fich_id));
            const uniqueInstructores = new Set(data.map(p => `${p.inst_nombres} ${p.inst_apellidos}`));

            countFichas.textContent = uniqueFichas.size;
            countInstructores.textContent = uniqueInstructores.size;

            if (data.length === 0) {
                list.innerHTML = '';
                noData.classList.remove('hidden');
                return;
            }

            noData.classList.add('hidden');
            list.innerHTML = '';

            data.forEach(p => {
                const item = document.createElement('div');
                item.className = 'flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-sena-green/30 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer group';
                item.onclick = () => window.location.href = `../asignacion/ver.php?id=${p.asig_id}`;

                item.innerHTML = `
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-white dark:bg-slate-800 rounded-lg shadow-sm group-hover:bg-sena-green/10 transition-colors">
                            <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-sena-green text-xl"></ion-icon>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 bg-sena-orange/10 text-sena-orange text-[10px] font-bold rounded-full uppercase">Ficha: ${p.fich_id}</span>
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-600 text-[10px] font-bold rounded-full uppercase">${p.comp_nombre_corto}</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-1 group-hover:text-sena-green transition-colors">${p.prog_denominacion}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1">
                                <ion-icon src="../../assets/ionicons/person-outline.svg"></ion-icon>
                                ${p.inst_nombres} ${p.inst_apellidos}
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 sm:mt-0 text-right flex items-center gap-3">
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Horario de Asignación</div>
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                ${this.formatDate(p.asig_fecha_ini)} - ${this.formatDate(p.asig_fecha_fin)}
                            </p>
                        </div>
                        <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-slate-300 group-hover:text-sena-green transition-all"></ion-icon>
                    </div>
                `;
                list.appendChild(item);
            });
        } catch (err) {
            console.error('Error loading programming:', err);
        }
    }

    formatDate(dateStr) {
        if (!dateStr) return 'N/A';
        const date = new Date(dateStr);
        return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    showDetails() {
        const loadingState = document.getElementById('loadingState');
        const ambienteDetails = document.getElementById('ambienteDetails');

        if (loadingState) loadingState.style.display = 'none';
        if (ambienteDetails) {
            ambienteDetails.style.display = 'block';
            ambienteDetails.classList.add('animate-fade-in');
        }
    }

    showError(message) {
        const loadingState = document.getElementById('loadingState');
        const errorCard = document.getElementById('errorCard');
        const errorMessage = document.getElementById('errorMessage');

        if (loadingState) loadingState.style.display = 'none';
        if (errorCard) errorCard.style.display = 'block';
        if (errorMessage) errorMessage.textContent = message;
    }
}

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    window.ambienteView = new AmbienteView();
});
