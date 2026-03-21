<?php
$pageTitle = "Mis Fichas - SENA";
$activeNavItem = 'mi_ficha';
require_once dirname(__DIR__) . '/layouts/head.php';

// Enforce role
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'instructor') {
    header("Location: ../../routing.php?controller=login&action=showLogin");
    exit;
}

require_once dirname(__DIR__, 2) . '/model/InstructorModel.php';
require_once dirname(__DIR__, 2) . '/model/AsignacionModel.php';

$instructorId = $_SESSION['id'];
$instructorModel = new InstructorModel($instructorId);
$fichasLider = $instructorModel->getFichasLider();

$asignacionModel = new AsignacionModel();

?>

<?php require_once dirname(__DIR__) . '/layouts/instructor_sidebar.php'; ?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../asignacion/instructor_index.php">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Mis Fichas</span>
            </nav>
            <h1 class="page-title">Gestión de Mis Fichas</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <?php if (empty($fichasLider)): ?>
            <div class="glass-container p-8 text-center mt-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-500 mb-4">
                    <ion-icon src="../../assets/ionicons/information-circle-outline.svg" class="text-3xl"></ion-icon>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">No tienes fichas asignadas como líder</h2>
                <p class="text-gray-500 max-w-md mx-auto">Actualmente no estás registrado como instructor líder de ninguna ficha en etapa lectiva. Si crees que esto es un error, contacta a tu coordinador.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-6 mt-4">
                <?php foreach ($fichasLider as $ficha): ?>
                    <?php
                        // Calculate total hours scheduled for the ficha
                        $totalHours = 0;
                        // Assuming getAsignaciones retrieves everything for a ficha
                        $asignacionesFicha = $asignacionModel->readByFicha($ficha['fich_id']);
                        $assignedComps = [];
                        foreach ($asignacionesFicha as $asig) {
                            $assignedComps[] = $asig['competencia_comp_id'];
                            // Horas programadas de esta asignación
                            if (!empty($asig['total_horas'])) {
                                $totalHours += floatval($asig['total_horas']);
                            }
                        }
                        $assignedComps = array_unique($assignedComps);
                        
                        // Get total competencies of the program
                        
                        // We need the program ID. Wait, $ficha['prog_codigo']? It was not selected in getFichasLider.
                        // Let's use the DB directly for total competencies count.
                        $db = Conexion::getConnect();
                        $stmtCount = $db->prepare("SELECT COUNT(*) FROM COMPETENCIA c INNER JOIN FICHA f ON c.programa_prog_id = f.PROGRAMA_prog_id WHERE f.fich_id = :fich_id");
                        $stmtCount->execute([':fich_id' => $ficha['fich_id']]);
                        $totalComps = $stmtCount->fetchColumn();
                        
                        $pendingComps = max(0, $totalComps - count($assignedComps));
                        
                        $progressPercent = $totalComps > 0 ? (count($assignedComps) / $totalComps) * 100 : 0;
                    ?>
                    <div class="glass-container overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                        <ion-icon src="../../assets/ionicons/folder-open-outline.svg" class="text-sena-green"></ion-icon>
                                        Ficha: <?php echo htmlspecialchars($ficha['fich_id']); ?>
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($ficha['prog_denominacion']); ?></p>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-2">
                                        <?php echo htmlspecialchars($ficha['titpro_nombre']); ?>
                                    </span>
                                </div>
                                <div class="mt-4 md:mt-0 text-right">
                                    <div class="text-sm text-gray-500 mb-1">Jornada</div>
                                    <div class="font-semibold text-gray-800 capitalize"><?php echo htmlspecialchars($ficha['fich_jornada']); ?></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-8 h-8 rounded-full bg-sena-green/10 flex items-center justify-center text-sena-green">
                                            <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">Etapa Lectiva</span>
                                    </div>
                                    <div class="text-xs text-gray-600 space-y-1 ml-11">
                                        <p>Inicio: <span class="font-medium text-gray-900"><?php echo date('d/m/Y', strtotime($ficha['fich_fecha_ini_lectiva'])); ?></span></p>
                                        <p>Fin: <span class="font-medium text-gray-900"><?php echo date('d/m/Y', strtotime($ficha['fich_fecha_fin_lectiva'])); ?></span></p>
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500">
                                            <ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">Horas Programadas</span>
                                    </div>
                                    <div class="text-2xl font-bold text-gray-800 ml-11">
                                        <?php echo number_format($totalHours, 0); ?> <span class="text-sm font-normal text-gray-500">h</span>
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-8 h-8 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-500">
                                            <ion-icon src="../../assets/ionicons/book-outline.svg"></ion-icon>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">Estado Competencias</span>
                                    </div>
                                    <div class="ml-11">
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span>Asignadas: <?php echo count($assignedComps); ?></span>
                                            <span>Pendientes: <?php echo $pendingComps; ?></span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
                                            <div class="bg-sena-green h-1.5 rounded-full" style="width: <?php echo $progressPercent; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Future expansion: you can list detailed competencies here -->
                            <div class="mt-4 flex justify-center gap-3">
                                <a href="../ficha/ver.php?id=<?php echo urlencode($ficha['fich_id']); ?>" class="btn-primary" style="display:inline-flex;">
                                    <ion-icon src="../../assets/ionicons/information-circle-outline.svg" class="mr-2"></ion-icon>
                                    Ver Detalles de la Ficha
                                </a>
                                <a href="../asignacion/instructor_calendario.php?ficha=<?php echo urlencode($ficha['fich_id']); ?>" class="btn-secondary" style="display:inline-flex;">
                                    <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="mr-2"></ion-icon>
                                    Ver Calendario
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
