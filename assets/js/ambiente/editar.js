document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    const form = document.getElementById('ambienteEditForm');
    const sedeSelect = document.getElementById('sede_sede_id');

    if (!id) {
        window.location.href = 'index.php';
        return;
    }

    // Load sedes and current data
    Promise.all([
        fetch('../../routing.php?controller=sede&action=index', {
            headers: { 'Accept': 'application/json' }
        }).then(res => res.json()),
        fetch(`../../routing.php?controller=ambiente&action=show&id=${id}`, {
            headers: { 'Accept': 'application/json' }
        }).then(res => res.json())
    ]).then(([sedes, data]) => {
        sedes.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.sede_id;
            opt.textContent = s.sede_nombre;
            sedeSelect.appendChild(opt);
        });

        document.getElementById('amb_id').value = data.amb_id;
        document.getElementById('amb_nombre').value = data.amb_nombre;
        sedeSelect.value = data.sede_sede_id;

        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('formCard').style.display = 'block';
    });

    form.onsubmit = (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        formData.append('controller', 'ambiente');
        formData.append('action', 'update');

        fetch('../../routing.php', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        }).then(res => res.json())
            .then(data => {
                if (data.message) {
                    NotificationService.showSuccess('Ambiente actualizado correctamente', () => {
                        window.location.href = `ver.php?id=${id}`;
                    });
                } else {
                    NotificationService.showError(data.error);
                }
            })
            .catch(err => {
                console.error('Error updating ambiente:', err);
                const detail = err.description || err.message || '';
                const fullMsg = (err.error || 'Error en el servidor') + (detail ? '\n\nDetalle: ' + detail : '');
                NotificationService.showError('Error al actualizar: ' + fullMsg);
            });
    };
});
