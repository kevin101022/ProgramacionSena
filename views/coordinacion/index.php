<?php
$pageTitle = "Coordinaciones Académicas - SENA";
$activeNavItem = 'coordinaciones';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Coordinaciones</span>
            </nav>
            <h1 class="page-title">Coordinaciones Académicas</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL COORDINACIONES</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/people-circle-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalCoordinaciones">0</span>
                    <span class="stat-card-desc">dependencias registradas</span>
                </div>
            </div>
        </div>

        <div class="action-bar">
            <div class="search-container flex-1">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                <input type="text" id="searchInput" placeholder="Buscar por nombre o centro..." class="search-input">
            </div>
            <button id="addBtn" class="btn-primary">
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                Nueva Coordinación
            </button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-20">ID</th>
                        <th>Nombre de la Coordinación</th>
                        <th>Centro de Formación</th>
                    </tr>
                </thead>
                <tbody id="coordinacionTableBody">
                    <tr>
                        <td colspan="3" class="text-center py-8">Cargando coordinaciones...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Create/Edit Modal -->
<?php require_once 'modal_edit.php'; ?>

<script src="../../assets/js/coordinacion/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>