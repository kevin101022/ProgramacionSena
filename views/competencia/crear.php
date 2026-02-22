<?php
$pageTitle = 'Nueva Competencia - Gestión de Transversales';
$activeNavItem = 'competencias';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <div class="breadcrumb">
                <a href="../dashboard/index.php">Principal</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <a href="index.php">Competencias</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Nueva Competencia</span>
            </div>
            <h1 class="page-title">Nueva Competencia</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <form id="crearCompetenciaForm" class="form-card">
            <div class="form-header">
                <div class="form-icon bg-sena-green text-white">
                    <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Información de la Competencia</h2>
                    <p class="text-sm text-gray-500">Completa los campos para registrar una nueva competencia académica</p>
                </div>
            </div>

            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="comp_nombre_corto" class="block text-sm font-semibold text-gray-700 mb-2">Nombre Corto *</label>
                        <input type="text" id="comp_nombre_corto" name="comp_nombre_corto" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none" placeholder="Ej: Comunicación" required>
                    </div>

                    <div class="form-group">
                        <label for="comp_horas" class="block text-sm font-semibold text-gray-700 mb-2">Total Horas *</label>
                        <input type="number" id="comp_horas" name="comp_horas" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none" placeholder="Ej: 48" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="comp_nombre_unidad_competencia" class="block text-sm font-semibold text-gray-700 mb-2">Unidad de Competencia</label>
                    <textarea id="comp_nombre_unidad_competencia" name="comp_nombre_unidad_competencia" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none" placeholder="Descripción detallada de la unidad de competencia..."></textarea>
                </div>

                <div class="form-group mt-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">Asociar con Programas</label>
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="relative flex-1">
                                <ion-icon src="../../assets/ionicons/search-outline.svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></ion-icon>
                                <input type="text" id="progSearch" class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 outline-none" placeholder="Filtrar programas...">
                            </div>
                        </div>
                        <div id="programasList" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-height-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            <!-- Programas cargados vía JS -->
                            <div class="animate-pulse text-gray-400 text-sm">Cargando programas...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-footer bg-gray-50 p-6 flex justify-end gap-4">
                <a href="index.php" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                    Guardar Competencia
                </button>
            </div>
        </form>
    </div>
</main>

<script src="../../assets/js/competencia/crear.js?v=<?php echo time(); ?>"></script>
</body>

</html>