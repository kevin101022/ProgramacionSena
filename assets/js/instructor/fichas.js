
Fatal error
: Uncaught PDOException: SQLSTATE[42703]: Undefined column: 7 ERROR: no existe la columna c.numero_documento LINE 9: ... JOIN COORDINACION c ON f.COORDINACION_coord_id = c.numero_d... ^ in C:\xampp\htdocs\ProgramacionSena\model\InstructorModel.php:269 Stack trace: #0 C:\xampp\htdocs\ProgramacionSena\model\InstructorModel.php(269): PDOStatement->execute(Array) #1 C:\xampp\htdocs\ProgramacionSena\views\instructor\mi_ficha.php(18): InstructorModel->getFichasLider() #2 {main} thrown in
C:\xampp\htdocs\ProgramacionSena\model\InstructorModel.php
on line
269
class MisFichasLider {
    constructor() {
        this.fichasInfo = [];
        this.userId = window.USER_ID || null;
        this.init();
    }

    async init() {
        if (!this.userId) {
            this.showError('No se pudo identificar el usuario.');
            return;
        }
        await this.loadFichas();
        this.setupSearch();
    }

    async loadFichas() {
        try {
            const tableBody = document.getElementById('fichasTableBody');
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-8">Cargando fichas...</td></tr>';
            }

            const response = await fetch(`../../routing.php?controller=instructor&action=getFichasLider&id=${this.userId}`);

            if (!response.ok) {
                throw new Error('Error al cargar fichas');
            }

            this.fichasInfo = await response.json();

            if (this.fichasInfo.error) {
                throw new Error(this.fichasInfo.error);
            }

            this.renderTable(this.fichasInfo);
        } catch (error) {
            console.error('Error:', error);
            this.showError('No se pudieron cargar sus fichas como líder.');
        }
    }

    renderTable(data) {
        const tbody = document.getElementById('fichasTableBody');
        if (!tbody) return;

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-8">
                        <div class="empty-state">
                            <ion-icon src="../../assets/ionicons/layers-outline.svg" class="empty-icon"></ion-icon>
                            <p>No eres líder de ninguna ficha actualmente.</p>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = data.map((ficha, index) => `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="text-center font-medium text-slate-500">${index + 1}</td>
                <td>
                    <a href="../ficha/ver.php?id=${ficha.fich_id}" class="font-bold text-[#00324D] text-lg hover:text-sena-green transition-colors decoration-none">
                        ${ficha.fich_id || 'N/A'}
                    </a>
                    <div class="text-xs text-slate-400">
                      ${this.formatDate(ficha.fich_fecha_ini_lectiva)} a ${this.formatDate(ficha.fich_fecha_fin_lectiva)}
                    </div>
                </td>
                <td>
                    <div class="font-medium text-slate-700">${ficha.prog_denominacion || 'N/A'}</div>
                    <div class="text-xs text-[#39A900] font-semibold">${ficha.titpro_nombre || ''}</div>
                </td>
                <td>
                    <span class="status-badge bg-blue-50 text-blue-700 border border-blue-200">
                        ${ficha.fich_jornada || 'No definida'}
                    </span>
                </td>
                <td>
                    <div class="text-sm font-medium text-slate-700">${ficha.coord_nombre || 'Sin coordinación'}</div>
                </td>
                <td>
                    <div class="text-sm text-slate-600">
                        <ion-icon src="../../assets/ionicons/location-outline.svg" class="text-[#39A900] align-middle mr-1"></ion-icon>
                        ${ficha.sede_nombre || 'No asignada'}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    formatDate(dateString) {
        if (!dateString) return 'N/A';
        const [year, month, day] = dateString.split('-');
        return `${day}/${month}/${year}`;
    }

    setupSearch() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                const filtered = this.fichasInfo.filter(f =>
                    String(f.fich_id).toLowerCase().includes(term) ||
                    (f.prog_denominacion && f.prog_denominacion.toLowerCase().includes(term))
                );
                this.renderTable(filtered);
            });
        }
    }

    showError(msg) {
        const tbody = document.getElementById('fichasTableBody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-8 text-red-500 font-medium">${msg}</td></tr>`;
        }
        if (typeof NotificationSystem !== 'undefined') {
            NotificationSystem.show('error', msg);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new MisFichasLider();
});
