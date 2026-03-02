document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("auditoriaTableBody");
    const searchInput = document.getElementById("searchInput");
    const coordFilter = document.getElementById("coordFilter");
    const actionFilter = document.getElementById("actionFilter");

    let allData = [];

    async function loadAuditoria() {
        try {
            const response = await fetch("../../routing.php?controller=auditoria_asignacion&action=index", {
                headers: { 'Accept': 'application/json' }
            });
            allData = await response.json();

            // Actualizar contador
            const totalEl = document.getElementById("totalAuditorias");
            if (totalEl) totalEl.textContent = allData.length;

            renderTable(allData);
        } catch (error) {
            console.error("Error cargando auditoría:", error);
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-red-500 py-8 text-sm">Error al cargar los datos.</td></tr>`;
        }
    }

    function renderTable(data) {
        if (!data || data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-12 text-gray-400 italic text-sm">No se encontraron registros de auditoría.</td></tr>`;
            return;
        }

        tableBody.innerHTML = data.map(item => {
            const badgeClass = getBadgeClass(item.tipo_accion);
            const instName = (item.inst_nombres || item.inst_apellidos) ? `${item.inst_nombres} ${item.inst_apellidos}` : 'Instructor no encontrado';

            return `
                <tr class="hover:bg-slate-50 cursor-pointer transition-colors group" data-id="${item.id_auditoria}">
                    <td class="whitespace-nowrap text-[11px] font-medium text-slate-500">${item.fecha_hora}</td>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-semibold text-sm text-slate-700">${item.correo_usuario || 'Desconocido'}</span>
                            <span class="text-[10px] text-slate-400">CC: ${item.documento_usuario_accion || 'N/A'}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge ${badgeClass} text-[10px] px-2 py-0.5">${item.tipo_accion}</span>
                    </td>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-bold text-sm text-sena-green">${instName}</span>
                            <span class="text-[10px] text-slate-500 italic">${item.comp_nombre_corto || 'Sin detalles de competencia'}</span>
                        </div>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-2 pr-2">
                            <span class="text-[10px] font-bold text-sena-green opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">VER DETALLE</span>
                            <div class="btn-icon text-sena-green bg-green-50 rounded-full p-2 flex items-center justify-center">
                                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Delegación de eventos para eficiencia
    tableBody.addEventListener("click", (e) => {
        const row = e.target.closest("tr");
        if (row && row.dataset.id) {
            window.location.href = `ver.php?id=${row.dataset.id}`;
        }
    });

    function getBadgeClass(action) {
        switch (action) {
            case 'INSERT': return 'badge-green';
            case 'UPDATE': return 'badge-blue';
            case 'DELETE': return 'badge-red';
            default: return 'badge-gray';
        }
    }

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const coordTerm = coordFilter.value.toLowerCase().trim();
        const actionTerm = actionFilter.value;

        const filtered = allData.filter(item => {
            const fullName = `${item.inst_nombres} ${item.inst_apellidos}`.toLowerCase();
            const matchesSearch = !searchTerm ||
                fullName.includes(searchTerm) ||
                (item.comp_nombre_corto && item.comp_nombre_corto.toLowerCase().includes(searchTerm)) ||
                item.tipo_accion.toLowerCase().includes(searchTerm);

            const matchesCoord = !coordTerm ||
                (item.documento_usuario_accion && item.documento_usuario_accion.toLowerCase().includes(coordTerm)) ||
                (item.correo_usuario && item.correo_usuario.toLowerCase().includes(coordTerm));

            const matchesAction = !actionTerm || item.tipo_accion === actionTerm;

            return matchesSearch && matchesCoord && matchesAction;
        });

        renderTable(filtered);
    }

    searchInput.addEventListener("input", applyFilters);
    coordFilter.addEventListener("input", applyFilters);
    actionFilter.addEventListener("change", applyFilters);

    loadAuditoria();
});
