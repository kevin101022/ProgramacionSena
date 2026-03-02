<?php
$pageTitle = "Directorio de Coordinadores - SENA";
$activeNavItem = 'usuarios_coordinadores';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../dashboard/index.php">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Gestión Humana</span>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span class="font-black">Coordinadores</span>
            </nav>
            <h1 class="page-title font-black">Personal de Coordinación</h1>
        </div>
        <div class="header-actions">
            <!-- Optional actions -->
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Stats Grid -->
        <div class="stats-grid grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/person-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label font-black text-slate-500">FUNCIONARIOS ACTIVOS</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/person-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number font-black text-slate-900" id="totalActivos">0</span>
                    <span class="stat-card-desc font-black text-sena-green uppercase tracking-tighter">habilitados</span>
                    <p class="stat-card-context font-bold text-slate-600">Personal con facultades vigentes para la planeación y ejecución académica.</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/person-remove-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label font-black text-slate-500">CUENTAS INACTIVAS</span>
                    <div class="stat-card-icon orange">
                        <ion-icon src="../../assets/ionicons/person-remove-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number font-black text-slate-400" id="totalInactivos">0</span>
                    <span class="stat-card-desc font-black text-slate-400 uppercase tracking-tighter">deshabilitados</span>
                    <p class="stat-card-context font-bold text-slate-600">Funcionarios sin acceso temporal o permanente al sistema de gestión.</p>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar bg-white/50 backdrop-blur-sm border border-slate-100 p-4 rounded-3xl flex flex-wrap items-center gap-4 mb-6 shadow-sm">
            <div class="search-container flex-1 bg-white border border-slate-200 rounded-2xl px-4 py-2.5 flex items-center gap-3 focus-within:ring-2 focus-within:ring-sena-green/20 focus-within:border-sena-green transition-all">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="text-slate-400 text-lg"></ion-icon>
                <input type="text" id="searchInput" placeholder="Buscar por cédula o nombre completo..." class="w-full text-sm font-black outline-none text-slate-700 placeholder:text-slate-400">
            </div>

            <div class="relative min-w-[200px]">
                <ion-icon src="../../assets/ionicons/filter-outline.svg" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 z-10"></ion-icon>
                <select id="estadoFilter" class="w-full pl-11 pr-10 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs font-black text-slate-600 appearance-none cursor-pointer focus:ring-2 focus:ring-sena-green/20 outline-none transition-all hover:border-slate-300">
                    <option value="">TODOS LOS ESTADOS</option>
                    <option value="1" selected>SOLO ACTIVOS</option>
                    <option value="0">SOLO INACTIVOS</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <ion-icon src="../../assets/ionicons/chevron-down-outline.svg" class="text-slate-400 text-xs"></ion-icon>
                </div>
            </div>

            <button onclick="usuarioCoordinadorModule.openModal()" class="btn-primary shadow-md hover:shadow-lg transition-all scale-100 active:scale-95">
                <ion-icon src="../../assets/ionicons/add-circle-outline.svg"></ion-icon>
                <span>Nuevo Coordinador</span>
            </button>
        </div>

        <!-- Data Table -->
        <div class="table-container bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-100">
            <table class="data-table w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="pl-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-widest w-[25%]">Documento / ID</th>
                        <th class="py-5 text-[10px] font-black text-slate-500 uppercase tracking-widest w-[45%]">Nombre del Coordinador</th>
                        <th class="py-5 text-[10px] font-black text-slate-500 uppercase tracking-widest w-[30%]">Contacto</th>
                    </tr>
                </thead>
                <tbody id="usuariosTableBody" class="divide-y divide-slate-50">
                    <tr>
                        <td colspan="3" class="text-center py-20">
                            <div class="animate-pulse flex flex-col items-center">
                                <div class="w-10 h-10 border-4 border-slate-100 border-t-sena-green rounded-full animate-spin"></div>
                                <p class="text-sm text-slate-400 font-black mt-4 uppercase tracking-tighter">Sincronizando directorio...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-container border-t border-slate-50 p-6 flex flex-wrap items-center justify-between gap-4">
                <div class="pagination-info">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-wider">
                        Mostrando <span id="showingFrom" class="text-sena-green font-black">0</span> a <span id="showingTo" class="text-sena-green font-black">0</span> de <span id="totalRecords" class="text-sena-green font-black">0</span> funcionarios
                    </p>
                </div>
                <nav class="pagination flex items-center gap-2">
                    <button class="pagination-btn w-10 h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-400 hover:border-sena-green hover:text-sena-green disabled:opacity-30 disabled:pointer-events-none transition-all" id="prevBtn">
                        <ion-icon src="../../assets/ionicons/chevron-back-outline.svg"></ion-icon>
                    </button>
                    <div id="paginationNumbers" class="flex items-center gap-1">
                        <!-- Numbers loaded by JS -->
                    </div>
                    <button class="pagination-btn w-10 h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-400 hover:border-sena-green hover:text-sena-green disabled:opacity-30 disabled:pointer-events-none transition-all" id="nextBtn">
                        <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</main>

<!-- Modales -->
<?php include 'modal_crear.php'; ?>
<?php include 'modal_edit.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../assets/js/usuario_coordinador/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>