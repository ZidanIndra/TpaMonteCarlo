# Simulasi Antrian Pengolahan Sampah TPA dengan Metode Monte Carlo

## Deskripsi
Aplikasi web ini merupakan sistem simulasi antrian untuk pengolahan sampah di Tempat Pembuangan Akhir (TPA) menggunakan metode Monte Carlo. Aplikasi ini membantu dalam menganalisis dan mengoptimalkan pengelolaan antrian truk sampah di TPA dengan mempertimbangkan berbagai variabel dan ketidakpastian dalam sistem.

## Fitur Utama
- Input data frekuensi kedatangan truk dan parameter simulasi
- Perhitungan distribusi probabilitas (PDF & CDF)
- Simulasi Monte Carlo untuk prediksi antrian
- Analisis waktu tunggu dan waktu pelayanan
- Visualisasi hasil simulasi dengan grafik
- Generasi laporan dalam format PDF
- Laporan detail per hari dan laporan keseluruhan

## Metode dan Perhitungan
Aplikasi ini menggunakan metode Monte Carlo dengan langkah-langkah sebagai berikut:

1. **Input Data**
   - Frekuensi kedatangan truk
   - Waktu pelayanan
   - Parameter simulasi lainnya

2. **Perhitungan Distribusi**
   - Perhitungan Probability Density Function (PDF)
   - Perhitungan Cumulative Distribution Function (CDF)
   - Generasi nilai acak berdasarkan distribusi

3. **Simulasi Monte Carlo**
   - Simulasi antrian untuk setiap truk
   - Perhitungan waktu tunggu
   - Perhitungan waktu pelayanan
   - Analisis waktu di sistem

4. **Analisis Hasil**
   - Rata-rata waktu tunggu per hari
   - Distribusi waktu pelayanan
   - Visualisasi hasil simulasi
   - Generasi laporan detail

## Teknologi yang Digunakan
- PHP untuk backend
- HTML, CSS, dan JavaScript untuk frontend
- Tailwind CSS untuk styling
- Chart.js untuk visualisasi data
- Font Awesome untuk ikon
- jsPDF untuk generasi PDF

## Cara Penggunaan
1. Pastikan server web (Apache/Nginx) dan PHP terinstal
2. Clone repository ini
3. Import database (jika ada)
4. Akses aplikasi melalui browser
5. Masukkan data yang diperlukan
6. Jalankan simulasi
7. Lihat hasil dan unduh laporan

## Struktur Aplikasi
- `index.php` - Halaman utama
- `pages/` - Halaman-halaman aplikasi
- `assets/` - File statis (CSS, JS, gambar)
- `includes/` - File PHP yang digunakan bersama
- `layouts/` - Template layout
- `vendor/` - Dependensi pihak ketiga

## Kontribusi
Kontribusi untuk pengembangan aplikasi ini sangat diterima. Silakan buat pull request atau buka issue untuk diskusi lebih lanjut.

## Lisensi
Proyek ini dilisensikan di bawah [MIT License](LICENSE).

## Kontak
Untuk pertanyaan dan dukungan, silakan buka issue di repository ini. 