<?php
$pageTitle = 'Editar Ambiente - SENA';
$activeNavItem = 'ambientes';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../dashboard/index.php">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <a href="index.php">Ambientes</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Editar Ambiente</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Volver
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <div id="loadingState" class="loading-card">
            <div class="loading-spinner"></div>
            <p>Cargando información del ambiente...</p>
        </div>

        <div id="formCard" class="form-card" style="display: none;">
            <div class="form-header">
                <div class="form-icon">
                    <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                </div>
                <div>
                    <h2>Modificar Información</h2>
                    <p>Actualice los datos del ambiente</p>
                </div>
            </div>

            <form id="ambienteEditForm" class="form-content">
                <input type="hidden" id="amb_id" name="amb_id">

                <div class="form-group">
                    <label for="amb_nombre" class="form-label required">Nombre del Ambiente</label>
                    <input type="text" id="amb_nombre" name="amb_nombre" class="form-input" placeholder="Ej: Ambiente 101 - Sistemas" required>
                    <div class="form-error" id="amb_nombre_error"></div>
                </div>

                <div class="form-group">
                    <label for="sede_sede_id" class="form-label required">Sede</label>
                    <select id="sede_sede_id" name="sede_sede_id" class="form-input" required>
                        <option value="">Seleccione una sede...</option>
                        <!-- Sedes will be loaded here -->
                    </select>
                    <div class="form-error" id="sede_sede_id_error"></div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <ion-icon src="../../assets/ionicons/close-circle-outline.svg"></ion-icon>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                        Actualizar Ambiente
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div id="infoCard" class="info-card">
            <div class="info-header">
                <ion-icon src="../../assets/ionicons/information-circle-outline.svg"></ion-icon>
                <h3>Información de Edición</h3>
            </div>
            <div class="info-content">
                <ul>
                    <li>Los cambios en el nombre se reflejarán en todos los horarios asociados</li>
                    <li>Si cambia la sede, verifique que no haya colisiones de programación</li>
                    <li>Mantenga los nombres estandarizados (Ej: Ambiente 101, Taller A)</li>
                    <li>Puede cancelar para descartar las modificaciones actuales</li>
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
            <h3>Ambiente Actualizado</h3>
        </div>
        <div class="modal-body">
            <p>Los cambios han sido guardados correctamente.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Aceptar</a>
        </div>
    </div>
</div>

<script src="../../assets/js/ambiente/editar.js?v=<?php echo time(); ?>"></script>
</body>

</html>