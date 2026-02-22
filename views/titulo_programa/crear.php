<?php
$pageTitle = 'Registrar Título - SENA';
$activeNavItem = 'titulos';
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
                <a href="index.php">Títulos</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Crear</span>
            </nav>
            <h1 class="page-title">Registrar Nuevo Título</h1>
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
                    <ion-icon src="../../assets/ionicons/ribbon-outline.svg"></ion-icon>
                </div>
                <div>
                    <h2>Información del Título</h2>
                    <p>Defina el nombre oficial del título académico</p>
                </div>
            </div>

            <form id="crearTituloForm" class="form-content">
                <div class="form-group">
                    <label for="titpro_nombre" class="form-label required">
                        Nombre del Título
                    </label>
                    <input
                        type="text"
                        id="titpro_nombre"
                        name="titpro_nombre"
                        class="form-input"
                        placeholder="Ej: Tecnólogo en Análisis y Desarrollo de Software"
                        required>
                    <div class="form-help">
                        Ingrese la denominación completa tal como aparece en el registro calificado.
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <ion-icon src="../../assets/ionicons/close-circle-outline.svg"></ion-icon>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                        Guardar Título
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="info-card">
            <div class="info-header">
                <ion-icon src="../../assets/ionicons/information-circle-outline.svg"></ion-icon>
                <h3>Recomendaciones</h3>
            </div>
            <div class="info-content">
                <ul>
                    <li>Use mayúsculas iniciales para nombres propios</li>
                    <li>Evite abreviaturas innecesarias</li>
                    <li>Este título podrá ser asociado a múltiples programas</li>
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
            <h3>Título Registrado</h3>
        </div>
        <div class="modal-body">
            <p>El título académico ha sido registrado correctamente.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Ver Todos</a>
            <button class="btn-secondary" onclick="closeSuccessModal()">Registrar Otro</button>
        </div>
    </div>
</div>

<script src="../../assets/js/titulo_programa/crear.js?v=<?php echo time(); ?>"></script>
</body>

</html>