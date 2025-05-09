<?php
session_start();
require_once '../includes/components.php';
require_once '../includes/pdf_generator.php';

// Check if required session data exists
if (!isset($_SESSION['frequency_data']) || !isset($_SESSION['probabilities']) || !isset($_SESSION['cumulative_intervals'])) {
    header('Location: input_data.php');
    exit;
}

// Get data from session
$frequency_data = $_SESSION['frequency_data'];
$probabilities = $_SESSION['probabilities'];
$cumulative_intervals = $_SESSION['cumulative_intervals'];

// Validate frequency data
if (!isset($frequency_data['senin']) || !isset($frequency_data['selasa']) || 
    !isset($frequency_data['rabu']) || !isset($frequency_data['kamis']) || 
    !isset($frequency_data['jumat']) || !isset($frequency_data['sabtu']) || 
    !isset($frequency_data['minggu'])) {
    $_SESSION['error'] = "Data frekuensi tidak lengkap. Silakan kembali ke halaman input data.";
    header("Location: input_data.php");
    exit();
}

// Validate probabilities and cumulative intervals
if (!isset($probabilities) || !isset($cumulative_intervals)) {
    $_SESSION['error'] = "Data probabilitas tidak lengkap. Silakan kembali ke halaman input data.";
    header("Location: input_data.php");
    exit();
}

// Get simulation results if available
$simulation_results = isset($_SESSION['simulation_results']) ? $_SESSION['simulation_results'] : [];
$total_trucks = isset($_SESSION['total_trucks']) ? $_SESSION['total_trucks'] : 0;
$average_trucks = isset($_SESSION['average_trucks']) ? $_SESSION['average_trucks'] : 0;
$busiest_day = isset($_SESSION['busiest_day']) ? $_SESSION['busiest_day'] : null;
$busiest_day_percentage = isset($_SESSION['busiest_day_percentage']) ? $_SESSION['busiest_day_percentage'] : 0;

// If simulation results are not in session, redirect to simulation page
if (empty($simulation_results)) {
    $_SESSION['error'] = "Data simulasi tidak ditemukan. Silakan lakukan simulasi terlebih dahulu.";
    header("Location: simulasi.php");
    exit();
}

// Handle PDF generation
if (isset($_POST['generate_pdf'])) {
    // Ensure temp directory exists
    if (!file_exists('../temp')) {
        mkdir('../temp', 0777, true);
    }

    // Save distribution chart as image using Chart.js
    $additionalScripts .= "
    // Save Distribution Chart as image
    setTimeout(function() {
        var chartCanvas = document.getElementById('distributionChart');
        var chartImageData = chartCanvas.toDataURL('image/png');
        var chartXHR = new XMLHttpRequest();
        chartXHR.open('POST', 'save_chart.php', true);
        chartXHR.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        chartXHR.send('image=' + encodeURIComponent(chartImageData) + '&name=distribution_chart');

        // Wait for image to be saved then generate PDF
        setTimeout(function() {
            document.getElementById('generate_pdf_form').submit();
        }, 1000);
    }, 1000);
    ";

    // If this is the actual PDF generation request
    if (isset($_POST['generate_pdf_final'])) {
        // Prepare data for PDF
        $pdf_data = [
            'days' => array_keys($frequency_data),
            'frequency_data' => $frequency_data,
            'probabilities' => $probabilities,
            'cumulative_intervals' => $cumulative_intervals,
            'distribution_chart' => '../temp/distribution_chart.png',
            'simulation_results' => $simulation_results,
            'total_trucks' => $total_trucks,
            'average_trucks' => $average_trucks,
            'busiest_day' => $busiest_day,
            'busiest_day_percentage' => $busiest_day_percentage
        ];

        // Generate PDF
        $pdf_content = generatePDFReport($pdf_data);

        // Send PDF to browser
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Laporan_Simulasi_Monte_Carlo.pdf"');
        echo $pdf_content;
        exit;
    }
}

$pageTitle = "Laporan - Sistem Simulasi Monte Carlo";

