<?php
$pageTitle = "Gestión de Fichas - SENA";
$activeNavItem = 'fichas';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Fichas</span>
            </nav>
            <h1 class="page-title">Fichas de Formación</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL FICHAS</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalFichas">0</span>
                    <span class="stat-card-desc">fichas activas</span>
                </div>
            </div>
        </div>

        <div class="action-bar">
            <div class="search-container flex-1">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                <input type="text" id="searchInput" placeholder="Buscar por número de ficha, programa o líder..." class="search-input">
            </div>
            <button id="addBtn" class="btn-primary">
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                Nueva Ficha
            </button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-32">N° Ficha</th>
                        <th>Programa de Formación</th>
                        <th>Sede / Coordinación</th>
                        <th>Instructor Líder</th>
                        <th>Jornada</th>
                    </tr>
                </thead>
                <tbody id="fichaTableBody">
                    <tr>
                        <td colspan="6" class="text-center py-8">Cargando fichas...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Create/Edit Modal -->
<?php require_once 'modal_edit.php'; ?>

<script src="../../assets/js/ficha/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>