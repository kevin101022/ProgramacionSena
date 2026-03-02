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

    async function loadAuditDetail() {
        try {
            const response = await fetch(`../../routing.php?controller=auditoria_asignacion&action=show&id=${auditId}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error("No se pudo conectar con el servidor.");

            const data = await response.json();
            if (data.error) throw new Error(data.error);

            renderDetails(data);
        } catch (error) {
            console.error("Error:", error);
            showError(error.message);
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
                actionTypeEl.className = "text-2xl font-black mb-1 flex items-center justify-center gap-2 text-sena-green";
                break;
            case 'UPDATE':
                bgColor = 'bg-blue-600';
                textColor = 'text-white';
                iconPath = '../../assets/ionicons/create-outline.svg';
                actionTypeEl.className = "text-2xl font-black mb-1 flex items-center justify-center gap-2 text-blue-600";
                break;
            case 'DELETE':
                bgColor = 'bg-red-500';
                textColor = 'text-white';
                iconPath = '../../assets/ionicons/trash-outline.svg';
                actionTypeEl.className = "text-2xl font-black mb-1 flex items-center justify-center gap-2 text-red-500";
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
        document.getElementById('detActionType').textContent = item.tipo_accion;
        document.getElementById('detActionDate').innerHTML = `<ion-icon src="../../assets/ionicons/time-outline.svg"></ion-icon> <span>${item.fecha_hora}</span>`;
        document.getElementById('detAuditId').textContent = `#${item.id_auditoria}`;

        // Responsible User
        document.getElementById('detUserEmail').textContent = item.correo_usuario || 'Usuario Desconocido';
        document.getElementById('detUserDoc').textContent = item.documento_usuario_accion || 'Sin Documento';

        // Operation Details
        document.getElementById('detInstructorName').textContent = `${item.inst_nombres || 'Instructor'} ${item.inst_apellidos || ''}`;
        document.getElementById('detInstructorDoc').textContent = `CC: ${item.instructor_inst_id || 'N/A'}`;
        document.getElementById('detCompetence').textContent = item.comp_nombre_corto || 'Sin Competencia';
        document.getElementById('detFicha').textContent = `Ficha: ${item.ficha_fich_id || 'N/A'}`;

        document.getElementById('detDateStart').textContent = item.asig_fecha_ini || '--';
        document.getElementById('detDateEnd').textContent = item.asig_fecha_fin || '--';

        document.getElementById('detAmbiente').textContent = `Ambiente: ${item.ambiente_amb_id || 'ID'} - ${item.amb_nombre || 'N/A'} | Sede: ${item.sede_nombre || 'N/A'}`;
        document.getElementById('detAsigId').textContent = item.asig_id || 'N/A';

        loadingState.style.display = 'none';
        auditDetails.style.display = 'grid';
    }

    function showError(msg) {
        loadingState.style.display = "none";
        errorState.style.display = "block";
        errorMessage.textContent = msg;
    }

    loadAuditDetail();
});
