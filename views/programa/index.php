<?php
$pageTitle = 'Gestión de Programas - SENA';
$activeNavItem = 'programas';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Programas</span>
            </nav>
            <h1 class="page-title">Administración de Programas</h1>
        </div>
        <div class="header-actions">
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Stats Card -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL DE PROGRAMAS</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalProgramas">0</span>
                    <span class="stat-card-desc">programas registrados</span>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="search-container">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                <input type="text" id="searchInput" placeholder="Buscar por código o denominación..." class="search-input">
            </div>

            <a href="crear.php" class="btn-primary">
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                Registrar Programa
            </a>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10 text-sena-green font-bold">ID</th>
                        <th class="w-15">Código</th>
                        <th>Denominación</th>
                        <th>Título</th>
                        <th class="w-15">Tipo</th>
                    </tr>
                </thead>
                <tbody id="programasTableBody">
                    <!-- Data will be loaded here -->
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-container">
                <div class="pagination-info">
                    <p>Mostrando <span id="showingFrom">1</span> a <span id="showingTo">5</span> de <span id="totalRecords">0</span> resultados</p>
                </div>
                <nav class="pagination">
                    <button class="pagination-btn" id="prevBtn">
                        <ion-icon src="../../assets/ionicons/chevron-back-outline.svg"></ion-icon>
                    </button>
                    <div id="paginationNumbers"></div>
                    <button class="pagination-btn" id="nextBtn">
                        <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</main>

<!-- Delete Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar Eliminación</h3>
            <button class="modal-close" onclick="closeDeleteModal()">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <div class="modal-body">
            <p>¿Está seguro que desea eliminar el programa <strong id="programaToDelete"></strong>?</p>
            <p class="text-sm text-gray-600">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
            <button class="btn-danger" id="confirmDeleteBtn">Eliminar</button>
        </div>
    </div>
</div>

<script src="../../assets/js/programa/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>