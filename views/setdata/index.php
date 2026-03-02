<?php
$pageTitle = "Sincronización SetData - SENA";
$activeNavItem = 'setdata';
require_once '../layouts/head.php';

// Enforzar rol coordinador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'coordinador') {
    header("Location: ../dashboard/index.php");
    exit;
}

require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="../dashboard/index.php">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Coordinador</span>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>SetData (CSV)</span>
            </nav>
            <h1 class="page-title">Sincronización de Datos Externos</h1>
            <p class="text-slate-500 mt-1">Sube un archivo CSV (exportación FET) para visualizar reportes y estadísticas.</p>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Zona de Carga -->
        <div class="glass-container p-8 mb-8 text-center border-2 border-dashed border-slate-200 hover:border-[#39A900] transition-colors cursor-pointer" id="dropZone">
            <input type="file" id="csvFileInput" accept=".csv,.txt" class="hidden">
            <div class="flex flex-col items-center gap-4">
                <div class="w-16 h-16 bg-[#39A900]/10 rounded-full flex items-center justify-center text-[#39A900]">
                    <ion-icon name="cloud-upload-outline" style="font-size: 32px;"></ion-icon>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Arrastra tu archivo CSV aquí</h3>
                    <p class="text-sm text-slate-500">O haz clic para seleccionar un archivo desde tu computadora</p>
                </div>
                <div class="flex gap-2 text-xs text-slate-400">
                    <span class="px-2 py-1 bg-slate-100 rounded">CSV</span>
                    <span class="px-2 py-1 bg-slate-100 rounded">TXT</span>
                    <span class="px-2 py-1 bg-slate-100 rounded">Separado por comas/puntos y coma</span>
                </div>
            </div>
        </div>

        <!-- Dashboard (Oculto inicialmente) -->
        <div id="dashboardContent" class="hidden">
            <!-- Tarjetas de Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="statsCards">
                <!-- Se llenará dinámicamente -->
            </div>

            <!-- Gráficos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8" id="chartsGrid">
                <!-- Se llenará dinámicamente -->
            </div>

            <!-- Tabla de Datos -->
            <div class="glass-container overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
                    <div class="flex items-center gap-4">
                        <h3 class="font-bold text-slate-800">Vista Previa de Datos</h3>
                        <button id="downloadBtn" class="flex items-center gap-2 px-3 py-1.5 bg-[#39A900] text-white text-xs font-bold rounded-lg hover:bg-[#2d8500] transition-colors shadow-sm">
                            <ion-icon name="document-text-outline"></ion-icon>
                            Descargar Reporte Visual (PDF)
                        </button>
                    </div>
                    <div class="search-container !w-64">
                        <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                        <input type="text" id="tableSearch" placeholder="Filtrar datos..." class="search-input">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table" id="dataTable">
                        <thead id="tableHead"></thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
                <div class="p-4 bg-slate-50 text-xs text-slate-400 text-center" id="tableFooter"></div>
            </div>
        </div>

        <!-- Estado Vacío / Guía -->
        <div id="emptyState" class="text-center py-20">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 rounded-full text-slate-400 mb-6">
                <ion-icon name="analytics-outline" style="font-size: 40px;"></ion-icon>
            </div>
            <h2 class="text-xl font-bold text-slate-400">Esperando archivo...</h2>
            <p class="text-slate-400 max-w-md mx-auto mt-2">Sube el archivo "setdata" exportado de FET para generar el tablero de control automático.</p>
        </div>
    </div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- PDF Export Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- Scripts -->
<script src="../../assets/js/setdata/index.js?v=<?php echo time(); ?>"></script>

</body>

</html>