<?php
$pageTitle = "Mis Competencias - SENA";
$activeNavItem = 'mis_competencias';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
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
                        <th>Programa Asociado</th>
                        <th class="text-center">Vigencia</th>
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
<!-- Pass session variable to JS -->
<script>
    window.USER_ID = <?php echo json_encode($_SESSION['numero_documento'] ?? current(explode('@', $_SESSION['correo'] ?? '')) ?: $_SESSION['id'] ?? null); ?>;
</script>
<script src="../../assets/js/instructor/competencias.js?v=<?php echo time(); ?>"></script>
</body>

</html>