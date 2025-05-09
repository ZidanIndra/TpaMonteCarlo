<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Simulasi Antrian Pengolahan Sampah - Monte Carlo</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    }
                }
            }
        }
    </script>
    <style>
        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23dcfce7' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-primary-700 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="index.php" class="text-white text-xl font-bold flex items-center">
                            <i class="fas fa-recycle mr-2"></i>
                            Simulasi Antrian Sampah
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="index.php" class="border-primary-500 text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Beranda
                        </a>
                        <a href="pages/input_data.php" class="border-transparent text-primary-100 hover:border-primary-300 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Input Data
                        </a>
                        <a href="pages/simulasi.php" class="border-transparent text-primary-100 hover:border-primary-300 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Simulasi
                        </a>
                        <a href="pages/laporan.php" class="border-transparent text-primary-100 hover:border-primary-300 hover:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-primary-600 hero-pattern">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-primary-700 mix-blend-multiply"></div>
        </div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Sistem Simulasi Antrian Sampah
            </h1>
            <p class="mt-6 text-xl text-primary-100 max-w-3xl">
                Menggunakan metode Monte Carlo untuk memprediksi dan mengoptimalkan pengelolaan antrian truk sampah di TPA.
            </p>
            <div class="mt-10">
                <a href="pages/input_data.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-primary-700 bg-white hover:bg-primary-50">
                    Mulai Simulasi
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-primary-600 font-semibold tracking-wide uppercase">Fitur Utama</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Solusi Cerdas untuk Pengelolaan Sampah
                </p>
            </div>

            <div class="mt-10">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white">
                            <i class="fas fa-database text-xl"></i>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Input Data</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Masukkan data frekuensi kedatangan truk dan parameter simulasi dengan mudah.
                            </p>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white">
                            <i class="fas fa-calculator text-xl"></i>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Simulasi Monte Carlo</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Lakukan simulasi menggunakan metode Monte Carlo untuk prediksi yang akurat.
                            </p>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white">
                            <i class="fas fa-chart-bar text-xl"></i>
                        </div>
                        <div class="ml-16">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Analisis & Laporan</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Dapatkan laporan lengkap dengan visualisasi data yang informatif.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center mb-12">
                <h2 class="text-base text-primary-600 font-semibold tracking-wide uppercase">Cara Kerja</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Proses Simulasi Monte Carlo
                </p>
            </div>

            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="px-3 bg-gray-50 text-lg font-medium text-gray-900">
                        Langkah-langkah
                    </span>
                </div>
            </div>

            <div class="mt-12">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-4 md:gap-x-8 md:gap-y-10">
                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white mx-auto">
                            <span class="text-lg font-bold">1</span>
                        </div>
                        <div class="mt-5">
                            <h3 class="text-lg font-medium text-gray-900">Input Data</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Masukkan data frekuensi kedatangan truk
                            </p>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white mx-auto">
                            <span class="text-lg font-bold">2</span>
                        </div>
                        <div class="mt-5">
                            <h3 class="text-lg font-medium text-gray-900">Hitung PDF & CDF</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Sistem menghitung distribusi probabilitas
                            </p>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white mx-auto">
                            <span class="text-lg font-bold">3</span>
                        </div>
                        <div class="mt-5">
                            <h3 class="text-lg font-medium text-gray-900">Simulasi</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Lakukan simulasi Monte Carlo
                            </p>
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white mx-auto">
                            <span class="text-lg font-bold">4</span>
                        </div>
                        <div class="mt-5">
                            <h3 class="text-lg font-medium text-gray-900">Analisis</h3>
                            <p class="mt-2 text-base text-gray-500">
                                Dapatkan hasil analisis dan rekomendasi
                            </p>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-primary-900">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-white text-lg font-semibold">Sistem Simulasi Antrian Sampah</h3>
                    <p class="mt-4 text-primary-200">
                        Menggunakan metode Monte Carlo untuk prediksi kedatangan truk sampah.
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-primary-200">&copy; 2024 Sistem Simulasi Antrian Sampah</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html> 