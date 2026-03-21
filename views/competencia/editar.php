<?php
$pageTitle = 'Editar Competencia - Programaciones';
$activeNavItem = 'competencias';
require_once '../layouts/head.php';

// Solo el Centro de Formación puede editar competencias
if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'centro') {
    header("Location: index.php");
    exit;
}

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
                <span>Editar Competencia</span>
            </div>
            <h1 class="page-title">Editar Competencia</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Volver
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <form id="editarCompetenciaForm" class="form-card">
            <input type="hidden" id="comp_id" name="comp_id">

            <div class="form-header">
                <div class="form-icon bg-sena-green text-white">
                    <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Actualizar Competencia</h2>
                    <p class="text-sm text-gray-500">Modifica los detalles de la competencia académica</p>
                </div>
            </div>

            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="comp_nombre_corto" class="block text-sm font-semibold text-gray-700 mb-2">Nombre Corto *</label>
                        <input type="text" id="comp_nombre_corto" name="comp_nombre_corto" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none" required>
                    </div>

                    <div class="form-group">
                        <label for="comp_horas" class="block text-sm font-semibold text-gray-700 mb-2">Total Horas *</label>
                        <input type="number" id="comp_horas" name="comp_horas" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="comp_nombre_unidad_competencia" class="block text-sm font-semibold text-gray-700 mb-2">Unidad de Competencia</label>
                    <textarea id="comp_nombre_unidad_competencia" name="comp_nombre_unidad_competencia" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none"></textarea>
                </div>

                <div class="form-group">
                    <label for="programa_prog_id" class="block text-sm font-semibold text-gray-700 mb-2">Programa Asociado *</label>
                    <select id="programa_prog_id" name="programa_prog_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none" required>
                        <option value="">Selecciona un programa...</option>
                        <!-- Programas cargados vía JS -->
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="requisitos_academicos" class="block text-sm font-semibold text-gray-700 mb-2">Requisitos Académicos</label>
                        <textarea id="requisitos_academicos" name="requisitos_academicos" rows="4" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none" placeholder="Ej: Título profesional en..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="experiencia_laboral" class="block text-sm font-semibold text-gray-700 mb-2">Experiencia Laboral Requerida</label>
                        <textarea id="experiencia_laboral" name="experiencia_laboral" rows="4" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none" placeholder="Ej: 24 meses de experiencia en..."></textarea>
                    </div>
                </div>
            </div>

            <div class="form-footer bg-gray-50 p-6 flex justify-end gap-4">
                <a href="index.php" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                    Actualizar Cambios
                </button>
            </div>
        </form>
    </div>
</main>

<script src="../../assets/js/competencia/editar.js?v=<?php echo time(); ?>"></script>
</body>

</html>