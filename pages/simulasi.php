<?php
session_start();
require_once '../includes/components.php';

// Check if required session data exists
if (!isset($_SESSION['frequency_data']) || !isset($_SESSION['simulation_params'])) {
    header('Location: input_data.php');
    exit;
}

if (!isset($_SESSION['simulation_params'])) {
    $_SESSION['simulation_params'] = [
        'days' => 7,
        'a' => 12,
        'c' => 23,
        'm' => 100,
        'x0' => 5
    ];
}

if (!isset($_SESSION['truck_capacity'])) {
    $_SESSION['truck_capacity'] = [
        'capacity' => 10,
        'count' => 2,
        'loading_time' => 30,
        'unloading_time' => 45
    ];
}

if (!isset($_SESSION['frequency_data'])) {
    $_SESSION['frequency_data'] = [
        'senin' => 38,
        'selasa' => 30,
        'rabu' => 26,
        'kamis' => 22,
        'jumat' => 28,
        'sabtu' => 29,
        'minggu' => 27
    ];
}

// Validate truck capacity data
$truck_capacity = $_SESSION['truck_capacity'];
if (!isset($truck_capacity['capacity']) || !isset($truck_capacity['count']) || 
    !isset($truck_capacity['loading_time']) || !isset($truck_capacity['unloading_time'])) {
    $_SESSION['error'] = "Data kapasitas truk tidak lengkap. Silakan kembali ke halaman input data.";
    header("Location: input_data.php");
    exit();
}

// Validate frequency data
$frequency_data = $_SESSION['frequency_data'];
if (!isset($frequency_data['senin']) || !isset($frequency_data['selasa']) || 
    !isset($frequency_data['rabu']) || !isset($frequency_data['kamis']) || 
    !isset($frequency_data['jumat']) || !isset($frequency_data['sabtu']) || 
    !isset($frequency_data['minggu'])) {
    $_SESSION['error'] = "Data frekuensi tidak lengkap. Silakan kembali ke halaman input data.";
    header("Location: input_data.php");
    exit();
}

// Check if simulation should be run
$run_simulation = isset($_POST['run_simulation']) && $_POST['run_simulation'] == '1';

// Only run simulation if explicitly requested
if ($run_simulation) {
    // 1. Calculate total frequency
    $total_frequency = array_sum($frequency_data);

    // 2. Calculate probabilities (PDF) for each day
    $probabilities = [];
    foreach ($frequency_data as $day => $frequency) {
        $probabilities[$day] = $frequency / $total_frequency;
    }

    // 3. Calculate cumulative distribution intervals
    $cumulative_intervals = [];
    $cumulative = 0;
    foreach ($probabilities as $day => $probability) {
        $interval_start = $cumulative;
        $interval_end = $cumulative + $probability;
        $cumulative_intervals[$day] = [
            'start' => $interval_start,
            'end' => $interval_end
        ];
        $cumulative = $interval_end;
    }

    // 4. Generate random numbers and perform simulation
    $simulation_count = $_SESSION['simulation_params']['simulation_count'];
    $simulation_results = [];
    $day_counts = array_fill_keys(array_keys($frequency_data), 0);

    for ($i = 0; $i < $simulation_count; $i++) {
        // Generate random number between 0 and 1
        $random_number = mt_rand() / mt_getrandmax();
        
        // Find which day corresponds to this random number
        $selected_day = null;
        foreach ($cumulative_intervals as $day => $interval) {
            if ($random_number >= $interval['start'] && $random_number < $interval['end']) {
                $selected_day = $day;
                $day_counts[$day]++;
                break;
            }
        }
        
        // If no day found (shouldn't happen due to floating point precision), default to Sunday
        if ($selected_day === null) {
            $selected_day = 'minggu';
            $day_counts['minggu']++;
        }
        
        $simulation_results[] = [
            'simulation_number' => $i + 1,
            'random_number' => number_format($random_number, 3),
            'selected_day' => $selected_day,
            'truck_count' => $frequency_data[$selected_day]
        ];
    }

    // Calculate statistics
    $total_trucks = array_sum(array_column($simulation_results, 'truck_count'));
    $average_trucks = $total_trucks / $simulation_count;

    // Find busiest day
    $busiest_day = array_search(max($day_counts), $day_counts);
    $busiest_day_count = $day_counts[$busiest_day];
    $busiest_day_percentage = ($busiest_day_count / $simulation_count) * 100;

    // Store simulation results in session
    $_SESSION['simulation_results'] = $simulation_results;
    $_SESSION['total_trucks'] = $total_trucks;
    $_SESSION['average_trucks'] = $average_trucks;
    $_SESSION['busiest_day'] = $busiest_day;
    $_SESSION['busiest_day_percentage'] = $busiest_day_percentage;
    $_SESSION['probabilities'] = $probabilities;
    $_SESSION['cumulative_intervals'] = $cumulative_intervals;
}

