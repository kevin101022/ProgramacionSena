<?php
$pageTitle = "Detalle Proyecto Formativo - SENA";
$activeNavItem = 'proyecto_formativo';

if (!isset($data) && isset($_GET['id'])) {
    require_once '../../Conexion.php';
    require_once '../../model/ProyectoFormativoModel.php';
    $model = new ProyectoFormativoModel();
    $proyecto = $model->getById($_GET['id']);
} else {
    $proyecto = $data ?? [];
}

if (!$proyecto) {
    die("Proyecto no encontrado");
}
$fases = $proyecto['fases'] ?? [];

require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <a href="index.php">Proyectos Formativos</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Detalle</span>
            </nav>
            <h1 class="page-title">Detalle del Proyecto Formativo</h1>
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
                <div style="background: #d1fae5; color: #059669; padding: 0.4rem; border-radius: 0.5rem; display: flex;">
                    <ion-icon src="../../assets/ionicons/folder-open-outline.svg"></ion-icon>
                </div>
                <?php echo htmlspecialchars($proyecto['pf_codigo'] . ' - ' . $proyecto['pf_nombre']); ?>
            </h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="grid-column: span 2;">
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Programa de Formación</span>
                    <p style="color: #374151; margin: 0; font-size: 0.95rem; font-weight: 500;">
                        <span style="background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 999px; font-size: 0.8rem; font-weight: bold; margin-right: 6px;">
                            <?php echo htmlspecialchars($proyecto['programa_prog_codigo']); ?>
                        </span>
                        <?php echo htmlspecialchars($proyecto['prog_denominacion'] ?? ''); ?>
                    </p>
                </div>
                <div style="grid-column: span 2;">
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Descripción</span>
                    <p style="color: #4b5563; margin: 0; font-size: 0.9rem; line-height: 1.4;"><?php echo htmlspecialchars($proyecto['pf_descripcion'] ?? 'Sin descripción proporcionada.'); ?></p>
                </div>
            </div>
        </div>

        <h3 style="font-size: 1.15rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <ion-icon src="../../assets/ionicons/git-network-outline.svg" style="color: #059669; font-size: 1.25rem;"></ion-icon>
            Fases del Proyecto y RAPs
        </h3>

        <div style="margin-bottom: 2rem;">
            <?php
            require_once '../../model/ResultadoAprendizajeModel.php';
            $rapModel = new ResultadoAprendizajeModel();
            $fasesConRaps = $rapModel->getRapsFasesDeProyecto($proyecto['pf_id']);
            ?>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <?php if (empty($fases)): ?>
                    <div style="text-align: center; padding: 2rem; color: #9ca3af; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem;">No hay fases registradas para este proyecto formativo.</div>
                <?php else: ?>
                    <?php foreach($fases as $fase): 
                        // Buscar los RAPs de esta fase
                        $rapsDeFase = [];
                        foreach($fasesConRaps as $g) {
                            if($g['fase_id'] == $fase['fase_id']) {
                                $rapsDeFase = $g['raps'];
                                break;
                            }
                        }
                    ?>
                        <details style="background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                            <summary style="padding: 1rem; cursor: pointer; font-weight: 600; color: #111827; display: flex; align-items: center; gap: 1rem; outline: none; user-select: none; background: #f9fafb;">
                                <span style="background: #e5e7eb; color: #374151; padding: 0.2rem 0.6rem; border-radius: 999px; font-weight: 700; font-size: 0.8rem;">Fase <?php echo htmlspecialchars($fase['fase_orden']); ?></span>
                                <span style="flex-grow: 1;"><?php echo htmlspecialchars($fase['fase_nombre']); ?></span>
                                <span style="font-size: 0.85rem; color: #6b7280; font-weight: normal; font-family: monospace;">
                                    <ion-icon src="../../assets/ionicons/calendar-outline.svg" style="vertical-align: middle; margin-right: 4px;"></ion-icon>
                                    <?php echo date('d/m/Y', strtotime($fase['fase_fecha_ini'])); ?> - <?php echo date('d/m/Y', strtotime($fase['fase_fecha_fin'])); ?>
                                </span>
                                <span style="background: #d1fae5; color: #047857; padding: 2px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: bold;">
                                    <?php echo count($rapsDeFase); ?> RAPs
                                </span>
                            </summary>
                            <div style="padding: 1rem; border-top: 1px solid #e5e7eb; background: white;">
                                <?php if(empty($rapsDeFase)): ?>
                                    <p style="color: #6b7280; font-size: 0.9rem; text-align: center; margin: 0; padding: 1rem;">No hay Resultados de Aprendizaje asignados a esta fase todavía.</p>
                                <?php else: ?>
                                    <ul style="list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: 1fr; gap: 0.75rem;">
                                        <?php foreach($rapsDeFase as $rap): ?>
                                            <li style="padding: 1rem; border: 1px solid #f3f4f6; border-radius: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem; background: #fff;">
                                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                                    <div>
                                                        <strong style="color: #111827; font-size: 0.95rem; display: block; margin-bottom: 2px;">
                                                            <ion-icon src="../../assets/ionicons/bookmark-outline.svg" style="color: #2563eb; vertical-align: middle; margin-right: 4px;"></ion-icon>
                                                            <?php echo htmlspecialchars($rap['rap_codigo']); ?>
                                                        </strong>
                                                    </div>
                                                    <span style="background: #eff6ff; color: #2563eb; padding: 2px 8px; border-radius: 999px; font-size: 0.75rem; font-weight: bold;"><?php echo htmlspecialchars($rap['rap_horas']); ?>h</span>
                                                </div>
                                                <p style="margin: 0; font-size: 0.85rem; color: #4b5563; line-height: 1.4; padding-left: 20px;"><?php echo htmlspecialchars($rap['rap_descripcion']); ?></p>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </details>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
    </div>
</main>
</body>
</html>
