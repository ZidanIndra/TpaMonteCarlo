<?php
session_start();
require_once '../includes/components.php';

// Check if day parameter exists
if (!isset($_GET['hari'])) {
    header('Location: laporan.php');
    exit;
}

$hari = strtolower($_GET['hari']);
$valid_days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

if (!in_array($hari, $valid_days)) {
    header('Location: laporan.php');
    exit;
}

// Check if simulation should be run
$run_simulation = isset($_POST['run_simulation']) && $_POST['run_simulation'] == '1';

// Get truck count for the selected day
$truck_count = isset($_SESSION['frequency_data'][$hari]) ? $_SESSION['frequency_data'][$hari] : 0;

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

// Generate random service times for each truck
function generateServiceTime($type) {
    global $service_times;
    if (!isset($service_times[$type]) || !isset($service_times[$type]['min']) || !isset($service_times[$type]['max'])) {
        // Default values if service times are not properly set
        return rand(10, 20);
    }
    return rand((int)$service_times[$type]['min'], (int)$service_times[$type]['max']);
}

// Simulate queue for the selected day
function simulateQueue($truck_count) {
    $queue = [];
    $current_time = (int)(8 * 60); // Start at 08:00 (in minutes)
    $end_time = (int)(17 * 60); // End at 17:00 (in minutes)
    
    // Generate arrival times (spread throughout the day)
    $interval = ($end_time - $current_time) / (float)$truck_count;
    
    for ($i = 0; $i < $truck_count; $i++) {
        $arrival_time = (float)$current_time + ((float)$i * $interval);
        // Add some randomness to arrival times (±30 minutes)
        $arrival_time += rand(-30, 30);
        $arrival_time = (float)max($current_time, min($end_time, $arrival_time));
        
        $queue[] = [
            'truck_id' => $i + 1,
            'arrival_time' => (float)$arrival_time,
            'type' => (rand(0, 1) == 0) ? 'organik' : 'anorganik',
            'service_start' => 0,
            'service_time' => 0,
            'service_end' => 0,
            'wait_time' => 0
        ];
    }

    // Sort by arrival time
    usort($queue, function($a, $b) {
        return (int)($a['arrival_time'] - $b['arrival_time']);
    });

    // Process queue
    $server_available_time = (float)$current_time;
    
    foreach ($queue as &$truck) {
        $service_time = (float)generateServiceTime($truck['type']);
        
        // Calculate service start time
        $service_start = (float)max($server_available_time, $truck['arrival_time']);
        $wait_time = (float)($service_start - $truck['arrival_time']);
        $service_end = (float)($service_start + $service_time);

        $truck['service_start'] = $service_start;
        $truck['service_time'] = $service_time;
        $truck['service_end'] = $service_end;
        $truck['wait_time'] = $wait_time;

        $server_available_time = $service_end;
    }

    return $queue;
}

// Initialize or get queue simulation data
if ($run_simulation || !isset($_SESSION['detail_simulation'][$hari])) {
    // Run new simulation and store in session
    $queue_simulation = simulateQueue($truck_count);
    if (!isset($_SESSION['detail_simulation'])) {
        $_SESSION['detail_simulation'] = [];
    }
    $_SESSION['detail_simulation'][$hari] = [
        'queue' => $queue_simulation,
        'timestamp' => time()
    ];
} else {
    // Use existing simulation data from session
    $queue_simulation = $_SESSION['detail_simulation'][$hari]['queue'];
}

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

$pageTitle = "Detail Simulasi - " . ucfirst($hari);

