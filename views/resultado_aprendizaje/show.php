<?php
$pageTitle = "Detalle RAP - SENA";
$activeNavItem = 'resultado_aprendizaje';

if (!isset($data) && isset($_GET['id'])) {
    require_once '../../Conexion.php';
    require_once '../../model/ResultadoAprendizajeModel.php';
    $model = new ResultadoAprendizajeModel();
    $rap = $model->getById($_GET['id']);
} else {
    $rap = $data ?? [];
}

if (!$rap) {
    die("Resultado de Aprendizaje no encontrado");
}

require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <a href="index.php">Resultados de Aprendizaje</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Detalle</span>
            </nav>
            <h1 class="page-title">Detalle del RAP</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Volver a la Lista
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; margin-bottom: 2rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <h2 style="font-size: 1.25rem; font-weight: bold; color: #111827; margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <div style="background: #eff6ff; color: #2563eb; padding: 0.4rem; border-radius: 0.5rem; display: flex;">
                    <ion-icon src="../../assets/ionicons/bookmark-outline.svg"></ion-icon>
                </div>
                <?php echo htmlspecialchars($rap['rap_codigo']); ?>
            </h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="grid-column: span 2;">
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Descripción</span>
                    <p style="color: #4b5563; margin: 0; font-size: 0.9rem; line-height: 1.4;"><?php echo htmlspecialchars($rap['rap_descripcion']); ?></p>
                </div>
                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Competencia Asociada</span>
                    <p style="color: #374151; margin: 0; font-size: 0.95rem; font-weight: 500;"><?php echo htmlspecialchars($rap['comp_nombre_corto'] ?? $rap['competxprog_comp_id']); ?></p>
                </div>
                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Unidad de Competencia</span>
                    <p style="color: #374151; margin: 0; font-weight: 500; font-size: 0.8rem;"><?php echo htmlspecialchars($rap['comp_nombre_unidad_competencia'] ?? 'N/A'); ?></p>
                </div>
                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Programa</span>
                    <p style="color: #374151; margin: 0; font-size: 0.95rem; font-weight: 500;"><?php echo htmlspecialchars($rap['prog_denominacion'] ?? $rap['competxprog_prog_id']); ?></p>
                </div>
                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Horas Asignadas</span>
                    <p style="color: #374151; margin: 0; font-size: 0.95rem; font-weight: 500;">
                        <span style="background: #eff6ff; color: #2563eb; padding: 2px 8px; border-radius: 999px; font-size: 0.75rem; font-weight: bold;">
                            <?php echo htmlspecialchars($rap['rap_horas']); ?>h
                        </span>
                    </p>
                </div>
                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Fase del Proyecto</span>
                    <p style="color: #374151; margin: 0; font-size: 0.95rem; font-weight: 500;">
                        <?php if(!empty($rap['fase_nombre'])): ?>
                            <span style="background: #d1fae5; color: #047857; padding: 2px 8px; border-radius: 999px; font-size: 0.8rem; font-weight: bold;">
                                Fase <?php echo htmlspecialchars($rap['fase_orden'] . ': ' . $rap['fase_nombre']); ?>
                            </span>
                            <br><span style="font-size: 0.75rem; color: #6b7280; margin-top: 4px; display: inline-block;">PF: <?php echo htmlspecialchars($rap['pf_codigo']); ?></span>
                        <?php else: ?>
                            <span style="color: #9ca3af; font-style: italic; font-size: 0.85rem;">Sin asignar a fase</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