// Get simulation results from session if they exist
$simulation_results = isset($_SESSION['simulation_results']) ? $_SESSION['simulation_results'] : [];
$total_trucks = isset($_SESSION['total_trucks']) ? $_SESSION['total_trucks'] : 0;
$average_trucks = isset($_SESSION['average_trucks']) ? $_SESSION['average_trucks'] : 0;
$busiest_day = isset($_SESSION['busiest_day']) ? $_SESSION['busiest_day'] : null;
$busiest_day_percentage = isset($_SESSION['busiest_day_percentage']) ? $_SESSION['busiest_day_percentage'] : 0;
$probabilities = isset($_SESSION['probabilities']) ? $_SESSION['probabilities'] : [];
$cumulative_intervals = isset($_SESSION['cumulative_intervals']) ? $_SESSION['cumulative_intervals'] : [];

$pageTitle = "Simulasi - Sistem Simulasi Antrian Sampah";

// Prepare content
$content = "
<div class='space-y-6'>
    <div class='flex justify-between items-center'>
        <h2 class='text-2xl font-bold text-gray-900'>Simulasi Monte Carlo</h2>
        <form method='POST' class='flex items-center space-x-4'>
            " . button('Jalankan Simulasi Baru', 'submit', 'primary', 'fas fa-sync-alt', 'animate-spin-slow') . "
            <input type='hidden' name='run_simulation' value='1'>
        </form>
    </div>

    " . (empty($simulation_results) ? "
    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <div class='text-center'>
                <i class='fas fa-chart-line text-4xl text-gray-400 mb-4'></i>
                <h3 class='text-lg font-medium text-gray-900'>Belum Ada Hasil Simulasi</h3>
                <p class='mt-1 text-sm text-gray-500'>Klik tombol 'Jalankan Simulasi Baru' untuk memulai simulasi.</p>
            </div>
        </div>
    </div>
    " : "
    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900'>
                Hasil Simulasi Monte Carlo
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
                    "<div class='text-3xl font-bold text-primary-600'>" . ucfirst($busiest_day) . "</div>",
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
    </div>

    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900 mb-4'>
                Distribusi Probabilitas
            </h3>
            <div class='mt-4'>
                <table class='min-w-full divide-y divide-gray-200'>
                    <thead class='bg-gray-50'>
                        <tr>
                            <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Hari</th>
                            <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Frekuensi</th>
                            <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Probabilitas</th>
                            <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>Interval Kumulatif</th>
                        </tr>
                    </thead>
                    <tbody class='bg-white divide-y divide-gray-200'>
                        " . implode('', array_map(function($day) use ($frequency_data, $probabilities, $cumulative_intervals) {
                            return "
                            <tr>
                                <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>" . ucfirst($day) . "</td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$frequency_data[$day]}</td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . number_format($probabilities[$day], 3) . "</td>
                                <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>" . 
                                    number_format($cumulative_intervals[$day]['start'], 3) . " - " . 
                                    number_format($cumulative_intervals[$day]['end'], 3) . 
                                "</td>
                            </tr>";
                        }, array_keys($frequency_data))) . "
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    ") . "
</div>";

// Include layout
require_once '../layouts/main.php';
?> 