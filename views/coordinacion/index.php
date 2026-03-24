<?php
$pageTitle = 'Áreas de Coordinación - SENA';
$activeNavItem = 'coordinaciones';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../dashboard/index.php">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Coordinaciones</span>
            </nav>
            <h1 class="page-title font-black">Áreas de Coordinación</h1>
        </div>
        <div class="header-actions">
            <!-- Actions can be added here -->
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Stats Card -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label font-black text-slate-500">TOTAL DE ÁREAS</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number font-black text-slate-900" id="totalCoordinaciones">0</span>
                    <span class="stat-card-desc font-black text-slate-500">áreas registradas</span>
                    <p class="stat-card-context font-bold text-slate-600">Células administrativas encargadas del control académico y la gestión de programas regionales.</p>
                </div>
                <div class="stat-card-pill-container border-t border-slate-50 pt-4 mt-4">
                    <div class="stat-pill font-black text-[10px] text-sena-green bg-green-50 border border-green-100">
                        <ion-icon src="../../assets/ionicons/checkmark-circle-outline.svg"></ion-icon>
                        Operativas
                    </div>
                    <div class="stat-pill font-black text-[10px] text-blue-600 bg-blue-50 border border-blue-100">
                        <ion-icon src="../../assets/ionicons/people-outline.svg"></ion-icon>
                        Gestión Humana
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar bg-white/50 backdrop-blur-sm border border-slate-100 p-4 xl:px-6 rounded-3xl flex flex-col md:flex-row items-stretch md:items-center gap-4 mb-6 shadow-sm">
            <div class="search-container flex-1 bg-white border border-slate-200 rounded-2xl px-4 py-2.5 flex items-center gap-3 focus-within:ring-2 focus-within:ring-sena-green/20 focus-within:border-sena-green transition-all w-full md:w-auto">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="text-slate-400 text-lg"></ion-icon>
                <input type="text" id="searchInput" placeholder="Buscar por nombre de área o centro..." class="w-full text-sm font-black outline-none text-slate-700 placeholder:text-slate-400">
            </div>

            <button onclick="coordinacionModule.openModal()" class="btn-primary shadow-md hover:shadow-lg transition-all scale-100 active:scale-95 w-full md:w-auto flex justify-center">
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                <span>Registrar Área</span>
            </button>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10">N°</th>
                        <th>Nombre del Área</th>
                        <th>Centro de Formación</th>
                        <th class="text-right w-15">Acciones</th>
                    </tr>
                </thead>
                <tbody id="coordinacionTableBody">
                    <tr>
                        <td colspan="4" class="text-center py-20">
                            <div class="animate-pulse flex flex-col items-center">
                                <div class="w-10 h-10 border-4 border-slate-100 border-t-sena-green rounded-full animate-spin"></div>
                                <p class="text-sm text-slate-400 font-black mt-4 uppercase tracking-tighter">Sincronizando información...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-container border-t border-slate-50 p-4 md:p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="pagination-info text-center sm:text-left w-full sm:w-auto">
                    <p class="text-[10px] md:text-xs font-black text-slate-500 uppercase tracking-wider">
                        Mostrando <span id="showingFrom" class="text-sena-green">0</span> a <span id="showingTo" class="text-sena-green">0</span> de <span id="totalRecords" class="text-sena-green font-black">0</span>
                    </p>
                </div>
                <nav class="pagination flex items-center justify-center gap-2 w-full sm:w-auto overflow-x-auto pb-2 sm:pb-0">
                    <button class="pagination-btn flex-shrink-0 w-8 h-8 md:w-10 md:h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-400 hover:border-sena-green hover:text-sena-green disabled:opacity-30 disabled:pointer-events-none transition-all" id="prevBtn">
                        <ion-icon src="../../assets/ionicons/chevron-back-outline.svg"></ion-icon>
                    </button>
                    <div id="paginationNumbers" class="flex flex-nowrap items-center gap-1">
                        <!-- Numbers loaded by JS -->
                    </div>
                    <button class="pagination-btn flex-shrink-0 w-8 h-8 md:w-10 md:h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-400 hover:border-sena-green hover:text-sena-green disabled:opacity-30 disabled:pointer-events-none transition-all" id="nextBtn">
                        <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</main>

<!-- Create/Edit Modal -->
<?php require_once 'modal_edit.php'; ?>

<script src="../../assets/js/coordinacion/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>