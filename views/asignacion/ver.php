<?php
$pageTitle = 'Detalle de Asignación - SENA';
$activeNavItem = 'asignaciones';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="index.php">Asignaciones</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Detalle de Asignación</span>
            </nav>
            <h1 class="page-title">Detalle de Carga Académica</h1>
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
            <p class="text-gray-500">Cargando detalles de asignación...</p>
        </div>

        <div id="asignacionDetails" class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="display: none;">
            <!-- Info General Tarjeta -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    <div class="bg-gray-50 h-20 flex items-center justify-center border-b border-gray-100 relative">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-gray-200 text-5xl"></ion-icon>
                    </div>

                    <div class="p-6">
                        <div class="mb-6 pb-6 border-b border-gray-50">
                            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em] mb-1">ASIGNACIÓN N°</p>
                            <p class="text-2xl font-black text-blue-600 tracking-tight" id="detAsigId">---</p>
                        </div>
                        <div class="space-y-6">
                            <!-- Instructor -->
                            <div>
                                <h3 class="text-xs font-bold text-gray-400 uppercase mb-3">Instructor Responsable</h3>
                                <a href="#" id="instLink" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 font-bold" id="detInstInic">
                                        --
                                    </div>
                                    <div>
                                        <p id="detInstructor" class="text-sm font-bold text-gray-900">--</p>
                                        <p class="text-xs text-gray-500">Ver perfil completo</p>
                                    </div>
                                </a>
                            </div>

                            <!-- Ficha -->
                            <div>
                                <h3 class="text-xs font-bold text-gray-400 uppercase mb-3">Ficha de Formación</h3>
                                <a href="#" id="fichaLink" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                                    <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center text-sena-orange font-bold">
                                        <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                                    </div>
                                    <div>
                                        <p id="detFicha" class="text-sm font-bold text-gray-900">--</p>
                                        <p class="text-xs text-gray-500">Detalles de la cohorte</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-50 flex flex-col gap-3">
                            <button id="editBtn" class="btn-primary w-full justify-center">
                                <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                                Editar Asignación
                            </button>
                            <button id="deleteBtn" class="btn-secondary w-full justify-center text-red-600 border-red-100 hover:bg-red-50">
                                <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon>
                                Eliminar Registro
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles Técnicos -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Tarjeta Competencia y Ambiente -->
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <ion-icon src="../../assets/ionicons/information-circle-outline.svg" class="text-sena-green"></ion-icon>
                        Especificaciones de la Clase
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase mb-2">Ambiente de Formación</p>
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-gray-50 rounded-lg">
                                    <ion-icon src="../../assets/ionicons/cube-outline.svg" class="text-gray-400 text-xl"></ion-icon>
                                </div>
                                <div>
                                    <p id="detAmbiente" class="font-bold text-gray-800">--</p>
                                    <p class="text-xs text-gray-500 mt-1">Sede Principal</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase mb-2">Competencia Técnica</p>
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-gray-50 rounded-lg">
                                    <ion-icon src="../../assets/ionicons/construct-outline.svg" class="text-gray-400 text-xl"></ion-icon>
                                </div>
                                <div>
                                    <p id="detCompetencia" class="font-bold text-gray-800 leading-tight">--</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 p-6 bg-blue-50/50 rounded-2xl border border-blue-100/50">
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <p class="text-xs font-bold text-blue-400 uppercase mb-2">Periodo Programado</p>
                                <div class="flex items-center gap-6">
                                    <div>
                                        <span class="text-xs text-blue-500 block">Inicio</span>
                                        <span id="detFechaIni" class="font-bold text-blue-900 text-lg">--</span>
                                    </div>
                                    <ion-icon src="../../assets/ionicons/arrow-forward-outline.svg" class="text-blue-300"></ion-icon>
                                    <div>
                                        <span class="text-xs text-blue-500 block">Finalización</span>
                                        <span id="detFechaFin" class="font-bold text-blue-900 text-lg">--</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nueva sección de Franjas Horarias -->
                    <div class="mt-8">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Horarios Programados</h4>
                            <a href="#" id="manageHorariosBtn" class="text-xs font-bold text-sena-green hover:underline flex items-center gap-1">
                                <ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon>
                                Gestionar Horarios
                            </a>
                        </div>
                        <div id="franjasContainer" class="space-y-2">
                            <p class="text-xs text-gray-500 italic">No hay horarios específicos registrados para este periodo.</p>
                        </div>
                    </div>
                </div>

                <!-- Aviso de Disponibilidad -->
                <div class="bg-gray-50 p-6 rounded-xl border border-dashed border-gray-200 flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm">
                        <ion-icon src="../../assets/ionicons/notifications-outline.svg" class="text-sena-orange"></ion-icon>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Cruce de Horarios</p>
                        <p class="text-xs text-gray-500">Ningún conflicto detectado para este instructor en el rango de fechas seleccionado.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div id="errorState" class="bg-white rounded-xl shadow-sm p-12 text-center" style="display: none;">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-3xl"></ion-icon>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Asignación no encontrada</h3>
            <p id="errorMessage" class="text-gray-500 mb-6">El registro solicitado no existe o fue eliminado.</p>
            <a href="index.php" class="btn-primary inline-flex">Volver al listado</a>
        </div>
    </div>
</main>

<!-- Modal de Edición -->
<?php require_once 'modal_edit.php'; ?>

<script src="../../assets/js/asignacion/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>