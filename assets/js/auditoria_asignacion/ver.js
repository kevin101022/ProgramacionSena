document.addEventListener("DOMContentLoaded", () => {
    const loadingState = document.getElementById("loadingState");
    const auditDetails = document.getElementById("auditDetails");
    const errorState = document.getElementById("errorState");
    const errorMessage = document.getElementById("errorMessage");

    // Get ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const auditId = urlParams.get('id');

    if (!auditId) {
        showError("ID de auditoría no encontrado en la URL.");
        return;
    }

    function formatDate(dateString) {
        if (!dateString) return '--';
        const date = new Date(dateString);
        const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const day = date.getDate().toString().padStart(2, '0');
        const month = months[date.getMonth()];
        const year = date.getFullYear();
        let hours = date.getHours();
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        return `${day} de ${month}, ${year} - ${hours}:${minutes} ${ampm}`;
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

    function renderDetails(item) {
        // UI Colors and Icons based on action
        const headerBg = document.getElementById('actionHeaderBg');
        const actionIcon = document.getElementById('actionIcon');
        const actionTypeEl = document.getElementById('detActionType');

        let bgColor = 'bg-slate-50';
        let textColor = 'text-slate-400';
        let iconPath = '../../assets/ionicons/receipt-outline.svg';

        switch (item.tipo_accion) {
            case 'INSERT':
                bgColor = 'bg-sena-green';
                textColor = 'text-white';
                iconPath = '../../assets/ionicons/add-circle-outline.svg';
                actionTypeEl.className = "text-xl font-black mb-1 flex items-center justify-center gap-2 text-sena-green";
                break;
            case 'UPDATE':
                bgColor = 'bg-blue-600';
                textColor = 'text-white';
                iconPath = '../../assets/ionicons/create-outline.svg';
                actionTypeEl.className = "text-xl font-black mb-1 flex items-center justify-center gap-2 text-blue-600";
                break;
            case 'DELETE':
                bgColor = 'bg-red-500';
                textColor = 'text-white';
                iconPath = '../../assets/ionicons/trash-outline.svg';
                actionTypeEl.className = "text-xl font-black mb-1 flex items-center justify-center gap-2 text-red-500";
                break;
        }

        if (headerBg) {
            headerBg.className = `h-32 flex items-center justify-center relative shadow-inner overflow-hidden transition-all duration-700 ${bgColor}`;
        }
        if (actionIcon) {
            actionIcon.setAttribute('src', iconPath);
            actionIcon.className = `text-7xl relative z-10 transition-all duration-700 ${textColor}`;
        }

        // Action info
        const actionPhrase = getActionPhrase(item.tipo_accion);
        const actionBadge = getActionBadgeLabel(item.tipo_accion);
        document.getElementById('detActionType').innerHTML = `
            <span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded mr-2 uppercase">${actionBadge}</span>
            ${actionPhrase}
        `;
        document.getElementById('detActionDate').innerHTML = `<ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon> <span>${formatDate(item.fecha_hora)}</span>`;
        document.getElementById('detAuditId').textContent = `#${item.id_auditoria}`;

        // Responsible User
        const name = item.nombre_responsable && item.nombre_responsable !== 'Sistema' ? item.nombre_responsable : 'Usuario';
        document.getElementById('detUserEmail').innerHTML = `${name}<br><span class="text-xs font-normal text-slate-500">${item.correo_usuario || ''}</span>`;
        document.getElementById('detUserDoc').textContent = item.documento_usuario_accion && item.documento_usuario_accion != 0 ? item.documento_usuario_accion : 'Desconocido';

        // Operation Details
        document.getElementById('detInstructorName').textContent = `${item.inst_nombres || 'Instructor'} ${item.inst_apellidos || ''}`;
        document.getElementById('detInstructorDoc').textContent = `CC: ${item.instructor_inst_id || 'N/A'}`;
        document.getElementById('detCompetence').textContent = item.comp_nombre_corto || 'Sin Competencia';
        document.getElementById('detFicha').textContent = `Ficha: ${item.ficha_fich_id || 'N/A'}`;

        document.getElementById('detDateStart').textContent = item.asig_fecha_ini || '--';
        document.getElementById('detDateEnd').textContent = item.asig_fecha_fin || '--';

        document.getElementById('detAmbiente').textContent = `${item.ambiente_amb_id || 'ID'} - ${item.amb_nombre || 'Desconocido'}`;
        document.getElementById('detAreaName').textContent = item.area_nombre || 'Sin Coordinación Asignada';
        document.getElementById('detAsigId').textContent = item.asig_id || 'N/A';

        loadingState.style.display = 'none';
        auditDetails.style.display = 'grid';
    }

    function showError(msg) {
        loadingState.style.display = "none";
        errorState.style.display = "block";
        errorMessage.textContent = msg;
    }

    async function loadAuditDetail() {
        try {
            const response = await fetch(`../../routing.php?controller=auditoria_asignacion&action=show&id=${auditId}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `Error del servidor: ${response.status}`);
            }

            const data = await response.json();
            renderDetails(data);
        } catch (error) {
            console.error("Error cargando detalle de auditoría:", error);
            showError(error.message || "Error al conectar con el servidor.");
        }
    }

    loadAuditDetail();
});
