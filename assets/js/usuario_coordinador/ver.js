/**
 * Detail View Manager - Usuario Coordinador Premium
 */
document.addEventListener('DOMContentLoaded', () => {
    const loadingState = document.getElementById('loadingState');
    const coordinadorDetails = document.getElementById('coordinadorDetails');
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');

    const urlParams = new URLSearchParams(window.location.search);
    const userId = urlParams.get('id');

    if (!userId) {
        showError('No se proporcionó la identificación del coordinador.');
        return;
    }

    async function loadDetail() {
        try {
            const response = await fetch(`../../routing.php?controller=usuario_coordinador&action=show&id=${userId}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Error al cargar perfil');

            const data = await response.json();
            if (data.error) throw new Error(data.error);

            renderDetail(data);
        } catch (error) {
            console.error(error);
            showError(error.message);
        }
    }

    function renderDetail(data) {
        const detNombre = document.getElementById('detNombre');
        const detDocumento = document.getElementById('detDocumento');
        const infoCorreo = document.getElementById('infoCorreo');
        const infoEstado = document.getElementById('infoEstado');
        const infoCargo = document.getElementById('infoCargo');
        const infoCargoDesc = document.getElementById('infoCargoDesc');
        const iconAsignacionContainer = document.getElementById('iconAsignacionContainer');
        const toggleBtn = document.getElementById('toggleBtn');
        const toggleText = document.getElementById('toggleText');
        const toggleIcon = document.getElementById('toggleIcon');
        const ctaAsignar = document.getElementById('ctaAsignar');

        // Datos básicos
        detNombre.textContent = data.coord_nombre_coordinador;
        detDocumento.textContent = `CC ${data.numero_documento}`;
        infoCorreo.textContent = data.coord_correo !== 'N/A' ? data.coord_correo : 'Sin correo registrado';

        // Estado
        const isActivo = parseInt(data.estado) === 1;
        infoEstado.innerHTML = isActivo
            ? `<span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-green-50 text-sena-green border border-green-100 shadow-sm uppercase tracking-widest">
                <span class="w-1.5 h-1.5 mr-2 bg-sena-green rounded-full animate-pulse"></span>Acceso Habilitado</span>`
            : `<span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-red-50 text-red-500 border border-red-100 shadow-sm uppercase tracking-widest">
                <span class="w-1.5 h-1.5 mr-2 bg-red-500 rounded-full"></span>Acceso Denegado</span>`;

        // Configuración botón toggle
        if (toggleBtn) {
            toggleBtn.className = isActivo
                ? "w-full flex items-center justify-center gap-2 py-3 text-xs font-black transition-all rounded-xl border border-red-50 text-red-400 hover:bg-red-50"
                : "w-full flex items-center justify-center gap-2 py-3 text-xs font-black transition-all rounded-xl border border-green-50 text-sena-green hover:bg-green-50 hover:shadow-md";

            toggleText.textContent = isActivo ? 'Deshabilitar Acceso' : 'Habilitar Acceso';
            if (toggleIcon) toggleIcon.setAttribute('src', isActivo ? '../../assets/ionicons/power-outline.svg' : '../../assets/ionicons/refresh-outline.svg');

            toggleBtn.onclick = () => {
                if (window.usuarioCoordinadorModule) {
                    window.usuarioCoordinadorModule.toggleEstado(data.numero_documento, isActivo ? 0 : 1);
                    // reload on success happens in module
                }
            };
        }

        // Asignación de Cargo
        if (data.coordinacion_asignada) {
            iconAsignacionContainer.classList.add('bg-sena-green', 'text-white');
            iconAsignacionContainer.innerHTML = `<ion-icon src="../../assets/ionicons/business-outline.svg" class="text-4xl"></ion-icon>`;
            infoCargo.textContent = data.coordinacion_asignada;
            infoCargo.className = "text-lg font-black text-sena-green leading-tight";
            infoCargoDesc.textContent = "Líder responsable de esta área de coordinación académica.";
            if (ctaAsignar) ctaAsignar.style.display = 'none';
        } else {
            iconAsignacionContainer.classList.add('bg-slate-50', 'text-slate-200');
            infoCargo.textContent = "Sin dependencia asignada";
            infoCargoDesc.textContent = "Este funcionario se encuentra disponible para ser asignado a un área de coordinación técnica.";
            if (ctaAsignar) ctaAsignar.style.display = 'block';
        }

        // Editar
        const editBtn = document.getElementById('editBtn');
        if (editBtn) {
            editBtn.onclick = () => {
                if (window.usuarioCoordinadorModule) {
                    window.usuarioCoordinadorModule.openEditModal(data);
                }
            };
        }

        loadingState.style.display = 'none';
        coordinadorDetails.style.display = 'grid';
    }

    function showError(msg) {
        if (loadingState) loadingState.style.display = 'none';
        if (errorState) errorState.style.display = 'block';
        if (errorMessage) errorMessage.textContent = msg;
    }

    loadDetail();
});
