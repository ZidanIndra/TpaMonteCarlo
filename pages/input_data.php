<?php
session_start();
require_once '../includes/components.php';

// Initialize session variables if not set
if (!isset($_SESSION['frequency_data'])) {
    $_SESSION['frequency_data'] = [
        'senin' => 200,
        'selasa' => 220,
        'rabu' => 205,
        'kamis' => 230,
        'jumat' => 285,
        'sabtu' => 201,
        'minggu' => 251
    ];
}

if (!isset($_SESSION['simulation_params'])) {
    $_SESSION['simulation_params'] = [
        'simulation_count' => 10  // Number of Monte Carlo simulations to run
    ];
}

if (!isset($_SESSION['trucks_data'])) {
    $_SESSION['trucks_data'] = [
        'senin' => 38,
        'selasa' => 30,
        'rabu' => 26,
        'kamis' => 22,
        'jumat' => 28,
        'sabtu' => 29,
        'minggu' => 27
    ];
}

// Initialize truck capacity data if not set
if (!isset($_SESSION['truck_capacity'])) {
    $_SESSION['truck_capacity'] = [
        'capacity' => 10,
        'count' => 2,
        'loading_time' => 30,
        'unloading_time' => 45
    ];
}

$pageTitle = "Input Data - Sistem Simulasi Antrian Sampah";

// Store session data in local variables for use in closure
$frequencyData = $_SESSION['frequency_data'];
$simulationParams = $_SESSION['simulation_params'];

// Prepare content
$content = "
<div class='space-y-6'>
    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900'>
                Input Data Frekuensi Kedatangan Truk
            </h3>
            <div class='mt-2 max-w-xl text-sm text-gray-500'>
                <p>Masukkan data frekuensi kedatangan truk untuk setiap hari dalam seminggu.</p>
            </div>
            <form action='process_input.php' method='POST' class='mt-5 space-y-6'>
                <div class='grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3'>
                    " . implode('', array_map(function($day) use ($frequencyData) {
                        return formGroup(
                            ucfirst($day),
                            "<input type='number' name='frequency[{$day}]' value='{$frequencyData[$day]}' class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm' required>"
                        );
                    }, array_keys($frequencyData))) . "
                </div>

                <div class='mt-8'>
                    <h3 class='text-lg leading-6 font-medium text-gray-900 mb-4'>
                        Parameter Simulasi Monte Carlo
                    </h3>
                    <div class='grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3'>
                        " . formGroup(
                            'Jumlah Simulasi',
                            "<input type='number' name='simulation_count' value='{$simulationParams['simulation_count']}' class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm' required>"
                        ) . "
                    </div>
                </div>

                <div class='flex justify-end'>
                    " . button('Simpan Data', 'submit', 'primary', 'fas fa-save') . "
                </div>
            </form>
        </div>
    </div>

    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900'>
                Informasi Parameter
            </h3>
            <div class='mt-2 max-w-xl text-sm text-gray-500'>
                <p>Parameter-parameter yang digunakan dalam simulasi Monte Carlo:</p>
                <ul class='mt-2 list-disc list-inside'>
                    <li>Jumlah Simulasi: Banyaknya simulasi Monte Carlo yang akan dijalankan</li>
                    <li>Data Frekuensi: Jumlah kedatangan truk per hari yang akan digunakan untuk menghitung probabilitas</li>
                </ul>
            </div>
        </div>
    </div>
</div>";

// Include layout
require_once '../layouts/main.php';
?> 