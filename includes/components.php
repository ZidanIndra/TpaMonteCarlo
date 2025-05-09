<?php
function card($title, $content, $icon = null, $color = 'primary') {
    return "
    <div class='bg-white rounded-lg shadow-md p-6 card-hover animate-fade-in' data-aos='fade-up'>
        " . ($icon ? "<div class='flex items-center mb-4'>
            <div class='flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-md bg-{$color}-500 text-white transform transition-transform duration-300 hover:scale-110'>
                <i class='{$icon} text-xl'></i>
            </div>
            <h3 class='ml-4 text-lg font-medium text-gray-900'>{$title}</h3>
        </div>" : "<h3 class='text-lg font-medium text-gray-900 mb-4'>{$title}</h3>") . "
        <div class='text-gray-500'>
            {$content}
        </div>
    </div>";
}

function table($headers, $rows, $classes = '') {
    $headerHtml = "<thead class='bg-gray-50'>
        <tr>";
    foreach ($headers as $header) {
        $headerHtml .= "<th class='px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'>{$header}</th>";
    }
    $headerHtml .= "</tr>
    </thead>";

    $bodyHtml = "<tbody class='bg-white divide-y divide-gray-200'>";
    foreach ($rows as $index => $row) {
        $bodyHtml .= "<tr class='hover:bg-gray-50 transition-colors duration-200' data-aos='fade-up' data-aos-delay='" . ($index * 50) . "'>";
        foreach ($row as $cell) {
            $bodyHtml .= "<td class='px-6 py-4 whitespace-nowrap text-sm text-gray-500'>{$cell}</td>";
        }
        $bodyHtml .= "</tr>";
    }
    $bodyHtml .= "</tbody>";

    return "<div class='overflow-x-auto rounded-lg shadow-sm animate-fade-in'>
        <table class='min-w-full divide-y divide-gray-200 {$classes}'>
            {$headerHtml}
            {$bodyHtml}
        </table>
    </div>";
}

function formGroup($label, $input, $error = null) {
    return "
    <div class='mb-4 animate-fade-in' data-aos='fade-up'>
        <label class='block text-gray-700 text-sm font-bold mb-2'>{$label}</label>
        <div class='relative'>
            {$input}
            " . ($error ? "<p class='text-red-500 text-xs italic mt-1 animate-slide-down'>{$error}</p>" : "") . "
        </div>
    </div>";
}

function button($text, $type = 'button', $color = 'primary', $icon = null, $classes = '') {
    $iconHtml = $icon ? "<i class='{$icon} mr-2'></i>" : "";
    return "
    <button type='{$type}' class='inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-{$color}-600 hover:bg-{$color}-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{$color}-500 transition-all duration-200 transform hover:scale-105 {$classes}'>
        {$iconHtml}{$text}
    </button>";
}

function alert($message, $type = 'info') {
    $colors = [
        'success' => 'green',
        'error' => 'red',
        'warning' => 'yellow',
        'info' => 'blue'
    ];
    $color = $colors[$type] ?? 'blue';
    
    return "
    <div class='rounded-md bg-{$color}-50 p-4 mb-4 animate-fade-in' data-aos='fade-up'>
        <div class='flex'>
            <div class='flex-shrink-0'>
                <i class='fas fa-" . ($type === 'success' ? 'check-circle' : ($type === 'error' ? 'exclamation-circle' : ($type === 'warning' ? 'exclamation-triangle' : 'info-circle'))) . " text-{$color}-400'></i>
            </div>
            <div class='ml-3'>
                <p class='text-sm font-medium text-{$color}-800'>{$message}</p>
            </div>
        </div>
    </div>";
}

function chartContainer($id, $title = '') {
    return "
    <div class='bg-white rounded-lg shadow-md p-6 card-hover animate-fade-in' data-aos='fade-up'>
        " . ($title ? "<h3 class='text-lg font-medium text-gray-900 mb-4'>{$title}</h3>" : "") . "
        <div class='relative h-64'>
            <canvas id='{$id}' class='w-full h-full'></canvas>
        </div>
    </div>";
}

function getNavigation() {
    return [
        ['title' => 'Beranda', 'url' => '/index.php', 'icon' => 'fas fa-home'],
        ['title' => 'Input Data', 'url' => '/pages/input_data.php', 'icon' => 'fas fa-database'],
        ['title' => 'Simulasi', 'url' => '/pages/simulasi.php', 'icon' => 'fas fa-calculator'],
        ['title' => 'Prediksi', 'url' => '/pages/prediksi_minggu.php', 'icon' => 'fas fa-chart-line'],
        ['title' => 'Laporan', 'url' => '/pages/laporan.php', 'icon' => 'fas fa-file-alt']
    ];
}
?> 