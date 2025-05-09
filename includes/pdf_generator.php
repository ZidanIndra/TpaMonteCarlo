<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function generatePDFReport($data) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistem Simulasi Monte Carlo');
    $pdf->SetTitle('Laporan Simulasi Monte Carlo');

    // Set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Laporan Simulasi Monte Carlo', PDF_HEADER_STRING);

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
    $pdf->Cell(0, 10, 'Laporan Simulasi Monte Carlo', 0, 1, 'C');
    $pdf->Ln(10);

    // Add distribution chart if available
    if (isset($data['distribution_chart']) && file_exists($data['distribution_chart'])) {
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Distribusi Frekuensi Kedatangan Truk', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Add the chart image
        $pdf->Image($data['distribution_chart'], 15, $pdf->GetY(), 180, 0, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        $pdf->Ln(100); // Add space after the chart
    }

    // Add probability distribution table
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Distribusi Probabilitas', 0, 1, 'L');
    $pdf->Ln(5);

    // Table header
    $header = array('Hari', 'Frekuensi', 'Probabilitas', 'Interval Kumulatif');
    $w = array(40, 40, 50, 60); // Column widths

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

    foreach($data['days'] as $day) {
        $pdf->Cell($w[0], 6, ucfirst($day), 'LR', 0, 'L', $fill);
        $pdf->Cell($w[1], 6, $data['frequency_data'][$day], 'LR', 0, 'C', $fill);
        $pdf->Cell($w[2], 6, number_format($data['probabilities'][$day], 3), 'LR', 0, 'C', $fill);
        $pdf->Cell($w[3], 6, number_format($data['cumulative_intervals'][$day]['start'], 3) . ' - ' . 
                            number_format($data['cumulative_intervals'][$day]['end'], 3), 'LR', 0, 'C', $fill);
        $pdf->Ln();
        $fill = !$fill;
    }

    // Closing line
    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Ln(10);

    // Add simulation results
    if (isset($data['simulation_results']) && !empty($data['simulation_results'])) {
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, 'Hasil Simulasi', 0, 1, 'L');
        $pdf->Ln(5);

        // Summary cards
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Ringkasan Simulasi', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);

        $summary = [
            'Total Truk' => $data['total_trucks'],
            'Rata-rata Truk/Simulasi' => number_format($data['average_trucks'], 2),
            'Hari Tersibuk' => ucfirst($data['busiest_day']),
            'Persentase Hari Tersibuk' => number_format($data['busiest_day_percentage'], 2) . '%'
        ];

        foreach ($summary as $key => $value) {
            $pdf->Cell(60, 7, $key . ':', 0, 0, 'L');
            $pdf->Cell(0, 7, $value, 0, 1, 'L');
        }

        $pdf->Ln(5);

        // Detailed results table
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Detail Hasil Simulasi', 0, 1, 'L');

        // Table header
        $header = array('No. Simulasi', 'Angka Acak', 'Hari Terpilih', 'Jumlah Truk');
        $w = array(40, 40, 50, 40); // Column widths

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

        foreach($data['simulation_results'] as $result) {
            $pdf->Cell($w[0], 6, $result['simulation_number'], 'LR', 0, 'C', $fill);
            $pdf->Cell($w[1], 6, $result['random_number'], 'LR', 0, 'C', $fill);
            $pdf->Cell($w[2], 6, ucfirst($result['selected_day']), 'LR', 0, 'C', $fill);
            $pdf->Cell($w[3], 6, $result['truck_count'], 'LR', 0, 'C', $fill);
            $pdf->Ln();
            $fill = !$fill;
        }

        // Closing line
        $pdf->Cell(array_sum($w), 0, '', 'T');
    }

    // Output PDF
    return $pdf->Output('', 'S');
}

function generateDetailPDF($data) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Sistem Simulasi Monte Carlo');
    $pdf->SetTitle($data['title']);

    // Set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $data['title'], PDF_HEADER_STRING);

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
    $pdf->Cell(0, 10, $data['title'], 0, 1, 'C');
    $pdf->Ln(10);

    // Summary section
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Ringkasan Simulasi', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);

    foreach ($data['summary'] as $key => $value) {
        $pdf->Cell(60, 7, $key . ':', 0, 0, 'L');
        $pdf->Cell(0, 7, $value, 0, 1, 'L');
    }

    $pdf->Ln(10);

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

    return $pdf->Output('', 'S');
}
?> 