<?php
$pageTitle = 'Competencias - Gestión de Transversales';
$activeNavItem = 'competencias';
require_once '../layouts/head.php';
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
            <h1 class="page-title">Competencias</h1>
        </div>
        <div class="header-actions">
        </div>
    </header>

    <div class="content-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL DE COMPETENCIAS</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalCompetencias">0</span>
                    <span class="stat-card-desc">competencias registradas</span>
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

            <a href="crear.php" class="btn-primary">
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                Nueva Competencia
            </a>
        </div>

        <div class="table-container">
            <table class="data-table" id="competenciasTable">
                <thead>
                    <tr>
                        <th>ID</th>
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
        </div>

        <div class="pagination-container">
            <div class="pagination-info">
                Mostrando <span id="showingCount">0</span> de <span id="totalCount">0</span> competencias
            </div>
            <div class="pagination" id="pagination">
                <!-- Pagination buttons -->
            </div>
        </div>
    </div>
</main>

<script src="../../assets/js/competencia/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>