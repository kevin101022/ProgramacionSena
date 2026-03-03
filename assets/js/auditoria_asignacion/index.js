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

    function formatDate(dateString) {
        if (!dateString) return '--';
        const date = new Date(dateString);
        const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const day = date.getDate().toString().padStart(2, '0');
        const month = months[date.getMonth()];
        let hours = date.getHours();
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        return `${day} ${month}, ${hours}:${minutes} ${ampm}`;
    }

    function getActionPhrase(action) {
        switch (action) {
            case 'INSERT': return 'Creación de Asignación';
            case 'UPDATE': return 'Modificación de Asignación';
            case 'DELETE': return 'Eliminación de Asignación';
            default: return action;
        }
    }

    function getActionBadgeLabel(action) {
        switch (action) {
            case 'INSERT': return 'NUEVO';
            case 'UPDATE': return 'MODIFICADO';
            case 'DELETE': return 'ELIMINADO';
            default: return action;
        }
    }

    function renderTable(data) {
        if (!data || data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-12 text-gray-400 italic text-sm">No se encontraron registros de auditoría.</td></tr>`;
            return;
        }

        tableBody.innerHTML = data.map(item => {
            const actionBadge = getActionBadgeLabel(item.tipo_accion);
            const badgeClass = getBadgeClass(item.tipo_accion);
            const instName = (item.inst_nombres || item.inst_apellidos) ? `${item.inst_nombres} ${item.inst_apellidos}` : 'Instructor no encontrado';
            const readableDate = formatDate(item.fecha_hora);
            const actionPhrase = getActionPhrase(item.tipo_accion);
            let authorName = item.nombre_responsable || item.correo_usuario || 'Desconocido';
            if (item.documento_usuario_accion && item.documento_usuario_accion != 0) {
                authorName += ` - CC: ${item.documento_usuario_accion}`;
            }
            const areaTag = item.area_nombre ? `<span class="text-[9px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-md mt-1 w-fit uppercase font-bold tracking-wider">${item.area_nombre}</span>` : '';

            return `
                <tr class="hover:bg-slate-50 cursor-pointer transition-colors group" data-id="${item.id_auditoria}">
                    <td class="whitespace-nowrap text-[11px] font-bold text-slate-500">${readableDate}</td>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-bold text-sm text-slate-700">${authorName}</span>
                            <span class="text-[10px] text-slate-400">${item.correo_usuario || ''}</span>
                        </div>
                    </td>
                    <td>
                        <div class="flex flex-col">
                            <span class="badge ${badgeClass} text-[10px] px-2 py-0.5 mb-1 w-fit">${actionBadge}</span>
                            <span class="text-[11px] font-medium text-slate-500">${actionPhrase}</span>
                        </div>
                    </td>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-bold text-sm text-sena-green">${instName}</span>
                            ${areaTag}
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
        if (!allData) return;

        const searchTerm = searchInput.value.toLowerCase().trim();
        const coordValue = coordFilter ? coordFilter.value : '';
        const actionTerm = actionFilter.value;

        const filtered = allData.filter(item => {
            const fullName = `${item.inst_nombres || ''} ${item.inst_apellidos || ''}`.toLowerCase();
            const matchesSearch = !searchTerm ||
                fullName.includes(searchTerm) ||
                (item.comp_nombre_corto && item.comp_nombre_corto.toLowerCase().includes(searchTerm)) ||
                (item.area_nombre && item.area_nombre.toLowerCase().includes(searchTerm)) ||
                item.tipo_accion.toLowerCase().includes(searchTerm);

            // Filtro por ID de coordinación (si existe el selector)
            const matchesCoord = !coordValue || String(item.area_id) === String(coordValue);

            const matchesAction = !actionTerm || item.tipo_accion === actionTerm;

            return matchesSearch && matchesCoord && matchesAction;
        });

        renderTable(filtered);
    }

    searchInput.addEventListener("input", applyFilters);
    if (coordFilter) {
        coordFilter.addEventListener("change", applyFilters);
    }
    actionFilter.addEventListener("change", applyFilters);

    loadAuditoria();
});
