<?php
$pageTitle = "Detalles de Horario - SENA";
$activeNavItem = 'asignaciones';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';

$asig_id = $_GET['id'] ?? null;
if (!$asig_id) {
    header('Location: index.php');
    exit;
}
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="index.php">Asignaciones</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Horarios de Asignación</span>
            </nav>
            <h1 class="page-title">Horarios del Periodo #<?php echo str_pad($asig_id, 3, '0', STR_PAD_LEFT); ?></h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="info-card mb-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 italic">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase">Instructor</span>
                    <span id="infoInstructor" class="text-gray-900 font-semibold">Cargando...</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase">Ficha</span>
                    <span id="infoFicha" class="text-gray-900 font-semibold">Cargando...</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase">Ambiente</span>
                    <span id="infoAmbiente" class="text-gray-900 font-semibold">Cargando...</span>
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase">Periodo</span>
                    <span id="infoPeriodo" class="text-gray-900 font-semibold">Cargando...</span>
                </div>
            </div>
        </div>

        <div class="action-bar">
            <h2 class="text-lg font-bold text-gray-800">Franjas Horarias</h2>
            <button id="addDetailBtn" class="btn-primary">
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                Nueva Franja
            </button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="detalleTableBody">
                    <tr>
                        <td colspan="3" class="text-center py-8">Cargando franjas...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Create/Edit Detail Modal -->
<div id="detalleModal" class="modal">
    <div class="modal-content" style="max-width: 450px;">
        <div class="modal-header">
            <h3 id="modalTitle">Nueva Franja Horaria</h3>
            <button class="modal-close" id="closeModal">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="detalleForm">
            <input type="hidden" id="detasig_id" name="detasig_id">
            <input type="hidden" id="asig_id_input" name="asignacion_asig_id" value="<?php echo $asig_id; ?>">
            <div class="modal-body p-6 space-y-4">
                <div class="form-group">
                    <label class="form-label">Hora Inicio <span class="text-red-500">*</span></label>
                    <input type="time" id="detasig_hora_ini" name="detasig_hora_ini" required class="search-input" style="padding-left: 12px !important;">
                </div>
                <div class="form-group">
                    <label class="form-label">Hora Fin <span class="text-red-500">*</span></label>
                    <input type="time" id="detasig_hora_fin" name="detasig_hora_fin" required class="search-input" style="padding-left: 12px !important;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="cancelBtn">Cancelar</button>
                <button type="submit" class="btn-primary" id="saveBtn">
                    <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon>
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script src="../../assets/js/asignacion/detalles.js"></script>
</body>

</html>