// Prepare content
$content = "
<div class='space-y-6'>
    <div class='flex justify-between items-center'>
        <h2 class='text-2xl font-bold text-gray-900'>Detail Simulasi Antrian - " . ucfirst($hari) . "</h2>
        <div class='flex items-center space-x-4'>
            <form method='POST' class='flex items-center'>
                <button type='submit' class='inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500'>
                    <i class='fas fa-sync-alt mr-2'></i>
                    Jalankan Simulasi Baru
                </button>
                <input type='hidden' name='run_simulation' value='1'>
            </form>
            <form method='POST' action='generate_pdf.php' target='_blank' class='flex items-center'>
                <button type='submit' class='inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-900 bg-yellow-400 hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-300'>
                    <i class='fas fa-file-pdf mr-2'></i>
                    Cetak PDF
                </button>
                <input type='hidden' name='hari' value='{$hari}'>
                <input type='hidden' name='truck_count' value='{$truck_count}'>
                <input type='hidden' name='avg_wait_time' value='{$avg_wait_time}'>
                <input type='hidden' name='max_wait_time' value='{$max_wait_time}'>
                <input type='hidden' name='avg_service_time' value='{$avg_service_time}'>
                <input type='hidden' name='organik_count' value='{$organik_count}'>
                <input type='hidden' name='anorganik_count' value='{$anorganik_count}'>
                <input type='hidden' name='queue_data' value='" . htmlspecialchars(json_encode($queue_simulation)) . "'>
            </form>
            <a href='laporan.php' class='inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700'>
                <i class='fas fa-arrow-left mr-2'></i>
                Kembali
            </a>
        </div>
    </div>

    <div class='grid grid-cols-1 gap-5 sm:grid-cols-4'>
        " . card(
            'Total Truk',
            "<div class='text-3xl font-bold text-primary-600'>{$truck_count}</div>",
            'fas fa-truck',
            'primary'
        ) . "
        " . card(
            'Rata-rata Waktu Tunggu',
            "<div class='text-3xl font-bold text-primary-600'>" . formatMinutes($avg_wait_time) . "</div>",
            'fas fa-clock',
            'primary'
        ) . "
        " . card(
            'Truk Organik',
            "<div class='text-3xl font-bold text-primary-600'>{$organik_count}</div>",
            'fas fa-recycle',
            'primary'
        ) . "
        " . card(
            'Truk Anorganik',
            "<div class='text-3xl font-bold text-primary-600'>{$anorganik_count}</div>",
            'fas fa-dumpster',
            'primary'
        ) . "
    </div>

    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900 mb-4'>
                Detail Antrian
            </h3>
            " . table(
                ['No. Truk', 'Jenis', 'Waktu Kedatangan', 'Mulai Layanan', 'Waktu Layanan', 'Selesai Layanan', 'Waktu Tunggu'],
                array_map(function($truck, $index) {
                    return [
                        $index + 1,
                        ucfirst($truck['type']),
                        formatTime($truck['arrival_time']),
                        formatTime($truck['service_start']),
                        $truck['service_time'] . ' menit',
                        formatTime($truck['service_end']),
                        formatMinutes($truck['wait_time'])
                    ];
                }, $queue_simulation, array_keys($queue_simulation))
            ) . "
        </div>
    </div>

    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900 mb-4'>
                Visualisasi Antrian
            </h3>
            <canvas id='queueChart' class='w-full'></canvas>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
<script>
    const ctx = document.getElementById('queueChart').getContext('2d');
    const data = " . json_encode($queue_simulation) . ";
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            datasets: [{
                label: 'Waktu Tunggu',
                data: data.map(truck => ({
                    x: truck.truck_id,
                    y: [truck.arrival_time, truck.service_start]
                })),
                backgroundColor: 'rgba(239, 68, 68, 0.5)',
                borderColor: 'rgb(239, 68, 68)',
                borderWidth: 1
            },
            {
                label: 'Waktu Layanan',
                data: data.map(truck => ({
                    x: truck.truck_id,
                    y: [truck.service_start, truck.service_end]
                })),
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Nomor Truk'
                    }
                },
                y: {
                    min: 8 * 60,
                    max: 17 * 60,
                    ticks: {
                        callback: function(value) {
                            return Math.floor(value / 60) + ':' + (value % 60).toString().padStart(2, '0');
                        }
                    },
                    title: {
                        display: true,
                        text: 'Waktu'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Visualisasi Antrian Truk'
                }
            }
        }
    });
</script>";

// Include layout
require_once '../layouts/main.php';
?> 