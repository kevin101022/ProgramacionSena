<?php
$pageTitle = "Auditoría de Asignaciones - SENA";
$activeNavItem = 'auditoria_asignacion';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';

$coordinaciones = [];
if (isset($rol) && $rol === 'centro' && isset($_SESSION['centro_id'])) {
    require_once '../../Conexion.php';
    $dbCoord = Conexion::getConnect();
    $stmtCoord = $dbCoord->prepare("SELECT coord_id, coord_descripcion FROM COORDINACION WHERE centro_formacion_cent_id = :cent_id AND estado = 1 ORDER BY coord_descripcion ASC");
    $stmtCoord->execute([':cent_id' => $_SESSION['centro_id']]);
    $coordinaciones = $stmtCoord->fetchAll(PDO::FETCH_ASSOC);
}
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../dashboard/index.php">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Auditoría</span>
            </nav>
            <h1 class="page-title">Historial de Auditoría</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/receipt-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL REGISTROS</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/receipt-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalAuditorias">0</span>
                    <span class="stat-card-desc">movimientos registrados</span>
                    <p class="stat-card-context">Trazabilidad completa de inserciones, cambios y eliminaciones en las programaciones.</p>
                </div>
                <div class="stat-card-pill-container">
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/shield-checkmark-outline.svg"></ion-icon>
                        Seguro
                    </div>
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon>
                        Historial
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar flex-col md:flex-row flex-wrap gap-4">
            <div class="flex flex-1 gap-4 items-center w-full md:w-auto">
                <div class="search-container flex-1 w-full">
                    <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                    <input type="text" id="searchInput" placeholder="Buscar por instructor, competencia o acción..." class="search-input w-full">
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-4 items-stretch md:items-center w-full md:w-auto">
                <?php if ($rol === 'centro'): ?>
                    <div class="search-container w-full md:w-64">
                        <ion-icon src="../../assets/ionicons/business-outline.svg" class="search-icon"></ion-icon>
                        <select id="coordFilter" class="search-input bg-transparent border-none focus:ring-0 cursor-pointer w-full">
                            <option value="">TODAS LAS COORDINACIONES</option>
                            <?php foreach ($coordinaciones as $c): ?>
                                <option value="<?php echo $c['coord_id']; ?>"><?php echo htmlspecialchars($c['coord_descripcion']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <select id="actionFilter" class="w-full md:w-auto p-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold text-gray-500 uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-sena-green/20">
                    <option value="">TODAS LAS ACCIONES</option>
                    <option value="INSERT">INSERT (CREAR)</option>
                    <option value="UPDATE">UPDATE (EDITAR)</option>
                    <option value="DELETE">DELETE (ELIMINAR)</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table class="data-table" id="auditoriaTable">
                <thead>
                    <tr>
                        <th class="w-10">Fecha/Hora</th>
                        <th>Usuario Responsable</th>
                        <th>Acción</th>
                        <th>Detalle del Cambio</th>
                        <th class="text-right">Detalles</th>
                    </tr>
                </thead>
                <tbody id="auditoriaTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-12">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 border-3 border-sena-green border-t-transparent rounded-full animate-spin mb-4"></div>
                                <p class="text-gray-400 text-sm">Cargando registros de trazabilidad...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="../../assets/js/auditoria_asignacion/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>