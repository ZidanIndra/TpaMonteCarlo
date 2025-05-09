<!-- Header section -->
<div class="d-flex justify-content-between align-items-center">
    <h1>Laporan Analisis Monte Carlo</h1>
    <div>
        <a class="btn btn-success" onclick="generatePDF()">Cetak PDF</a>
        <a class="btn btn-warning ms-2" onclick="generateDailyPDF()">Cetak PDF Harian</a>
    </div>
</div>

<!-- Content sections -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Distribusi Probabilitas</h5>
    </div>
    <div class="card-body">
        <!-- Existing table content -->
    </div>
</div>

<script>
function generatePDF() {
    // Capture the chart as an image
    const chartCanvas = document.getElementById('chart');
    const chartImage = chartCanvas.toDataURL('image/png');
    
    // Create a new window for PDF generation
    const pdfWindow = window.open('', '_blank');
    
    // Create HTML content for PDF
    const htmlContent = `
        <html>
        <head>
            <title>Laporan Simulasi</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .chart-container { margin: 20px 0; }
            </style>
        </head>
        <body>
            <h1>Laporan Simulasi</h1>
            <div class="chart-container">
                <img src="${chartImage}" style="width: 100%; max-width: 800px;">
            </div>
            <h2>Rata-rata Waktu Tunggu per Hari</h2>
            <table>
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Rata-rata Waktu Tunggu (menit)</th>
                    </tr>
                </thead>
                <tbody>
                    ${Object.entries(rataRataWaktuTunggu).map(([hari, waktu]) => `
                        <tr>
                            <td>${hari}</td>
                            <td>${waktu.toFixed(2)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </body>
        </html>
    `;
    
    // Write the content to the new window
    pdfWindow.document.write(htmlContent);
    pdfWindow.document.close();
    
    // Wait for the image to load before printing
    pdfWindow.onload = function() {
        setTimeout(function() {
            pdfWindow.print();
        }, 1000);
    };
}

function generateDailyPDF() {
    // Create a new window for PDF generation
    const pdfWindow = window.open('', '_blank');
    
    // Create HTML content for PDF
    const htmlContent = `
        <html>
        <head>
            <title>Laporan Harian Simulasi</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .day-section { margin-bottom: 30px; }
                .day-title { color: #333; margin-bottom: 10px; }
            </style>
        </head>
        <body>
            <h1>Laporan Harian Simulasi</h1>
            ${Object.entries(detailSimulation).map(([hari, data]) => `
                <div class="day-section">
                    <h2 class="day-title">${hari}</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>No Truk</th>
                                <th>Waktu Kedatangan</th>
                                <th>Waktu Mulai Pelayanan</th>
                                <th>Waktu Selesai Pelayanan</th>
                                <th>Waktu Tunggu</th>
                                <th>Waktu Pelayanan</th>
                                <th>Waktu di Sistem</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${Object.entries(data).map(([no_truk, detail]) => `
                                <tr>
                                    <td>${no_truk}</td>
                                    <td>${detail.waktu_kedatangan}</td>
                                    <td>${detail.waktu_mulai_pelayanan}</td>
                                    <td>${detail.waktu_selesai_pelayanan}</td>
                                    <td>${detail.waktu_tunggu}</td>
                                    <td>${detail.waktu_pelayanan}</td>
                                    <td>${detail.waktu_di_sistem}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `).join('')}
        </body>
        </html>
    `;
    
    // Write the content to the new window
    pdfWindow.document.write(htmlContent);
    pdfWindow.document.close();
    
    // Print the PDF
    pdfWindow.onload = function() {
        setTimeout(function() {
            pdfWindow.print();
        }, 1000);
    };
}
</script> 