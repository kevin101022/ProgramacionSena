class SetDataDashboard {
    constructor() {
        this.data = null;
        this.charts = [];
        this.init();
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('csvFileInput');

        // Click to upload
        dropZone.addEventListener('click', () => fileInput.click());

        // Drag & Drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-[#39A900]', 'bg-[#39A900]/5');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-[#39A900]', 'bg-[#39A900]/5');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-[#39A900]', 'bg-[#39A900]/5');
            const files = e.dataTransfer.files;
            if (files.length) this.handleFileUpload(files[0]);
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) this.handleFileUpload(e.target.files[0]);
        });

        // Search
        document.getElementById('tableSearch').addEventListener('input', (e) => {
            this.filterTable(e.target.value);
        });

        // Download
        document.getElementById('downloadBtn').addEventListener('click', () => {
            this.exportToPDF();
        });
    }

    async handleFileUpload(file) {
        const formData = new FormData();
        formData.append('csv_file', file);
        formData.append('controller', 'setdata');
        formData.append('action', 'upload');

        try {
            // Loading state
            document.getElementById('emptyState').innerHTML = `
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 border-4 border-[#39A900] border-t-transparent rounded-full animate-spin mb-4"></div>
                    <p class="text-slate-500 font-medium">Procesando archivo y generando estadísticas...</p>
                </div>
            `;

            const response = await fetch('../../routing.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.error) throw new Error(result.error);

            this.data = result;
            this.renderDashboard();

        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
            this.resetUploadState();
        }
    }

    resetUploadState() {
        document.getElementById('emptyState').innerHTML = `
            <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 rounded-full text-slate-400 mb-6">
                <ion-icon name="analytics-outline" style="font-size: 40px;"></ion-icon>
            </div>
            <h2 class="text-xl font-bold text-slate-400">Esperando archivo...</h2>
            <p class="text-slate-400 max-w-md mx-auto mt-2">Sube el archivo "setdata" exportado de FET para generar el tablero de control automático.</p>
        `;
    }

    renderDashboard() {
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('dashboardContent').classList.remove('hidden');

        this.renderStats();
        this.renderCharts();
        this.renderTable();
    }

    renderStats() {
        const container = document.getElementById('statsCards');
        container.innerHTML = '';

        // Card Total Registros
        this.createStatCard(container, 'Total Registros', this.data.total, 'layers-outline', 'bg-blue-500');

        // Buscar columnas numéricas para sumar
        this.data.stats.forEach(stat => {
            if (stat.is_numeric && stat.sum > 0) {
                this.createStatCard(container, `Total ${stat.column}`, stat.sum, 'calculator-outline', 'bg-[#39A900]');
            } else if (stat.unique > 1 && stat.unique < 100 && !stat.is_numeric) {
                this.createStatCard(container, `${stat.column} Únicos`, stat.unique, 'list-outline', 'bg-orange-500');
            }
        });
    }

    createStatCard(container, title, value, icon, colorClass) {
        const card = document.createElement('div');
        card.className = 'glass-container p-6 flex items-center gap-4';
        card.innerHTML = `
            <div class="w-12 h-12 ${colorClass} text-white rounded-xl flex items-center justify-center text-2xl shadow-lg">
                <ion-icon name="${icon}"></ion-icon>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">${title}</p>
                <h3 class="text-2xl font-bold text-slate-800">${value.toLocaleString()}</h3>
            </div>
        `;
        container.appendChild(card);
    }

    renderCharts() {
        const container = document.getElementById('chartsGrid');
        container.innerHTML = '';

        // Destruir gráficos anteriores
        this.charts.forEach(chart => chart.destroy());
        this.charts = [];

        this.data.charts.forEach((chartData, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'glass-container p-6';
            wrapper.innerHTML = `
                <h3 class="font-bold text-slate-800 mb-6 flex items-center justify-between">
                    Distribución de ${chartData.title}
                    <span class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-500 font-normal">Frecuencia</span>
                </h3>
                <div class="h-64">
                    <canvas id="chart-${index}"></canvas>
                </div>
            `;
            container.appendChild(wrapper);

            const ctx = document.getElementById(`chart-${index}`).getContext('2d');
            const newChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Cantidad',
                        data: chartData.data,
                        backgroundColor: '#39A900',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: chartData.labels.length > 10 ? 'y' : 'x',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { display: false } },
                        x: { grid: { display: false } }
                    }
                }
            });
            this.charts.push(newChart);
        });
    }

    renderTable(filteredRows = null) {
        const head = document.getElementById('tableHead');
        const body = document.getElementById('tableBody');
        const footer = document.getElementById('tableFooter');
        const rows = filteredRows || this.data.rows;

        // Headers
        head.innerHTML = `<tr>${this.data.headers.map(h => `<th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">${h}</th>`).join('')}</tr>`;

        // Body (Límitado a 100 para performance, el resto se busca)
        body.innerHTML = rows.slice(0, 100).map(row => `
            <tr class="hover:bg-slate-50 transition-colors border-b border-slate-50">
                ${this.data.headers.map(h => `<td class="px-6 py-4 text-sm text-slate-600">${row[h] || '-'}</td>`).join('')}
            </tr>
        `).join('');

        footer.innerText = `Mostrando ${Math.min(rows.length, 100)} de ${rows.length} registros encontrados.`;
    }

    filterTable(term) {
        if (!this.data) return;
        term = term.toLowerCase();
        const filtered = this.data.rows.filter(row => {
            return Object.values(row).some(val => String(val).toLowerCase().includes(term));
        });
        this.renderTable(filtered);
    }

    exportToPDF() {
        if (!this.data) return;

        const { jsPDF } = window.jspdf;
        const dashboard = document.getElementById('dashboardContent');
        const downloadBtn = document.getElementById('downloadBtn');

        // Estética de carga
        const originalText = downloadBtn.innerHTML;
        downloadBtn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Generando...';
        downloadBtn.disabled = true;

        // Ocultar elementos que no queremos en el PDF (como la tabla gigante si es muy larga, o el buscador)
        const searchContainer = document.querySelector('.search-container');
        if (searchContainer) searchContainer.style.visibility = 'hidden';

        html2canvas(dashboard, {
            scale: 2, // Mayor calidad
            useCORS: true,
            logging: false,
            backgroundColor: '#f8fafc' // Fondo slate-50 como la app
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF('p', 'mm', 'a4');
            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

            // Añadir encabezado institucional al PDF
            pdf.setFillColor(57, 169, 0); // Verde SENA
            pdf.rect(0, 0, pdfWidth, 15, 'F');
            pdf.setTextColor(255, 255, 255);
            pdf.setFontSize(10);
            pdf.text('REPORTE DE SINCRONIZACIÓN SETDATA - SENA', 10, 10);

            pdf.addImage(imgData, 'PNG', 0, 20, pdfWidth, pdfHeight);

            const timestamp = new Date().toLocaleString();
            pdf.setFontSize(8);
            pdf.setTextColor(150, 150, 150);
            pdf.text(`Generado el: ${timestamp}`, 10, pdfHeight + 30);

            pdf.save(`Reporte_SetData_${new Date().getTime()}.pdf`);

            // Restaurar UI
            if (searchContainer) searchContainer.style.visibility = 'visible';
            downloadBtn.innerHTML = originalText;
            downloadBtn.disabled = false;

            if (typeof NotificationSystem !== 'undefined') {
                NotificationSystem.show('success', 'Reporte PDF generado correctamente');
            }
        }).catch(err => {
            console.error('Error al generar PDF:', err);
            downloadBtn.innerHTML = originalText;
            downloadBtn.disabled = false;
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new SetDataDashboard();
});
