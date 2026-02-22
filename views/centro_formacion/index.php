<?php
$pageTitle = "Centros de Formación - SENA";
$activeNavItem = 'centros';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Centros de Formación</span>
            </nav>
            <h1 class="page-title">Centros de Formación</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL CENTROS</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalCentros">0</span>
                    <span class="stat-card-desc">centros registrados</span>
                </div>
            </div>
        </div>

        <div class="action-bar">
            <div class="search-container flex-1">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                <input type="text" id="searchInput" placeholder="Buscar centro por nombre..." class="search-input">
            </div>
            <button id="addBtn" class="btn-primary">
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                Nuevo Centro
            </button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-20">ID</th>
                        <th>Nombre del Centro</th>
                    </tr>
                </thead>
                <tbody id="centroTableBody">
                    <tr>
                        <td colspan="2" class="text-center py-8">Cargando centros...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Create/Edit Modal -->
<?php require_once 'modal_edit.php'; ?>

<script src="../../assets/js/centro_formacion/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>