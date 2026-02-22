document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ambienteForm');
    const sedeSelect = document.getElementById('sede_sede_id');

    // Load sedes into select
    fetch('../../routing.php?controller=sede&action=index', {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(sedes => {
            sedes.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.sede_id;
                opt.textContent = s.sede_nombre;
                sedeSelect.appendChild(opt);
            });
        })
        .catch(err => console.error('Error loading sedes:', err));

    form.onsubmit = (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        formData.append('controller', 'ambiente');
        formData.append('action', 'store');

        fetch('../../routing.php', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(res => {
                if (!res.ok) return res.json().then(err => { throw err; });
                return res.json();
            })
            .then(data => {
                if (data.message) {
                    NotificationService.showSuccess('Ambiente creado correctamente', () => {
                        window.location.href = 'index.php';
                    });
                } else {
                    NotificationService.showError(data.error || 'Ocurrió un error inesperado');
                }
            })
            .catch(err => {
                console.error('Error saving ambiente:', err);
                const detail = err.description || err.message || '';
                const fullMsg = (err.error || 'Error en el servidor') + (detail ? '\n\nDetalle: ' + detail : '');
                NotificationService.showError('Error al guardar: ' + fullMsg);
            });
    };

    window.closeSuccessModal = () => {
        document.getElementById('successModal').classList.remove('show');
        form.reset();
    };
});
