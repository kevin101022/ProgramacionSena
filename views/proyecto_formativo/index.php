<?php
$pageTitle = "Proyectos Formativos - SENA";
$activeNavItem = 'proyecto_formativo';
require_once '../layouts/head.php';
require_once '../layouts/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Proyectos Formativos</span>
            </nav>
            <h1 class="page-title">Proyectos Formativos</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="action-bar">
            <div class="search-container">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                <input type="text" id="searchInput" placeholder="Buscar proyecto..." class="search-input">
            </div>
            <button onclick="abrirModal()" class="btn-primary">
                <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon>
                Nuevo Proyecto
            </button>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre del Proyecto</th>
                        <th>Programa</th>
                        <th>Fases</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="proyectosTable">
                    <tr><td colspan="5" style="text-align: center; padding: 20px;">Cargando datos...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Nuevo Proyecto -->
<div id="modalProyecto" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3>Registrar Proyecto Formativo</h3>
            <button class="modal-close" onclick="cerrarModal()">
                <ion-icon src="../../assets/ionicons/close-outline.svg"></ion-icon>
            </button>
        </div>
        <form id="formProyecto" onsubmit="guardarProyecto(event)">
            <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                <div id="modalError" class="hidden" style="margin-bottom: 1rem; padding: 0.75rem; background-color: #fef2f2; color: #dc2626; border-radius: 0.5rem; text-align: center; font-size: 0.875rem;"></div>
                
                <h4 style="font-size: 0.85rem; font-weight: bold; color: #6b7280; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 1rem;">Datos Base</h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label class="form-label" style="font-size: 0.8rem;">Código del Proyecto</label>
                        <input type="text" name="pf_codigo" required class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;">
                    </div>
                    <div>
                        <label class="form-label" style="font-size: 0.8rem;">Programa de Formación</label>
                        <select id="programaSelect" name="programa_prog_codigo" required class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;">
                            <option value="">Seleccione un programa...</option>
                        </select>
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="form-label" style="font-size: 0.8rem;">Nombre del Proyecto</label>
                        <input type="text" name="pf_nombre" required class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;">
                    </div>
                    <div style="grid-column: span 2;">
                        <label class="form-label" style="font-size: 0.8rem;">Descripción</label>
                        <textarea name="pf_descripcion" rows="2" class="form-input w-full" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; width: 100%;"></textarea>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 1rem;">
                    <h4 style="font-size: 0.85rem; font-weight: bold; color: #6b7280; text-transform: uppercase; margin: 0;">Fases del Proyecto (Mín 4, Máx 6)</h4>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="button" onclick="quitarFase()" id="btnQuitarFase" class="btn-danger-soft" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; display: flex; align-items: center; gap: 0.25rem;">
                            <ion-icon src="../../assets/ionicons/remove-outline.svg"></ion-icon> Quitar
                        </button>
                        <button type="button" onclick="agregarFase()" id="btnAgregarFase" class="btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; background: #d1fae5; color: #059669; display: flex; align-items: center; gap: 0.25rem;">
                            <ion-icon src="../../assets/ionicons/add-outline.svg"></ion-icon> Agregar
                        </button>
                    </div>
                </div>
                
                <div id="fasesContainer" style="display: flex; flex-direction: column; gap: 0.75rem;"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" id="btnGuardar" class="btn-primary">
                    <ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Guardar Proyecto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const API_URL = '../../routing.php?controller=proyecto_formativo';
    let numFases = 4;

    document.addEventListener('DOMContentLoaded', () => {
        cargarProgramas();
        cargarProyectos();
        renderFases();
    });

    async function cargarProgramas() {
        try {
            const res = await fetch('../../routing.php?controller=programa&action=index', { headers: { 'Accept': 'application/json' }});
            const data = await res.json();
            const sel = document.getElementById('programaSelect');
            data.forEach(p => { sel.innerHTML += `<option value="${p.prog_codigo}">${p.prog_codigo} - ${p.prog_denominacion}</option>`; });
        } catch (error) { console.error('Error cargando programas', error); }
    }

    async function cargarProyectos() {
        try {
            const res = await fetch(`${API_URL}&action=index`, { headers: { 'Accept': 'application/json' }});
            const data = await res.json();
            const tbody = document.getElementById('proyectosTable');
            tbody.innerHTML = '';
            
            if(data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 20px; color: #6b7280;">No hay proyectos registrados en tu Centro.</td></tr>`;
                return;
            }

            data.forEach(p => {
                tbody.innerHTML += `
                    <tr>
                        <td><strong>${p.pf_codigo}</strong></td>
                        <td>${p.pf_nombre}</td>
                        <td style="font-size: 0.8rem; color: #6b7280;">${p.prog_denominacion || p.programa_prog_codigo}</td>
                        <td>
                            <span style="background: #d1fae5; color: #047857; padding: 2px 8px; border-radius: 999px; font-size: 0.75rem; font-weight: bold;">
                                ${p.fases ? p.fases.length : 'N/A'}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="../proyecto_formativo/show.php?id=${p.pf_id}" style="color: #9ca3af; padding: 4px; display: inline-block;">
                                <ion-icon src="../../assets/ionicons/eye-outline.svg" style="font-size: 1.25rem;"></ion-icon>
                            </a>
                        </td>
                    </tr>
                `;
            });
        } catch (error) { console.error(error); }
    }

    function renderFases() {
        const container = document.getElementById('fasesContainer');
        container.innerHTML = '';
        const presetNames = ["Análisis", "Planeación", "Ejecución", "Evaluación"];
        
        for (let i = 1; i <= numFases; i++) {
            let presetName = presetNames[i-1] || `Fase ${i}`;
            container.innerHTML += `
                <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; align-items: end;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #6b7280; font-weight: 600; margin-bottom: 0.25rem;">Nombre Fase ${i}</label>
                        <input type="text" name="fases[${i-1}][fase_nombre]" value="${presetName}" required style="width: 100%; padding: 0.4rem; border: 1px solid #d1d5db; border-radius: 0.25rem; font-size: 0.8rem;">
                        <input type="hidden" name="fases[${i-1}][fase_orden]" value="${i}">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #6b7280; font-weight: 600; margin-bottom: 0.25rem;">Fecha Inicio</label>
                        <input type="date" name="fases[${i-1}][fase_fecha_ini]" required style="width: 100%; padding: 0.4rem; border: 1px solid #d1d5db; border-radius: 0.25rem; font-size: 0.8rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; color: #6b7280; font-weight: 600; margin-bottom: 0.25rem;">Fecha Fin</label>
                        <input type="date" name="fases[${i-1}][fase_fecha_fin]" required style="width: 100%; padding: 0.4rem; border: 1px solid #d1d5db; border-radius: 0.25rem; font-size: 0.8rem;">
                    </div>
                </div>
            `;
        }

        document.getElementById('btnQuitarFase').disabled = (numFases <= 4);
        document.getElementById('btnAgregarFase').disabled = (numFases >= 6);
    }

    function agregarFase() { if (numFases < 6) { numFases++; renderFases(); } }
    function quitarFase() { if (numFases > 4) { numFases--; renderFases(); } }

    function abrirModal() {
        document.getElementById('formProyecto').reset();
        numFases = 4;
        renderFases();
        document.getElementById('modalError').classList.add('hidden');
        document.getElementById('modalProyecto').classList.add('show');
    }

    function cerrarModal() {
        document.getElementById('modalProyecto').classList.remove('show');
    }

    async function guardarProyecto(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btnGuardar');
        const errorDiv = document.getElementById('modalError');
        
        errorDiv.classList.add('hidden');
        btn.disabled = true;
        btn.innerHTML = 'Guardando...';

        const formData = new URLSearchParams();
        formData.append('proyecto[pf_codigo]', form.pf_codigo.value);
        formData.append('proyecto[pf_nombre]', form.pf_nombre.value);
        formData.append('proyecto[pf_descripcion]', form.pf_descripcion.value);
        formData.append('proyecto[programa_prog_codigo]', form.programa_prog_codigo.value);

        for(let i=0; i<numFases; i++) {
            formData.append(`fases[${i}][fase_nombre]`, form[`fases[${i}][fase_nombre]`].value);
            formData.append(`fases[${i}][fase_orden]`, form[`fases[${i}][fase_orden]`].value);
            formData.append(`fases[${i}][fase_fecha_ini]`, form[`fases[${i}][fase_fecha_ini]`].value);
            formData.append(`fases[${i}][fase_fecha_fin]`, form[`fases[${i}][fase_fecha_fin]`].value);
        }

        try {
            const res = await fetch(`${API_URL}&action=store`, {
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
                cerrarModal();
                cargarProyectos();
            }
        } catch (error) {
            errorDiv.innerHTML = `<ion-icon src="../../assets/ionicons/warning-outline.svg" style="vertical-align: middle;"></ion-icon> Problema de red o servidor no responde`;
            errorDiv.classList.remove('hidden');
            errorDiv.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<ion-icon src="../../assets/ionicons/save-outline.svg"></ion-icon> Guardar Proyecto`;
        }
    }
</script>
</body>
</html>
