<?php
session_start();

// Process frequency data
if (isset($_POST['frequency'])) {
    $_SESSION['frequency_data'] = $_POST['frequency'];
}

// Process simulation parameters
if (isset($_POST['simulation_count'])) {
    $_SESSION['simulation_params'] = [
        'simulation_count' => $_POST['simulation_count']
    ];
}

// Define fixed cumulative intervals for each day
$cumulative_intervals = [
    'senin' => [
        'start' => 0.000,
        'end' => 0.126
    ],
    'selasa' => [
        'start' => 0.127,
        'end' => 0.265
    ],
    'rabu' => [
        'start' => 0.266,
        'end' => 0.394
    ],
    'kamis' => [
        'start' => 0.395,
        'end' => 0.538
    ],
    'jumat' => [
        'start' => 0.539,
        'end' => 0.717
    ],
    'sabtu' => [
        'start' => 0.718,
        'end' => 0.843
    ],
    'minggu' => [
        'start' => 0.844,
        'end' => 1.000
    ]
];

// Calculate probabilities based on the intervals
$probabilities = [];
foreach ($cumulative_intervals as $day => $interval) {
    $probabilities[$day] = $interval['end'] - $interval['start'];
}

// Store calculated data in session
$_SESSION['probabilities'] = $probabilities;
$_SESSION['cumulative_intervals'] = $cumulative_intervals;

// Redirect to simulation page
header('Location: simulasi.php');
exit;
?> 