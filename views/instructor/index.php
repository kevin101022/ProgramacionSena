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
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL INSTRUCTORES</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalInstructores">0</span>
                    <span class="stat-card-desc">instructores registrados</span>
                    <p class="stat-card-context">Talento humano calificado para procesos de aprendizaje de alta calidad.</p>
                </div>
                <div class="stat-card-pill-container">
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/ribbon-outline.svg"></ion-icon>
                        Certificados
                    </div>
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/person-add-outline.svg"></ion-icon>
                        Vinculados
                    </div>
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
                        <th class="w-10">N°</th>
                        <th>Instructor</th>
                        <th>Contacto</th>
                        <th>Centro de Formación</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="instructorTableBody">
                    <tr>
                        <td colspan="6" class="text-center py-8">Cargando instructores...</td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination-container border-t border-slate-50 p-6 flex flex-wrap items-center justify-between gap-4">
                <div class="pagination-info">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider">
                        Mostrando <span id="showingFrom" class="text-sena-green font-black">0</span> a <span id="showingTo" class="text-sena-green font-black">0</span> de <span id="totalRecords" class="text-sena-green font-black">0</span> instructores
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

<script src="../../assets/js/instructor/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>