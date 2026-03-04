<?php
$pageTitle = "Mis Fichas (Líder) - SENA";
$activeNavItem = 'mis_fichas';
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
                <span>Mis Fichas Líder</span>
            </nav>
            <h1 class="page-title">Mis Fichas a cargo</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Volver
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="action-bar mb-6">
            <div class="flex gap-4 items-center flex-1">
                <div class="search-container flex-1">
                    <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                    <input type="text" id="searchInput" placeholder="Buscar ficha o programa..." class="search-input">
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10 text-center">N°</th>
                        <th>N° Ficha</th>
                        <th>Programa</th>
                        <th>Jornada</th>
                        <th>Coordinación</th>
                        <th>Sede / Centro</th>
                    </tr>
                </thead>
                <tbody id="fichasTableBody">
                    <tr>
                        <td colspan="6" class="text-center py-8">Cargando fichas...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>
<script>
    window.USER_ID = <?php echo json_encode($_SESSION['numero_documento'] ?? current(explode('@', $_SESSION['correo'] ?? '')) ?: $_SESSION['id'] ?? null); ?>;
</script>
<script src="../../assets/js/instructor/fichas.js?v=<?php echo time(); ?>"></script>
</body>

</html>