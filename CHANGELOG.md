# Changelog - Perbaikan Proyek Analisis Efisiensi Algoritma

## Versi 2.0 - Desember 2025

### 🔒 Keamanan
- **Fixed SQL Injection Risk**: Mengganti query dengan string concatenation menjadi prepared statement di `run.php`
- **Input Validation**: Menambahkan validasi input yang lebih ketat untuk ukuran dataset dan algoritma
- **Error Handling**: Mengganti `die()` dengan error handler yang lebih user-friendly

### 🐛 Bug Fixes
- **Fixed Data Saving Bug**: Memperbaiki bug dimana data disimpan dua kali saat memilih "both" algoritma. Sekarang disimpan sekali dengan kedua waktu.
- **Stack Overflow Protection**: Menambahkan proteksi untuk Insertion Sort rekursif pada dataset besar (>3000)

### ✨ Fitur Baru
- **Export to CSV**: Menambahkan fitur export hasil pengujian ke CSV (`export.php`)
- **Filter & Search**: Menambahkan filter berdasarkan ukuran data di halaman hasil
- **Statistik Detail**: Menambahkan statistik lebih lengkap (min, max, rata-rata, jumlah kemenangan)
- **UI Consistency**: Memperbarui `hasil.php` dan `grafik.php` dengan styling yang konsisten dengan `index.php`

### 📚 Dokumentasi
- **PHPDoc Comments**: Menambahkan dokumentasi PHPDoc lengkap di semua fungsi
- **Code Comments**: Menambahkan komentar yang lebih deskriptif di seluruh kode
- **Error Handler**: Membuat file `error_handler.php` dengan fungsi error handling yang reusable

### 🧪 Testing
- **Unit Tests**: Menambahkan file `test_sorting.php` dengan 13 test cases untuk memastikan algoritma bekerja benar

### 🎨 UI/UX Improvements
- **Modern Design**: Konsistensi desain modern di semua halaman
- **Better Navigation**: Menambahkan tombol navigasi yang lebih mudah diakses
- **Responsive Layout**: Layout yang lebih responsif dan user-friendly
- **Export Button**: Menambahkan tombol export di halaman utama

### 📊 Statistik & Analisis
- **Enhanced Statistics**: Statistik lebih detail dengan min, max, dan jumlah kemenangan
- **Percentage Display**: Menampilkan persentase perbedaan waktu di tabel hasil
- **Better Chart**: Grafik yang lebih informatif dengan statistik

### 🔧 Technical Improvements
- **Prepared Statements**: Menggunakan prepared statement untuk semua query database
- **Exception Handling**: Menggunakan try-catch untuk error handling yang lebih baik
- **Code Organization**: Struktur kode yang lebih terorganisir dan mudah dipelihara

## File yang Diperbarui

### File Baru
- `error_handler.php` - Error handling utilities
- `export.php` - Export hasil ke CSV
- `test_sorting.php` - Unit tests untuk algoritma
- `CHANGELOG.md` - File changelog ini

### File yang Diperbaiki
- `run.php` - Perbaikan keamanan, bug fix, dan error handling
- `sorting.php` - Dokumentasi PHPDoc lengkap
- `hasil.php` - UI konsisten, filter, statistik detail
- `grafik.php` - UI konsisten, statistik lebih lengkap
- `index.php` - Menambahkan tombol navigasi
- `koneksi.php` - Dokumentasi yang lebih baik
- `insert_dataset.php` - Error handling dan dokumentasi

## Cara Menggunakan Fitur Baru

### Export CSV
1. Buka halaman hasil atau grafik
2. Klik tombol "📥 Export ke CSV"
3. File CSV akan terdownload dengan semua data hasil pengujian

### Filter Hasil
1. Buka halaman "📊 Lihat Hasil"
2. Pilih ukuran data yang ingin difilter
3. Pilih jumlah data yang ingin ditampilkan
4. Klik "🔍 Terapkan Filter"

### Unit Testing
Jalankan di terminal:
```bash
php test_sorting.php
```

## Breaking Changes
Tidak ada breaking changes. Semua perubahan backward compatible.

## Catatan
- Stack overflow protection membatasi Insertion Sort rekursif maksimal 3000 data
- Export CSV menggunakan delimiter semicolon (;) untuk kompatibilitas Excel
- Semua error sekarang ditampilkan dengan halaman error yang user-friendly

