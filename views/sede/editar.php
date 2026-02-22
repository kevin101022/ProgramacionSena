<?php
$pageTitle = 'Editar Sede - SENA';
$activeNavItem = 'sedes';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <a href="index.php">Sedes</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Editar Sede</h1>
        </div>

        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Volver
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Loading State -->
        <div id="loadingState" class="loading-card">
            <div class="loading-spinner"></div>
            <p>Cargando información de la sede...</p>
        </div>

        <!-- Form Card -->
        <div id="formCard" class="form-card" style="display: none;">
            <div class="form-header">
                <div class="form-icon">
                    <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                </div>
                <div>
                    <h2>Modificar Información</h2>
                    <p>Actualice los datos de la sede seleccionada</p>
                </div>
            </div>

            <form id="sedeEditForm" class="form-content">
                <input type="hidden" id="sede_id" name="sede_id">

                <div class="form-group">
                    <label for="sede_nombre" class="form-label required">
                        Nombre de la Sede
                    </label>
                    <input
                        type="text"
                        id="sede_nombre"
                        name="sede_nombre"
                        class="form-input"
                        placeholder="Ej: Centro de Tecnologías Avanzadas"
                        required>
                    <div class="form-error" id="sede_nombre_error"></div>
                    <div class="form-help">
                        Modifique el nombre de la sede. Debe ser único en el sistema.
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto de la Sede</label>
                    <div class="flex items-center gap-6 mt-2 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                        <div id="imagePreviewContainer" class="w-32 h-32 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center bg-gradient-to-br from-sena-green to-emerald-700 text-white shadow-md">
                            <ion-icon id="placeholderIcon" src="../../assets/ionicons/business-outline.svg" class="text-5xl"></ion-icon>
                            <img id="imagePreview" src="" alt="Vista previa" class="w-full h-full object-cover hidden">
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Cambiar imagen del campus</p>
                            <p class="text-xs text-slate-500 mb-4">Formatos sugeridos: JPG, PNG o WEBP (Máx. 2MB)</p>
                            <label for="sede_foto" class="inline-flex items-center gap-2 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-sena-green border border-sena-green/50 px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer transition-all">
                                <ion-icon src="../../assets/ionicons/cloud-upload-outline.svg" class="text-lg"></ion-icon>
                                Seleccionar Imagen
                            </label>
                            <input type="file" id="sede_foto" name="sede_foto" class="hidden" accept="image/*">
                        </div>
                    </div>
                </div>

                <script>
                    document.getElementById('sede_foto').addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        const preview = document.getElementById('imagePreview');
                        const placeholder = document.getElementById('placeholderIcon');

                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(event) {
                                preview.src = event.target.result;
                                preview.classList.remove('hidden');
                                placeholder.classList.add('hidden');
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                </script>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <ion-icon src="../../assets/ionicons/close-circle-outline.svg"></ion-icon>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                        Actualizar Sede
                    </button>
                </div>
            </form>
        </div>

        <!-- Error Card -->
        <div id="errorCard" class="error-card" style="display: none;">
            <div class="error-icon">
                <ion-icon src="../../assets/ionicons/alert-circle-outline.svg"></ion-icon>
            </div>
            <div>
                <h3>Error al Cargar</h3>
                <p id="errorMessage">No se pudo cargar la información de la sede.</p>
                <a href="index.php" class="btn-primary mt-4">Volver a Sedes</a>
            </div>
        </div>

        <!-- Info Card -->
        <div id="infoCard" class="info-card" style="display: none;">
            <div class="info-header">
                <ion-icon src="../../assets/ionicons/information-circle-outline.svg"></ion-icon>
                <h3>Información de Edición</h3>
            </div>
            <div class="info-content">
                <ul>
                    <li>Los cambios se aplicarán inmediatamente al guardar</li>
                    <li>El nombre debe ser único en el sistema</li>
                    <li>Verifique que no existan programas asignados antes de cambios importantes</li>
                    <li>Puede cancelar en cualquier momento sin guardar cambios</li>
                </ul>
            </div>
        </div>
    </div>
</main>

<!-- Success Modal -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <div class="modal-header success">
            <ion-icon src="../../assets/ionicons/checkmark-circle-outline.svg"></ion-icon>
            <h3>Sede Actualizada</h3>
        </div>
        <div class="modal-body">
            <p>La sede <strong id="updatedSedeName"></strong> ha sido actualizada correctamente.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Ver Sedes</a>
            <button class="btn-secondary" onclick="closeSuccessModal()">Continuar Editando</button>
        </div>
    </div>
</div>

<script src="../../assets/js/sede/editar.js"></script>
</body>

</html>