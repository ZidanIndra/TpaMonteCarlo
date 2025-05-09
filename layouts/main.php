<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sistem Simulasi Antrian Pengolahan Sampah - Monte Carlo'; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS - Animate On Scroll -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'slide-down': 'slideDown 0.5s ease-out',
                        'bounce-slow': 'bounce 3s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        slideDown: {
                            '0%': { transform: 'translateY(-20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                    },
                }
            }
        }
    </script>
    
    <!-- Custom Styles -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #22c55e 0%, #14532d 100%);
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
    
    <?php if (isset($additionalStyles)): ?>
        <?php echo $additionalStyles; ?>
    <?php endif; ?>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="gradient-bg shadow-lg fixed w-full z-50" x-data="{ isOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="<?php echo $currentPage === 'index.php' ? 'index.php' : '../index.php'; ?>" class="text-white text-xl font-bold flex items-center hover-scale">
                            <i class="fas fa-recycle mr-2"></i>
                            Simulasi Antrian Sampah
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <?php
                        $currentPage = basename($_SERVER['PHP_SELF']);
                        $currentPath = $_SERVER['PHP_SELF'];
                        
                        $navItems = [
                            ['url' => '../index.php', 'title' => 'Beranda'],
                            ['url' => '../pages/input_data.php', 'title' => 'Input Data'],
                            ['url' => '../pages/simulasi.php', 'title' => 'Simulasi'],
                            ['url' => '../pages/laporan.php', 'title' => 'Laporan']
                        ];
                        
                        foreach ($navItems as $item) {
                            $isActive = false;
                            
                            // Check if current page matches the navigation item
                            if ($currentPage === basename($item['url'])) {
                                $isActive = true;
                            }
                            // Special case for index.php and adjust paths based on current location
                            else if ($currentPage === 'index.php' && basename($item['url']) === 'index.php') {
                                $isActive = true;
                            }
                            
                            // Adjust URL based on current location
                            $finalUrl = $item['url'];
                            if ($currentPage === 'index.php') {
                                $finalUrl = str_replace('../', '', $item['url']);
                            }
                            
                            $activeClass = $isActive 
                                ? 'border-primary-500 text-white' 
                                : 'border-transparent text-primary-100 hover:border-primary-300 hover:text-white';
                                
                            echo "<a href='{$finalUrl}' class='inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200 {$activeClass}'>";
                            echo "{$item['title']}</a>";
                        }
                        ?>
                    </div>
                </div>
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="isOpen = !isOpen" class="inline-flex items-center justify-center p-2 rounded-md text-primary-100 hover:text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <i :class="isOpen ? 'fas fa-times' : 'fas fa-bars'" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="sm:hidden" x-show="isOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="pt-2 pb-3 space-y-1">
                <?php
                foreach ($navItems as $item) {
                    $isActive = ($currentPage === basename($item['url'])) || 
                               ($currentPage === 'index.php' && basename($item['url']) === 'index.php');
                    
                    // Adjust URL based on current location for mobile menu
                    $finalUrl = $item['url'];
                    if ($currentPage === 'index.php') {
                        $finalUrl = str_replace('../', '', $item['url']);
                    }
                    
                    $activeClass = $isActive 
                        ? 'border-primary-500 text-white bg-primary-700' 
                        : 'border-transparent text-primary-100 hover:bg-primary-700 hover:border-primary-300 hover:text-white';
                    
                    echo "<a href='{$finalUrl}' class='block pl-3 pr-4 py-2 border-l-4 text-base font-medium {$activeClass}'>";
                    echo "{$item['title']}</a>";
                }
                ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-16">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <?php echo $content; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="gradient-bg mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="text-white">
                    <h3 class="text-lg font-semibold mb-4">Sistem Simulasi Antrian Sampah</h3>
                    <p class="text-primary-100">
                        Menggunakan metode Monte Carlo untuk prediksi kedatangan truk sampah.
                    </p>
                </div>
                <div class="text-right text-primary-100">
                    <p>&copy; 2024 Sistem Simulasi Antrian Sampah</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- AOS Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>

    <?php if (isset($additionalScripts)): ?>
        <?php echo $additionalScripts; ?>
    <?php endif; ?>
</body>
</html> 