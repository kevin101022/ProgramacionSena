<?php
$pageTitle = 'Detalle de Coordinación - SENA';
$activeNavItem = 'coordinaciones';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="index.php">Coordinaciones</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Detalle de Área</span>
            </nav>
            <h1 class="page-title">Gestión de Coordinación</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Regresar
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <div id="loadingState" class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100">
            <div class="w-10 h-10 border-4 border-sena-green border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-500 font-medium italic">Sincronizando información técnica...</p>
        </div>

        <div id="coordinacionDetails" class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="display: none;">
            <!-- Left Card: Status & Meta -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 sticky top-24">
                    <div class="h-32 bg-slate-50 flex items-center justify-center relative shadow-inner">
                        <div class="absolute inset-0 opacity-10 bg-grid-slate-200"></div>
                        <ion-icon src="../../assets/ionicons/people-circle-outline.svg" class="text-7xl text-slate-200 relative z-10"></ion-icon>
                    </div>
                    <div class="p-8 text-center">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">NOMBRE DEL ÁREA</p>
                        <h2 class="text-2xl font-black text-sena-green mb-1" id="detCoordNombre">--</h2>
                        <div class="inline-flex items-center gap-1 text-[10px] text-slate-600 font-black bg-slate-50 border border-slate-100 px-3 py-1.5 rounded-full mt-2" id="detCentroPertenece">
                            <ion-icon src="../../assets/ionicons/business-outline.svg"></ion-icon>
                            <span>Centro de Formación</span>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-50 grid grid-cols-1 gap-4">
                            <button id="editBtn" class="btn-primary w-full justify-center py-3 shadow-md hover:shadow-lg transition-all scale-100 hover:scale-[1.02]">
                                <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                                Editar Datos
                            </button>
                            <button id="deleteBtn" class="text-xs font-bold text-red-400 hover:text-red-600 transition-colors flex items-center justify-center gap-2 mt-2">
                                <ion-icon src="../../assets/ionicons/link-outline.svg"></ion-icon>
                                Desvincular Coordinador
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content: Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Responsible Info -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-green-50 rounded-bl-full opacity-30"></div>
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3 relative z-10">
                        <div class="w-8 h-8 bg-green-50 text-sena-green rounded-lg flex items-center justify-center">
                            <ion-icon src="../../assets/ionicons/person-circle-outline.svg"></ion-icon>
                        </div>
                        Líder de Coordinación
                    </h3>

                    <div id="coordinadorInfo" class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nombre Completo</p>
                                <p class="text-base font-black text-slate-800" id="detCoordNombreCoordinador">--</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Documento de Identidad</p>
                                <p class="text-sm font-black text-slate-600" id="detCoordDoc">--</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Correo Electrónico</p>
                                <div class="flex items-center gap-2">
                                    <ion-icon src="../../assets/ionicons/mail-outline.svg" class="text-sena-green"></ion-icon>
                                    <p class="text-sm font-black text-sena-green" id="detCoordCorreo">--</p>
                                </div>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 inline-block">
                                <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Estado de Cargo</span>
                                <span class="badge badge-green text-[10px]">ASIGNADO</span>
                            </div>
                        </div>
                    </div>

                    <div id="vacanteState" style="display: none;" class="py-10 text-center relative z-10">
                        <div class="w-16 h-16 bg-sena-orange/10 text-sena-orange rounded-full flex items-center justify-center mx-auto mb-4">
                            <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-3xl"></ion-icon>
                        </div>
                        <h4 class="text-xl font-black text-slate-800 mb-1">Área sin Coordinador</h4>
                        <p class="text-sm text-slate-500 max-w-xs mx-auto">Esta coordinación no tiene un responsable asignado actualmente.</p>
                        <button onclick="document.getElementById('editBtn').click()" class="mt-6 text-xs font-black text-sena-green hover:underline">ASIGNAR AHORA</button>
                    </div>
                </div>

                <!-- Academic Structure -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center">
                                <ion-icon src="../../assets/ionicons/school-outline.svg"></ion-icon>
                            </div>
                            Programas Vinculados
                        </h3>
                        <div class="bg-blue-50 text-blue-600 px-4 py-1.5 rounded-full text-xs font-black flex items-center gap-2">
                            <span id="countProgramas">0</span>
                            <span class="opacity-50 tracking-tighter uppercase">PROGS</span>
                        </div>
                    </div>

                    <div id="programasList" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Dynamic content -->
                    </div>

                    <div id="noProgramas" class="hidden py-16 text-center">
                        <div class="w-20 h-20 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <ion-icon src="../../assets/ionicons/bookmarks-outline.svg" class="text-4xl"></ion-icon>
                        </div>
                        <p class="text-slate-400 font-medium italic">No se detectaron programas asociados a esta coordinación.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="errorState" class="bg-white rounded-2xl shadow-sm p-16 text-center border border-gray-100" style="display: none;">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-4xl"></ion-icon>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">Área no encontrada</h3>
            <p id="errorMessage" class="text-gray-500 max-w-sm mx-auto">La coordinación solicitada no existe o ha sido dada de baja del sistema.</p>
            <div class="mt-8">
                <a href="index.php" class="btn-primary inline-flex">Volver al Listado</a>
            </div>
        </div>
    </div>
</main>

<?php require_once 'modal_edit.php'; ?>
<script src="../../assets/js/coordinacion/index.js?v=<?php echo time(); ?>"></script>
<script src="../../assets/js/coordinacion/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>