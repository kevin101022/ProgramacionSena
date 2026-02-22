<?php
$pageTitle = 'Detalle de Sede - SENA';
$activeNavItem = 'sedes';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <!-- Header -->
    <header class="bg-surface-light/80 dark:bg-surface-dark/80 backdrop-blur-sm sticky top-0 z-20 border-b border-slate-200 dark:border-slate-700 px-8 py-4 flex justify-between items-center">
        <div>
            <nav aria-label="Breadcrumb" class="flex text-sm text-slate-500 dark:text-slate-400 mb-1">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="index.php" class="hover:text-sena-green dark:hover:text-sena-green transition-colors">Sedes</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-base mx-1"></ion-icon>
                            <span class="text-slate-800 dark:text-white font-medium">Detalle de Sede</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <ion-icon src="../../assets/ionicons/information-circle-outline.svg" class="text-2xl text-sena-orange"></ion-icon>
                Información Detallada
            </h1>
        </div>

        <div class="flex items-center gap-4">
            <a href="index.php" class="flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-sena-orange transition-colors px-3 py-2 rounded-lg hover:bg-orange-50 dark:hover:bg-orange-900/10 border border-transparent hover:border-sena-orange/20">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                <span class="text-sm font-medium">Regresar</span>
            </a>
        </div>
    </header>

    <div class="p-8 max-w-7xl mx-auto space-y-6">
        <!-- Loading State -->
        <div id="loadingState" class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-12 text-center">
            <div class="w-8 h-8 border-3 border-sena-green border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-slate-600 dark:text-slate-400">Cargando información de la sede...</p>
        </div>

        <!-- Sede Details -->
        <div id="sedeDetails" class="space-y-6" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- SENA Regional Card -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="bg-gray-50 flex flex-col items-center text-center relative overflow-hidden h-64 justify-center border-b border-gray-100">
                            <div id="sedeFotoCard" class="absolute inset-0 hidden">
                                <img id="sedeFotoImg" src="" alt="Sede Foto" class="w-full h-full object-cover">
                            </div>
                            <div id="sedeDefaultInfo" class="flex flex-col items-center px-6">
                                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm relative z-10 border border-gray-100">
                                    <ion-icon src="../../assets/ionicons/business-outline.svg" class="text-5xl text-gray-200"></ion-icon>
                                </div>
                                <h3 class="text-gray-400 font-extrabold text-[10px] uppercase tracking-[0.2em] relative z-10">SEDE DE FORMACIÓN</h3>
                                <p class="text-sena-green text-xl font-black relative z-10 mt-1" id="sedeNombreHeader">SENA Regional</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Datos del Registro</h4>
                                <a href="#" id="editLink" class="text-sena-green hover:text-emerald-700 transition-colors flex items-center gap-1 text-sm font-medium">
                                    <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                                    Editar
                                </a>
                            </div>
                            <div class="space-y-4">
                                <div class="py-2 border-b border-slate-100 dark:border-slate-700">
                                    <p class="text-slate-500 dark:text-slate-400 text-xs uppercase mb-1">Nombre de la Sede</p>
                                    <p id="sedeNombreCard" class="text-slate-900 dark:text-white text-sm font-bold leading-tight">Cargando...</p>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-700">
                                    <span class="text-slate-500 dark:text-slate-400 text-sm">ID Sede</span>
                                    <span id="sedeId" class="font-mono text-slate-700 dark:text-slate-300 text-sm">-</span>
                                </div>
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-slate-500 dark:text-slate-400 text-sm">Registro de Sede</span>
                                    <span class="text-slate-900 dark:text-white text-sm">SENA Regional</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coordinator Info -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <ion-icon src="../../assets/ionicons/shield-checkmark-outline.svg" class="text-blue-600 dark:text-blue-400"></ion-icon>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Coordinador Encargado</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                    Esta sede está actualmente bajo la supervisión de la coordinación académica central.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
                            <div class="p-3 rounded-full bg-orange-100 text-sena-orange dark:bg-orange-900/30">
                                <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase">Programas</p>
                                <p id="totalProgramas" class="text-xl font-bold text-slate-900 dark:text-white">0</p>
                            </div>
                        </div>
                        <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30">
                                <ion-icon src="../../assets/ionicons/cube-outline.svg"></ion-icon>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase">Ambientes</p>
                                <p id="totalAmbientes" class="text-xl font-bold text-slate-900 dark:text-white">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Programs Card -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <ion-icon src="../../assets/ionicons/layers-outline.svg" class="text-slate-400"></ion-icon>
                                Programas Asociados
                            </h3>
                            <button id="verTodosProgramas" class="text-sm text-sena-green hover:text-green-700 font-medium transition-colors">Ver todos</button>
                        </div>
                        <div class="p-6">
                            <!-- Preview Mode -->
                            <div id="programasPreview">
                                <div id="programasList" class="space-y-4">
                                    <!-- Programs will be loaded here -->
                                </div>
                            </div>

                            <!-- Full List Mode -->
                            <div id="programasFullList" class="space-y-3" style="display: none;">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Total:</span>
                                        <span id="totalProgramasCount" class="text-sm font-bold text-sena-green">0</span>
                                        <span class="text-slate-400">|</span>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Filtrados:</span>
                                        <span id="filteredProgramasCount" class="text-sm font-bold text-sena-orange">0</span>
                                    </div>
                                    <button id="volverProgramasPreview" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                                        <ion-icon src="../../assets/ionicons/close-outline.svg" class="text-sm"></ion-icon>
                                    </button>
                                </div>

                                <!-- Filters -->
                                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Search by Name -->
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <ion-icon src="../../assets/ionicons/search-outline.svg" class="text-slate-400 text-sm"></ion-icon>
                                            </div>
                                            <input
                                                type="text"
                                                id="searchPrograma"
                                                placeholder="Buscar por nombre..."
                                                class="block w-full pl-10 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sena-green focus:border-sena-green transition duration-150 ease-in-out">
                                        </div>

                                        <!-- Filter by Nivel -->
                                        <div class="relative">
                                            <select
                                                id="filterNivelPrograma"
                                                class="block w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-sena-green focus:border-sena-green transition duration-150 ease-in-out">
                                                <option value="">Todos los niveles</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <ion-icon src="../../assets/ionicons/chevron-down-outline.svg" class="text-slate-400 text-sm"></ion-icon>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Active Filters Display -->
                                    <div id="activeProgramaFilters" class="mt-3 flex flex-wrap gap-2" style="display: none;">
                                        <span class="text-xs text-slate-500 dark:text-slate-400">Filtros activos:</span>
                                        <div id="activeProgramaFiltersList" class="flex flex-wrap gap-1">
                                        </div>
                                        <button id="clearProgramaFilters" class="text-xs text-sena-orange hover:text-orange-600 font-medium transition-colors">
                                            Limpiar filtros
                                        </button>
                                    </div>
                                </div>

                                <div id="programasListComplete" class="space-y-2 max-h-64 overflow-y-auto">
                                    <!-- Full programs list will be loaded here -->
                                </div>

                                <!-- No Results State -->
                                <div id="noProgramaFilterResults" class="text-center py-8" style="display: none;">
                                    <ion-icon src="../../assets/ionicons/filter-outline.svg" class="text-gray-400 text-3xl mb-2"></ion-icon>
                                    <p class="text-gray-500 text-sm">No se encontraron programas con los filtros aplicados</p>
                                    <button id="clearProgramaFiltersBtn" class="mt-2 text-sm text-sena-green hover:text-green-700 font-medium">
                                        Limpiar filtros
                                    </button>
                                </div>
                            </div>

                            <div id="noProgramas" class="text-center py-8" style="display: none;">
                                <ion-icon src="../../assets/ionicons/school-outline.svg" class="text-gray-400 text-4xl mb-2"></ion-icon>
                                <p class="text-gray-500">No hay programas asignados a esta sede</p>
                            </div>
                        </div>
                    </div>

                    <!-- Ambientes Card -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <ion-icon src="../../assets/ionicons/cube-outline.svg" class="text-slate-400"></ion-icon>
                                Ambientes de la Sede
                            </h3>
                            <button id="verTodosAmbientes" class="text-sm text-sena-green hover:text-green-700 font-medium transition-colors">Ver todos</button>
                        </div>
                        <div class="p-6">
                            <!-- Preview Mode -->
                            <div id="ambientesPreview">
                                <div id="ambientesList" class="space-y-4">
                                    <!-- Ambientes will be loaded here -->
                                </div>
                            </div>

                            <!-- Full List Mode -->
                            <div id="ambientesFullList" class="space-y-3" style="display: none;">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Total:</span>
                                        <span id="totalAmbientesCount" class="text-sm font-bold text-sena-green">0</span>
                                        <span class="text-slate-400">|</span>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Filtrados:</span>
                                        <span id="filteredAmbientesCount" class="text-sm font-bold text-sena-orange">0</span>
                                    </div>
                                    <button id="volverAmbientesPreview" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                                        <ion-icon src="../../assets/ionicons/close-outline.svg" class="text-sm"></ion-icon>
                                    </button>
                                </div>

                                <!-- Filters -->
                                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 mb-4">
                                    <!-- Search only for now -->
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <ion-icon src="../../assets/ionicons/search-outline.svg" class="text-slate-400 text-sm"></ion-icon>
                                        </div>
                                        <input
                                            type="text"
                                            id="searchAmbiente"
                                            placeholder="Buscar por nombre o número..."
                                            class="block w-full pl-14 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sena-green focus:border-sena-green transition duration-150 ease-in-out">
                                    </div>
                                </div>

                                <div id="ambientesListComplete" class="space-y-2 max-h-64 overflow-y-auto">
                                    <!-- Full ambientes list will be loaded here -->
                                </div>

                                <!-- No Results State -->
                                <div id="noAmbienteFilterResults" class="text-center py-8" style="display: none;">
                                    <ion-icon src="../../assets/ionicons/filter-outline.svg" class="text-gray-400 text-3xl mb-2"></ion-icon>
                                    <p class="text-gray-500 text-sm">No se encontraron ambientes con los filtros aplicados</p>
                                </div>
                            </div>

                            <div id="noAmbientes" class="text-center py-8" style="display: none;">
                                <ion-icon src="../../assets/ionicons/cube-outline.svg" class="text-gray-400 text-4xl mb-2"></ion-icon>
                                <p class="text-gray-500">No hay ambientes registrados en esta sede</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fichas Card -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <ion-icon src="../../assets/ionicons/layers-outline.svg" class="text-slate-400"></ion-icon>
                                Fichas de la Sede
                            </h3>
                            <button id="verTodosFichas" class="text-sm text-sena-green hover:text-green-700 font-medium transition-colors">Ver todos</button>
                        </div>
                        <div class="p-6">
                            <!-- Preview Mode -->
                            <div id="fichasPreview">
                                <div id="fichasList" class="space-y-4">
                                    <!-- Fichas will be loaded here -->
                                </div>
                            </div>

                            <!-- Full List Mode -->
                            <div id="fichasFullList" class="space-y-3" style="display: none;">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Total:</span>
                                        <span id="totalFichasCount" class="text-sm font-bold text-sena-green">0</span>
                                        <span class="text-slate-400">|</span>
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Filtrados:</span>
                                        <span id="filteredFichasCount" class="text-sm font-bold text-sena-orange">0</span>
                                    </div>
                                    <button id="volverFichasPreview" class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                                        <ion-icon src="../../assets/ionicons/close-outline.svg" class="text-sm"></ion-icon>
                                    </button>
                                </div>

                                <!-- Filters -->
                                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 mb-4">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <ion-icon src="../../assets/ionicons/search-outline.svg" class="text-slate-400 text-sm"></ion-icon>
                                        </div>
                                        <input
                                            type="text"
                                            id="searchFichaSede"
                                            placeholder="Buscar por número o programa..."
                                            class="block w-full pl-14 pr-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sena-green focus:border-sena-green transition duration-150 ease-in-out">
                                    </div>
                                </div>

                                <div id="fichasListComplete" class="space-y-2 max-h-64 overflow-y-auto">
                                    <!-- Full fichas list will be loaded here -->
                                </div>

                                <!-- No Results State -->
                                <div id="noFichaFilterResults" class="text-center py-8" style="display: none;">
                                    <ion-icon src="../../assets/ionicons/filter-outline.svg" class="text-gray-400 text-3xl mb-2"></ion-icon>
                                    <p class="text-gray-500 text-sm">No se encontraron fichas con los filtros aplicados</p>
                                </div>
                            </div>

                            <div id="noFichas" class="text-center py-8" style="display: none;">
                                <ion-icon src="../../assets/ionicons/layers-outline.svg" class="text-gray-400 text-4xl mb-2"></ion-icon>
                                <p class="text-gray-500">No hay fichas registradas en esta sede</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Card -->
        <div id="errorCard" class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-red-200 dark:border-red-700 p-12 text-center" style="display: none;">
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-red-600 dark:text-red-400"></ion-icon>
            </div>
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Error al Cargar</h3>
            <p id="errorMessage" class="text-slate-600 dark:text-slate-400 mb-6">No se pudo cargar la información de la sede.</p>
            <a href="index.php" class="inline-flex items-center gap-2 bg-sena-green hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Volver a Sedes
            </a>
        </div>

        <!-- Static Delete Button -->
        <div class="flex justify-end pt-4">
            <button id="deleteSedeBtn" class="flex items-center gap-2 bg-white dark:bg-slate-800 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 px-4 py-3 rounded-xl shadow-md border border-red-100 dark:border-red-900/30 transition-all duration-300 hover:translate-y-[-2px] active:translate-y-[0px] group">
                <ion-icon src="../../assets/ionicons/trash-outline.svg" class="text-xl group-hover:shake"></ion-icon>
                <span class="font-bold text-sm">Eliminar Sede</span>
            </button>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" id="modalOverlay"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-surface-light dark:bg-surface-dark w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden relative z-10 scale-95 opacity-0 transition-all duration-300" id="modalContent">
                    <div class="p-6">
                        <div class="w-14 h-14 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4 mx-auto">
                            <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-3xl text-red-600 dark:text-red-400"></ion-icon>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white text-center mb-2">¿Eliminar Sede?</h3>
                        <p class="text-slate-600 dark:text-slate-400 text-center text-sm mb-6">
                            Estás a punto de eliminar la sede <strong id="sedeToDeleteName" class="text-slate-900 dark:text-white"></strong>. Esta acción es irreversible y podría afectar a los programas vinculados.
                        </p>
                        <div class="flex gap-3">
                            <button id="cancelDeleteBtn" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                Cancelar
                            </button>
                            <button id="confirmDeleteBtn" class="flex-1 px-4 py-3 rounded-xl bg-red-600 text-white font-bold text-sm hover:bg-red-700 shadow-md shadow-red-200 dark:shadow-none transition-colors">
                                Sí, eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="text-center text-xs text-slate-400 dark:text-slate-600 mt-8 mb-4">
            © 2023 Servicio Nacional de Aprendizaje SENA. Todos los derechos reservados.
        </footer>
    </div>
</main>
<script src="../../assets/js/sede/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>