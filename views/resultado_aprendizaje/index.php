<?php
$pageTitle = "Resultados Aprendizaje - SENA";
$activeNavItem = 'resultado_aprendizaje';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Resultados de Aprendizaje</span>
            </nav>
            <h1 class="page-title">Resultados de Aprendizaje (RAP)</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="action-bar">
            <div class="search-container">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                <input type="text" id="searchInput" placeholder="Buscar RAP por código o descripción..." class="search-input" onkeyup="filtrarTeclado()">
            </div>
            
            <div class="filter-container" style="display: flex; align-items: center; gap: 0.5rem; background: white; padding: 0.25rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; min-width: 300px;">
                <ion-icon src="../../assets/ionicons/filter-outline.svg" style="color: #6b7280;"></ion-icon>
                <select id="programFilter" class="form-input" style="border: none; padding: 0.35rem; font-size: 0.85rem; width: 100%; outline: none; background: transparent;">
                    <option value="">Todos los programas</option>
                </select>
            </div>
            <button onclick="abrirModal()" class="btn-primary">
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                Nuevo RAP
            </button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Código RAP</th>
                        <th>Competencia / Descripción</th>
                        <th style="text-align: center; width: 100px;">Horas</th>
                        <th>Programa</th>
                    </tr>
                </thead>
                <tbody id="rapsTable">
                    <tr><td colspan="5" style="text-align: center; padding: 20px;">Cargando RAPs...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Nuevo RAP -->
