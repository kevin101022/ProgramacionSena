<?php
$pageTitle = 'Competencias - Programaciones';
$activeNavItem = 'competencias';
require_once '../layouts/head.php';

// Prevent instructors from accessing the Coordinator's Competencia View
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'instructor') {
    header("Location: instructor_index.php");
    exit;
}

require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <div class="breadcrumb">
                <a href="../dashboard/index.php">Principal</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Competencias</span>
            </div>
            <h1 class="page-title">
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'coordinador'): ?>
                    Competencias (Consulta)
                <?php else: ?>
                    Competencias
                <?php endif; ?>
            </h1>
        </div>
        <div class="header-actions">
        </div>
    </header>

    <div class="content-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL DE COMPETENCIAS</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalCompetencias">0</span>
                    <span class="stat-card-desc">competencias registradas</span>
                    <p class="stat-card-context">Normas de competencia laboral que definen las habilidades técnicas y conocimientos.</p>
                </div>
                <div class="stat-card-pill-container">
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon>
                        Carga
                    </div>
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/document-text-outline.svg"></ion-icon>
                        Norma
                    </div>
                </div>
            </div>
        </div>
        <div class="action-bar">
            <div class="search-container">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                <input type="text" id="searchTerm" class="search-input" placeholder="Buscar por nombre o unidad...">
            </div>
            <div class="filter-group">
                <!-- Additional filters can be added here -->
            </div>

            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'centro'): ?>
                <a href="crear.php" class="btn-primary">
                    <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                    Nueva Competencia
                </a>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table class="data-table" id="competenciasTable">
                <thead>
                    <tr>
                        <th class="w-10">N°</th>
                        <th>Nombre Corto</th>
                        <th>Horas</th>
                        <th>Unidad de Competencia</th>
                    </tr>
                </thead>
                <tbody id="competenciasBody">
                    <!-- Loaded via JavaScript -->
                    <tr>
                        <td colspan="4" class="text-center">Cargando competencias...</td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination-container border-t border-slate-50 p-6 flex flex-wrap items-center justify-between gap-4">
                <div class="pagination-info">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider">
                        Mostrando <span id="showingFrom" class="text-sena-green font-black">0</span> a <span id="showingTo" class="text-sena-green font-black">0</span> de <span id="totalCount" class="text-sena-green font-black">0</span> competencias
                    </p>
                </div>
                <nav class="pagination flex items-center gap-2">
                    <button class="pagination-btn w-10 h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-400 hover:border-sena-green hover:text-sena-green disabled:opacity-30 disabled:pointer-events-none transition-all" id="prevBtn">
                        <ion-icon src="../../assets/ionicons/chevron-back-outline.svg"></ion-icon>
                    </button>
                    <div id="paginationNumbers" class="flex items-center gap-1"></div>
                    <button class="pagination-btn w-10 h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-400 hover:border-sena-green hover:text-sena-green disabled:opacity-30 disabled:pointer-events-none transition-all" id="nextBtn">
                        <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</main>

<script src="../../assets/js/competencia/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>