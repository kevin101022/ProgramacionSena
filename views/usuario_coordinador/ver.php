<?php
$pageTitle = 'Perfil de Coordinador - SENA';
$activeNavItem = 'usuarios_coordinadores';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="index.php">Coordinadores</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Perfil de Funcionario</span>
            </nav>
            <h1 class="page-title">Detalle del Coordinador</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Regresar
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <div id="loadingState" class="bg-white rounded-2xl shadow-sm p-16 text-center border border-slate-100">
            <div class="w-12 h-12 border-4 border-sena-green border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
            <p class="text-slate-400 font-bold italic tracking-wide">Recuperando perfil del funcionario...</p>
        </div>

        <div id="coordinadorDetails" class="grid grid-cols-1 lg:grid-cols-3 gap-8" style="display: none;">
            <!-- Profile Column -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-100 sticky top-24">
                    <div class="h-40 bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center relative shadow-inner">
                        <div class="absolute inset-0 opacity-20 bg-grid-slate-200"></div>
                        <div class="w-24 h-24 bg-white rounded-2xl shadow-xl flex items-center justify-center relative z-10 border-4 border-white overflow-hidden group">
                            <ion-icon src="../../assets/ionicons/person-circle-outline.svg" class="text-6xl text-slate-200 group-hover:scale-110 transition-transform"></ion-icon>
                        </div>
                    </div>
                    <div class="p-8 text-center pt-10">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">IDENTIFICACIÓN OFICIAL</p>
                        <h2 class="text-2xl font-black text-slate-800 mb-1" id="detNombre">--</h2>
                        <span class="text-xs font-bold text-sena-green bg-green-50 px-4 py-1.5 rounded-full inline-block mt-2 border border-green-100 shadow-sm" id="detDocumento">--</span>

                        <div class="mt-12 pt-8 border-t border-slate-50 grid grid-cols-1 gap-4">
                            <button id="editBtn" class="btn-primary w-full justify-center py-3.5 shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon>
                                Editar Funcionario
                            </button>
                            <button id="toggleBtn" class="w-full flex items-center justify-center gap-2 py-3 text-xs font-black transition-all rounded-xl border border-slate-100 hover:bg-slate-50">
                                <ion-icon src="../../assets/ionicons/power-outline.svg" id="toggleIcon"></ion-icon>
                                <span id="toggleText">Gestionar Acceso</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Info Card -->
                <div class="bg-white p-10 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden h-full">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-sena-green/5 rounded-bl-full"></div>

                    <h3 class="text-xl font-black text-slate-800 mb-10 flex items-center gap-4 relative z-10">
                        <div class="w-10 h-10 bg-green-50 text-sena-green rounded-xl flex items-center justify-center shadow-sm">
                            <ion-icon src="../../assets/ionicons/information-circle-outline.svg"></ion-icon>
                        </div>
                        Información Administrativa
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 relative z-10">
                        <div class="space-y-8">
                            <div class="group">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 group-hover:text-sena-green transition-colors">Datos de Contacto</p>
                                <div class="flex items-center gap-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl group-hover:bg-white group-hover:shadow-md transition-all">
                                    <div class="w-8 h-8 bg-white text-sena-green rounded-lg flex items-center justify-center border border-slate-100 shadow-sm">
                                        <ion-icon src="../../assets/ionicons/mail-outline.svg"></ion-icon>
                                    </div>
                                    <span class="text-sm font-bold text-slate-600 truncate" id="infoCorreo">--</span>
                                </div>
                            </div>

                            <div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Estado del Perfil</p>
                                <div id="infoEstado" class="inline-block">
                                    <!-- Dynamic badge -->
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 flex flex-col items-center justify-center text-center space-y-4 hover:shadow-inner transition-all">
                            <div class="w-20 h-20 bg-white shadow-md rounded-2xl flex items-center justify-center mb-2" id="iconAsignacionContainer">
                                <ion-icon src="../../assets/ionicons/briefcase-outline.svg" class="text-4xl text-slate-200"></ion-icon>
                            </div>
                            <div class="px-4">
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Dependencia a Cargo</p>
                                <h4 class="text-lg font-black text-slate-800 leading-tight" id="infoCargo">Sin asignación</h4>
                                <p class="text-xs mt-3 text-slate-600 font-black leading-relaxed max-w-[200px] mx-auto" id="infoCargoDesc">El funcionario no lidera ninguna área de coordinación en este momento.</p>
                            </div>

                            <div id="ctaAsignar" style="display: none;" class="mt-4 pt-4 border-t border-slate-200 w-full">
                                <a href="../coordinacion/index.php" class="text-[10px] font-black text-sena-green hover:underline tracking-widest uppercase">IR A ASIGNAR AREA</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="errorState" class="bg-white rounded-3xl shadow-sm p-20 text-center border border-slate-100" style="display: none;">
            <div class="w-24 h-24 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-5xl"></ion-icon>
            </div>
            <h3 class="text-3xl font-black text-slate-900 mb-4 tracking-tighter">Funcionario no encontrado</h3>
            <p id="errorMessage" class="text-slate-500 max-w-sm mx-auto font-medium">La cuenta que intenta visualizar no existe o los permisos son insuficientes.</p>
            <div class="mt-10">
                <a href="index.php" class="btn-primary inline-flex px-10 py-3 rounded-2xl">Volver al Directorio</a>
            </div>
        </div>
    </div>
</main>

<?php require_once 'modal_edit.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../assets/js/usuario_coordinador/index.js?v=<?php echo time(); ?>"></script>
<script src="../../assets/js/usuario_coordinador/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>