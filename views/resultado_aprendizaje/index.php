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
                <input type="text" id="searchInput" placeholder="Buscar RAP..." class="search-input">
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
                        <th style="text-align: right; width: 80px;">Acciones</th>
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
                <div id="modalError" class="hidden" style="margin-bottom: 1rem; padding: 0.75rem; background-color: #fef2f2; color: #dc2626; border-radius: 0.5rem; text-align: center; font-size: 0.875rem;"></div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label class="form-label" style="font-size: 0.8rem;">Programa</label>
                        <select id="progSelect" name="competxprog_prog_id" required class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;">
                            <option value="">Seleccione programa...</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.8rem;">Competencia Asociada</label>
                        <select id="compSelect" name="competxprog_comp_id" required disabled class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%; opacity: 0.5;">
                            <option value="">Primero seleccione programa</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.8rem;">Proyecto (Opcional para asignación)</label>
                        <select id="proyectoSelect" class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;">
                            <option value="">Seleccione proyecto...</option>
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
            
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" id="btnGuardar" class="btn-primary">
                    <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Registrar RAP
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const API_URL = '../../routing.php?controller=resultado_aprendizaje';

    document.addEventListener('DOMContentLoaded', () => {
        cargarRaps();
        cargarProgramas();
        cargarProyectos();
        
        document.getElementById('progSelect').addEventListener('change', cargarCompetencias);
        document.getElementById('proyectoSelect').addEventListener('change', cargarFases);
    });

    async function cargarRaps() {
        try {
            const res = await fetch(`${API_URL}&action=index`, { headers: { 'Accept': 'application/json' }});
            const data = await res.json();
            const tbody = document.getElementById('rapsTable');
            tbody.innerHTML = '';
            
            if(data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 20px; color: #6b7280;">No hay RAPs registrados.</td></tr>`;
                return;
            }

            data.forEach(r => {
                tbody.innerHTML += `
                    <tr>
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
                        <td style="text-align: right;">
                            <a href="../resultado_aprendizaje/show.php?id=${r.rap_id}" style="color: #9ca3af; padding: 4px; display: inline-block;">
                                <ion-icon src="../../assets/ionicons/eye-outline.svg" style="font-size: 1.25rem;"></ion-icon>
                            </a>
                        </td>
                    </tr>
                `;
            });
        } catch (error) { console.error(error); }
    }

    async function cargarProgramas() {
        try {
            const res = await fetch('../../routing.php?controller=programa&action=index', { headers: { 'Accept': 'application/json' }});
            const data = await res.json();
            const sel = document.getElementById('progSelect');
            data.forEach(p => sel.innerHTML += `<option value="${p.prog_codigo}">${p.prog_denominacion}</option>`);
        } catch (e) {}
    }

    async function cargarCompetencias(e) {
        const prog_codigo = e.target.value;
        const sel = document.getElementById('compSelect');
        if(!prog_codigo) {
            sel.disabled = true;
            sel.style.opacity = '0.5';
            sel.innerHTML = '<option value="">Primero seleccione programa</option>';
            return;
        }
        
        sel.disabled = false;
        sel.style.opacity = '1';
        sel.innerHTML = '<option value="">Cargando competencias...</option>';
        try {
            const res = await fetch(`../../routing.php?controller=competencia_programa&action=getByPrograma&prog_id=${prog_codigo}`);
            const data = await res.json();
            sel.innerHTML = '<option value="">Seleccione competencia...</option>';
            data.forEach(c => {
                sel.innerHTML += `<option value="${c.comp_id}">${c.comp_nombre_corto || c.comp_id}</option>`;
            });
        } catch(error) { sel.innerHTML = '<option value="">Error obteniendo</option>'; }
    }

    async function cargarProyectos() {
        try {
            const res = await fetch('../../routing.php?controller=proyecto_formativo&action=index', { headers: { 'Accept': 'application/json' }});
            const data = await res.json();
            const sel = document.getElementById('proyectoSelect');
            data.forEach(p => sel.innerHTML += `<option value="${p.pf_id}">${p.pf_nombre}</option>`);
        } catch (e) {}
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

    function abrirModal() {
        document.getElementById('formRap').reset();
        document.getElementById('compSelect').disabled = true;
        document.getElementById('compSelect').style.opacity = '0.5';
        document.getElementById('faseSelect').disabled = true;
        document.getElementById('faseSelect').style.opacity = '0.5';
        document.getElementById('modalError').classList.add('hidden');
        document.getElementById('modalError').style.display = 'none';
        document.getElementById('modalRap').classList.add('show');
    }

    function cerrarModal() {
        document.getElementById('modalRap').classList.remove('show');
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
        formData.append('competxprog_prog_id', form.competxprog_prog_id.value);
        formData.append('competxprog_comp_id', form.competxprog_comp_id.value);
        
        const faseSelec = form.fase_id.value;

        try {
            const res = await fetch(`${API_URL}&action=store`, {
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
                if (data.success && faseSelec) {
                    const fdFase = new URLSearchParams();
                    fdFase.append('rap_id', data.rap_id);
                    fdFase.append('fase_id', faseSelec);
                    
                    await fetch(`${API_URL}&action=asignarAFase`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json' },
                        body: fdFase.toString()
                    });
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
