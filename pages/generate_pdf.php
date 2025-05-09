<?php
require_once '../vendor/autoload.php';
require_once '../includes/pdf_generator.php';

// Check if required data exists
if (!isset($_POST['hari']) || !isset($_POST['queue_data'])) {
    header('Location: detail_simulasi.php');
    exit;
}

// Get data from POST
$hari = $_POST['hari'];
$truck_count = $_POST['truck_count'];
$avg_wait_time = $_POST['avg_wait_time'];
$max_wait_time = $_POST['max_wait_time'];
$avg_service_time = $_POST['avg_service_time'];
$organik_count = $_POST['organik_count'];
$anorganik_count = $_POST['anorganik_count'];
$queue_data = json_decode($_POST['queue_data'], true);

// Format time function
function formatTime($minutes) {
    $minutes = (float)$minutes;
    $hours = (int)floor($minutes / 60);
    $mins = (int)floor(fmod($minutes, 60));
    return sprintf("%02d:%02d", $hours, $mins);
}

function formatMinutes($minutes) {
    $minutes = (float)$minutes;
    if ($minutes < 60) {
        return (int)floor($minutes) . " menit";
    }
    $hours = (int)floor($minutes / 60);
    $mins = (int)floor(fmod($minutes, 60));
    return $hours . " jam " . $mins . " menit";
}

// Prepare data for PDF
$pdf_data = [
    'title' => 'Detail Simulasi Antrian - ' . ucfirst($hari),
    'summary' => [
        'Total Truk' => $truck_count,
        'Rata-rata Waktu Tunggu' => formatMinutes($avg_wait_time),
        'Waktu Tunggu Maksimum' => formatMinutes($max_wait_time),
        'Rata-rata Waktu Layanan' => formatMinutes($avg_service_time),
        'Jumlah Truk Organik' => $organik_count,
        'Jumlah Truk Anorganik' => $anorganik_count
    ],
    'queue_data' => array_map(function($truck) {
        return [
            'No. Truk' => $truck['truck_id'],
            'Jenis' => ucfirst($truck['type']),
            'Waktu Kedatangan' => formatTime($truck['arrival_time']),
            'Mulai Layanan' => formatTime($truck['service_start']),
            'Waktu Layanan' => $truck['service_time'] . ' menit',
            'Selesai Layanan' => formatTime($truck['service_end']),
            'Waktu Tunggu' => formatMinutes($truck['wait_time'])
        ];
    }, $queue_data)
];

// Generate PDF
$pdf_content = generateDetailPDF($pdf_data);

// Send PDF to browser
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Detail_Simulasi_' . ucfirst($hari) . '.pdf"');
echo $pdf_content;
exit;
?> 