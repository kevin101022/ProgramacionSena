<?php
$pageTitle = "Gestión de Instructores - SENA";
$activeNavItem = 'instructores';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Instructores</span>
            </nav>
            <h1 class="page-title">Gestión de Instructores</h1>
        </div>
        <div class="header-actions"></div>
    </header>

    <div class="content-wrapper">
        <!-- Stats Card with SVG Footer Background -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL INSTRUCTORES</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalInstructores">0</span>
                    <span class="stat-card-desc">instructores registrados</span>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="flex gap-4 items-center flex-1">
                <div class="search-container flex-1">
                    <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                    <input type="text" id="searchInput" placeholder="Buscar por nombre, apellido o correo..." class="search-input">
                </div>
                <div class="filter-container">
                    <select id="sedeFilter" class="search-input" style="padding-left: 12px !important; width: 240px;">
                        <option value="">Todos los Centros</option>
                    </select>
                </div>
            </div>

            <a href="crear.php" class="btn-primary">
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                Registrar Instructor
            </a>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-20">N°</th>
                        <th>Instructor</th>
                        <th>Contacto</th>
                        <th>Centro de Formación</th>
                    </tr>
                </thead>
                <tbody id="instructorTableBody">
                    <tr>
                        <td colspan="6" class="text-center py-8">Cargando instructores...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="../../assets/js/instructor/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>