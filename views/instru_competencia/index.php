<?php
$pageTitle = "Habilitación de Instructores - SENA";
$activeNavItem = 'habilitaciones';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Habilitación Instructores</span>
            </nav>
            <h1 class="page-title">Habilitación de Instructores</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL HABILITACIONES</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/shield-checkmark-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalHabilitaciones">0</span>
                    <span class="stat-card-desc">vínculos activos</span>
                </div>
            </div>
        </div>

        <div class="action-bar">
            <div class="search-container flex-1">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                <input type="text" id="searchInput" placeholder="Buscar por instructor, programa o competencia..." class="search-input">
            </div>
            <button id="addBtn" class="btn-primary">
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                Nueva Habilitación
            </button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-16">ID</th>
                        <th>Instructor</th>
                        <th>Programa</th>
                        <th>Competencia</th>
                        <th>Vigencia</th>
                    </tr>
                </thead>
                <tbody id="habilitacionTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-8">Cargando habilitaciones...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Create/Edit Modal -->
<?php require_once 'modal_edit.php'; ?>

<script src="../../assets/js/instru_competencia/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>