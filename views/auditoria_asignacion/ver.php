<?php
$pageTitle = 'Detalle de Auditoría - SENA';
$activeNavItem = 'auditoria_asignacion';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="index.php">Auditoría</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Detalle de Registro</span>
            </nav>
            <h1 class="page-title">Detalle de Actividad</h1>
        </div>
        <div class="header-actions">
            <a href="index.php" class="btn-secondary">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Regresar al Historial
            </a>
        </div>
    </header>

    <div class="content-wrapper">
        <div id="loadingState" class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100">
            <div class="w-10 h-10 border-4 border-sena-green border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-gray-500 font-medium">Recuperando trazabilidad del sistema...</p>
        </div>

        <div id="auditDetails" class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="display: none;">
            <!-- Left Card: Status & Meta -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 sticky top-24">
                    <div class="h-32 flex items-center justify-center relative shadow-inner" id="actionHeaderBg">
                        <div class="absolute inset-0 opacity-10 bg-grid-slate-200"></div>
                        <ion-icon id="actionIcon" src="../../assets/ionicons/receipt-outline.svg" class="text-7xl relative z-10"></ion-icon>
                    </div>
                    <div class="p-8 text-center">
                        <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em] mb-2">TIPO DE OPERACIÓN</p>
                        <h2 class="text-2xl font-black mb-1 flex items-center justify-center gap-2" id="detActionType">
                            --
                        </h2>
                        <div class="inline-flex items-center gap-1 text-xs text-gray-500 font-medium bg-gray-50 px-3 py-1 rounded-full mt-2" id="detActionDate">
                            <ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon>
                            <span>Fecha y Hora</span>
                        </div>

                        <div class="mt-10 pt-8 border-t border-gray-50 grid grid-cols-1 gap-4">
                            <div class="text-left">
                                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mb-1">ID DE RASTREO</p>
                                <p class="text-sm font-mono font-bold text-gray-700 bg-gray-50 p-2 rounded-lg border border-gray-100" id="detAuditId">--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content: Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Card -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-50 text-sena-green rounded-lg flex items-center justify-center">
                            <ion-icon src="../../assets/ionicons/person-outline.svg"></ion-icon>
                        </div>
                        Autor del Cambio
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">NOMBRE / CORREO</p>
                            <p class="text-base font-bold text-gray-900" id="detUserEmail">--</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">DOCUMENTO IDENTIDAD</p>
                            <p class="text-base font-bold text-slate-600" id="detUserDoc">--</p>
                        </div>
                    </div>
                </div>

                <!-- Technical Details -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-8 flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center">
                            <ion-icon src="../../assets/ionicons/layers-outline.svg"></ion-icon>
                        </div>
                        Recursos Afectados
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 relative overflow-hidden group">
                            <div class="relative z-10">
                                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">INSTRUCTOR</p>
                                <p class="text-lg font-black text-gray-900" id="detInstructorName">--</p>
                                <p class="text-xs font-bold text-sena-green mt-1" id="detInstructorDoc">CC: --</p>
                            </div>
                            <ion-icon src="../../assets/ionicons/school-outline.svg" class="absolute -right-4 -bottom-4 text-8xl text-slate-200/50 transform -rotate-12"></ion-icon>
                        </div>
                        <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 relative overflow-hidden group">
                            <div class="relative z-10">
                                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-2">COMPETENCIA</p>
                                <p class="text-sm font-bold text-gray-800 leading-tight" id="detCompetence">--</p>
                                <p class="text-xs font-medium text-blue-500 mt-2" id="detFicha">Ficha: --</p>
                            </div>
                            <ion-icon src="../../assets/ionicons/bookmark-outline.svg" class="absolute -right-4 -bottom-4 text-8xl text-slate-200/50 transform rotate-12"></ion-icon>
                        </div>
                    </div>

                    <!-- Timeline/Period -->
                    <div class="mb-8 p-6 bg-sena-green/[0.03] rounded-2xl border border-sena-green/10">
                        <p class="text-[10px] font-extrabold text-sena-green uppercase tracking-[0.2em] mb-4">VIGENCIA DE LA ASIGNACIÓN</p>
                        <div class="flex items-center gap-8">
                            <div class="flex-1 text-center">
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Inicia el</p>
                                <p class="text-base font-black text-gray-800" id="detDateStart">--</p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center text-sena-green scale-110">
                                <ion-icon src="../../assets/ionicons/calendar-outline.svg" class="text-xl"></ion-icon>
                            </div>
                            <div class="flex-1 text-center">
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Finaliza el</p>
                                <p class="text-base font-black text-gray-800" id="detDateEnd">--</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-50">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                                <ion-icon src="../../assets/ionicons/business-outline.svg" class="text-xl"></ion-icon>
                            </div>
                            <div>
                                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Ambiente / Aula</p>
                                <p class="text-sm font-bold text-gray-800" id="detAmbiente">--</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                                <ion-icon src="../../assets/ionicons/business-outline.svg" class="text-xl"></ion-icon>
                            </div>
                            <div>
                                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Coordinación / Área</p>
                                <p class="text-sm font-bold text-gray-800" id="detAreaName">--</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                                <ion-icon src="../../assets/ionicons/key-outline.svg" class="text-xl"></ion-icon>
                            </div>
                            <div>
                                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">ID Interno (Asignación)</p>
                                <p class="text-sm font-bold text-gray-800" id="detAsigId">--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="errorState" class="bg-white rounded-2xl shadow-sm p-16 text-center border border-gray-100" style="display: none;">
            <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" class="text-4xl"></ion-icon>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">Error de Trazabilidad</h3>
            <p id="errorMessage" class="text-gray-500 max-w-sm mx-auto">No pudimos recuperar la información histórica de este registro. Puede que ya no exista en el sistema.</p>
            <div class="mt-8">
                <a href="index.php" class="btn-primary inline-flex">Volver al Historial</a>
            </div>
        </div>
    </div>
</main>

<script src="../../assets/js/auditoria_asignacion/ver.js?v=<?php echo time(); ?>"></script>
</body>

</html>