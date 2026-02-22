<?php
$pageTitle = 'Registrar Programa - SENA';
$activeNavItem = 'programas';
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
                <a href="index.php">Programas</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Crear</span>
            </nav>
            <h1 class="page-title">Registrar Nuevo Programa</h1>
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
                    <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                </div>
                <div>
                    <h2>Información del Programa</h2>
                    <p>Defina los parámetros básicos de la formación académica</p>
                </div>
            </div>

            <form id="crearProgramaForm" class="form-content">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="prog_codigo" class="form-label required">Código de Programa</label>
                        <input type="number" id="prog_codigo" name="prog_codigo" class="form-input" placeholder="Ej: 228106" required>
                        <div class="form-help">Código nacional único del programa.</div>
                    </div>

                    <div class="form-group">
                        <label for="prog_tipo" class="form-label required">Tipo de Programa</label>
                        <select id="prog_tipo" name="prog_tipo" class="form-input" required>
                            <option value="" disabled selected>Seleccione tipo...</option>
                            <option value="Tecnólogo">Tecnólogo</option>
                            <option value="Técnico">Técnico</option>
                            <option value="Especialización">Especialización</option>
                            <option value="Curso Corto">Curso Corto</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="prog_denominacion" class="form-label required">Denominación del Programa</label>
                        <input type="text" id="prog_denominacion" name="prog_denominacion" class="form-input" placeholder="Ej: Análisis y Desarrollo de Software" required>
                        <div class="form-help">Nombre completo tal como aparece en el registro calificado.</div>
                    </div>

                    <div class="form-group full-width">
                        <label for="sede_sede_id" class="form-label required">Sede a la que pertenece</label>
                        <select id="sede_sede_id" name="sede_sede_id" class="form-input" required>
                            <option value="" disabled selected>Cargando sedes...</option>
                        </select>
                        <div class="form-help">Seleccione el campus donde se impartirá principalmente este programa.</div>
                    </div>

                    <div class="form-group full-width">
                        <label for="tit_programa_titpro_id" class="form-label required">Título Académico que Otorga</label>
                        <select id="tit_programa_titpro_id" name="tit_programa_titpro_id" class="form-input" required>
                            <option value="" disabled selected>Cargando títulos...</option>
                        </select>
                        <div class="form-help">El título debe estar previamente registrado en el sistema.</div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn-secondary">
                        <ion-icon src="../../assets/ionicons/close-circle-outline.svg"></ion-icon>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                        Guardar Programa
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="info-card">
            <div class="info-header">
                <ion-icon src="../../assets/ionicons/information-circle-outline.svg"></ion-icon>
                <h3>Información Adicional</h3>
            </div>
            <div class="info-content">
                <p>Al registrar un programa, podrá posteriormente asociarle competencias y resultados de aprendizaje específicos.</p>
                <ul class="mt-4">
                    <li>Verifique que el código no exista</li>
                    <li>Asegúrese de seleccionar el título correcto</li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script src="../../assets/js/programa/crear.js?v=<?php echo time(); ?>"></script>
</body>

</html>