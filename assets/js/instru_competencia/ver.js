document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');

    const loadingState = document.getElementById('loadingState');
    const detailsSection = document.getElementById('habilitacionDetails');
    const errorState = document.getElementById('errorState');
    const deleteBtn = document.getElementById('deleteBtn');

    if (!id) {
        showError('No se proporcionó un ID de habilitación.');
        return;
    }

    const loadHabilitacion = async () => {
        try {
            const res = await fetch(`../../routing.php?controller=instru_competencia&action=show&id=${id}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (!res.ok || data.error) {
                showError(data.error || 'Habilitación no encontrada');
                return;
            }

            document.getElementById('detInstructor').textContent = `${data.inst_nombres || ''} ${data.inst_apellidos || ''}`;
            document.getElementById('detPrograma').textContent = data.prog_denominacion || 'N/A';
            document.getElementById('detInstructorFull').textContent = `${data.inst_nombres || ''} ${data.inst_apellidos || ''}`;
            document.getElementById('detProgramaFull').textContent = data.prog_denominacion || 'N/A';
            document.getElementById('detCompetencia').textContent = data.comp_nombre_corto || 'N/A';

            const vigencia = data.inscomp_vigencia ? new Date(data.inscomp_vigencia).toLocaleDateString('es-CO', {
                year: 'numeric', month: 'long', day: 'numeric'
            }) : 'N/A';
            document.getElementById('detVigencia').textContent = vigencia;

            if (loadingState) loadingState.style.display = 'none';
            if (detailsSection) detailsSection.style.display = '';
        } catch (e) {
            showError('Error de conexión al cargar la habilitación.');
        }
    };

    const showError = (msg) => {
        if (loadingState) loadingState.style.display = 'none';
        if (errorState) {
            errorState.style.display = '';
            const errorMsg = document.getElementById('errorMessage');
            if (errorMsg) errorMsg.textContent = msg;
        }
    };

    if (deleteBtn) {
        deleteBtn.onclick = async () => {
            if (!confirm('¿Está seguro de eliminar esta habilitación?')) return;
            try {
                const res = await fetch(`../../routing.php?controller=instru_competencia&action=destroy&id=${id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const result = await res.json();

                if (res.ok) {
                    NotificationService.showSuccess('Habilitación eliminada correctamente');
                    setTimeout(() => window.location.href = 'index.php', 1000);
                } else {
                    NotificationService.showError(result.error || 'Error al eliminar');
                }
            } catch (e) {
                NotificationService.showError('Error de conexión');
            }
        };
    }

    loadHabilitacion();
});
