<?php
$pageTitle = 'Editar Programa - SENA';
$activeNavItem = 'programas';
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
                <a href="index.php">Programas</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Editar</span>
            </nav>
            <h1 class="page-title">Modificar Programa</h1>
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
                    <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                </div>
                <div>
                    <h2>Actualizar Registro</h2>
                    <p>Modifique los datos del programa académico</p>
                </div>
            </div>

            <form id="editarProgramaForm" class="form-content">
                <input type="hidden" name="prog_id" value="<?php echo htmlspecialchars($id); ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="prog_codigo" class="form-label required">Código de Programa</label>
                        <input type="number" id="prog_codigo" name="prog_codigo" class="form-input" required>
                        <div class="form-help">Código SofíaPlus original.</div>
                    </div>

                    <div class="form-group">
                        <label for="prog_tipo" class="form-label required">Tipo de Programa</label>
                        <select id="prog_tipo" name="prog_tipo" class="form-input" required>
                            <option value="" disabled>Seleccione tipo...</option>
                            <option value="Tecnólogo">Tecnólogo</option>
                            <option value="Técnico">Técnico</option>
                            <option value="Especialización">Especialización</option>
                            <option value="Curso Corto">Curso Corto</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="prog_denominacion" class="form-label required">Denominación</label>
                        <input type="text" id="prog_denominacion" name="prog_denominacion" class="form-input" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="sede_sede_id" class="form-label required">Sede a la que pertenece</label>
                        <select id="sede_sede_id" name="sede_sede_id" class="form-input" required>
                            <option value="" disabled>Seleccione una sede...</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="tit_programa_titpro_id" class="form-label required">Título Académico</label>
                        <select id="tit_programa_titpro_id" name="tit_programa_titpro_id" class="form-input" required>
                            <option value="" disabled>Seleccione un título...</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="ver.php?id=<?php echo htmlspecialchars($id); ?>" class="btn-secondary">
                        <ion-icon src="../../assets/ionicons/close-circle-outline.svg"></ion-icon>
                        Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                        Actualizar Programa
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="../../assets/js/programa/editar.js?v=<?php echo time(); ?>"></script>
</body>

</html>