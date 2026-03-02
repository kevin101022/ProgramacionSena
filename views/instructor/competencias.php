<?php
$pageTitle = "Mis Competencias - SENA";
$activeNavItem = 'mis_competencias';
require_once '../layouts/head.php';

// Enforce role
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'instructor') {
    header("Location: ../../routing.php?controller=login&action=showLogin");
    exit;
}

require_once '../layouts/instructor_sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Instructor</span>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Mis Competencias</span>
            </nav>
            <h1 class="page-title">Mis Competencias</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="action-bar mb-6">
            <div class="flex gap-4 items-center flex-1">
                <div class="search-container flex-1">
                    <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                    <input type="text" id="searchInput" placeholder="Buscar competencia..." class="search-input">
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10 text-center">N°</th>
                        <th>Competencia</th>
                        <th>Descripción</th>
                        <th>Programas Habilitados</th>

                    </tr>
                </thead>
                <tbody id="competenciasTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-8">Cargando competencias...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal de Detalles de Competencia -->
<div id="compModal" class="modal">
    <div class="modal-content max-w-2xl">
        <div class="modal-header border-b pb-4 mb-4 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-slate-800" id="modalCompNombre">Detalles de Competencia</h2>
                <p class="text-xs text-slate-400 font-mono mt-1" id="modalCompId">ID: --</p>
            </div>
            <button id="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                <ion-icon name="close-outline" class="text-2xl"></ion-icon>
            </button>
        </div>
        <div class="modal-body space-y-6">
            <!-- Descripción -->
            <div>
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <ion-icon name="information-circle-outline" class="text-sena-green"></ion-icon>
                    Descripción
                </h3>
                <div class="p-4 bg-slate-50 rounded-xl text-sm text-slate-600 leading-relaxed border border-slate-100" id="modalCompDesc">
                    --
                </div>
            </div>

            <!-- Programas -->
            <div>
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <ion-icon name="school-outline" class="text-sena-green"></ion-icon>
                    Programas Habilitados
                </h3>
                <div id="modalProgsList" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Dinámico -->
                </div>
            </div>
        </div>
        <div class="modal-footer mt-8 pt-4 border-t flex justify-end">
            <button id="closeModalBtn" class="btn-primary">Entendido</button>
        </div>
    </div>
</div>
<!-- Pass session variable to JS -->
<script>
    // El numero_documento del instructor logueado, guardado en sesión como 'id'
    window.USER_ID = <?php echo json_encode($_SESSION['id'] ?? null); ?>;
</script>
<script src="../../assets/js/instructor/competencias.js?v=<?php echo time(); ?>"></script>
</body>

</html>