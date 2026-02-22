<?php
$pageTitle = 'Detalle de Competencia - SENA';
$activeNavItem = 'competencias';
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
                        <a href="index.php" class="hover:text-sena-green dark:hover:text-sena-green transition-colors">Competencias</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-base mx-1"></ion-icon>
                            <span id="breadcrumbCompetencia" class="text-slate-800 dark:text-white font-medium">Detalle de Competencia</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <ion-icon src="../../assets/ionicons/information-circle-outline.svg" class="text-2xl text-sena-orange"></ion-icon>
                Información de la Competencia
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
            <p class="text-slate-600 dark:text-slate-400">Cargando información de la competencia...</p>
        </div>

        <!-- Competencia Details -->
        <div id="competenciaDetails" class="space-y-6" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Competencia Card -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="bg-gray-50 dark:bg-slate-800 h-24 flex items-center justify-center border-b border-gray-100 dark:border-slate-700">
                            <ion-icon src="../../assets/ionicons/bookmarks-outline.svg" class="text-gray-200 dark:text-slate-600 text-6xl"></ion-icon>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">DATOS DE LA COMPETENCIA</h4>
                                <a href="#" id="editLink" class="text-sena-green hover:text-emerald-700 transition-colors flex items-center gap-1 text-sm font-medium">
                                    <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                                    Editar
                                </a>
                            </div>
                            <div class="space-y-4">
                                <div class="py-2 border-b border-slate-100 dark:border-slate-700">
                                    <p class="text-slate-500 dark:text-slate-400 text-xs uppercase mb-1">Nombre Corto</p>
                                    <p id="compNombreDisplay" class="text-slate-900 dark:text-white text-sm font-bold leading-tight">---</p>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-700">
                                    <span class="text-slate-500 dark:text-slate-400 text-sm">ID Competencia</span>
                                    <span id="compIdDisplay" class="font-mono text-slate-700 dark:text-slate-300 text-sm">---</span>
                                </div>
                                <div class="py-2 border-b border-slate-100 dark:border-slate-700">
                                    <p class="text-slate-500 dark:text-slate-400 text-xs uppercase mb-1">Total Horas</p>
                                    <span class="inline-flex px-3 py-1 rounded-full bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 font-bold text-sm" id="compHorasDisplay">---h</span>
                                </div>
                                <div class="py-2">
                                    <p class="text-slate-500 dark:text-slate-400 text-xs uppercase mb-1">Unidad de Competencia</p>
                                    <p id="compUnidadDisplay" class="text-slate-900 dark:text-white text-sm leading-relaxed">Sin descripción disponible.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Programas Asociados -->
                    <div class="bg-surface-light dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <ion-icon src="../../assets/ionicons/layers-outline.svg" class="text-slate-400"></ion-icon>
                                    Programas de Formación
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Programas que incluyen esta competencia</p>
                            </div>
                            <span class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-1.5 rounded-lg text-sm font-bold text-slate-700 dark:text-slate-300" id="programCount">0 asociados</span>
                        </div>
                        <div id="associatedProgramasList" class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[500px] overflow-y-auto">
                            <div class="p-8 text-center text-slate-400 dark:text-slate-500 italic">Cargando programas asociados...</div>
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
            <p id="errorMessage" class="text-slate-600 dark:text-slate-400 mb-6">No se pudo cargar la información de la competencia.</p>
            <a href="index.php" class="inline-flex items-center gap-2 bg-sena-green hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Volver a Competencias
            </a>
        </div>

        <!-- Static Delete Button -->
        <div id="deleteButtonWrapper" class="flex justify-end pt-4" style="display: none;">
            <button id="deleteBtn" class="flex items-center gap-2 bg-white dark:bg-slate-800 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 px-4 py-3 rounded-xl shadow-md border border-red-100 dark:border-red-900/30 transition-all duration-300 hover:translate-y-[-2px] active:translate-y-[0px] group">
                <ion-icon src="../../assets/ionicons/trash-outline.svg" class="text-xl"></ion-icon>
                <span class="font-bold text-sm">Eliminar Competencia</span>
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
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white text-center mb-2">¿Eliminar Competencia?</h3>
                        <p class="text-slate-600 dark:text-slate-400 text-center text-sm mb-6">
                            Estás a punto de eliminar la competencia <strong id="competenciaToDeleteName" class="text-slate-900 dark:text-white"></strong>. Esta acción es irreversible y se eliminarán las asociaciones con programas.
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

<!-- Success Overlay -->
<div id="successOverlay" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-2xl text-center max-w-sm mx-4">
        <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-6 mx-auto">
            <ion-icon src="../../assets/ionicons/checkmark-circle-outline.svg" class="text-5xl text-sena-green"></ion-icon>
        </div>
        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">¡Eliminado!</h3>
        <p class="text-slate-600 dark:text-slate-400">La competencia ha sido eliminada exitosamente.</p>
    </div>
</div>

<script src="../../assets/js/competencia/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>
