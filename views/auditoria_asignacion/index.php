<?php
$pageTitle = "Auditoría de Asignaciones - SENA";
$activeNavItem = 'auditoria_asignacion';
// Asumiendo que las rutas para layouts son las mismas
require_once 'views/layouts/head.php';
require_once 'views/layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Auditoría de Asignaciones</span>
            </nav>
            <h1 class="page-title">Historial de Cambios</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="table-container">
            <table class="data-table" id="auditoriaTable">
                <thead>
                    <tr>
                        <th class="w-10">Fecha y Hora</th>
                        <th>Documento Usuario</th>
                        <th>Correo Usuario</th>
                        <th>Acción</th>
                        <th>Instructor Afectado</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($auditorias)) : ?>
                        <?php foreach ($auditorias as $auditoria) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($auditoria['fecha_hora']); ?></td>
                                <td><?php echo htmlspecialchars($auditoria['documento_usuario_accion']); ?></td>
                                <td><?php echo htmlspecialchars($auditoria['correo_usuario']); ?></td>
                                <td>
                                    <?php
                                    $clase = 'badge-gray';
                                    if ($auditoria['tipo_accion'] == 'INSERT') $clase = 'badge-green';
                                    if ($auditoria['tipo_accion'] == 'UPDATE') $clase = 'badge-blue';
                                    if ($auditoria['tipo_accion'] == 'DELETE') $clase = 'badge-red';
                                    ?>
                                    <span class="badge <?php echo $clase; ?>"><?php echo htmlspecialchars($auditoria['tipo_accion']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($auditoria['inst_nombres'] . ' ' . $auditoria['inst_apellidos'] . ' (' . $auditoria['instructor_inst_id'] . ')'); ?></td>
                                <td><?php echo htmlspecialchars($auditoria['asig_fecha_ini']); ?></td>
                                <td><?php echo htmlspecialchars($auditoria['asig_fecha_fin']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-8">No hay registros de auditoría aún.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="assets/js/auditoria_asignacion/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>