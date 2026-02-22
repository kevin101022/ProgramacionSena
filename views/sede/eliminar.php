<?php
$pageTitle = 'Eliminar Sede - SENA';
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
                <span>Eliminar</span>
            </nav>
            <h1 class="page-title">Eliminar Sede</h1>
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

        <!-- Delete Confirmation -->
        <div id="deleteConfirmation" class="delete-container" style="display: none;">
            <!-- Warning Card -->
            <div class="warning-card">
                <div class="warning-icon">
                    <ion-icon src="../../assets/ionicons/warning-outline.svg"></ion-icon>
                </div>
                <div class="warning-content">
                    <h2>¡Atención! Eliminación de Sede</h2>
                    <p>Está a punto de eliminar permanentemente la siguiente sede del sistema:</p>
                </div>
            </div>

            <!-- Sede Info Card -->
            <div class="sede-info-card">
                <div class="sede-info-header">
                    <div class="sede-info-icon">
                        <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                    </div>
                    <div>
                        <h3 id="sedeNombre">Cargando...</h3>
                        <p>ID: <span id="sedeId">-</span></p>
                    </div>
                </div>
            </div>

            <!-- Impact Analysis -->
            <div class="impact-card">
                <div class="impact-header">
                    <ion-icon src="../../assets/ionicons/analytics-outline.svg"></ion-icon>
                    <h3>Análisis de Impacto</h3>
                </div>
                <div class="impact-content">
                    <div class="impact-item">
                        <div class="impact-icon">
                            <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                        </div>
                        <div class="impact-details">
                            <span class="impact-count" id="programasCount">0</span>
                            <span class="impact-label">Programas asociados</span>
                        </div>
                    </div>

                    <div class="impact-item">
                        <div class="impact-icon">
                            <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                        </div>
                        <div class="impact-details">
                            <span class="impact-count" id="instructoresCount">0</span>
                            <span class="impact-label">Instructores asignados</span>
                        </div>
                    </div>

                    <div class="impact-item">
                        <div class="impact-icon">
                            <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                        </div>
                        <div class="impact-details">
                            <span class="impact-count" id="fichasCount">0</span>
                            <span class="impact-label">Fichas en formación</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consequences Warning -->
            <div id="consequencesWarning" class="consequences-card" style="display: none;">
                <div class="consequences-header">
                    <ion-icon src="../../assets/ionicons/alert-circle-outline.svg"></ion-icon>
                    <h3>Consecuencias de la Eliminación</h3>
                </div>
                <div class="consequences-content">
                    <ul id="consequencesList">
                        <!-- Dynamic consequences will be added here -->
                    </ul>
                </div>
            </div>

            <!-- Confirmation Form -->
            <div class="confirmation-card">
                <div class="confirmation-header">
                    <ion-icon src="../../assets/ionicons/shield-checkmark-outline.svg"></ion-icon>
                    <h3>Confirmación Requerida</h3>
                </div>

                <form id="deleteForm" class="confirmation-form">
                    <input type="hidden" id="sede_id_delete" name="sede_id">

                    <div class="form-group">
                        <label for="confirmText" class="form-label required">
                            Para confirmar, escriba el nombre de la sede:
                        </label>
                        <input
                            type="text"
                            id="confirmText"
                            name="confirmText"
                            class="form-input"
                            placeholder="Escriba el nombre exacto de la sede"
                            required>
                        <div class="form-error" id="confirmText_error"></div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="confirmUnderstand" name="confirmUnderstand" required>
                        <label for="confirmUnderstand">
                            Entiendo que esta acción es irreversible y eliminará permanentemente la sede y todas sus relaciones.
                        </label>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn-secondary">
                            <ion-icon src="../../assets/ionicons/close-circle-outline.svg"></ion-icon>
                            Cancelar
                        </a>
                        <button type="submit" class="btn-danger" id="deleteButton" disabled>
                            <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon>
                            Eliminar Permanentemente
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cannot Delete Card -->
        <div id="cannotDeleteCard" class="error-card" style="display: none;">
            <div class="error-icon">
                <ion-icon src="../../assets/ionicons/ban-outline.svg"></ion-icon>
            </div>
            <div>
                <h3>No se puede Eliminar</h3>
                <p id="cannotDeleteMessage">Esta sede no puede ser eliminada porque tiene elementos asociados.</p>
                <div id="blockingElements" class="blocking-elements">
                    <!-- Blocking elements will be listed here -->
                </div>
                <a href="index.php" class="btn-primary mt-4">Volver a Sedes</a>
            </div>
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
    </div>
</main>

<!-- Success Modal -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <div class="modal-header success">
            <ion-icon src="../../assets/ionicons/checkmark-circle-outline.svg"></ion-icon>
            <h3>Sede Eliminada</h3>
        </div>
        <div class="modal-body">
            <p>La sede <strong id="deletedSedeName"></strong> ha sido eliminada permanentemente del sistema.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Volver a Sedes</a>
        </div>
    </div>
</div>

<script src="../../assets/js/sede/eliminar.js"></script>
</body>

</html>