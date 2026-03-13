<?php
$pageTitle = "Dashboard Instructor - SENA";
$activeNavItem = 'dashboard';
require_once '../layouts/head.php';

// Enforce role
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'instructor') {
    header("Location: ../../routing.php?controller=login&action=showLogin");
    exit;
}

// Get the Centro de Formación name to display it in the welcome section
$centroNombre = "Centro de Formación";
if (isset($_SESSION['centro_id'])) {
    require_once '../../Conexion.php';
    try {
        $db = Conexion::getConnect();
        $stmt = $db->prepare("SELECT cent_nombre FROM CENTRO_FORMACION WHERE cent_id = :id");
        $stmt->execute([':id' => $_SESSION['centro_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $centroNombre = $row['cent_nombre'];
        }
    } catch (Exception $e) {
        // Silently continue
    }
}
$instructorNombre = $_SESSION['nombre'] ?? 'Instructor';
?>

<?php require_once '../layouts/instructor_sidebar.php'; ?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Dashboard Instructor</span>
            </nav>
            <h1 class="page-title">Mi Espacio</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Welcome Section -->
        <div class="welcome-section glass-container">
            <div class="welcome-content">
                <h2>¡Hola, <?php echo htmlspecialchars($instructorNombre); ?>! 👋</h2>
                <p>Bienvenido al sistema de programación. Tienes asignaciones activas en <span class="font-bold text-sena-green"><?php echo htmlspecialchars($centroNombre); ?></span>.</p>
            </div>
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-sena-green/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
            <div class="absolute bottom-0 left-10 w-48 h-48 bg-blue-500/5 rounded-full blur-2xl translate-y-1/3 pointer-events-none"></div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">MIS ASIGNACIONES</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/calendar-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalAsignaciones">0</span>
                    <span class="stat-card-desc">clases programadas</span>
                    <p class="stat-card-context">Resumen de responsabilidades. Accede al menú lateral para ver tu calendario o competencias organizadas.</p>
                </div>
                <div class="stat-card-pill-container">
                    <a href="instructor_calendario.php" class="stat-pill hover:bg-sena-green hover:text-white transition-colors cursor-pointer" style="text-decoration:none;">
                        <ion-icon src="../../assets/ionicons/arrow-forward-outline.svg"></ion-icon>
                        Ver Calendario Completo
                    </a>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // We fetch assignments for THIS instructor to show the count
        const instructorId = <?php echo json_encode($_SESSION['id']); ?>;

        fetch(`../../routing.php?controller=instructor&action=getAsignaciones&id=${instructorId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalAsignaciones').textContent = data.length || 0;
            })
            .catch(err => console.error("Error loading events", err));
    });
</script>
</body>

</html>