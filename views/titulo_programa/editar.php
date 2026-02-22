<?php
$pageTitle = 'Editar Título - SENA';
$activeNavItem = 'titulos';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';

$id = $_GET['id'] ?? null;
?>

<!-- Main Content -->
<main class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <a href="index.php">Títulos</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Modificar Título</h1>
        </div>

        <div class="header-actions">
            <a href="ver.php?id=<?php echo htmlspecialchars($id); ?>" class="btn-secondary">
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
                    <ion-icon src="../../assets/ionicons/ribbon-outline.svg"></ion-icon>
                </div>
                <div>
                    <h2>Editar Información</h2>
                    <p>Actualice los datos del título académico seleccionado</p>
                </div>
            </div>

            <form id="editarTituloForm" class="form-content">
                <input type="hidden" name="titpro_id" value="<?php echo htmlspecialchars($id); ?>">

                <div class="form-group">
                    <label for="titpro_nombre" class="form-label required">
                        Nombre del Título
                    </label>
                    <input
                        type="text"
                        id="titpro_nombre"
                        name="titpro_nombre"
                        class="form-input"
                        required>
                    <div class="form-help">
                        Asegúrese de que el nombre sea correcto antes de guardar los cambios.
                    </div>
                </div>

                <div class="form-actions">
                    <a href="ver.php?id=<?php echo htmlspecialchars($id); ?>" class="btn-secondary">
                        <ion-icon src="../../assets/ionicons/close-circle-outline.svg"></ion-icon>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                        Actualizar Título
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="../../assets/js/titulo_programa/editar.js?v=<?php echo time(); ?>"></script>
</body>

</html>