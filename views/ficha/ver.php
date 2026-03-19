<?php
$pageTitle = 'Detalle de Ficha - SENA';
$activeNavItem = 'fichas';
require_once '../layouts/head.php';
if ($_SESSION['rol'] === 'instructor') {
    require_once '../layouts/instructor_sidebar.php';
} else {
    require_once '../layouts/sidebar.php';
}
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <?php if ($_SESSION['rol'] === 'instructor'): ?>
                    <a href="../instructor/mi_ficha.php">Mis Fichas</a>
                <?php else: ?>
                    <a href="index.php">Fichas</a>
                <?php endif; ?>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Detalle de Ficha</span>
            </nav>
            <h1 class="page-title">Información de la Ficha</h1>
        </div>
        <div class="header-actions">
            <a href="<?php echo ($_SESSION['rol'] === 'instructor') ? '../instructor/mi_ficha.php' : 'index.php'; ?>" class="btn-secondary">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Regresar
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <div id="loadingState" class="bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="w-8 h-8 border-3 border-sena-green border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-500">Cargando detalles de la ficha...</p>
        </div>

        <div id="fichaDetails" class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="display: none;">
            <!-- Info Principal -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    <div class="bg-gray-50 h-20 flex items-center justify-center relative border-b border-gray-100">
                        <ion-icon src="../../assets/ionicons/school-outline.svg" class="text-gray-200 text-5xl"></ion-icon>
                    </div>

                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Programa de Formación</h3>
                            <p class="text-lg font-bold text-gray-900 leading-tight" id="detPrograma">Cargando...</p>
                            <p class="text-sm text-sena-green font-medium mt-1" id="detJornada">--</p>
                        </div>

                        <div class="space-y-4 pt-6 border-t border-gray-50">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase mb-1">Número de Ficha</p>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <ion-icon src="../../assets/ionicons/layers-outline.svg" class="text-gray-400"></ion-icon>
                                    <span id="detFichaId" class="font-semibold text-gray-800">--</span>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase mb-1">Instructor Líder</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center text-sena-green font-bold" id="detInstInic">
                                        --
                                    </div>
                                    <span id="detInstructor" class="text-sm font-semibold text-gray-700">--</span>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase mb-1">Coordinación</p>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <ion-icon src="../../assets/ionicons/business-outline.svg" class="text-gray-400"></ion-icon>
                                    <span id="detCoordinacion">--</span>
                                </div>
                            </div>
                        </div>

                        <?php if ($_SESSION['rol'] !== 'instructor'): ?>
                        <div class="mt-8 pt-6 border-t border-gray-50 flex flex-col gap-3">
                            <button id="editBtn" class="btn-primary w-full justify-center">
                                <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                                Editar Ficha
                            </button>
                            <button id="deleteBtn" class="btn-secondary w-full justify-center text-red-600 border-red-100 hover:bg-red-50">
                                <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon>
                                Eliminar Ficha
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Datos Complementarios -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 min-h-[400px]">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-sena-green"></ion-icon>
                            Horario y Planeación
                        </h3>
                    </div>

                    <!-- Competencias Vistas -->
                    <div class="mb-8">
                        <h4 class="text-sm font-bold text-sena-green mb-3 flex items-center gap-2">
                            <ion-icon src="../../assets/ionicons/checkmark-circle-outline.svg"></ion-icon>
                            Competencias Vistas (Con Instructor)
                        </h4>
                        <div id="compVistasList" class="space-y-3">
                            <p class="text-sm text-gray-400 italic">Cargando competencias vistas...</p>
                        </div>
                        <div id="noCompVistas" class="p-6 bg-green-50/50 rounded-xl border border-dashed border-green-200 text-center" style="display: none;">
                            <ion-icon src="../../assets/ionicons/leaf-outline.svg" class="text-4xl text-green-300 mb-2"></ion-icon>
                            <p class="text-sm text-green-600 font-medium">Aún no se ha impartido ninguna competencia a esta ficha.</p>
                        </div>
                    </div>

                    <!-- Competencias Faltantes -->
                    <div>
                        <h4 class="text-sm font-bold text-amber-500 mb-3 flex items-center gap-2">
                            <ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon>
                            Competencias Faltantes por Ver
                        </h4>
                        <div id="compFaltantesList" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <p class="text-sm text-gray-400 italic col-span-full">Cargando competencias faltantes...</p>
                        </div>
                        <div id="noCompFaltantes" class="p-6 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-center" style="display: none;">
                            <ion-icon src="../../assets/ionicons/trophy-outline.svg" class="text-4xl text-slate-300 mb-2"></ion-icon>
                            <p class="text-sm text-slate-500 font-medium">¡Excelente! Esta ficha ya tiene cubiertas todas las competencias del programa.</p>
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
            <h3 class="text-lg font-bold text-gray-900 mb-2">Error de Carga</h3>
            <p id="errorMessage" class="text-gray-500 mb-6">No se pudo cargar la información de la ficha.</p>
            <a href="<?php echo ($_SESSION['rol'] === 'instructor') ? '../instructor/mi_ficha.php' : 'index.php'; ?>" class="btn-primary inline-flex">Volver a Fichas</a>
        </div>
    </div>
</main>

<!-- Modal de Edición (Reutilizamos el de index si es necesario o simplificamos) -->
<?php if ($_SESSION['rol'] !== 'instructor') require_once 'modal_edit.php'; ?>

<script>
    window.isInstructor = <?php echo ($_SESSION['rol'] === 'instructor') ? 'true' : 'false'; ?>;
</script>
<script src="../../assets/js/ficha/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>