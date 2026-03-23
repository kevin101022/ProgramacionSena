<?php
$pageTitle = "Detalle Proyecto Formativo - SENA";
$activeNavItem = 'proyecto_formativo';

if (!isset($data) && isset($_GET['id'])) {
    require_once '../../Conexion.php';
    require_once '../../model/ProyectoFormativoModel.php';
    require_once '../../model/ResultadoAprendizajeModel.php';
    $model = new ProyectoFormativoModel();
    $rapModel = new ResultadoAprendizajeModel();
    $proyecto = $model->getById($_GET['id']);
    // Usar la nueva jerarquía de 3 niveles
    $jerarquia = $rapModel->getHierarchyByProyecto($_GET['id']);
} else {
    $proyecto = $data ?? [];
    $jerarquia = [];
}

if (!$proyecto) {
    die("Proyecto no encontrado");
}

require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <a href="index.php">Proyectos Formativos</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Detalle Estructural</span>
            </nav>
            <h1 class="page-title">Estructura del Proyecto Formativo</h1>
        </div>
        <div class="header-actions" style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="index.php" class="btn-secondary" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <ion-icon src="../../assets/ionicons/arrow-back-outline.svg"></ion-icon>
                Volver
            </a>
            <button onclick='abrirModalEditarProyecto(<?php echo htmlspecialchars(json_encode($proyecto), ENT_QUOTES); ?>)' class="btn-secondary" style="display: flex; align-items: center; gap: 0.5rem; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">
                <ion-icon src="../../assets/ionicons/create-outline.svg"></ion-icon> Editar Proyecto
            </button>
            <button onclick="confirmarEliminarProyecto(<?php echo $proyecto['pf_id']; ?>)" class="btn-secondary" style="display: flex; align-items: center; gap: 0.5rem; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">
                <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon> Eliminar 
            </button>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Información General del Proyecto -->
        <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; margin-bottom: 2rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.75rem;">
               <h2 style="font-size: 1.25rem; font-weight: bold; color: #111827; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <div style="background: #d1fae5; color: #059669; padding: 0.4rem; border-radius: 0.5rem; display: flex;">
                        <ion-icon src="../../assets/ionicons/folder-open-outline.svg"></ion-icon>
                    </div>
                    <?php echo htmlspecialchars($proyecto['pf_codigo'] . ' - ' . $proyecto['pf_nombre']); ?>
                </h2>
                <span style="background: #e0e7ff; color: #4338ca; padding: 4px 12px; border-radius: 999px; font-size: 0.85rem; font-weight: bold;">
                    ID: <?php echo htmlspecialchars($proyecto['pf_id']); ?>
                </span>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="grid-column: span 2;">
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Programa de Formación</span>
                    <p style="color: #374151; margin: 0; font-size: 0.95rem; font-weight: 500;">
                        <span style="background: #f3f4f6; color: #4b5563; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; margin-right: 6px;">
                            <?php echo htmlspecialchars($proyecto['programa_prog_codigo']); ?>
                        </span>
                        <?php echo htmlspecialchars($proyecto['prog_denominacion'] ?? ''); ?>
                    </p>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <ion-icon src="../../assets/ionicons/git-network-outline.svg" style="color: #059669; font-size: 1.25rem;"></ion-icon>
                Jerarquía: Fase > Actividad > RAP
            </h3>
            <?php if (count($jerarquia) < 6): ?>
                <button onclick="abrirModalFase()" class="btn-primary" style="background: #d1fae5; color: #059669; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.85rem; display: flex; align-items: center; gap: 0.4rem; border: 1px solid #a7f3d0; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <ion-icon src="../../assets/ionicons/add-circle-outline.svg"></ion-icon>
                    Agregar Fase
                </button>
            <?php endif; ?>
        </div>

        <!-- LISTADO DE FASES (ACORDEÓN) -->
        <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 3rem;">
            <?php if (empty($jerarquia)): ?>
                <div style="text-align: center; padding: 3rem; color: #9ca3af; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <ion-icon src="../../assets/ionicons/alert-circle-outline.svg" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></ion-icon>
                    <p>No se encontraron fases configuradas para este proyecto.</p>
                </div>
            <?php else: ?>
                <?php foreach($jerarquia as $fase): ?>
                    <details class="fase-details" style="background: white; border: 1px solid #e5e7eb; border-radius: 0.6rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);" open>
                        <summary style="padding: 1.25rem; cursor: pointer; font-weight: 600; color: #111827; display: flex; align-items: center; gap: 1rem; outline: none; user-select: none; background: #f9fafb; border-bottom: 1px solid transparent;">
                            <span style="background: #111827; color: white; padding: 0.25rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 0.8rem;">
                                Fase <?php echo htmlspecialchars($fase['fase_orden']); ?>
                            </span>
                            <span style="flex-grow: 1; font-size: 1.05rem; display: flex; align-items: center; gap: 0.75rem;">
                                <?php echo htmlspecialchars($fase['fase_nombre']); ?>
                                <span style="font-size: 0.75rem; color: #6b7280; font-weight: 500; background: #f3f4f6; padding: 2px 8px; border-radius: 4px; border: 1px solid #e5e7eb;">
                                    <ion-icon src="../../assets/ionicons/calendar-outline.svg" style="vertical-align: middle; margin-right: 4px;"></ion-icon>
                                    <?php echo date('d/m/Y', strtotime($fase['fase_fecha_ini'])); ?> - <?php echo date('d/m/Y', strtotime($fase['fase_fecha_fin'])); ?>
                                </span>
                            </span>
                            
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span style="background: #ecfdf5; color: #065f46; padding: 2px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: bold; border: 1px solid #a7f3d0;">
                                    <?php echo count($fase['actividades']); ?> Actividades
                                </span>
                                <div style="display: flex; gap: 4px; padding-right: 8px; border-right: 1px solid #e5e7eb;">
                                    <button onclick='event.preventDefault(); abrirModalFase(<?php echo $fase["fase_id"]; ?>)' title="Editar Fase" style="color: #6b7280; background: none; border: none; cursor: pointer; padding: 4px; border-radius: 4px; transition: background 0.2s; display: flex;">
                                        <ion-icon src="../../assets/ionicons/create-outline.svg" style="font-size: 1.1rem;"></ion-icon>
                                    </button>
                                    <button onclick="event.preventDefault(); confirmarEliminarFase(<?php echo $fase['fase_id']; ?>)" title="Eliminar Fase" style="color: #ef4444; background: none; border: none; cursor: pointer; padding: 4px; border-radius: 4px; transition: background 0.2s; display: flex;">
                                        <ion-icon src="../../assets/ionicons/trash-outline.svg" style="font-size: 1.1rem;"></ion-icon>
                                    </button>
                                </div>
                                <button onclick="event.preventDefault(); abrirModalActividad(<?php echo $fase['fase_id']; ?>)" class="btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; display: flex; align-items: center; gap: 0.3rem;">
                                    <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon> Actividad
                                </button>
                            </div>
                        </summary>

                        <div style="padding: 1.25rem; background: #fff;">
                            <?php if(empty($fase['actividades'])): ?>
                                <div style="padding: 2rem; text-align: center; border: 2px dashed #e5e7eb; border-radius: 0.5rem; color: #9ca3af;">
                                    <p style="margin: 0; font-size: 0.9rem;">Esta fase no tiene actividades registradas.</p>
                                    <button onclick="abrirModalActividad(<?php echo $fase['fase_id']; ?>)" style="margin-top: 0.75rem; color: #059669; background: none; border: none; font-weight: 600; cursor: pointer; text-decoration: underline;">
                                        Crear la primera actividad ahora
                                    </button>
                                </div>
                            <?php else: ?>
                                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                                    <?php foreach($fase['actividades'] as $act): ?>
                                        <!-- NIVEL 2: ACTIVIDAD -->
                                        <div class="actividad-card" style="border: 1px solid #e5e7eb; border-radius: 0.5rem; background: #fcfcfc;">
                                            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #f3f4f6;">
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <ion-icon src="../../assets/ionicons/list-outline.svg" style="color: #6b7280;"></ion-icon>
                                                    <strong style="color: #374151; font-size: 0.95rem;"><?php echo htmlspecialchars($act['act_nombre']); ?></strong>
                                                </div>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <button onclick='abrirModalAsignarRap(<?php echo $act["act_id"]; ?>, <?php echo htmlspecialchars(json_encode($act["act_nombre"]), ENT_QUOTES); ?>)' title="Asignar RAP" style="background: #e0e7ff; color: #4338ca; border: none; border-radius: 4px; padding: 4px 8px; cursor: pointer; display: flex; align-items: center; font-size: 0.75rem; font-weight: 600; gap: 3px;">
                                                        <ion-icon src="../../assets/ionicons/link-outline.svg"></ion-icon> Asignar RAP
                                                    </button>
                                                    <button onclick='editarActividad(<?php echo $act["act_id"]; ?>, <?php echo htmlspecialchars(json_encode($act["act_nombre"]), ENT_QUOTES); ?>)' title="Editar" style="color: #2563eb; background: none; border: none; cursor: Pointer; padding: 2px;">
                                                        <ion-icon src="../../assets/ionicons/create-outline.svg" style="font-size: 1.1rem;"></ion-icon>
                                                    </button>
                                                    <button onclick="eliminarActividad(<?php echo $act['act_id']; ?>)" title="Eliminar" style="color: #dc2626; background: none; border: none; cursor: Pointer; padding: 2px;">
                                                        <ion-icon src="../../assets/ionicons/trash-outline.svg" style="font-size: 1.1rem;"></ion-icon>
                                                    </button>
                                                </div>
                                            </div>
                                                                                     <div style="padding: 1rem;">
                                                <?php if(empty($act['competencias'])): ?>
                                                    <p style="margin: 0; font-size: 0.8rem; color: #9ca3af; font-style: italic;">Sin Resultados de Aprendizaje (RAP) asociados.</p>
                                                <?php else: ?>
                                                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                                                        <?php foreach($act['competencias'] as $comp): ?>
                                                            <div class="competencia-group">
                                                                <div style="font-size: 0.75rem; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.025em; margin-bottom: 0.4rem; padding-bottom: 2px; border-bottom: 1px solid #d1fae5; display: inline-block;">
                                                                    <?php echo htmlspecialchars($comp['comp_nombre_corto']); ?>
                                                                </div>
                                                                <div style="display: grid; grid-template-columns: 1fr; gap: 0.4rem;">
                                                                    <?php foreach($comp['raps'] as $rap): ?>
                                                                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: white; border: 1px solid #f3f4f6; border-radius: 0.35rem; font-size: 0.8rem; transition: all 0.2s;" onmouseover="this.style.borderColor='#d1d5db'" onmouseout="this.style.borderColor='#f3f4f6'">
                                                                            <div style="display: flex; align-items: flex-start; gap: 0.75rem; flex: 1;">
                                                                                <div style="background: #f8fafc; color: #64748b; padding: 1px 5px; border-radius: 4px; font-weight: 700; font-size: 0.65rem; border: 1px solid #e2e8f0; min-width: 80px; text-align: center;">
                                                                                    <?php echo htmlspecialchars($rap['rap_codigo']); ?>
                                                                                </div>
                                                                                <div style="color: #4b5563; line-height: 1.4;">
                                                                                    <?php echo htmlspecialchars($rap['rap_descripcion']); ?>
                                                                                    <span style="color: #9ca3af; font-size: 0.7rem; font-weight: 600; margin-left: 6px;">(<?php echo $rap['rap_horas']; ?>h)</span>
                                                                                </div>
                                                                            </div>
                                                                            <button onclick="desasignarRap(<?php echo $rap['rap_id']; ?>, <?php echo $act['act_id']; ?>)" title="Quitar de esta actividad" style="color: #9ca3af; background: none; border: none; cursor: Pointer; padding: 4px; transition: color 0.2s;" onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#9ca3af'">
                                                                                <ion-icon src="../../assets/ionicons/close-circle-outline.svg" style="font-size: 1.1rem;"></ion-icon>
                                                                            </button>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- MODAL FASE (Crear/Editar) -->