<div id="modalRap" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Registrar Resultado de Aprendizaje</h3>
            <button class="modal-close" onclick="cerrarModal()">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="formRap" onsubmit="guardarRap(event)">
            <div class="modal-body" style="max-height: 65vh; overflow-y: auto;">
                <input type="hidden" name="rap_id" id="rapIdInput">
                <div id="modalError" class="hidden" style="margin-bottom: 1rem; padding: 0.75rem; background-color: #fef2f2; color: #dc2626; border-radius: 0.5rem; text-align: center; font-size: 0.875rem;"></div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label class="form-label" style="font-size: 0.8rem;">Programa</label>
                        <select id="progSelect" name="programa_prog_id" required class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;">
                            <option value="">Seleccione programa...</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.8rem;">Competencia Asociada</label>
                        <select id="compSelect" name="competencia_comp_id" required disabled class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%; opacity: 0.5;">
                            <option value="">Primero seleccione programa</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.8rem;">Proyecto (Opcional para asignación)</label>
                        <select id="proyectoSelect" disabled class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%; opacity: 0.5;">
                            <option value="">Primero seleccione programa</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.8rem;">Asignar a Fase (Opcional)</label>
                        <select id="faseSelect" name="fase_id" disabled class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%; opacity: 0.5;">
                            <option value="">Primero seleccione proyecto</option>
                        </select>
                    </div>
                    
                    <div style="grid-column: span 2; border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 0.5rem;">
                        <label class="form-label" style="font-size: 0.8rem; display: block; margin-bottom: 0.25rem;">Código Identificador (RAP)</label>
                        <input type="text" name="rap_codigo" required class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="form-label" style="font-size: 0.8rem; display: block; margin-bottom: 0.25rem;">Descripción de la Evaluación</label>
                        <textarea name="rap_descripcion" rows="3" required class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%; resize: vertical;"></textarea>
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="form-label" style="font-size: 0.8rem; display: block; margin-bottom: 0.25rem;">Horas Asignadas</label>
                        <input type="number" name="rap_horas" min="1" required class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;">
                    </div>
                </div>
            </div>
            
            <div class="modal-footer" style="display: flex; justify-content: space-between; align-items: center;">
                <button type="button" id="btnEliminarModal" class="btn-icon" style="color: #dc2626; background: #fef2f2; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; display: none; align-items: center; gap: 4px;" onclick="eliminarRapDesdeModal()">
                    <ion-icon src="../../assets/ionicons/trash-outline.svg"></ion-icon> Eliminar
                </button>
                <div style="display: flex; gap: 0.5rem; margin-left: auto;">
                    <button type="button" class="btn-secondary" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" id="btnGuardar" class="btn-primary">
                        <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Registrar RAP
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const API_URL = '../../routing.php?controller=resultado_aprendizaje';

    document.addEventListener('DOMContentLoaded', () => {
        cargarRaps();
        cargarProgramas();
        
        document.getElementById('progSelect').addEventListener('change', cargarCompetencias);
        document.getElementById('proyectoSelect').addEventListener('change', cargarFases);
        
        document.getElementById('programFilter').addEventListener('change', (e) => {
            cargarRaps(e.target.value);
        });
    });

    let ALL_RAPS = [];

    async function cargarRaps(prog_id = '') {
        try {
            console.log('Cargando RAPs para programa:', prog_id);
            const url = prog_id ? `${API_URL}&action=index&prog_id=${prog_id}` : `${API_URL}&action=index`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
            const data = await res.json();
            console.log('RAPs cargados:', data);
            ALL_RAPS = Array.isArray(data) ? data : [];
            renderTablaRaps(ALL_RAPS);
        } catch (error) { 
            console.error('Error cargando RAPs:', error);
            renderTablaRaps([]);
        }
    }

    function renderTablaRaps(data) {
        const tbody = document.getElementById('rapsTable');
        if (!tbody) return;
        tbody.innerHTML = '';
        
        if(!data || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 40px; color: #9ca3af;">
                <ion-icon src="../../assets/ionicons/search-outline.svg" style="font-size: 2.5rem; display: block; margin: 0 auto 0.5rem; opacity: 0.3;"></ion-icon>
                No se encontraron Resultados de Aprendizaje.
            </td></tr>`;
            return;
        }

        data.forEach(r => {
            tbody.innerHTML += `
                <tr onclick="abrirModal(${r.rap_id})" style="cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                    <td><strong>${r.rap_codigo}</strong></td>
                    <td>
                        <div style="font-weight: 600; color: #059669; font-size: 0.85rem; margin-bottom: 2px;">${r.comp_nombre_corto || r.comp_denominacion || 'Competencia'}</div>
                        <div style="color: #6b7280; font-size: 0.8rem; max-width: 300px; white-space: normal;">${r.rap_descripcion}</div>
                    </td>
                    <td style="text-align: center;">
                        <span style="background: #eff6ff; color: #2563eb; padding: 2px 8px; border-radius: 999px; font-size: 0.75rem; font-weight: bold;">
                            ${r.rap_horas}h
                        </span>
                    </td>
                    <td style="font-size: 0.8rem; color: #4b5563;">${r.prog_denominacion}</td>
                </tr>
            `;
        });
    }

    async function cargarProgramas() {
        try {
            console.log('Cargando programas para filtros...');
            const res = await fetch('../../routing.php?controller=programa&action=index', { headers: { 'Accept': 'application/json' }});
            const data = await res.json();
            console.log('Programas cargados:', data);
            
            const sel = document.getElementById('progSelect');
            const filterSel = document.getElementById('programFilter');
            
            if (Array.isArray(data)) {
                data.forEach(p => {
                    const opt = `<option value="${p.prog_codigo}">${p.prog_denominacion}</option>`;
                    if (sel) sel.innerHTML += opt;
                    if (filterSel) filterSel.innerHTML += opt;
                });
            }
        } catch (e) {
            console.error('Error cargando programas:', e);
        }
    }

    function filtrarTeclado() {
        const term = document.getElementById('searchInput').value.toLowerCase();
        const filtrados = ALL_RAPS.filter(r => 
            r.rap_codigo.toLowerCase().includes(term) || 
            r.rap_descripcion.toLowerCase().includes(term) ||
            r.prog_denominacion.toLowerCase().includes(term)
        );
        renderTablaRaps(filtrados);
    }

    async function cargarCompetencias(e) {
        const prog_codigo = e.target.value;
        const sel = document.getElementById('compSelect');
        const projSel = document.getElementById('proyectoSelect');
        
        if(!prog_codigo) {
            sel.disabled = true;
            sel.style.opacity = '0.5';
            sel.innerHTML = '<option value="">Primero seleccione programa</option>';
            
            projSel.disabled = true;
            projSel.style.opacity = '0.5';
            projSel.innerHTML = '<option value="">Primero seleccione programa</option>';
            return;
        }
        
        // Cargar Competencias
        sel.disabled = false;
        sel.style.opacity = '1';
        sel.innerHTML = '<option value="">Cargando competencias...</option>';
        try {
            const res = await fetch(`../../routing.php?controller=competencia&action=getByPrograma&prog_id=${prog_codigo}`);
            const data = await res.json();
            sel.innerHTML = '<option value="">Seleccione competencia...</option>';
            data.forEach(c => {
                sel.innerHTML += `<option value="${c.comp_id}">${c.comp_nombre_corto || c.comp_id}</option>`;
            });
        } catch(error) { sel.innerHTML = '<option value="">Error obteniendo</option>'; }

        // Cargar Proyectos Formativos del programa
        cargarProyectos(prog_codigo);
    }

    async function cargarProyectos(prog_codigo = null) {
        const sel = document.getElementById('proyectoSelect');
        if (!prog_codigo) {
            sel.disabled = true;
            sel.style.opacity = '0.5';
            sel.innerHTML = '<option value="">Primero seleccione programa</option>';
            return;
        }

        sel.disabled = false;
        sel.style.opacity = '1';
        sel.innerHTML = '<option value="">Cargando proyectos...</option>';

        try {
            const res = await fetch(`../../routing.php?controller=proyecto_formativo&action=getByPrograma&prog_id=${prog_codigo}`, { headers: { 'Accept': 'application/json' }});
            const data = await res.json();
            sel.innerHTML = '<option value="">Seleccione proyecto...</option>';
            if (data.length === 0) {
                sel.innerHTML = '<option value="">No hay proyectos para este programa</option>';
                sel.disabled = true;
                sel.style.opacity = '0.5';
            } else {
                data.forEach(p => sel.innerHTML += `<option value="${p.pf_id}">${p.pf_nombre}</option>`);
            }
        } catch (e) {
            sel.innerHTML = '<option value="">Error cargando proyectos</option>';
        }
    }

    async function cargarFases(e) {
        const pf_id = e.target.value;
        const sel = document.getElementById('faseSelect');
        if(!pf_id) {
            sel.disabled = true;
            sel.style.opacity = '0.5';
            sel.innerHTML = '<option value="">Primero seleccione proyecto</option>';
            return;
        }
        
        sel.disabled = false;
        sel.style.opacity = '1';
        sel.innerHTML = '<option value="">Cargando fases...</option>';
        try {
            const res = await fetch(`../../routing.php?controller=proyecto_formativo&action=getFases&id=${pf_id}`);
            const data = await res.json();
            sel.innerHTML = '<option value="">Seleccione fase asignable...</option>';
            data.forEach(f => {
                sel.innerHTML += `<option value="${f.fase_id}">Fase ${f.fase_orden}: ${f.fase_nombre}</option>`;
            });
        } catch(error) { sel.innerHTML = '<option value="">Error obteniendo</option>'; }
    }

    async function abrirModal(id = null) {
        const form = document.getElementById('formRap');
        const title = document.querySelector('#modalRap h3');
        const btn = document.getElementById('btnGuardar');
        const errorDiv = document.getElementById('modalError');
        
        form.reset();
        document.getElementById('rapIdInput').value = id || '';
        errorDiv.classList.add('hidden');
        errorDiv.style.display = 'none';

        // Reset selects
        document.getElementById('compSelect').disabled = true;
        document.getElementById('compSelect').style.opacity = '0.5';
        document.getElementById('proyectoSelect').disabled = true;
        document.getElementById('proyectoSelect').style.opacity = '0.5';
        document.getElementById('faseSelect').disabled = true;
        document.getElementById('faseSelect').style.opacity = '0.5';

        const btnEliminar = document.getElementById('btnEliminarModal');

        if (id) {
            title.textContent = 'Editar Resultado de Aprendizaje';
            btn.innerHTML = `<ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Actualizar RAP`;
            btnEliminar.style.display = 'flex';
            
            try {
                const res = await fetch(`${API_URL}&action=show&id=${id}`, { headers: { 'Accept': 'application/json' }});
                const r = await res.json();
                
                form.rap_codigo.value = r.rap_codigo;
                form.rap_descripcion.value = r.rap_descripcion;
                form.rap_horas.value = r.rap_horas;
                form.programa_prog_id.value = r.programa_prog_id;
                
                // Trigger cascading loads
                await cargarCompetencias({ target: { value: r.programa_prog_id } });
                form.competencia_comp_id.value = r.competencia_comp_id;
                
                // Note: project and phase are not stored directly in RAP, 
                // but in relation tables. We leave them empty for now unless asked.
            } catch (e) {
                console.error(e);
            }
        } else {
            title.textContent = 'Registrar Resultado de Aprendizaje';
            btn.innerHTML = `<ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Registrar RAP`;
            btnEliminar.style.display = 'none';
        }

        document.getElementById('modalRap').classList.add('show');
    }

    function cerrarModal() {
        document.getElementById('modalRap').classList.remove('show');
    }

    function eliminarRapDesdeModal() {
        const id = document.getElementById('rapIdInput').value;
        if (!id) return;
        eliminarRap(id);
    }

    function eliminarRap(id) {
        if (window.NotificationService) {
            NotificationService.showConfirm('¿Está seguro de eliminar este RAP? Esta acción no se puede deshacer.', async () => {
                await ejecutarEliminarRap(id);
            }, { title: 'Eliminar Resultado de Aprendizaje', confirmText: 'Sí, eliminar', type: 'danger' });
        } else {
            if (!confirm('¿Está seguro de eliminar este RAP? Esta acción no se puede deshacer.')) return;
            ejecutarEliminarRap(id);
        }
    }

    async function ejecutarEliminarRap(id) {
        try {
            const res = await fetch(`${API_URL}&action=destroy&id=${id}`, {
                method: 'POST',
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            
            if (data.success) {
                if (window.NotificationService) {
                    NotificationService.showSuccess('Resultado de Aprendizaje eliminado correctamente');
                }
                cerrarModal();
                cargarRaps();
            } else {
                if (window.NotificationService) {
                    NotificationService.showError(data.error || 'No se pudo eliminar el RAP');
                }
            }
        } catch (error) {
            console.error(error);
            if (window.NotificationService) {
                NotificationService.showError('Error de conexión al eliminar');
            }
        }
    }

    async function guardarRap(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnGuardar');
        const errorDiv = document.getElementById('modalError');
        
        btn.disabled = true;
        btn.innerHTML = 'Guardando...';
        errorDiv.classList.add('hidden');
        errorDiv.style.display = 'none';

        const formData = new URLSearchParams();
        formData.append('rap_codigo', form.rap_codigo.value);
        formData.append('rap_descripcion', form.rap_descripcion.value);
        formData.append('rap_horas', form.rap_horas.value);
        formData.append('programa_prog_id', form.programa_prog_id.value);
        formData.append('competencia_comp_id', form.competencia_comp_id.value);
        
        const faseSelec = form.fase_id.value;

        const id = form.rap_id.value;
        const action = id ? 'update' : 'store';
        const url = id ? `${API_URL}&action=${action}&id=${id}` : `${API_URL}&action=${action}`;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
                body: formData.toString()
            });
            const data = await res.json();
            
            if (!res.ok || data.error) {
                errorDiv.innerHTML = `<ion-icon src="../../assets/ionicons/warning-outline.svg" style="vertical-align: middle; margin-right: 4px;"></ion-icon> ${data.error || 'Error de servidor'}`;
                errorDiv.classList.remove('hidden');
                errorDiv.style.display = 'block';
            } else {
                if (!id && data.success && faseSelec) {
                    // Only assign to phase on creation flow for simplicity
                    const fdFase = new URLSearchParams();
                    fdFase.append('rap_id', data.rap_id);
                    fdFase.append('fase_id', faseSelec);
                    
                    await fetch(`${API_URL}&action=asignarAFase`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
                        body: fdFase.toString()
                    });
                }
                
                if (window.NotificationService) {
                    NotificationService.showSuccess(id ? 'RAP actualizado correctamente' : 'RAP registrado correctamente');
                }
                
                cerrarModal();
                cargarRaps();
            }
        } catch (error) {
            errorDiv.innerHTML = `<ion-icon src="../../assets/ionicons/warning-outline.svg" style="vertical-align: middle; margin-right: 4px;"></ion-icon> Hubo un error contactando el servidor.`;
            errorDiv.classList.remove('hidden');
            errorDiv.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Registrar RAP`;
        }
    }
</script>
</body>
</html>
