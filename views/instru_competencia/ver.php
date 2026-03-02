<?php
$pageTitle = 'Detalle de Habilitación - SENA';
$activeNavItem = 'instruc_comp';
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
                        <h2 class="text-xl font-black text-sena-green mb-1" id="detInstructor">--</h2>


                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'centro'): ?>
                            <div class="mt-8 flex flex-col gap-3">
                                <button id="deleteBtn" class="btn-secondary w-full justify-center text-red-600 border-red-100 hover:bg-red-50">
                                    <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon>
                                    Eliminar Habilitación
                                </button>
                            </div>
                        <?php endif; ?>
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
                            <p class="text-xs font-bold text-gray-400 uppercase">Documento</p>
                            <p class="text-base font-bold text-gray-900 mt-1" id="detDocumento">--</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase">Instructor</p>
                            <p class="text-base font-bold text-gray-900 mt-1" id="detInstructorFull">--</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase">Correo Electrónico</p>
                            <p class="text-base font-bold text-gray-900 mt-1" id="detCorreo">--</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase">Teléfono</p>
                            <p class="text-base font-bold text-gray-900 mt-1" id="detTelefono">--</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 md:col-span-1">
                            <p class="text-xs font-bold text-gray-400 uppercase">Centro de Formación</p>
                            <p class="text-base font-bold text-gray-900 mt-1" id="detCentro">--</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 md:col-span-1">
                            <p class="text-xs font-bold text-gray-400 uppercase">Competencia</p>
                            <p class="text-base font-bold text-gray-900 mt-1" id="detCompetencia">--</p>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Nueva sección de Asignaciones (Ancho completo abajo) -->
            <div class="lg:col-span-3">
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-sena-green text-xl"></ion-icon>
                        Asignaciones Actuales del Instructor
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider">Programa / Ficha</th>
                                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider">Competencia</th>
                                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider">Ambiente / Sede</th>
                                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider">Fechas</th>
                                </tr>
                            </thead>
                            <tbody id="assignmentsTableBody">
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <div class="w-8 h-8 border-4 border-sena-green border-t-transparent rounded-full animate-spin"></div>
                                            <p class="text-sm font-medium">Cargando horario...</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Nueva sección de Fichas Lideradas -->
            <div class="lg:col-span-3 mt-6" id="fichasLiderSection" style="display: none;">
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <ion-icon src="../../assets/ionicons/ribbon-outline.svg" class="text-sena-green text-xl"></ion-icon>
                        Fichas bajo su Liderazgo
                    </h3>

                    <div id="fichasLiderContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2 text-center py-8 text-gray-400 italic">
                            Cargando información de liderazgo...
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