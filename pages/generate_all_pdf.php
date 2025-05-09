<?php
session_start();
require_once '../vendor/autoload.php';
require_once '../includes/pdf_generator.php';

// Check if required session data exists
if (!isset($_SESSION['frequency_data'])) {
    header('Location: laporan.php');
    exit;
}

// Get all days in order
$days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
$all_simulations = [];

// Waktu layanan (dalam menit)
$service_times = [
    'organik' => [
        'min' => 10,
        'max' => 20
    ],
    'anorganik' => [
        'min' => 15,
        'max' => 25
    ]
];

// Function to generate random service time
function generateServiceTime($type) {
    global $service_times;
    if (!isset($service_times[$type]) || !isset($service_times[$type]['min']) || !isset($service_times[$type]['max'])) {
        return rand(10, 20);
    }
    return rand((int)$service_times[$type]['min'], (int)$service_times[$type]['max']);
}

// Function to simulate queue
function simulateQueue($truck_count) {
    $queue = [];
    $current_time = 0;
    $service_end_time = 0;

    for ($i = 1; $i <= $truck_count; $i++) {
        // Generate random arrival time (0-60 minutes)
        $arrival_time = $current_time + rand(0, 60);
        $current_time = $arrival_time;

        // Randomly determine truck type (organik or anorganik)
        $type = rand(0, 1) ? 'organik' : 'anorganik';
        
        // Generate service time
        $service_time = generateServiceTime($type);

        // Calculate service start and end times
        $service_start = max($arrival_time, $service_end_time);
        $service_end = $service_start + $service_time;
        $service_end_time = $service_end;

        // Calculate wait time
        $wait_time = $service_start - $arrival_time;

        $queue[] = [
            'truck_id' => $i,
            'type' => $type,
            'arrival_time' => $arrival_time,
            'service_start' => $service_start,
            'service_time' => $service_time,
            'service_end' => $service_end,
            'wait_time' => $wait_time
        ];
    }

    return $queue;
}

// Run simulation for each day
foreach ($days as $hari) {
    if (isset($_SESSION['frequency_data'][$hari])) {
        $truck_count = $_SESSION['frequency_data'][$hari];
        
        // Run simulation
        $queue_simulation = simulateQueue($truck_count);
        
        // Calculate statistics
        $total_wait_time = 0.0;
        $max_wait_time = 0.0;
        $total_service_time = 0.0;
        $organik_count = 0;
        $anorganik_count = 0;

        foreach ($queue_simulation as $truck) {
            $total_wait_time += (float)$truck['wait_time'];
            $max_wait_time = (float)max($max_wait_time, $truck['wait_time']);
            $total_service_time += (float)$truck['service_time'];
            if ($truck['type'] == 'organik') {
                $organik_count++;
            } else {
                $anorganik_count++;
            }
        }

        $avg_wait_time = $truck_count > 0 ? (float)($total_wait_time / $truck_count) : 0.0;
        $avg_service_time = $truck_count > 0 ? (float)($total_service_time / $truck_count) : 0.0;

        // Prepare data for this day
        $all_simulations[$hari] = [
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
            }, $queue_simulation)
        ];
    }
}

// Generate single PDF with all data
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistem Simulasi Monte Carlo');
$pdf->SetTitle('Detail Simulasi Semua Hari');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Detail Simulasi Semua Hari', PDF_HEADER_STRING);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Detail Simulasi Semua Hari', 0, 1, 'C');
$pdf->Ln(10);

// Add content for each day
foreach ($days as $hari) {
    if (isset($all_simulations[$hari])) {
        $data = $all_simulations[$hari];
        
        // Day title
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, $data['title'], 0, 1, 'L');
        $pdf->Ln(5);

        // Summary section
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Ringkasan Simulasi', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);

        foreach ($data['summary'] as $key => $value) {
            $pdf->Cell(60, 7, $key . ':', 0, 0, 'L');
            $pdf->Cell(0, 7, $value, 0, 1, 'L');
        }

        $pdf->Ln(5);

        // Queue data table
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Detail Antrian', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);

        // Table header
        $header = array_keys($data['queue_data'][0]);
        $w = array(20, 20, 30, 30, 30, 30, 30); // Column widths

        // Header
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', 'B', 10);
        for($i = 0; $i < count($header); $i++) {
            $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Data
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0);
        $fill = false;

        foreach($data['queue_data'] as $row) {
            $i = 0;
            foreach($row as $col) {
                $pdf->Cell($w[$i], 6, $col, 'LR', 0, 'C', $fill);
                $i++;
            }
            $pdf->Ln();
            $fill = !$fill;
        }

        // Closing line
        $pdf->Cell(array_sum($w), 0, '', 'T');
        
        // Add page break if not the last day
        if ($hari !== end($days)) {
            $pdf->AddPage();
        }
    } else {
        // Add empty page for missing days
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Detail Simulasi Antrian - ' . ucfirst($hari), 0, 1, 'L');
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 10, 'Tidak ada data simulasi untuk hari ini.', 0, 1, 'L');
        
        if ($hari !== end($days)) {
            $pdf->AddPage();
        }
    }
}

// Output PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Detail_Simulasi_Semua_Hari.pdf"');
echo $pdf->Output('', 'S');
exit;

// Helper functions
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
?> 