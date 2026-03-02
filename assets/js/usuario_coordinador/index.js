window.usuarioCoordinadorModule = (() => {
    let usuarios = [];
    let currentPage = 1;
    let itemsPerPage = 10;

    const loadUsuarios = async () => {
        try {
            const estadoNode = document.getElementById('estadoFilter');
            const estadoFilter = estadoNode ? estadoNode.value : '';
            const url = `../../routing.php?controller=usuario_coordinador&action=index${estadoFilter !== '' ? '&estado=' + estadoFilter : ''}`;
            const response = await fetch(url);

            if (!response.ok) throw new Error('Error al cargar usuarios coordinadores');

            usuarios = await response.json();

            // Renderizar la tabla y estadísticas solo si estamos en index.php
            if (document.getElementById('usuariosTableBody')) {
                updateStats();
                currentPage = 1; // Reset a página 1 al cargar
                renderTable();
            }
        } catch (error) {
            console.error(error);
            if (window.NotificationService) {
                NotificationService.showError('No se pudieron cargar los datos');
            }
        }
    };

    const updateStats = () => {
        const totalActivos = document.getElementById('totalActivos');
        const totalInactivos = document.getElementById('totalInactivos');

        if (totalActivos) {
            const activos = usuarios.filter(u => parseInt(u.estado) === 1).length;
            totalActivos.textContent = activos;
        }
        if (totalInactivos) {
            const inactivos = usuarios.filter(u => parseInt(u.estado) === 0).length;
            totalInactivos.textContent = inactivos;
        }
    };

    const renderTable = () => {
        const tbody = document.getElementById('usuariosTableBody');
        if (!tbody) return;

        const searchInputNode = document.getElementById('searchInput');
        const searchTerm = searchInputNode ? searchInputNode.value.toLowerCase().trim() : '';

        const filteredData = usuarios.filter(u => {
            const matchQuery = !searchTerm ||
                (u.coord_nombre_coordinador || '').toLowerCase().includes(searchTerm) ||
                (u.numero_documento || '').toString().includes(searchTerm) ||
                (u.coord_correo || '').toLowerCase().includes(searchTerm);
            return matchQuery;
        });

        // Metadatos de paginación
        const totalRecords = document.getElementById('totalRecords');
        if (totalRecords) totalRecords.textContent = filteredData.length;

        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

        const start = (currentPage - 1) * itemsPerPage;
        const end = Math.min(start + itemsPerPage, filteredData.length);
        const paginatedData = filteredData.slice(start, end);

        if (paginatedData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="py-20 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mb-4">
                                <ion-icon src="../../assets/ionicons/search-outline.svg" class="text-3xl"></ion-icon>
                            </div>
                            <p class="text-slate-400 font-medium italic">No se encontraron resultados para "${searchTerm}"</p>
                        </div>
                    </td>
                </tr>
            `;
            updatePaginationInfo(0, 0, filteredData.length, totalPages);
            return;
        }

        tbody.innerHTML = paginatedData.map(u => {
            const isActivo = parseInt(u.estado) === 1;
            const estadoBadge = isActivo
                ? `<span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-green-50 text-sena-green border border-green-100 uppercase tracking-tighter">Activo</span>`
                : `<span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-red-50 text-red-500 border border-red-100 uppercase tracking-tighter">Inactivo</span>`;

            const initials = (u.coord_nombre_coordinador || 'U').split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

            return `
                <tr class="hover:bg-slate-50/50 transition-all group cursor-pointer border-b border-slate-50/50" onclick="window.location.href='ver.php?id=${u.numero_documento}'">
                    <td class="pl-6 py-4">
                        <span class="text-sm font-black text-slate-700">${u.numero_documento}</span>
                    </td>
                    <td class="py-4">
                        <div class="flex flex-col">
                            <span class="font-black text-slate-800 text-sm leading-tight">${u.coord_nombre_coordinador}</span>
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Coordinador Académico</span>
                        </div>
                    </td>
                    <td class="py-4">
                        <div class="flex items-center gap-2">
                            <ion-icon src="../../assets/ionicons/mail-outline.svg" class="text-sena-green text-xs"></ion-icon>
                            <span class="text-xs font-black text-slate-600">${u.coord_correo || 'No registrado'}</span>
                        </div>
                    </td>
                    <td class="py-4 pr-6 text-right">
                        <div class="flex items-center justify-end gap-2 text-sena-green font-black text-[10px] opacity-0 group-hover:opacity-100 transition-all">
                            VER PERFIL
                            <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg" class="text-lg"></ion-icon>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        updatePaginationInfo(start + 1, end, filteredData.length, totalPages);
    };

    const updatePaginationInfo = (from, to, total, totalPages) => {
        const showingFrom = document.getElementById('showingFrom');
        const showingTo = document.getElementById('showingTo');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const paginationNumbers = document.getElementById('paginationNumbers');

        if (showingFrom) showingFrom.textContent = from;
        if (showingTo) showingTo.textContent = to;
        if (prevBtn) prevBtn.disabled = currentPage === 1;
        if (nextBtn) nextBtn.disabled = currentPage === totalPages || totalPages === 0;

        if (paginationNumbers) {
            paginationNumbers.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.className = `w-7 h-7 rounded-lg text-[10px] font-black transition-all ${i === currentPage ? 'bg-sena-green text-white shadow-md' : 'bg-white text-slate-400 border border-slate-100 hover:border-sena-green hover:text-sena-green'}`;
                btn.textContent = i;
                btn.onclick = (e) => {
                    e.stopPropagation();
                    currentPage = i;
                    renderTable();
                };
                paginationNumbers.appendChild(btn);
            }
        }
    };

    const toggleEstado = async (id, nuevoEstado) => {
        if (!window.NotificationService) return;

        const accion = nuevoEstado === 1 ? 'Habilitar' : 'Deshabilitar';
        const msg = nuevoEstado === 1
            ? 'El coordinador volverá a tener acceso al sistema.'
            : 'El coordinador perderá el acceso al sistema inmediatamente.';

        NotificationService.showConfirm(`¿${accion} Coordinador? ${msg}`, async () => {
            try {
                const response = await fetch('../../routing.php?controller=usuario_coordinador&action=toggle', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ numero_documento: id, estado: nuevoEstado })
                });

                if (response.ok) {
                    NotificationService.showSuccess(`Coordinador ${nuevoEstado === 1 ? 'Habilitado' : 'Deshabilitado'}`);
                    // Si estamos en la vista de detalle, recargar la página; si estamos en el listado, recargar la tabla
                    if (document.getElementById('usuariosTableBody')) {
                        loadUsuarios();
                    } else {
                        setTimeout(() => location.reload(), 1200);
                    }
                } else {
                    NotificationService.showError('No se pudo modificar el estado');
                }
            } catch (error) {
                console.error(error);
                NotificationService.showError('Error de conexión');
            }
        });
    };

    const openModal = () => {
        const form = document.getElementById('coordinadorForm');
        if (form) form.reset();
        const modal = document.getElementById('coordinadorModal');
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                const content = modal.querySelector('.modal-content');
                if (content) content.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }
    };

    const closeModal = () => {
        const modal = document.getElementById('coordinadorModal');
        if (modal) {
            const content = modal.querySelector('.modal-content');
            if (content) content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    };

    const openEditModal = (user) => {
        const editId = document.getElementById('edit_coord_id');
        const editNombre = document.getElementById('edit_coord_nombre');
        const editCorreo = document.getElementById('edit_coord_correo');
        const editPass = document.getElementById('edit_coord_password');

        if (editId) editId.value = user.numero_documento;
        if (editNombre) editNombre.value = user.coord_nombre_coordinador;
        if (editCorreo) editCorreo.value = user.coord_correo !== 'N/A' ? user.coord_correo : '';
        if (editPass) editPass.value = '';

        const modal = document.getElementById('coordinadorEditModal');
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                const content = modal.querySelector('.modal-content');
                if (content) content.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }
    };

    const closeEditModal = () => {
        const modal = document.getElementById('coordinadorEditModal');
        if (modal) {
            const content = modal.querySelector('.modal-content');
            if (content) content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    };

    const handleFormSubmit = async (e) => {
        e.preventDefault();

        const data = {
            numero_documento: document.getElementById('coord_id').value,
            coord_nombre_coordinador: document.getElementById('coord_nombre').value,
            coord_correo: document.getElementById('coord_correo').value || 'N/A',
            coord_password: document.getElementById('coord_password').value || '123456'
        };

        try {
            const response = await fetch('../../routing.php?controller=usuario_coordinador&action=store', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                if (window.NotificationService) NotificationService.showSuccess('Coordinador registrado');
                closeModal();
                loadUsuarios();
            } else {
                const err = await response.json();
                if (window.NotificationService) NotificationService.showError(err.error || 'Error al registrar');
            }
        } catch (error) {
            console.error(error);
            if (window.NotificationService) NotificationService.showError('Error de conexión');
        }
    };

    const handleEditSubmit = async (e) => {
        e.preventDefault();

        const data = {
            numero_documento: document.getElementById('edit_coord_id').value,
            coord_nombre_coordinador: document.getElementById('edit_coord_nombre').value,
            coord_correo: document.getElementById('edit_coord_correo').value || 'N/A',
            coord_password: document.getElementById('edit_coord_password').value
        };

        try {
            const response = await fetch('../../routing.php?controller=usuario_coordinador&action=update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                if (window.NotificationService) NotificationService.showSuccess('Datos actualizados');
                closeEditModal();
                loadUsuarios();
            } else {
                if (window.NotificationService) NotificationService.showError('Error al actualizar');
            }
        } catch (error) {
            console.error(error);
            if (window.NotificationService) NotificationService.showError('Error de conexión');
        }
    };

    // Inicialización al cargar el DOM
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('usuariosTableBody')) {
            loadUsuarios();
        }

        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                currentPage = 1;
                renderTable();
            });
        }

        const estadoFilter = document.getElementById('estadoFilter');
        if (estadoFilter) {
            estadoFilter.addEventListener('change', loadUsuarios);
        }

        const prevBtn = document.getElementById('prevBtn');
        if (prevBtn) {
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable();
                }
            };
        }

        const nextBtn = document.getElementById('nextBtn');
        if (nextBtn) {
            nextBtn.onclick = () => {
                const totalFiltered = usuarios.filter(u => {
                    const searchInputNode = document.getElementById('searchInput');
                    const searchTerm = searchInputNode ? searchInputNode.value.toLowerCase().trim() : '';
                    return !searchTerm ||
                        (u.coord_nombre_coordinador || '').toLowerCase().includes(searchTerm) ||
                        (u.numero_documento || '').toString().includes(searchTerm) ||
                        (u.coord_correo || '').toLowerCase().includes(searchTerm);
                }).length;
                const totalPages = Math.ceil(totalFiltered / itemsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable();
                }
            };
        }
    });

    return {
        openModal, closeModal, handleFormSubmit,
        openEditModal, closeEditModal, handleEditSubmit,
        toggleEstado
    };
})();
