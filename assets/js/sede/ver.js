// Sede View JavaScript
class SedeView {
    constructor() {
        this.sedeId = this.getSedeIdFromUrl();
        this.sedeData = null;
        this.filteredProgramas = [];
        this.programaFilters = {
            search: '',
            nivel: ''
        };
        this.ambientes = [];
        this.filteredAmbientes = [];
        this.ambienteFilters = {
            search: ''
        };
        this.fichas = [];
        this.filteredFichas = [];
        this.fichaFilters = {
            search: ''
        };
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadSedeData();
        this.initDeleteModal();
    }

    bindEvents() {
        // ... (existing program and ambiente events) ...

        // Fichas toggle functionality
        const verTodosFichasBtn = document.getElementById('verTodosFichas');
        const volverFichasBtn = document.getElementById('volverFichasPreview');

        if (verTodosFichasBtn) {
            verTodosFichasBtn.addEventListener('click', () => {
                this.showFullFichasList();
            });
        }

        if (volverFichasBtn) {
            volverFichasBtn.addEventListener('click', () => {
                this.showFichasPreview();
            });
        }

        // Ficha filter events
        const searchFicha = document.getElementById('searchFichaSede');
        if (searchFicha) {
            searchFicha.addEventListener('input', (e) => {
                this.fichaFilters.search = e.target.value;
                this.applyFichaFilters();
            });
        }

        // Programas toggle functionality
        const verTodosProgramasBtn = document.getElementById('verTodosProgramas');
        const volverProgramasBtn = document.getElementById('volverProgramasPreview');

        if (verTodosProgramasBtn) {
            verTodosProgramasBtn.addEventListener('click', () => {
                this.showFullProgramasList();
            });
        }

        if (volverProgramasBtn) {
            volverProgramasBtn.addEventListener('click', () => {
                this.showProgramasPreview();
            });
        }

        // Programa filter events
        const searchPrograma = document.getElementById('searchPrograma');
        const filterNivel = document.getElementById('filterNivelPrograma');
        const clearProgramaFilters = document.getElementById('clearProgramaFilters');
        const clearProgramaFiltersBtn = document.getElementById('clearProgramaFiltersBtn');

        if (searchPrograma) {
            searchPrograma.addEventListener('input', (e) => {
                this.programaFilters.search = e.target.value;
                this.applyProgramaFilters();
            });
        }

        if (filterNivel) {
            filterNivel.addEventListener('change', (e) => {
                this.programaFilters.nivel = e.target.value;
                this.applyProgramaFilters();
            });
        }

        if (clearProgramaFilters) {
            clearProgramaFilters.addEventListener('click', () => {
                this.clearAllProgramaFilters();
            });
        }

        if (clearProgramaFiltersBtn) {
            clearProgramaFiltersBtn.addEventListener('click', () => {
                this.clearAllProgramaFilters();
            });
        }

        // Ambientes toggle functionality
        const verTodosAmbientesBtn = document.getElementById('verTodosAmbientes');
        const volverAmbientesBtn = document.getElementById('volverAmbientesPreview');

        if (verTodosAmbientesBtn) {
            verTodosAmbientesBtn.addEventListener('click', () => {
                this.showFullAmbientesList();
            });
        }

        if (volverAmbientesBtn) {
            volverAmbientesBtn.addEventListener('click', () => {
                this.showAmbientesPreview();
            });
        }

        // Ambiente filter events
        const searchAmbiente = document.getElementById('searchAmbiente');
        if (searchAmbiente) {
            searchAmbiente.addEventListener('input', (e) => {
                this.ambienteFilters.search = e.target.value;
                this.applyAmbienteFilters();
            });
        }

        // Delete button
        const deleteSedeBtn = document.getElementById('deleteSedeBtn');
        if (deleteSedeBtn) {
            deleteSedeBtn.addEventListener('click', () => {
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
        this.sedeNameSpan = document.getElementById('sedeToDeleteName');

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
        if (!this.modal || !this.sedeData) return;

        if (this.sedeNameSpan) {
            this.sedeNameSpan.textContent = this.sedeData.sede_nombre;
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
        if (!this.sedeId) return;

        try {
            this.confirmBtn.disabled = true;
            this.confirmBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';

            const formData = new FormData();
            formData.append('controller', 'sede');
            formData.append('action', 'destroy');
            formData.append('sede_id', this.sedeId);

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();
            if (!response.ok || data.error) {
                throw new Error(data.error || 'Error al eliminar la sede');
            }

            this.showSuccessFeedback();

            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);

        } catch (error) {
            console.error('Error deleting sede:', error);
            NotificationService.showError(error.message || 'Hubo un error al intentar eliminar la sede. Por favor, intente de nuevo.');
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

    getSedeIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    async loadSedeData() {
        if (!this.sedeId) {
            NotificationService.showError('ID de sede no válido');
            return;
        }

        try {
            const sede = await this.fetchSede(this.sedeId);

            if (sede) {
                this.sedeData = sede;
                await this.loadRelatedData();
                this.populateSedeInfo();
                this.showDetails();
            } else {
                this.showError('Sede no encontrada');
            }
        } catch (error) {
            console.error('Error loading sede:', error);
            this.showError('Error al cargar la información de la sede');
        }
    }

    async fetchSede(sedeId) {
        const response = await fetch(`../../routing.php?controller=sede&action=show&id=${sedeId}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!response.ok) {
            return null;
        }
        return await response.json();
    }

    async loadRelatedData() {
        try {
            const [programas, ambientes, fichas] = await Promise.all([
                this.fetchProgramas(this.sedeId),
                this.fetchAmbientes(this.sedeId),
                this.fetchFichas(this.sedeId)
            ]);

            this.sedeData.programas = programas;
            this.sedeData.programas_count = programas.length;
            this.filteredProgramas = [...programas];
            this.ambientes = ambientes;
            this.filteredAmbientes = [...ambientes];
            this.fichas = fichas;
            this.filteredFichas = [...fichas];
        } catch (error) {
            console.error('Error loading related data:', error);
            this.sedeData.programas = [];
            this.ambientes = [];
            this.fichas = [];
        }
    }

    async fetchFichas(sedeId) {
        try {
            const response = await fetch(`../../routing.php?controller=sede&action=getFichas&sede_id=${sedeId}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) return [];
            return await response.json();
        } catch (error) {
            console.error('Error fetching fichas:', error);
            return [];
        }
    }

    async fetchProgramas(sedeId) {
        try {
            const response = await fetch(`../../routing.php?controller=sede&action=getProgramas&sede_id=${sedeId}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) return [];
            return await response.json();
        } catch (error) {
            console.error('Error fetching programas:', error);
            return [];
        }
    }

    async fetchAmbientes(sedeId) {
        try {
            const response = await fetch(`../../routing.php?controller=ambiente&action=index&sede_id=${sedeId}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) return [];
            return await response.json();
        } catch (error) {
            console.error('Error fetching ambientes:', error);
            return [];
        }
    }

    populateSedeInfo() {
        // Basic info
        const sedeNombreCard = document.getElementById('sedeNombreCard');
        const sedeId = document.getElementById('sedeId');

        if (sedeNombreCard) {
            sedeNombreCard.textContent = this.sedeData.sede_nombre;
        }

        if (sedeId) {
            sedeId.textContent = String(this.sedeData.sede_id).padStart(3, '0');
        }

        // Sede Photo in Regional Card
        const sedeFotoCard = document.getElementById('sedeFotoCard');
        const sedeFotoImg = document.getElementById('sedeFotoImg');
        const sedeDefaultInfo = document.getElementById('sedeDefaultInfo');

        if (sedeFotoCard && sedeFotoImg && sedeDefaultInfo) {
            if (this.sedeData.sede_foto) {
                sedeFotoImg.src = this.sedeData.sede_foto;
                sedeFotoCard.classList.remove('hidden');
                sedeDefaultInfo.classList.add('hidden');
            } else {
                sedeFotoCard.classList.add('hidden');
                sedeDefaultInfo.classList.remove('hidden');
            }
        }

        // Statistics
        const totalProgramas = document.getElementById('totalProgramas');

        if (totalProgramas) {
            totalProgramas.textContent = this.sedeData.programas_count || 0;
        }

        const totalAmbientes = document.getElementById('totalAmbientes');
        if (totalAmbientes) {
            totalAmbientes.textContent = this.ambientes.length || 0;
        }

        // Edit link
        const editLink = document.getElementById('editLink');
        if (editLink) {
            editLink.href = `editar.php?id=${this.sedeData.sede_id}`;
        }

        // Populate programs, environments and fichas
        this.populateProgramas();
        this.populateAmbientes();
        this.populateFichas();
        this.setupNivelFilter();
    }

    populateFichas() {
        const fichasList = document.getElementById('fichasList');
        const noFichas = document.getElementById('noFichas');

        if (!this.fichas || this.fichas.length === 0) {
            if (fichasList) fichasList.style.display = 'none';
            if (noFichas) noFichas.style.display = 'block';
            return;
        }

        if (noFichas) noFichas.style.display = 'none';
        if (fichasList) {
            fichasList.style.display = 'block';
            fichasList.innerHTML = '';

            // Show first 3 fichas in preview mode
            const fichasToShow = this.fichas.slice(0, 3);

            fichasToShow.forEach(ficha => {
                const fichaItem = document.createElement('div');
                fichaItem.className = 'flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer group';

                fichaItem.onclick = () => {
                    window.location.href = `../ficha/ver.php?id=${ficha.fich_id}`;
                };

                fichaItem.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-white dark:bg-slate-700 flex items-center justify-center text-slate-400 shadow-sm group-hover:text-sena-orange transition-colors">
                            <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Ficha ${ficha.fich_id}</p>
                            <p class="text-xs text-slate-500">${ficha.prog_denominacion || 'N/A'}</p>
                        </div>
                    </div>
                    <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-slate-400 group-hover:translate-x-1 transition-transform"></ion-icon>
                `;
                fichasList.appendChild(fichaItem);
            });

            if (this.fichas.length > 3) {
                const additionalDiv = document.createElement('div');
                additionalDiv.className = 'mt-4 text-center';
                additionalDiv.innerHTML = `
                    <div class="inline-flex items-center justify-center px-4 py-2 border border-dashed border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-500 dark:text-slate-400 w-full bg-slate-50/50 dark:bg-slate-800/30">
                        + ${this.fichas.length - 3} fichas registradas en esta sede
                    </div>
                `;
                fichasList.appendChild(additionalDiv);
            }
        }

        this.renderFichasCompleteList();
    }

    renderFichasCompleteList() {
        const fichasListComplete = document.getElementById('fichasListComplete');
        const totalFichasCount = document.getElementById('totalFichasCount');
        const noFichaFilterResults = document.getElementById('noFichaFilterResults');

        if (!fichasListComplete) return;

        if (totalFichasCount) {
            totalFichasCount.textContent = this.fichas.length;
        }

        this.updateFilteredFichasCount();

        if (this.filteredFichas.length === 0 && this.fichaFilters.search) {
            fichasListComplete.style.display = 'none';
            if (noFichaFilterResults) noFichaFilterResults.style.display = 'block';
            return;
        } else {
            fichasListComplete.style.display = 'block';
            if (noFichaFilterResults) noFichaFilterResults.style.display = 'none';
        }

        fichasListComplete.innerHTML = '';
        this.filteredFichas.forEach(ficha => {
            const fichaItem = document.createElement('div');
            fichaItem.className = 'flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer group';

            fichaItem.onclick = () => {
                window.location.href = `../ficha/ver.php?id=${ficha.fich_id}`;
            };

            fichaItem.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-white dark:bg-slate-700 flex items-center justify-center text-slate-400 shadow-sm group-hover:text-sena-orange transition-colors">
                        <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Ficha ${ficha.fich_id}</p>
                        <p class="text-xs text-slate-500">${ficha.prog_denominacion || 'N/A'}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="text-slate-400 hover:text-sena-green transition-colors" title="Ver detalles">
                        <ion-icon src="../../assets/ionicons/eye-outline.svg" class="text-sm"></ion-icon>
                    </button>
                </div>
            `;
            fichasListComplete.appendChild(fichaItem);
        });
    }

    showFullFichasList() {
        const preview = document.getElementById('fichasPreview');
        const fullList = document.getElementById('fichasFullList');
        if (preview) preview.style.display = 'none';
        if (fullList) fullList.style.display = 'block';
        this.filteredFichas = [...this.fichas];
        this.renderFichasCompleteList();
    }

    showFichasPreview() {
        const preview = document.getElementById('fichasPreview');
        const fullList = document.getElementById('fichasFullList');
        if (preview) preview.style.display = 'block';
        if (fullList) fullList.style.display = 'none';
        this.clearAllFichaFilters();
    }

    applyFichaFilters() {
        const searchTerm = this.fichaFilters.search.toLowerCase();
        this.filteredFichas = this.fichas.filter(ficha => {
            return !searchTerm ||
                String(ficha.fich_id).includes(searchTerm) ||
                (ficha.prog_denominacion || '').toLowerCase().includes(searchTerm);
        });
        this.renderFichasCompleteList();
    }

    updateFilteredFichasCount() {
        const filteredCount = document.getElementById('filteredFichasCount');
        if (filteredCount) filteredCount.textContent = this.filteredFichas.length;
    }

    clearAllFichaFilters() {
        this.fichaFilters = { search: '' };
        const searchInput = document.getElementById('searchFichaSede');
        if (searchInput) searchInput.value = '';
        this.applyFichaFilters();
    }

    getStatusBadgeClass(estado) {
        // Method removed as it's no longer used
        return '';
    }

    populateProgramas() {
        const programasList = document.getElementById('programasList');
        const noProgramas = document.getElementById('noProgramas');

        if (!this.sedeData.programas || this.sedeData.programas.length === 0) {
            if (programasList) programasList.style.display = 'none';
            if (noProgramas) noProgramas.style.display = 'block';
            return;
        }

        if (noProgramas) noProgramas.style.display = 'none';
        if (programasList) {
            programasList.style.display = 'block';
            programasList.innerHTML = '';

            // Show first 3 programs in preview mode
            const programsToShow = this.sedeData.programas.slice(0, 3);

            programsToShow.forEach(programa => {
                const programaItem = document.createElement('div');
                programaItem.className = 'flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer group';

                programaItem.onclick = () => {
                    window.location.href = `../programa/ver.php?id=${programa.prog_codigo}`;
                };

                const iconType = this.getProgramIcon(programa.prog_denominacion);

                programaItem.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-white dark:bg-slate-700 flex items-center justify-center text-slate-400 shadow-sm group-hover:text-sena-orange transition-colors">
                            <ion-icon src="../../assets/ionicons/${iconType}"></ion-icon>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">${programa.prog_denominacion}</p>
                            <p class="text-xs text-slate-500">${programa.titpro_nombre}</p>
                        </div>
                    </div>
                    <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-slate-400 group-hover:translate-x-1 transition-transform"></ion-icon>
                `;
                programasList.appendChild(programaItem);
            });

            // Add additional programs indicator if more than 3
            if (this.sedeData.programas.length > 3) {
                const additionalDiv = document.createElement('div');
                additionalDiv.className = 'mt-4 text-center';
                additionalDiv.innerHTML = `
                    <div class="inline-flex items-center justify-center px-4 py-2 border border-dashed border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-500 dark:text-slate-400 w-full bg-slate-50/50 dark:bg-slate-800/30">
                        + ${this.sedeData.programas.length - 3} programas adicionales vinculados a esta sede
                    </div>
                `;
                programasList.appendChild(additionalDiv);
            }
        }

        // Populate complete programs list
        this.renderProgramasCompleteList();
    }

    populateAmbientes() {
        const ambientesList = document.getElementById('ambientesList');
        const noAmbientes = document.getElementById('noAmbientes');

        if (!this.ambientes || this.ambientes.length === 0) {
            if (ambientesList) ambientesList.style.display = 'none';
            if (noAmbientes) noAmbientes.style.display = 'block';
            return;
        }

        if (noAmbientes) noAmbientes.style.display = 'none';
        if (ambientesList) {
            ambientesList.style.display = 'block';
            ambientesList.innerHTML = '';

            // Show first 3 ambientes in preview mode
            const ambientesToShow = this.ambientes.slice(0, 3);

            ambientesToShow.forEach(ambiente => {
                const ambienteItem = document.createElement('div');
                ambienteItem.className = 'flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer group';

                ambienteItem.onclick = () => {
                    window.location.href = `../ambiente/ver.php?id=${ambiente.amb_id}`;
                };

                ambienteItem.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-white dark:bg-slate-700 flex items-center justify-center text-slate-400 shadow-sm group-hover:text-sena-orange transition-colors">
                            <ion-icon src="../../assets/ionicons/cube-outline.svg"></ion-icon>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">${ambiente.amb_nombre}</p>
                            <p class="text-xs text-slate-500">ID: ${String(ambiente.amb_id).padStart(3, '0')}</p>
                        </div>
                    </div>
                    <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-slate-400 group-hover:translate-x-1 transition-transform"></ion-icon>
                `;
                ambientesList.appendChild(ambienteItem);
            });

            // Add additional indicator if more than 3
            if (this.ambientes.length > 3) {
                const additionalDiv = document.createElement('div');
                additionalDiv.className = 'mt-4 text-center';
                additionalDiv.innerHTML = `
                    <div class="inline-flex items-center justify-center px-4 py-2 border border-dashed border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-500 dark:text-slate-400 w-full bg-slate-50/50 dark:bg-slate-800/30">
                        + ${this.ambientes.length - 3} ambientes registrados en esta sede
                    </div>
                `;
                ambientesList.appendChild(additionalDiv);
            }
        }

        // Populate complete list
        this.renderAmbientesCompleteList();
    }

    renderAmbientesCompleteList() {
        const ambientesListComplete = document.getElementById('ambientesListComplete');
        const totalAmbientesCount = document.getElementById('totalAmbientesCount');
        const noAmbienteFilterResults = document.getElementById('noAmbienteFilterResults');

        if (!ambientesListComplete) return;

        if (totalAmbientesCount) {
            totalAmbientesCount.textContent = this.ambientes.length;
        }

        this.updateFilteredAmbientesCount();

        // Show/hide no results message
        if (this.filteredAmbientes.length === 0 && this.ambienteFilters.search) {
            ambientesListComplete.style.display = 'none';
            if (noAmbienteFilterResults) noAmbienteFilterResults.style.display = 'block';
            return;
        } else {
            ambientesListComplete.style.display = 'block';
            if (noAmbienteFilterResults) noAmbienteFilterResults.style.display = 'none';
        }

        ambientesListComplete.innerHTML = '';
        this.filteredAmbientes.forEach(ambiente => {
            const ambienteItem = document.createElement('div');
            ambienteItem.className = 'flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer group';

            ambienteItem.onclick = () => {
                window.location.href = `../ambiente/ver.php?id=${ambiente.amb_id}`;
            };

            ambienteItem.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-white dark:bg-slate-700 flex items-center justify-center text-slate-400 shadow-sm group-hover:text-sena-orange transition-colors">
                        <ion-icon src="../../assets/ionicons/cube-outline.svg"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">${ambiente.amb_nombre}</p>
                        <p class="text-xs text-slate-500">ID: ${String(ambiente.amb_id).padStart(3, '0')}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="text-slate-400 hover:text-sena-green transition-colors" title="Ver detalles">
                        <ion-icon src="../../assets/ionicons/eye-outline.svg" class="text-sm"></ion-icon>
                    </button>
                </div>
            `;

            ambientesListComplete.appendChild(ambienteItem);
        });
    }

    showFullAmbientesList() {
        const preview = document.getElementById('ambientesPreview');
        const fullList = document.getElementById('ambientesFullList');

        if (preview) preview.style.display = 'none';
        if (fullList) fullList.style.display = 'block';

        // Reset filters when opening full list
        this.filteredAmbientes = [...this.ambientes];
        this.renderAmbientesCompleteList();
    }

    showAmbientesPreview() {
        const preview = document.getElementById('ambientesPreview');
        const fullList = document.getElementById('ambientesFullList');

        if (preview) preview.style.display = 'block';
        if (fullList) fullList.style.display = 'none';

        // Clear filters when going back to preview
        this.clearAllAmbienteFilters();
    }

    applyAmbienteFilters() {
        const searchTerm = this.ambienteFilters.search.toLowerCase();

        this.filteredAmbientes = this.ambientes.filter(ambiente => {
            return !searchTerm ||
                ambiente.amb_nombre.toLowerCase().includes(searchTerm) ||
                String(ambiente.amb_id).includes(searchTerm);
        });

        this.renderAmbientesCompleteList();
    }

    updateFilteredAmbientesCount() {
        const filteredCount = document.getElementById('filteredAmbientesCount');
        if (filteredCount) {
            filteredCount.textContent = this.filteredAmbientes.length;
        }
    }

    clearAllAmbienteFilters() {
        this.ambienteFilters = { search: '' };
        const searchInput = document.getElementById('searchAmbiente');
        if (searchInput) searchInput.value = '';
        this.applyAmbienteFilters();
    }

    renderProgramasCompleteList() {
        const programasListComplete = document.getElementById('programasListComplete');
        const totalProgramasCount = document.getElementById('totalProgramasCount');
        const noProgramaFilterResults = document.getElementById('noProgramaFilterResults');

        if (!programasListComplete) return;

        if (totalProgramasCount) {
            totalProgramasCount.textContent = this.sedeData.programas.length;
        }

        this.updateFilteredProgramasCount();

        // Show/hide no results message
        if (this.filteredProgramas.length === 0 && (this.programaFilters.search || this.programaFilters.nivel)) {
            programasListComplete.style.display = 'none';
            if (noProgramaFilterResults) noProgramaFilterResults.style.display = 'block';
            return;
        } else {
            programasListComplete.style.display = 'block';
            if (noProgramaFilterResults) noProgramaFilterResults.style.display = 'none';
        }

        programasListComplete.innerHTML = '';

        this.filteredProgramas.forEach(programa => {
            const programaItem = document.createElement('div');
            programaItem.className = 'flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer group';

            programaItem.onclick = () => {
                window.location.href = `../programa/ver.php?id=${programa.prog_codigo}`;
            };

            const iconType = this.getProgramIcon(programa.prog_denominacion);

            programaItem.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-white dark:bg-slate-700 flex items-center justify-center text-slate-400 shadow-sm group-hover:text-sena-orange transition-colors">
                        <ion-icon src="../../assets/ionicons/${iconType}"></ion-icon>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">${programa.prog_denominacion}</p>
                        <p class="text-xs text-slate-500">${programa.titpro_nombre}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button class="text-slate-400 hover:text-sena-green transition-colors" title="Ver detalles">
                        <ion-icon src="../../assets/ionicons/eye-outline.svg" class="text-sm"></ion-icon>
                    </button>
                </div>
            `;

            programasListComplete.appendChild(programaItem);
        });
    }

    showFullProgramasList() {
        const preview = document.getElementById('programasPreview');
        const fullList = document.getElementById('programasFullList');

        if (preview) preview.style.display = 'none';
        if (fullList) fullList.style.display = 'block';

        // Reset filters when opening full list
        this.filteredProgramas = [...this.sedeData.programas];
        this.renderProgramasCompleteList();
    }

    showProgramasPreview() {
        const preview = document.getElementById('programasPreview');
        const fullList = document.getElementById('programasFullList');

        if (preview) preview.style.display = 'block';
        if (fullList) fullList.style.display = 'none';

        // Clear filters when going back to preview
        this.clearAllProgramaFilters();
    }

    applyProgramaFilters() {
        const searchTerm = this.programaFilters.search.toLowerCase();
        const nivelFilter = this.programaFilters.nivel;

        this.filteredProgramas = this.sedeData.programas.filter(programa => {
            const matchesSearch = !searchTerm ||
                programa.prog_denominacion.toLowerCase().includes(searchTerm);

            const matchesNivel = !nivelFilter ||
                programa.prog_denominacion.toLowerCase().includes(nivelFilter.toLowerCase());

            return matchesSearch && matchesNivel;
        });

        this.renderProgramasCompleteList();
        this.updateActiveProgramaFilters();
    }

    updateFilteredProgramasCount() {
        const filteredCount = document.getElementById('filteredProgramasCount');
        if (filteredCount) {
            filteredCount.textContent = this.filteredProgramas.length;
        }
    }

    updateActiveProgramaFilters() {
        const activeFilters = document.getElementById('activeProgramaFilters');
        const activeFiltersList = document.getElementById('activeProgramaFiltersList');

        if (!activeFilters || !activeFiltersList) return;

        const hasActiveFilters = this.programaFilters.search || this.programaFilters.nivel;

        if (hasActiveFilters) {
            activeFilters.style.display = 'flex';
            activeFiltersList.innerHTML = '';

            if (this.programaFilters.search) {
                const filterTag = document.createElement('span');
                filterTag.className = 'inline-flex items-center gap-1 px-2 py-1 bg-sena-green/10 text-sena-green text-xs rounded-full';
                filterTag.innerHTML = `
                    Nombre: "${this.programaFilters.search}"
                    <button onclick="sedeView.removeProgramaFilter('search')" class="hover:text-green-700">
                        <ion-icon src="../../assets/ionicons/close-outline.svg" class="text-xs"></ion-icon>
                    </button>
                `;
                activeFiltersList.appendChild(filterTag);
            }

            if (this.programaFilters.nivel) {
                const filterTag = document.createElement('span');
                filterTag.className = 'inline-flex items-center gap-1 px-2 py-1 bg-sena-orange/10 text-sena-orange text-xs rounded-full';
                filterTag.innerHTML = `
                    Nivel: "${this.programaFilters.nivel}"
                    <button onclick="sedeView.removeProgramaFilter('nivel')" class="hover:text-orange-700">
                        <ion-icon src="../../assets/ionicons/close-outline.svg" class="text-xs"></ion-icon>
                    </button>
                `;
                activeFiltersList.appendChild(filterTag);
            }
        } else {
            activeFilters.style.display = 'none';
        }
    }

    removeProgramaFilter(filterType) {
        this.programaFilters[filterType] = '';

        if (filterType === 'search') {
            const searchInput = document.getElementById('searchPrograma');
            if (searchInput) searchInput.value = '';
        } else if (filterType === 'nivel') {
            const nivelSelect = document.getElementById('filterNivelPrograma');
            if (nivelSelect) nivelSelect.value = '';
        }

        this.applyProgramaFilters();
    }

    clearAllProgramaFilters() {
        this.programaFilters = { search: '', nivel: '' };

        const searchInput = document.getElementById('searchPrograma');
        const nivelSelect = document.getElementById('filterNivelPrograma');

        if (searchInput) searchInput.value = '';
        if (nivelSelect) nivelSelect.value = '';

        this.applyProgramaFilters();
    }

    setupNivelFilter() {
        const nivelSelect = document.getElementById('filterNivelPrograma');
        if (!nivelSelect || !this.sedeData.programas) return;

        nivelSelect.innerHTML = '<option value="">Todos los niveles</option>';

        const niveles = ['Tecnólogo', 'Técnico', 'Complementaria', 'Especialización'];
        niveles.forEach(nivel => {
            const option = document.createElement('option');
            option.value = nivel;
            option.textContent = nivel;
            nivelSelect.appendChild(option);
        });
    }

    getInitials(name) {
        return name.split(' ')
            .map(word => word.charAt(0))
            .join('')
            .substring(0, 2)
            .toUpperCase();
    }

    getProgramIcon(programName) {
        if (!programName) return 'school-outline.svg';
        const name = programName.toLowerCase();
        if (name.includes('software') || name.includes('desarrollo')) return 'code-slash-outline.svg';
        if (name.includes('redes') || name.includes('network')) return 'wifi-outline.svg';
        if (name.includes('mantenimiento') || name.includes('equipos')) return 'hardware-chip-outline.svg';
        if (name.includes('diseño')) return 'color-palette-outline.svg';
        if (name.includes('manufactura')) return 'construct-outline.svg';
        if (name.includes('administración')) return 'briefcase-outline.svg';
        return 'school-outline.svg';
    }

    showDetails() {
        const loadingState = document.getElementById('loadingState');
        const sedeDetails = document.getElementById('sedeDetails');

        if (loadingState) {
            loadingState.style.display = 'none';
        }

        if (sedeDetails) {
            sedeDetails.style.display = 'block';
        }
    }

    showError(message) {
        const loadingState = document.getElementById('loadingState');
        const errorCard = document.getElementById('errorCard');
        const errorMessage = document.getElementById('errorMessage');

        if (loadingState) {
            loadingState.style.display = 'none';
        }

        if (errorMessage) {
            errorMessage.textContent = message;
        }

        if (errorCard) {
            errorCard.style.display = 'block';
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.sedeView = new SedeView();
});