<div id="modalFase" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 id="modalFaseTitle">Registrar Fase</h3>
            <button class="modal-close" onclick="cerrarModalFase()">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="formFase" onsubmit="guardarFase(event)">
            <input type="hidden" id="faseIdModalInput" name="fase_id">
            <input type="hidden" name="pf_pf_id" value="<?php echo $proyecto['pf_id']; ?>">
            <div class="modal-body">
                <div id="modalFaseError" class="hidden" style="margin-bottom: 1rem; padding: 0.75rem; background-color: #fef2f2; color: #dc2626; border-radius: 0.5rem; text-align: center; font-size: 0.875rem;"></div>
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <label class="form-label">Nombre de la Fase</label>
                        <input type="text" name="fase_nombre" required class="form-input w-full" placeholder="Ej: Fase Ejecución">
                    </div>
                    <div>
                        <label class="form-label">Orden</label>
                        <input type="number" name="fase_orden" required class="form-input w-full" min="1" max="10">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="fase_fecha_ini" required class="form-input w-full">
                    </div>
                    <div>
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" name="fase_fecha_fin" required class="form-input w-full">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalFase()">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ACTIVIDAD (Crear/Editar) -->
<div id="modalActividad" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 id="modalActTitle">Nueva Actividad de Proyecto</h3>
            <button class="modal-close" onclick="cerrarModalActividad()">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="formActividad" onsubmit="guardarActividad(event)">
            <input type="hidden" id="faseIdInput" name="fase_id">
            <input type="hidden" id="actIdInput" name="act_id">
            <div class="modal-body">
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombre/Descripción de la Actividad</label>
                    <textarea name="act_nombre" id="actNombreInput" rows="3" required class="form-input w-full" style="padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%; resize: vertical;" placeholder="Ej: Realizar levantamiento de requisitos..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalActividad()">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ASIGNAR RAP -->
