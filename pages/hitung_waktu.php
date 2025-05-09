<?php
session_start();
require_once '../includes/components.php';

// Initialize queue parameters if not set
if (!isset($_SESSION['queue_time_params'])) {
    $_SESSION['queue_time_params'] = [
        'organic_loading' => 30,    // minutes
        'organic_unloading' => 45,  // minutes
        'inorganic_loading' => 25,  // minutes
        'inorganic_unloading' => 35 // minutes
    ];
}

$pageTitle = "Hitung Waktu - Sistem Simulasi Antrian Sampah";

// Prepare content
$content = "
<div class='space-y-6'>
    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900'>
                Pengaturan Waktu Bongkar Muat
            </h3>
            <div class='mt-2 max-w-xl text-sm text-gray-500'>
                <p>Masukkan estimasi waktu yang dibutuhkan untuk proses bongkar muat sampah (dalam menit).</p>
            </div>
            <div class='mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2'>
                <div class='bg-green-50 p-4 rounded-lg'>
                    <h4 class='text-green-800 font-medium mb-4'>Sampah Organik</h4>
                    <div class='space-y-4'>
                        <div>
                            <label class='block text-sm font-medium text-gray-700'>Waktu Muat</label>
                            <input type='number' id='organic_loading' value='{$_SESSION['queue_time_params']['organic_loading']}' 
                                class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm'
                                min='1'>
                        </div>
                        <div>
                            <label class='block text-sm font-medium text-gray-700'>Waktu Bongkar</label>
                            <input type='number' id='organic_unloading' value='{$_SESSION['queue_time_params']['organic_unloading']}'
                                class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm'
                                min='1'>
                        </div>
                    </div>
                </div>
                <div class='bg-blue-50 p-4 rounded-lg'>
                    <h4 class='text-blue-800 font-medium mb-4'>Sampah Anorganik</h4>
                    <div class='space-y-4'>
                        <div>
                            <label class='block text-sm font-medium text-gray-700'>Waktu Muat</label>
                            <input type='number' id='inorganic_loading' value='{$_SESSION['queue_time_params']['inorganic_loading']}'
                                class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm'
                                min='1'>
                        </div>
                        <div>
                            <label class='block text-sm font-medium text-gray-700'>Waktu Bongkar</label>
                            <input type='number' id='inorganic_unloading' value='{$_SESSION['queue_time_params']['inorganic_unloading']}'
                                class='mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm'
                                min='1'>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class='bg-white shadow sm:rounded-lg'>
        <div class='px-4 py-5 sm:p-6'>
            <h3 class='text-lg leading-6 font-medium text-gray-900 mb-4'>
                Simulasi Antrian Truk
            </h3>
            <div class='grid grid-cols-1 gap-6 sm:grid-cols-2'>
                <div class='bg-green-50 p-4 rounded-lg'>
                    <h4 class='text-green-800 font-medium mb-4'>Truk Sampah Organik</h4>
                    <div class='flex items-center space-x-4'>
                        <span class='text-2xl font-bold text-green-600' id='organic_count'>0</span>
                        <div class='space-x-2'>
                            <button onclick='addTruck(\"organic\")' class='inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500'>
                                <i class='fas fa-plus mr-2'></i> Tambah Truk
                            </button>
                            <button onclick='removeTruck(\"organic\")' class='inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500'>
                                <i class='fas fa-minus mr-2'></i> Kurang
                            </button>
                        </div>
                    </div>
                </div>
                <div class='bg-blue-50 p-4 rounded-lg'>
                    <h4 class='text-blue-800 font-medium mb-4'>Truk Sampah Anorganik</h4>
                    <div class='flex items-center space-x-4'>
                        <span class='text-2xl font-bold text-blue-600' id='inorganic_count'>0</span>
                        <div class='space-x-2'>
                            <button onclick='addTruck(\"inorganic\")' class='inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'>
                                <i class='fas fa-plus mr-2'></i> Tambah Truk
                            </button>
                            <button onclick='removeTruck(\"inorganic\")' class='inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500'>
                                <i class='fas fa-minus mr-2'></i> Kurang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class='mt-8'>
                <div class='bg-gray-50 p-4 rounded-lg'>
                    <h4 class='text-gray-800 font-medium mb-4'>Hasil Perhitungan Waktu</h4>
                    <div class='grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4'>
                        <div class='bg-white p-4 rounded-lg shadow'>
                            <div class='text-sm text-gray-500'>Total Waktu Antrian</div>
                            <div class='text-2xl font-bold text-gray-900' id='total_time'>0 menit</div>
                        </div>
                        <div class='bg-white p-4 rounded-lg shadow'>
                            <div class='text-sm text-gray-500'>Estimasi Selesai</div>
                            <div class='text-2xl font-bold text-gray-900' id='finish_time'>-</div>
                        </div>
                        <div class='bg-white p-4 rounded-lg shadow'>
                            <div class='text-sm text-gray-500'>Total Truk</div>
                            <div class='text-2xl font-bold text-gray-900' id='total_trucks'>0 truk</div>
                        </div>
                        <div class='bg-white p-4 rounded-lg shadow'>
                            <div class='text-sm text-gray-500'>Rata-rata Waktu per Truk</div>
                            <div class='text-2xl font-bold text-gray-900' id='avg_time'>0 menit</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>";