// Prepare content
$content = "
<div class='space-y-6'>
    <div class='flex justify-between items-center'>
        <h2 class='text-2xl font-bold text-gray-900'>Laporan Analisis Monte Carlo</h2>
        <div class='flex items-center space-x-4'>
            <form id='generate_pdf_form' method='POST' class='flex items-center'>
                " . button('Cetak PDF', 'submit', 'primary', 'fas fa-file-pdf') . "
                <input type='hidden' name='generate_pdf' value='1'>
                <input type='hidden' name='generate_pdf_final' value='1'>
            </form>
            <form method='POST' action='generate_all_pdf.php' target='_blank' class='flex items-center'>
                " . button('Cetak PDF Harian', 'submit', 'warning', 'fas fa-file-pdf', 'bg-yellow-400 hover:bg-yellow-500 text-gray-900') . "
            </form>
        </div>
    </div>

    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900'>
                Distribusi Probabilitas
            </h3>
            <div class='mt-5'>
                " . table(
                    ['Hari', 'Frekuensi', 'Probabilitas', 'Interval Kumulatif', 'Aksi'],
                    array_map(function($day) use ($frequency_data, $probabilities, $cumulative_intervals) {
                        return [
                            ucfirst($day),
                            $frequency_data[$day],
                            number_format($probabilities[$day], 3),
                            number_format($cumulative_intervals[$day]['start'], 3) . ' - ' . 
                            number_format($cumulative_intervals[$day]['end'], 3),
                            "<a href='detail_simulasi.php?hari=" . $day . "' class='inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700'>
                                <i class='fas fa-chart-line mr-2'></i>
                                Detail
                            </a>"
                        ];
                    }, array_keys($frequency_data))
                ) . "
            </div>
        </div>
    </div>

    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900 mb-4'>
                Visualisasi Distribusi
            </h3>
            <div class='grid grid-cols-1 gap-6'>
                <div>
                    <canvas id='distributionChart' class='w-full'></canvas>
                </div>
            </div>
        </div>
    </div>";

// Add simulation results section if available
if (!empty($simulation_results)) {
    $content .= "
    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900'>
                Ringkasan Simulasi
            </h3>
            <div class='mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4'>
                " . card(
                    'Total Truk',
                    "<div class='text-3xl font-bold text-primary-600'>{$total_trucks}</div>",
                    'fas fa-truck',
                    'primary'
                ) . "
                " . card(
                    'Rata-rata Truk/Simulasi',
                    "<div class='text-3xl font-bold text-primary-600'>" . number_format($average_trucks, 2) . "</div>",
                    'fas fa-calculator',
                    'primary'
                ) . "
                " . card(
                    'Hari Tersibuk',
                    "<div class='text-3xl font-bold text-primary-600'>" . ($busiest_day ? ucfirst($busiest_day) : '-') . "</div>",
                    'fas fa-chart-line',
                    'primary'
                ) . "
                " . card(
                    'Persentase Hari Tersibuk',
                    "<div class='text-3xl font-bold text-primary-600'>" . number_format($busiest_day_percentage, 2) . "%</div>",
                    'fas fa-percent',
                    'primary'
                ) . "
            </div>
        </div>
    </div>

    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900 mb-4'>
                Detail Hasil Simulasi
            </h3>
            " . table(
                ['No. Simulasi', 'Angka Acak', 'Hari Terpilih', 'Jumlah Truk'],
                array_map(function($result) {
                    return [
                        $result['simulation_number'],
                        $result['random_number'],
                        ucfirst($result['selected_day']),
                        $result['truck_count']
                    ];
                }, $simulation_results)
            ) . "
        </div>
    </div>";
}

$content .= "
</div>";

// Add chart script
$additionalScripts = "
<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
<script>
    // Distribution Chart
    const distributionCtx = document.getElementById('distributionChart').getContext('2d');
    new Chart(distributionCtx, {
        type: 'bar',
        data: {
            labels: " . json_encode(array_map('ucfirst', array_keys($frequency_data))) . ",
            datasets: [{
                label: 'Frekuensi Kedatangan Truk',
                data: " . json_encode(array_values($frequency_data)) . ",
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Distribusi Frekuensi Kedatangan Truk'
                }
            }
        }
    });
</script>";

// Include layout
require_once '../layouts/main.php';
?> 