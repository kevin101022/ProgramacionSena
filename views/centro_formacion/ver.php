<?php
$pageTitle = 'Detalle del Centro - SENA';
$activeNavItem = 'centros';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="index.php">Centros</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Detalle del Centro</span>
            </nav>
            <h1 class="page-title">Centro de Formación</h1>
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
            <p class="text-gray-500">Cargando información del centro...</p>
        </div>

        <div id="centroDetails" class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="display: none;">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    <div class="bg-gray-50 h-24 flex items-center justify-center border-b border-gray-50">
                        <ion-icon src="../../assets/ionicons/business-outline.svg" class="text-gray-200 text-6xl"></ion-icon>
                    </div>
                    <div class="p-6">
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em] mb-1 text-center">CENTRO DE FORMACIÓN</p>
                        <h2 class="text-xl font-black text-sena-green mb-2 text-center" id="detCentroNombre">--</h2>
                        <p class="text-xs text-center text-gray-500 mb-6 font-medium">Sede Administrativa / Formativa</p>

                        <div class="space-y-4 pt-4 border-t border-gray-50 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">ID del Sistema:</span>
                                <span class="font-mono text-sena-green font-bold" id="detCentroId">--</span>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col gap-3">
                            <button id="editBtn" class="btn-primary w-full justify-center">
                                <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                                Editar Centro
                            </button>
                            <button id="deleteBtn" class="btn-secondary w-full justify-center text-red-600 border-red-100 hover:bg-red-50">
                                <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon>
                                Eliminar Centro
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <!-- Instructores Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <ion-icon src="../../assets/ionicons/people-outline.svg" class="text-sena-green text-lg"></ion-icon>
                            Instructores Vinculados
                        </h3>
                        <span id="countInstructores" class="px-2.5 py-0.5 bg-sena-green/10 text-sena-green text-xs font-bold rounded-full">0</span>
                    </div>
                    <div class="p-6">
                        <div id="instructoresList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Data loaded via JS -->
                        </div>
                        <div id="noInstructores" class="hidden text-center py-8">
                            <p class="text-gray-400 text-sm italic">No hay instructores vinculados a este centro.</p>
                        </div>
                    </div>
                </div>

                <!-- Coordinaciones Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <ion-icon src="../../assets/ionicons/git-network-outline.svg" class="text-sena-green text-lg"></ion-icon>
                            Coordinaciones
                        </h3>
                        <span id="countCoordinaciones" class="px-2.5 py-0.5 bg-sena-green/10 text-sena-green text-xs font-bold rounded-full">0</span>
                    </div>
                    <div class="p-6">
                        <div id="coordinacionesList" class="space-y-3">
                            <!-- Data loaded via JS -->
                        </div>
                        <div id="noCoordinaciones" class="hidden text-center py-8">
                            <p class="text-gray-400 text-sm italic">No hay coordinaciones registradas en este centro.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="errorState" class="bg-white rounded-xl shadow-sm p-12 text-center" style="display: none;">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-3xl"></ion-icon>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Error de Carga</h3>
            <p id="errorMessage" class="text-gray-500">No se pudo encontrar el centro de formación.</p>
        </div>
    </div>
</main>

<?php require_once 'modal_edit.php'; ?>
<script src="../../assets/js/centro_formacion/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>