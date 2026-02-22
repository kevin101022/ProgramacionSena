<?php
$pageTitle = 'Crear Sede - SENA';
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
                <span>Crear</span>
            </nav>
            <h1 class="page-title">Registrar Nueva Sede</h1>
        </div>

        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Volver
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Form Card -->
        <div class="form-card">
            <div class="form-header">
                <div class="form-icon">
                    <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                </div>
                <div>
                    <h2>Información de la Sede</h2>
                    <p>Complete los datos para registrar una nueva sede</p>
                </div>
            </div>

            <form id="sedeForm" class="form-content">


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
                        Ingrese el nombre completo de la sede. Debe ser único en el sistema.
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
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Subir una imagen del campus</p>
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
                        Guardar Sede
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="info-card">
            <div class="info-header">
                <ion-icon src="../../assets/ionicons/information-circle-outline.svg"></ion-icon>
                <h3>Información Importante</h3>
            </div>
            <div class="info-content">
                <ul>
                    <li>El nombre de la sede debe ser único en el sistema</li>
                    <li>Una vez creada, la sede estará disponible para asignar programas</li>
                    <li>Puede editar la información posteriormente si es necesario</li>
                    <li>Las sedes inactivas no aparecerán en las listas de selección</li>
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
            <h3>Sede Creada Exitosamente</h3>
        </div>
        <div class="modal-body">
            <p>La sede <strong id="createdSedeName"></strong> ha sido registrada correctamente.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Ver Sedes</a>
            <button class="btn-secondary" onclick="closeSuccessModal()">Crear Otra</button>
        </div>
    </div>
</div>

<script src="../../assets/js/sede/crear.js"></script>
</body>

</html>