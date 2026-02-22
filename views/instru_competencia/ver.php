<?php
$pageTitle = 'Detalle de Habilitación - SENA';
$activeNavItem = 'habilitaciones';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="index.php">Habilitaciones</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Detalle de Habilitación</span>
            </nav>
            <h1 class="page-title">Información de Habilitación</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Regresar
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <div id="loadingState" class="bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="w-8 h-8 border-3 border-sena-green border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-500">Cargando habilitación...</p>
        </div>

        <div id="habilitacionDetails" class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="display: none;">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    <div class="bg-gray-50 h-24 flex items-center justify-center border-b border-gray-50">
                        <ion-icon src="../../assets/ionicons/shield-checkmark-outline.svg" class="text-gray-200 text-6xl"></ion-icon>
                    </div>
                    <div class="p-6 text-center">
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em] mb-1">HABILITACIÓN</p>
                        <h2 class="text-xl font-black text-sena-green mb-1" id="detInstructor">--</h2>
                        <p class="text-xs text-gray-500 mb-6 font-medium" id="detPrograma">Programa</p>

                        <div class="mt-8 flex flex-col gap-3">
                            <button id="deleteBtn" class="btn-secondary w-full justify-center text-red-600 border-red-100 hover:bg-red-50">
                                <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon>
                                Eliminar Habilitación
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 h-full">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <ion-icon src="../../assets/ionicons/list-outline.svg" class="text-sena-green"></ion-icon>
                        Detalles de la Habilitación
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase">Instructor</p>
                            <p class="text-base font-bold text-gray-900 mt-1" id="detInstructorFull">--</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase">Programa</p>
                            <p class="text-base font-bold text-gray-900 mt-1" id="detProgramaFull">--</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase">Competencia</p>
                            <p class="text-base font-bold text-gray-900 mt-1" id="detCompetencia">--</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase">Vigencia</p>
                            <p class="text-base font-bold text-gray-900 mt-1" id="detVigencia">--</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="errorState" class="bg-white rounded-xl shadow-sm p-12 text-center" style="display: none;">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-3xl"></ion-icon>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">¡Oh no!</h3>
            <p id="errorMessage" class="text-gray-500">No encontramos la habilitación que buscas.</p>
        </div>
    </div>
</main>

<script src="../../assets/js/instru_competencia/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>