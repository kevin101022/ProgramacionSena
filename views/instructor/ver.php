<?php
$pageTitle = 'Detalle de Instructor - SENA';
$activeNavItem = 'instructores';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="index.php">Instructores</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Detalle de Instructor</span>
            </nav>
            <h1 class="page-title">Información del Instructor</h1>
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
            <p class="text-gray-500">Cargando información...</p>
        </div>

        <div id="instructorDetails" class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="display: none;">
            <!-- Perfil Card -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    <div class="bg-gray-50 h-24 flex items-center justify-center border-b border-gray-50 relative">
                        <ion-icon src="../../assets/ionicons/people-outline.svg" class="text-gray-200 text-6xl"></ion-icon>
                    </div>
                    <div class="px-6 pb-6 -mt-12 relative">
                        <div class="w-24 h-24 bg-white rounded-2xl shadow-lg flex items-center justify-center text-3xl font-bold text-sena-green mb-4 mx-auto border-4 border-white" id="instIniciales">
                            --
                        </div>
                        <div class="text-center mb-6">
                            <h2 class="text-xl font-bold text-gray-900" id="instNombreCompleto">Cargando...</h2>
                            <p class="text-sm text-gray-500">Instructor de Formación Profesional</p>
                        </div>

                        <div class="space-y-4 border-t border-gray-50 pt-6">
                            <div class="flex items-center gap-3 text-sm">
                                <ion-icon src="../../assets/ionicons/mail-outline.svg" class="text-sena-green text-lg"></ion-icon>
                                <span id="instCorreo" class="text-gray-600">--</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <ion-icon src="../../assets/ionicons/call-outline.svg" class="text-sena-green text-lg"></ion-icon>
                                <span id="instTelefono" class="text-gray-600">--</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <ion-icon src="../../assets/ionicons/business-outline.svg" class="text-sena-green text-lg"></ion-icon>
                                <span id="instCentro" class="text-gray-600 font-semibold">--</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <ion-icon src="../../assets/ionicons/book-outline.svg" class="text-sena-green text-lg"></ion-icon>
                                <span id="instEspecialidad" class="text-gray-600">--</span>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col gap-3">
                            <a href="#" id="editBtn" class="btn-primary w-full justify-center">
                                <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                                Editar Datos
                            </a>
                            <button id="deleteBtn" class="btn-secondary w-full justify-center text-red-600 hover:bg-red-50 border-red-100">
                                <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon>
                                Eliminar Instructor
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Estadísticas Rápidas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="p-3 bg-green-50 rounded-xl text-sena-green">
                            <ion-icon src="../../assets/ionicons/layers-outline.svg" class="text-2xl"></ion-icon>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase">Fichas Líder</p>
                            <p class="text-2xl font-black text-gray-900" id="countFichas">0</p>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="p-3 bg-orange-50 rounded-xl text-sena-orange">
                            <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-2xl"></ion-icon>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase">Asignaciones</p>
                            <p class="text-2xl font-black text-gray-900" id="countAsignaciones">0</p>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="p-3 bg-blue-50 rounded-xl text-blue-500">
                            <ion-icon src="../../assets/ionicons/ribbon-outline.svg" class="text-2xl"></ion-icon>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase">Competencias</p>
                            <p class="text-2xl font-black text-gray-900" id="countCompetencias">0</p>
                        </div>
                    </div>
                </div>

                <!-- Fichas Líder -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <ion-icon src="../../assets/ionicons/list-outline.svg" class="text-sena-green"></ion-icon>
                            Fichas Líder
                        </h3>
                    </div>
                    <div class="p-5">
                        <div id="fichasList" class="space-y-3">
                            <p class="text-sm text-gray-500 text-center py-4 italic">Cargando fichas vinculadas...</p>
                        </div>
                    </div>
                </div>

                <!-- Asignaciones del Instructor -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-sena-orange"></ion-icon>
                            Asignaciones
                        </h3>
                    </div>
                    <div class="p-5">
                        <div id="asignacionesList" class="space-y-3">
                            <p class="text-sm text-gray-500 text-center py-4 italic">Cargando asignaciones...</p>
                        </div>
                    </div>
                </div>

                <!-- Competencias Habilitadas -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <ion-icon src="../../assets/ionicons/ribbon-outline.svg" class="text-blue-500"></ion-icon>
                            Competencias Habilitadas
                        </h3>
                    </div>
                    <div class="p-5">
                        <div id="competenciasList" class="space-y-3">
                            <p class="text-sm text-gray-500 text-center py-4 italic">Cargando competencias...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div id="errorState" class="bg-white rounded-xl shadow-sm p-12 text-center" style="display: none;">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-3xl"></ion-icon>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">¡Ups! Algo salió mal</h3>
            <p id="errorMessage" class="text-gray-500 mb-6">No pudimos encontrar al instructor solicitado.</p>
            <a href="index.php" class="btn-primary inline-flex">Volver al inicio</a>
        </div>
    </div>
</main>

<script src="../../assets/js/instructor/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>