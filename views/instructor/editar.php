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

                        <div class="form-group">
                            <label class="form-label">Centro de Formación <span class="text-red-500">*</span></label>
                            <select id="centro_id" name="centro_formacion_cent_id" required class="search-input" style="padding-left: 12px !important;">
                                <option value="">Seleccione un centro...</option>
                            </select>
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

<script src="../../assets/js/instructor/editar.js"></script>
</body>

</html>