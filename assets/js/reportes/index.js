document.addEventListener('DOMContentLoaded', () => {
    const reportCards = document.querySelectorAll('.report-card');
    const placeholder = document.getElementById('reportPlaceholder');
    const loading = document.getElementById('reportLoading');
    const content = document.getElementById('reportContent');

    reportCards.forEach(card => {
        card.addEventListener('click', async () => {
            const report = card.dataset.report;
            reportCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            await loadReport(report);
        });
    });

    const loadReport = async (reportName) => {
        placeholder.style.display = 'none';
        loading.style.display = '';
        content.style.display = 'none';

        try {
            const res = await fetch(`../../routing.php?controller=reporte&action=${reportName}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (!res.ok) throw new Error(data.error || 'Error al generar reporte');

            let html = '';
            switch (reportName) {
                case 'instructoresPorCentro':
                    html = renderInstructoresPorCentro(data);
                    break;
                case 'fichasActivasPorPrograma':
                    html = renderFichasPorPrograma(data);
                    break;
                case 'asignacionesPorInstructor':
                    html = renderAsignacionesPorInstructor(data);
                    break;
                case 'competenciasPorPrograma':
                    html = renderCompetenciasPorPrograma(data);
                    break;
            }

            content.innerHTML = html;
            loading.style.display = 'none';
            content.style.display = '';
        } catch (e) {
            loading.style.display = 'none';
            content.innerHTML = `<div class="text-center py-8 text-red-500"><p>Error: ${e.message}</p></div>`;
            content.style.display = '';
        }
    };

    // ── Render: Instructores por Centro ────────────────────────
    const renderInstructoresPorCentro = (data) => {
        if (!data.length) return '<p class="text-gray-400 text-center py-8">No hay datos disponibles</p>';
        return data.map(centro => `
            <div class="report-group">
                <div class="report-group-header">
                    <ion-icon src="../../assets/ionicons/business-outline.svg" class="text-sena-green"></ion-icon>
                    ${centro.cent_nombre}
                    <span class="badge">${centro.instructores.length}</span>
                </div>
                <table class="report-table">
                    <thead><tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Teléfono</th></tr></thead>
                    <tbody>
                        ${centro.instructores.map(i => `
                            <tr>
                                <td class="font-semibold text-sena-green">${i.inst_id}</td>
                                <td class="font-semibold">${i.inst_nombres} ${i.inst_apellidos}</td>
                                <td>${i.inst_correo || '-'}</td>
                                <td>${i.inst_telefono || '-'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `).join('');
    };

    // ── Render: Fichas por Programa ───────────────────────────
    const renderFichasPorPrograma = (data) => {
        if (!data.length) return '<p class="text-gray-400 text-center py-8">No hay datos disponibles</p>';
        return data.map(prog => `
            <div class="report-group">
                <div class="report-group-header">
                    <ion-icon src="../../assets/ionicons/school-outline.svg" class="text-blue-500"></ion-icon>
                    ${prog.prog_codigo} — ${prog.prog_denominacion}
                    <span class="badge">${prog.fichas.length}</span>
                </div>
                <table class="report-table">
                    <thead><tr><th>Ficha</th><th>Jornada</th><th>Instructor Líder</th><th>Inicio Lectiva</th><th>Fin Lectiva</th></tr></thead>
                    <tbody>
                        ${prog.fichas.map(f => `
                            <tr>
                                <td class="font-semibold text-sena-green">${f.fich_id}</td>
                                <td><span class="status-badge status-active">${f.fich_jornada || 'N/A'}</span></td>
                                <td>${f.inst_lider || '-'}</td>
                                <td>${f.fich_fecha_ini_lectiva ? new Date(f.fich_fecha_ini_lectiva).toLocaleDateString('es-CO') : '-'}</td>
                                <td>${f.fich_fecha_fin_lectiva ? new Date(f.fich_fecha_fin_lectiva).toLocaleDateString('es-CO') : '-'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `).join('');
    };

    // ── Render: Asignaciones por Instructor ───────────────────
    const renderAsignacionesPorInstructor = (data) => {
        if (!data.length) return '<p class="text-gray-400 text-center py-8">No hay datos disponibles</p>';
        return data.map(inst => `
            <div class="report-group">
                <div class="report-group-header">
                    <ion-icon src="../../assets/ionicons/person-outline.svg" class="text-purple-500"></ion-icon>
                    ${inst.inst_nombres} ${inst.inst_apellidos}
                    <span class="badge">${inst.asignaciones.length}</span>
                </div>
                <table class="report-table">
                    <thead><tr><th>Ficha</th><th>Programa</th><th>Competencia</th><th>Ambiente</th><th>Inicio</th><th>Fin</th></tr></thead>
                    <tbody>
                        ${inst.asignaciones.map(a => `
                            <tr>
                                <td class="font-semibold">${a.fich_id}</td>
                                <td>${a.programa || '-'}</td>
                                <td>${a.competencia || '-'}</td>
                                <td>${a.ambiente || '-'}</td>
                                <td>${a.fecha_ini ? new Date(a.fecha_ini).toLocaleDateString('es-CO') : '-'}</td>
                                <td>${a.fecha_fin ? new Date(a.fecha_fin).toLocaleDateString('es-CO') : '-'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `).join('');
    };

    // ── Render: Competencias por Programa ─────────────────────
    const renderCompetenciasPorPrograma = (data) => {
        if (!data.length) return '<p class="text-gray-400 text-center py-8">No hay datos disponibles</p>';
        return data.map(prog => `
            <div class="report-group">
                <div class="report-group-header">
                    <ion-icon src="../../assets/ionicons/book-outline.svg" class="text-amber-500"></ion-icon>
                    ${prog.prog_codigo} — ${prog.prog_denominacion}
                    <span class="badge">${prog.competencias.length}</span>
                </div>
                <table class="report-table">
                    <thead><tr><th>ID</th><th>Nombre Corto</th><th>Unidad</th><th>Horas</th></tr></thead>
                    <tbody>
                        ${prog.competencias.map(c => `
                            <tr>
                                <td class="font-semibold text-sena-green">${c.comp_id}</td>
                                <td class="font-semibold">${c.comp_nombre_corto || '-'}</td>
                                <td>${c.comp_unidad || '-'}</td>
                                <td>${c.comp_horas || '-'} hrs</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `).join('');
    };
});