<div id="modalAsignarRap" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <div>
                <h3 style="margin: 0;">Asignar Resultado de Aprendizaje</h3>
                <p id="actNameDisplay" style="margin: 4px 0 0 0; font-size: 0.85rem; color: #6b7280; font-weight: normal;"></p>
            </div>
            <button class="modal-close" onclick="cerrarModalAsignarRap()">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <div class="modal-body">
            <!-- Selector de Competencia -->
            <div style="margin-bottom: 1.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: end;">
                <div>
                    <label class="form-label" style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem; font-weight: 600; color: #374151;">Competencia del Programa</label>
                    <select id="competenciaFiltro" class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;" onchange="cargarRapsDisponibles()">
                        <option value="">Cargando competencias...</option>
                    </select>
                </div>
                <div style="position: relative;">
                    <ion-icon src="../../assets/ionicons/search-outline.svg" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.9rem;"></ion-icon>
                    <input type="text" id="busquedaRap" oninput="filtrarRaps()" placeholder="Filtrar RAPs..." style="width: 100%; padding: 0.5rem 0.5rem 0.5rem 32px; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.85rem;">
                </div>
            </div>

            <div id="rapsListaContainer" style="max-height: 350px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: #f9fafb;">
                <div style="padding: 3rem; text-align: center; color: #9ca3af;">
                    <ion-icon src="../../assets/ionicons/layers-outline.svg" style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5;"></ion-icon>
                    <p>Seleccione una competencia para ver sus RAPs</p>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 0.85rem; color: #6b7280;">
                <span id="countSelected" style="font-weight: 700; color: #059669;">0</span> seleccionados
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn-secondary" onclick="cerrarModalAsignarRap()">Cerrar</button>
                <button type="button" id="btnEjecutarAsignacion" class="btn-primary" onclick="ejecutarAsignacionMasiva()" disabled style="display: flex; align-items: center; gap: 0.4rem;">
                    <ion-icon src="../../assets/ionicons/checkbox-outline.svg"></ion-icon>
                    Asignar Seleccionados
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const PF_ID = <?php echo $proyecto['pf_id']; ?>;
    // Global data for robust access
    const JERARQUIA_DATA = <?php echo json_encode($jerarquia); ?>;
    const PROG_ID = '<?php echo $proyecto['programa_prog_codigo']; ?>';
    const API_URL = '../../routing.php?controller=proyecto_formativo';

    let CURRENT_ACT_ID = null;
    let RAPS_CACHE = [];
    let SELECTED_RAPS = new Set();

    // Maneja modales
    function abrirModalActividad(faseId) {
        document.getElementById('modalActTitle').textContent = 'Nueva Actividad de Proyecto';
        document.getElementById('formActividad').reset();
        document.getElementById('faseIdInput').value = faseId;
        document.getElementById('actIdInput').value = '';
        document.getElementById('modalActividad').classList.add('show');
    }

    function editarActividad(actId, nombre) {
        document.getElementById('modalActTitle').textContent = 'Editar Actividad';
        document.getElementById('faseIdInput').value = '';
        document.getElementById('actIdInput').value = actId;
        document.getElementById('actNombreInput').value = nombre;
        document.getElementById('modalActividad').classList.add('show');
    }

    function cerrarModalActividad() {
        document.getElementById('modalActividad').classList.remove('show');
    }

    async function guardarActividad(e) {
        e.preventDefault();
        const actId = document.getElementById('actIdInput').value;
        const action = actId ? 'updateActivity&id=' + actId : 'storeActivity';
        const fd = new URLSearchParams(new FormData(e.target));
        
        try {
            const res = await fetch(`${API_URL}&action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: fd.toString()
            });
            const data = await res.json();
            if(data.success || data.act_id) {
                NotificationService.showSuccess(actId ? 'Actividad actualizada' : 'Actividad creada');
                setTimeout(() => location.reload(), 800);
            }
            else NotificationService.showError('Error: ' + (data.error || 'Desconocido'));
        } catch(e) { console.error(e); }
    }

    function eliminarActividad(actId) {
        NotificationService.showConfirm('¿Está seguro de eliminar esta actividad?', async () => {
            try {
                const res = await fetch(`${API_URL}&action=destroyActivity&id=${actId}`, { method: 'POST' });
                if((await res.json()).success) {
                    NotificationService.showSuccess('Actividad eliminada');
                    setTimeout(() => location.reload(), 800);
                }
            } catch(e) { console.error(e); }
        }, { title: 'Eliminar Actividad' });
    }

    // --- MANEJO DE FASES ---
    function abrirModalFase(faseId = null) {
        const form = document.getElementById('formFase');
        form.reset();
        document.getElementById('modalFaseError').classList.add('hidden');
        
        if (faseId) {
            const fase = JERARQUIA_DATA.find(f => f.fase_id == faseId);
            if (!fase) return;

            document.getElementById('modalFaseTitle').textContent = 'Editar Fase';
            document.getElementById('faseIdModalInput').value = fase.fase_id;
            form.fase_nombre.value = fase.fase_nombre;
            form.fase_orden.value = fase.fase_orden;
            
            // Dates are now guaranteed to be present and in YYYY-MM-DD format from DB
            form.fase_fecha_ini.value = fase.fase_fecha_ini ? fase.fase_fecha_ini.split(' ')[0] : '';
            form.fase_fecha_fin.value = fase.fase_fecha_fin ? fase.fase_fecha_fin.split(' ')[0] : '';
        } else {
            document.getElementById('modalFaseTitle').textContent = 'Nueva Fase';
            document.getElementById('faseIdModalInput').value = '';
            form.pf_pf_id.value = PF_ID;
            form.fase_orden.value = JERARQUIA_DATA.length + 1;
        }
        
        document.getElementById('modalFase').classList.add('show');
    }

    function cerrarModalFase() {
        document.getElementById('modalFase').classList.remove('show');
    }

    async function guardarFase(e) {
        e.preventDefault();
        const faseId = document.getElementById('faseIdModalInput').value;
        const action = faseId ? `updatePhase&id=${faseId}` : 'storePhase';
        const fd = new URLSearchParams(new FormData(e.target));
        
        const btn = e.target.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Guardando...';

        try {
            const res = await fetch(`${API_URL}&action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: fd.toString()
            });
            const data = await res.json();
            if(data.success || data.fase_id) {
                NotificationService.showSuccess(faseId ? 'Fase actualizada' : 'Fase creada');
                setTimeout(() => location.reload(), 800);
            } else {
                document.getElementById('modalFaseError').textContent = data.error || 'Error al guardar';
                document.getElementById('modalFaseError').classList.remove('hidden');
            }
        } catch(e) { console.error(e); }
        finally { btn.disabled = false; btn.textContent = 'Guardar Cambios'; }
    }

    function confirmarEliminarFase(faseId) {
        NotificationService.showConfirm('¿Realmente desea eliminar esta fase? Todas sus actividades y asociaciones de RAP se perderán.', async () => {
            try {
                const res = await fetch(`${API_URL}&action=destroyPhase&id=${faseId}`, { method: 'POST' });
                if((await res.json()).success) {
                    NotificationService.showSuccess('Fase eliminada correctamente');
                    setTimeout(() => location.reload(), 800);
                }
            } catch(e) { console.error(e); }
        }, { title: 'Eliminar Fase' });
    }

    // MANEJO DE RAPS MEJORADO
    async function abrirModalAsignarRap(actId, actNombre) {
        CURRENT_ACT_ID = actId;
        SELECTED_RAPS.clear();
        actualizarInterfazSeleccion();
        
        document.getElementById('actNameDisplay').textContent = actNombre;
        document.getElementById('modalAsignarRap').classList.add('show');
        document.getElementById('busquedaRap').value = '';
        
        cargarCompetenciasDelPrograma();
    }
    // ... (rest of the functions already exist but I'll make sure to replace alerts)

    async function cargarCompetenciasDelPrograma() {
        const sel = document.getElementById('competenciaFiltro');
        sel.innerHTML = '<option value="">Cargando competencias...</option>';
        
        try {
            const res = await fetch(`../../routing.php?controller=competencia&action=getByPrograma&prog_id=${PROG_ID}`, {
                headers: { 'Accept': 'application/json' }
            });
            const comps = await res.json();
            
            sel.innerHTML = '<option value="">Seleccione una competencia...</option>';
            comps.forEach(c => {
                sel.innerHTML += `<option value="${c.comp_id}">${c.comp_nombre_corto} - ${c.comp_nombre_unidad_competencia.substring(0, 60)}...</option>`;
            });
        } catch(e) { 
            console.error(e);
            sel.innerHTML = '<option value="">Error al cargar competencias</option>';
        }
    }

    async function cargarRapsDisponibles() {
        const compId = document.getElementById('competenciaFiltro').value;
        const container = document.getElementById('rapsListaContainer');
        
        if (!compId) {
            container.innerHTML = `
                <div style="padding: 3rem; text-align: center; color: #9ca3af;">
                    <ion-icon src="../../assets/ionicons/layers-outline.svg" style="font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5;"></ion-icon>
                    <p>Seleccione una competencia para ver sus RAPs</p>
                </div>`;
            return;
        }

        container.innerHTML = '<div style="padding: 2rem; text-align: center; color: #9ca3af;">Cargando RAPs...</div>';
        
        try {
            const res = await fetch(`../../routing.php?controller=resultado_aprendizaje&action=getByCompetenciaPrograma&prog_id=${PROG_ID}&comp_id=${compId}&pf_id=${PF_ID}`, {
                headers: { 'Accept': 'application/json' }
            });
            RAPS_CACHE = await res.json();
            renderRapsCheckboxes();
        } catch(e) { console.error(e); }
    }

    function renderRapsCheckboxes(filtro = '') {
        const container = document.getElementById('rapsListaContainer');
        const term = filtro.toLowerCase();
        
        const filtrados = RAPS_CACHE.filter(r => 
            r.rap_codigo.toLowerCase().includes(term) || 
            r.rap_descripcion.toLowerCase().includes(term)
        );

        if (filtrados.length === 0) {
            container.innerHTML = '<div style="padding: 2rem; text-align: center; color: #9ca3af;">No hay RAPs en esta competencia que coincidan.</div>';
            return;
        }

        let html = '<div style="display: flex; flex-direction: column;">';
        filtrados.forEach(r => {
            const isSelected = SELECTED_RAPS.has(r.rap_id.toString());
            html += `
                <label style="padding: 1rem; border-bottom: 1px solid #f3f4f6; display: flex; align-items: flex-start; gap: 1rem; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">
                    <input type="checkbox" value="${r.rap_id}" ${isSelected ? 'checked' : ''} onchange="toggleRapSelection(this)" style="margin-top: 4px; width: 1.1rem; height: 1.1rem; cursor: pointer; accent-color: #2563eb;">
                    <div style="flex: 1;">
                        <span style="background: #f1f5f9; color: #64748b; padding: 2px 6px; border-radius: 4px; font-weight: 800; font-size: 0.7rem; display: inline-block; margin-bottom: 4px; border: 1px solid #e2e8f0;">
                            ${r.rap_codigo}
                        </span>
                        <p style="margin: 0; font-size: 0.85rem; color: #374151; line-height: 1.4;">${r.rap_descripcion}</p>
                        <span style="display: block; margin-top: 4px; font-size: 0.75rem; color: #9ca3af; font-weight: 600;">Duración: ${r.rap_horas} horas</span>
                    </div>
                </label>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    }

    function toggleRapSelection(checkbox) {
        if (checkbox.checked) SELECTED_RAPS.add(checkbox.value);
        else SELECTED_RAPS.delete(checkbox.value);
        actualizarInterfazSeleccion();
    }

    function actualizarInterfazSeleccion() {
        const count = SELECTED_RAPS.size;
        document.getElementById('countSelected').textContent = count;
        document.getElementById('btnEjecutarAsignacion').disabled = (count === 0);
    }

    function filtrarRaps() {
        const val = document.getElementById('busquedaRap').value;
        renderRapsCheckboxes(val);
    }

    async function ejecutarAsignacionMasiva() {
        if(!CURRENT_ACT_ID || SELECTED_RAPS.size === 0) return;
        
        const btn = document.getElementById('btnEjecutarAsignacion');
        btn.disabled = true;
        btn.innerHTML = 'Asignando...';
        
        const fd = new URLSearchParams();
        fd.append('act_id', CURRENT_ACT_ID);
        SELECTED_RAPS.forEach(id => {
            fd.append('rap_id[]', id);
        });
        
        try {
            const res = await fetch(`${API_URL}&action=assignRap`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: fd.toString()
            });
            const data = await res.json();
            if(data.success) {
                NotificationService.showSuccess('RAPs asignados correctamente');
                setTimeout(() => location.reload(), 800);
            }
            else {
                NotificationService.showError(data.error || 'Error al asignar');
                btn.disabled = false;
                btn.innerHTML = 'Asignar Seleccionados';
            }
        } catch(e) { console.error(e); }
    }

    async function desasignarRap(rapId, actId) {
        NotificationService.showConfirm('¿Quitar este RAP de la actividad?', async () => {
            const fd = new URLSearchParams();
            fd.append('rap_id', rapId);
            fd.append('act_id', actId);
            
            try {
                const res = await fetch(`${API_URL}&action=unassignRap`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: fd.toString()
                });
                if((await res.json()).success) {
                    NotificationService.showSuccess('RAP desasignado');
                    setTimeout(() => location.reload(), 800);
                }
            } catch(e) { console.error(e); }
        }, { title: 'Desasignar RAP' });
    }

    function cerrarModalAsignarRap() {
        document.getElementById('modalAsignarRap').classList.remove('show');
        CURRENT_ACT_ID = null;
    }
</script>

<style>
    .fase-details[open] summary {
        border-bottom-color: #e5e7eb;
        background: #f1f5f9;
    }
    .actividad-card:hover {
        border-color: #3b82f6 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        backdrop-filter: blur(2px);
        align-items: center;
        justify-content: center;
    }
    .modal.show { display: flex; animation: fadeIn 0.2s ease-out; }
    .modal-content { 
        background: white; 
        border-radius: 0.75rem; 
        width: 95%; 
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: slideUp 0.3s ease-out;
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>
<!-- Modal Editar Proyecto Base -->
<div id="modalEditProyecto" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Editar Proyecto Formativo</h3>
            <button class="modal-close" onclick="cerrarModalEditarProyecto()">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="formEditProyecto" onsubmit="guardarEdicionProyecto(event)">
            <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                <div id="modalErrorEditProyecto" class="hidden" style="margin-bottom: 1rem; padding: 0.75rem; background-color: #fef2f2; color: #dc2626; border-radius: 0.5rem; text-align: center; font-size: 0.875rem;"></div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label class="form-label" style="font-size: 0.8rem;">Código del Proyecto</label>
                        <input type="text" name="pf_codigo" required class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;">
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.8rem;">Programa de Formación</label>
                        <!-- Disable to avoid modifying program since it's loaded only here simply -->
                        <input type="text" readonly value="<?php echo htmlspecialchars($proyecto['programa_prog_codigo']); ?>" class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%; background: #f3f4f6;">
                        <input type="hidden" name="programa_prog_codigo" value="<?php echo htmlspecialchars($proyecto['programa_prog_codigo']); ?>">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="form-label" style="font-size: 0.8rem;">Nombre del Proyecto</label>
                        <input type="text" name="pf_nombre" required class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="form-label" style="font-size: 0.8rem;">Descripción</label>
                        <textarea name="pf_descripcion" rows="3" class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModalEditarProyecto()">Cancelar</button>
                <button type="submit" id="btnGuardarEditProyecto" class="btn-primary">
                    <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmarEliminarProyecto(id) {
        if (window.NotificationService) {
            NotificationService.showConfirm('¿Está seguro de eliminar este proyecto? Esta acción eliminará permanentemente todas sus fases, actividades y asociaciones.', async () => {
                await ejecutarEliminarProyecto(id);
            }, { title: 'Eliminar Proyecto', confirmText: 'Sí, eliminar', type: 'danger' });
        } else {
            if (!confirm('¿Está seguro de eliminar este proyecto?')) return;
            ejecutarEliminarProyecto(id);
        }
    }

    async function ejecutarEliminarProyecto(id) {
        try {
            const res = await fetch(`${API_URL}&action=destroy&id=${id}`, { method: 'POST' });
            const data = await res.json();
            if (data.success) {
                if (window.NotificationService) {
                    NotificationService.showSuccess('Proyecto eliminado correctamente');
                }
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1000);
            } else {
                if (window.NotificationService) NotificationService.showError('No se pudo eliminar el proyecto');
            }
        } catch (error) {
            if (window.NotificationService) NotificationService.showError('Error de red al intentar eliminar');
        }
    }

    function abrirModalEditarProyecto(p) {
        const form = document.getElementById('formEditProyecto');
        form.pf_codigo.value = p.pf_codigo;
        form.pf_nombre.value = p.pf_nombre;
        form.pf_descripcion.value = p.pf_descripcion;
        
        document.getElementById('modalErrorEditProyecto').classList.add('hidden');
        document.getElementById('modalEditProyecto').classList.add('show');
    }

    function cerrarModalEditarProyecto() {
        document.getElementById('modalEditProyecto').classList.remove('show');
    }

    async function guardarEdicionProyecto(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnGuardarEditProyecto');
        const errorDiv = document.getElementById('modalErrorEditProyecto');
        
        errorDiv.classList.add('hidden');
        btn.disabled = true;
        btn.innerHTML = 'Guardando...';

        const formData = new URLSearchParams();
        formData.append('pf_codigo', form.pf_codigo.value);
        formData.append('pf_nombre', form.pf_nombre.value);
        formData.append('pf_descripcion', form.pf_descripcion.value);
        formData.append('programa_prog_codigo', form.programa_prog_codigo.value);

        try {
            const res = await fetch(`${API_URL}&action=update&id=${PF_ID}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
                body: formData.toString()
            });
            const data = await res.json();
            
            if (!res.ok || data.error) {
                errorDiv.innerHTML = `<ion-icon src="../../assets/ionicons/warning-outline.svg" style="vertical-align: middle;"></ion-icon> ${data.error || 'Error de servidor'}`;
                errorDiv.classList.remove('hidden');
                errorDiv.style.display = 'block';
            } else {
                cerrarModalEditarProyecto();
                if (window.NotificationService) NotificationService.showSuccess('Proyecto actualizado con éxito');
                setTimeout(() => location.reload(), 800);
            }
        } catch (error) {
            errorDiv.innerHTML = `<ion-icon src="../../assets/ionicons/warning-outline.svg" style="vertical-align: middle;"></ion-icon> Problema de red o servidor no responde`;
            errorDiv.classList.remove('hidden');
            errorDiv.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Guardar Cambios`;
        }
    }
</script>

</body>
</html>
