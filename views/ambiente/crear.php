<?php
$pageTitle = 'Registrar Ambiente - SENA';
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
                <span>Registrar</span>
            </nav>
            <h1 class="page-title">Registrar Nuevo Ambiente</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Volver
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="form-card">
            <div class="form-header">
                <div class="form-icon">
                    <ion-icon src="../../assets/ionicons/cube-outline.svg"></ion-icon>
                </div>
                <div>
                    <h2>Información del Ambiente</h2>
                    <p>Complete los datos para registrar un nuevo ambiente de aprendizaje</p>
                </div>
            </div>

            <form id="ambienteForm" class="form-content">
                <div class="form-group">
                    <label for="amb_id" class="form-label required">Número / ID del Ambiente</label>
                    <input type="text" id="amb_id" name="amb_id" class="form-input" placeholder="Ej: 101, T05, LAB-A" required>
                    <div class="form-help">Ingrese el número identificador único del ambiente.</div>
                    <div class="form-error" id="amb_id_error"></div>
                </div>

                <div class="form-group">
                    <label for="amb_nombre" class="form-label required">Nombre del Ambiente</label>
                    <input type="text" id="amb_nombre" name="amb_nombre" class="form-input" placeholder="Ej: Ambiente 101 - Sistemas" required>
                    <div class="form-error" id="amb_nombre_error"></div>
                </div>

                <div class="form-group">
                    <label for="tipo_ambiente" class="form-label required">Tipo de Ambiente</label>
                    <select id="tipo_ambiente" name="tipo_ambiente" class="form-input" required>
                        <option value="">Seleccione un tipo...</option>
                        <option value="Convencional">Convencional</option>
                        <option value="Especializado">Especializado</option>
                        <option value="Ext SENA-MEN">Ext SENA-MEN</option>
                        <option value="Ext Agropecuario">Ext Agropecuario</option>
                    </select>
                    <div class="form-error" id="tipo_ambiente_error"></div>
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
                        Guardar Ambiente
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
                    <li>El nombre del ambiente debe identificar claramente el espacio</li>
                    <li>Asegúrese de seleccionar la sede correcta para la ubicación física</li>
                    <li>Los ambientes se pueden deshabilitar si entran en mantenimiento</li>
                    <li>Puede configurar la capacidad y equipos en la sección de detalles</li>
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
            <h3>Ambiente Registrado</h3>
        </div>
        <div class="modal-body">
            <p>El ambiente ha sido registrado correctamente.</p>
        </div>
        <div class="modal-footer">
            <a href="index.php" class="btn-primary">Ver Todos</a>
            <button class="btn-secondary" onclick="closeSuccessModal()">Registrar Otro</button>
        </div>
    </div>
</div>

<script src="../../assets/js/ambiente/crear.js?v=<?php echo time(); ?>"></script>
</body>

</html>