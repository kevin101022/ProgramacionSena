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
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">TOTAL FICHAS</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalFichas">0</span>
                    <span class="stat-card-desc">fichas activas</span>
                    <p class="stat-card-context">Grupos de formación en proceso que representan la vitalidad institucional.</p>
                </div>
                <div class="stat-card-pill-container">
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                        En Formación
                    </div>
                    <div class="stat-pill">
                        <ion-icon src="../../assets/ionicons/checkmark-circle-outline.svg"></ion-icon>
                        Vigentes
                    </div>
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
                        <th class="w-24">N° Ficha</th>
                        <th>Programa</th>
                        <th>Instructor Líder</th>
                        <th>Jornada</th>
                        <th class="text-right">Progreso</th>
                    </tr>
                </thead>
                <tbody id="fichaTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-8">Cargando fichas...</td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination-container border-t border-slate-50 p-6 flex flex-wrap items-center justify-between gap-4">
                <div class="pagination-info">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider">
                        Mostrando <span id="showingFrom" class="text-sena-green font-black">0</span> a <span id="showingTo" class="text-sena-green font-black">0</span> de <span id="totalRecords" class="text-sena-green font-black">0</span> fichas
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

<!-- Create/Edit Modal -->
<?php require_once 'modal_edit.php'; ?>

<script src="../../assets/js/ficha/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>