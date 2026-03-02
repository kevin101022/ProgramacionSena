<?php
$pageTitle = "Editar Instructor - SENA";
$id = $_GET['id'] ?? null;
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
                <a href="ver.php?id=<?php echo htmlspecialchars($id); ?>">Detalle</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Editar Instructor</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="form-container">
            <form id="instructorForm" class="form-card">
                <input type="hidden" id="inst_id" name="inst_id" value="<?php echo htmlspecialchars($id); ?>">

                <div class="form-header">
                    <div class="form-icon">
                        <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Información del Instructor</h3>
                        <p class="text-sm text-gray-500">Actualice los datos del instructor seleccionado.</p>
                    </div>
                </div>

                <div class="form-body p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">Número de Documento <span class="text-red-500">*</span></label>
                            <input type="number" id="numero_documento" name="numero_documento" readonly class="search-input bg-gray-100 cursor-not-allowed" style="padding-left: 12px !important;" placeholder="Ej: 1001234567">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nombres <span class="text-red-500">*</span></label>
                            <input type="text" id="inst_nombres" name="inst_nombres" required class="search-input" style="padding-left: 12px !important;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Apellidos <span class="text-red-500">*</span></label>
                            <input type="text" id="inst_apellidos" name="inst_apellidos" required class="search-input" style="padding-left: 12px !important;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Correo Electrónico <span class="text-red-500">*</span></label>
                            <input type="email" id="inst_correo" name="inst_correo" required class="search-input" style="padding-left: 12px !important;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Teléfono</label>
                            <input type="number" id="inst_telefono" name="inst_telefono" class="search-input" style="padding-left: 12px !important;">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contraseña <span class="text-red-500">*</span></label>
                            <input type="text" id="inst_password" name="inst_password" required class="search-input" style="padding-left: 12px !important;" placeholder="Ej: Sena123*">
                        </div>

                        <div class="form-group md:col-span-2">
                            <hr class="my-4 border-gray-200">
                            <h4 class="text-md font-bold mb-2">Asignación de Competencias (Habilitación)</h4>
                            <p class="text-sm text-gray-500 mb-4">Seleccione las competencias que dictará este instructor.</p>
                        </div>

                        <div class="form-group md:col-span-2" id="programasChecklistContainer">
                            <label class="form-label">Buscador de Competencias</label>
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <div class="relative mb-3">
                                    <ion-icon src="../../assets/ionicons/search-outline.svg" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></ion-icon>
                                    <input type="text" id="compSearch" class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 outline-none" placeholder="Buscar por nombre...">
                                </div>
                                <div id="competenciasContainer" class="flex flex-col gap-2 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                                    <p id="emptyAviso" class="text-gray-400 text-sm italic text-center py-4">Cargando competencias disponibles...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-footer px-8 py-6 bg-gray-50 flex justify-end gap-4">
                    <a href="ver.php?id=<?php echo htmlspecialchars($id); ?>" class="btn-secondary">Cancelar</a>
                    <button type="submit" id="submitBtn" class="btn-primary">
                        <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                        Actualizar Instructor
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="../../assets/js/instructor/editar.js?v=<?php echo time(); ?>"></script>
</body>

</html>