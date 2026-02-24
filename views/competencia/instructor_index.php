<?php
$pageTitle = 'Mis Competencias - Programaciones';
$activeNavItem = 'competencias';
require_once '../layouts/head.php';

// Enforce role
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'instructor') {
    header("Location: ../../routing.php?controller=login&action=showLogin");
    exit;
}
?>

<?php require_once '../layouts/instructor_sidebar.php'; ?>

<main class="main-content">
    <header class="main-header">
        <div class="header-content">
            <nav class="breadcrumb">
                <a href="#">Inicio</a>
                <ion-icon src="../../assets/ionicons/chevron-forward-outline.svg"></ion-icon>
                <span>Mis Competencias</span>
            </nav>
            <h1 class="page-title">Competencias Habilitadas</h1>
        </div>
    </header>

    <div class="content-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-bg-icon">
                    <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                </div>
                <div class="stat-card-header">
                    <span class="stat-card-label">MIS COMPETENCIAS</span>
                    <div class="stat-card-icon green">
                        <ion-icon src="../../assets/ionicons/bookmarks-outline.svg"></ion-icon>
                    </div>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-number" id="totalCompetencias">0</span>
                    <span class="stat-card-desc">competencias habilitadas</span>
                    <p class="stat-card-context">Normas de competencia laboral que estás habilitado para impartir.</p>
                </div>
            </div>
        </div>

        <div class="action-bar">
            <div class="search-container">
                <ion-icon src="../../assets/ionicons/search-outline.svg" class="search-icon"></ion-icon>
                <input type="text" id="searchTerm" class="search-input" placeholder="Buscar por código o descripción...">
            </div>
        </div>

        <div class="table-container">
            <table class="data-table" id="competenciasTable">
                <thead>
                    <tr>
                        <th class="w-10">Código</th>
                        <th>Descripción</th>
                        <th>Horas</th>
                        <th>Programa Vinculado</th>
                    </tr>
                </thead>
                <tbody id="competenciasBody">
                    <tr>
                        <td colspan="4" class="text-center">Cargando competencias...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const instructorId = <?php echo json_encode($_SESSION['id']); ?>;
        let allCompetencias = [];

        function renderTable(data) {
            const tbody = document.getElementById('competenciasBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay competencias asignadas.</td></tr>';
                return;
            }

            data.forEach(comp => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td><strong>${comp.comp_codigo}</strong></td>
                <td>${comp.comp_descripcion}</td>
                <td>${comp.comp_horas}</td>
                <td><span class="badge ${comp.prog_nombre ? 'badge-primary' : 'badge-secondary'}">${comp.prog_nombre || 'N/A'}</span></td>
            `;
                tbody.appendChild(tr);
            });
            document.getElementById('totalCompetencias').textContent = data.length;
        }

        fetch(`../../routing.php?controller=instructor&action=getCompetencias&id=${instructorId}`)
            .then(response => response.json())
            .then(data => {
                allCompetencias = data;
                renderTable(data);
            })
            .catch(err => {
                console.error("Error loading competencias", err);
                document.getElementById('competenciasBody').innerHTML = '<tr><td colspan="4" class="text-center text-red-500">Error al cargar datos.</td></tr>';
            });

        // Simple client-side search
        document.getElementById('searchTerm').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const filtered = allCompetencias.filter(comp =>
                (comp.comp_codigo && comp.comp_codigo.toLowerCase().includes(term)) ||
                (comp.comp_descripcion && comp.comp_descripcion.toLowerCase().includes(term)) ||
                (comp.prog_nombre && comp.prog_nombre.toLowerCase().includes(term))
            );
            renderTable(filtered);
        });
    });
</script>
</body>

</html>