// Additional scripts
$additionalScripts = "
<script>
let organicCount = 0;
let inorganicCount = 0;

function addTruck(type) {
    if (type === 'organic') {
        organicCount++;
        document.getElementById('organic_count').textContent = organicCount;
    } else {
        inorganicCount++;
        document.getElementById('inorganic_count').textContent = inorganicCount;
    }
    calculateTime();
}

function removeTruck(type) {
    if (type === 'organic' && organicCount > 0) {
        organicCount--;
        document.getElementById('organic_count').textContent = organicCount;
    } else if (type === 'inorganic' && inorganicCount > 0) {
        inorganicCount--;
        document.getElementById('inorganic_count').textContent = inorganicCount;
    }
    calculateTime();
}

function calculateTime() {
    // Get input values
    const organicLoading = parseInt(document.getElementById('organic_loading').value) || 0;
    const organicUnloading = parseInt(document.getElementById('organic_unloading').value) || 0;
    const inorganicLoading = parseInt(document.getElementById('inorganic_loading').value) || 0;
    const inorganicUnloading = parseInt(document.getElementById('inorganic_unloading').value) || 0;

    // Calculate total time for each type
    const organicTime = organicCount * (organicLoading + organicUnloading);
    const inorganicTime = inorganicCount * (inorganicLoading + inorganicUnloading);

    // Calculate total time (assuming sequential processing)
    const totalTime = organicTime + inorganicTime;
    const totalTrucks = organicCount + inorganicCount;
    
    // Calculate average time per truck
    const avgTime = totalTrucks > 0 ? Math.round(totalTime / totalTrucks) : 0;

    // Update display
    document.getElementById('total_time').textContent = totalTime + ' menit';
    document.getElementById('total_trucks').textContent = totalTrucks + ' truk';
    document.getElementById('avg_time').textContent = avgTime + ' menit';

    // Calculate finish time
    if (totalTime > 0) {
        const now = new Date();
        const finishTime = new Date(now.getTime() + totalTime * 60000);
        document.getElementById('finish_time').textContent = finishTime.toLocaleTimeString();
    } else {
        document.getElementById('finish_time').textContent = '-';
    }
}

// Add event listeners to input fields
document.getElementById('organic_loading').addEventListener('change', calculateTime);
document.getElementById('organic_unloading').addEventListener('change', calculateTime);
document.getElementById('inorganic_loading').addEventListener('change', calculateTime);
document.getElementById('inorganic_unloading').addEventListener('change', calculateTime);
</script>";

// Include layout
require_once '../layouts/main.php';
?> 