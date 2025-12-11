# Analisis Efisiensi Algoritma Pengurutan

Proyek PHP Native untuk membandingkan **Selection Sort (iteratif)** dan **Insertion Sort (rekursif)** pada data nilai mahasiswa.

## 📋 Persyaratan
- **Laragon** (dengan PHP 8+)
- **MySQL** (included dalam Laragon)
- Browser (Chrome, Firefox, etc.)

## 🚀 Langkah Menjalankan di Laragon

### 1. Persiapan Folder
Salin seluruh isi folder proyek ke `C:\laragon\www\TUBES-AKA`

Folder sudah berisi:
```
TUBES-AKA/
├── koneksi.php
├── insert_dataset.php
├── sorting.php
├── index.php
├── run.php
├── hasil.php
├── grafik.php
├── create_tables.sql
├── sample_dataset_100.sql
└── README.md
```

### 2. Mulai Laragon
1. Buka **Laragon** (pastikan sudah diinstall di `C:\laragon`)
2. Klik tombol **Start** untuk menjalankan Apache + MySQL
3. Jika perlu, klik **MySQL** untuk memastikan MySQL berjalan

### 3. Setup Database
Buka **phpMyAdmin**: http://localhost/phpmyadmin

**Opsi A (Rekomendasi - Via phpMyAdmin):**
1. Login dengan username `root`, password kosong
2. Klik tab **SQL** di toolbar atas
3. Copy-paste isi file `create_tables.sql`
4. Klik **Execute** (tombol biru)
5. Database `algoritma_pengurutan` dan tabel akan terbuat otomatis

**Opsi B (Menggunakan MySQL CLI):**
```bash
cd C:\laragon\bin\mysql\mysql-[version]\bin
mysql -u root < "C:\laragon\www\TUBES-AKA\create_tables.sql"
```

### 4. Isi Database dengan Sampel Data

**Opsi A (100 data contoh):**
1. Di phpMyAdmin, buka tab **SQL**
2. Copy-paste isi file `sample_dataset_100.sql`
3. Klik **Execute**

**Opsi B (Generate 10.000 data realistis otomatis):**
1. Buka browser: http://localhost/TUBES-AKA/index.php
2. Buka PowerShell, jalankan:
```powershell
cd C:\laragon\www\TUBES-AKA
php insert_dataset.php
```
Script akan insert 10.000 nilai dengan distribusi:
- 40-55 (rendah): 15%
- 56-70 (sedang): 35%
- 71-85 (baik): 35%
- 86-100 (sangat baik): 15%

### 5. Akses Aplikasi
Buka di browser: **http://localhost/TUBES-AKA/index.php**

## 📊 Fitur Aplikasi

### Halaman Utama (`index.php`)
- Pilih ukuran dataset: 100, 500, 1000, 2000, 5000, 10000
- Klik "Jalankan Pengujian"

### Menjalankan Pengujian (`run.php`)
- Ambil sample data dari database
- Jalankan Selection Sort (iteratif)
- Jalankan Insertion Sort (rekursif)
- Ukur waktu dengan `microtime(true)`
- Simpan hasil ke tabel `hasil_running`
- Tampilkan hasil perbandingan

### Hasil (`hasil.php`)
- Tabel semua hasil pengujian (50 terbaru)
- Kolom: ID, Ukuran Data, Waktu Selection, Waktu Insertion, Selisih, Lebih Cepat, Timestamp
- Warna merah = algoritma yang lebih lambat
- Warna hijau = algoritma yang lebih cepat

### Grafik (`grafik.php`)
- Visualisasi dengan Chart.js
- Garis merah = Selection Sort
- Garis biru = Insertion Sort
- Statistik rata-rata waktu
- Interaktif dan responsive

## 🔍 Analisis Algoritma

### Selection Sort (Iteratif)
```php
function selection_sort(array $arr): array {
    $n = count($arr);
    for ($i = 0; $i < $n - 1; $i++) {
        $minIdx = $i;
        for ($j = $i + 1; $j < $n; $j++) {
            if ($arr[$j] < $arr[$minIdx]) {
                $minIdx = $j;
            }
        }
        if ($minIdx !== $i) {
            $tmp = $arr[$i];
            $arr[$i] = $arr[$minIdx];
            $arr[$minIdx] = $tmp;
        }
    }
    return $arr;
}
```

**Karakteristik:**
- **Kompleksitas Waktu:** O(n²) best, average, worst case
- **Kompleksitas Ruang:** O(1)
- **Stabil:** Tidak
- **Pendekatan:** Iteratif (loop dengan index)
- **Cara Kerja:** Cari nilai minimum, tukar dengan elemen pertama, ulangi untuk sisa array

### Insertion Sort (Rekursif)
```php
function insertion_sort_recursive(array $arr, ?int $n = null): array {
    if ($n === null) $n = count($arr);
    if ($n <= 1) return $arr;

    // Sort first n-1 elements
    $arr = insertion_sort_recursive($arr, $n - 1);

    // Insert nth element into correct position
    $last = $arr[$n - 1];
    $j = $n - 2;
    while ($j >= 0 && $arr[$j] > $last) {
        $arr[$j + 1] = $arr[$j];
        $j--;
    }
    $arr[$j + 1] = $last;
    return $arr;
}
```

**Karakteristik:**
- **Kompleksitas Waktu:** O(n) best case, O(n²) average & worst case
- **Kompleksitas Ruang:** O(n) (karena stack rekursi)
- **Stabil:** Ya
- **Pendekatan:** Rekursif (call function sampai base case)
- **Cara Kerja:** Urutkan n-1 elemen, kemudian insert elemen ke-n ke posisi yang benar

## 📈 Perbandingan Iteratif vs Rekursif

| Aspek | Iteratif | Rekursif |
|-------|----------|----------|
| **Readability** | Lebih mudah dipahami | Lebih abstrak |
| **Memory** | O(1) stack | O(n) stack (call stack) |
| **Speed** | Umumnya lebih cepat | Lebih lambat (overhead function call) |
| **Scalability** | Baik untuk data besar | Rentan stack overflow |
| **Elegance** | Less elegant | More elegant |

## ⚠️ Catatan Penting

1. **Performance pada Data Besar:**
   - Selection Sort & Insertion Sort keduanya O(n²)
   - Untuk n=10.000, bisa memakan waktu hingga beberapa detik
   - Untuk n=5.000+, perhatikan memory dan waktu timeout

2. **Time Limit:**
   - Setting `set_time_limit(0)` sudah aktif di `run.php` untuk menghindari timeout
   - Tapi tetap hati-hati dengan dataset sangat besar

3. **Recursive Stack:**
   - Insertion Sort rekursif menggunakan call stack
   - Untuk data sangat besar (>10.000), bisa trigger stack overflow
   - PHP default stack limit ~8MB

4. **Best Practice:**
   - Mulai dari dataset 100-500 dulu
   - Observasi perbedaan waktu
   - Gradual naik ke 1000, 2000, dst

## 📁 Struktur File

| File | Fungsi |
|------|--------|
| `koneksi.php` | Koneksi ke MySQL menggunakan mysqli |
| `insert_dataset.php` | Generator 10.000 data nilai realistis |
| `sorting.php` | Implementasi Selection Sort & Insertion Sort |
| `index.php` | Halaman utama pilih ukuran dataset |
| `run.php` | Eksekusi sorting, hitung waktu, simpan ke DB |
| `hasil.php` | Tampilkan tabel hasil pengujian |
| `grafik.php` | Visualisasi Chart.js |
| `create_tables.sql` | SQL untuk buat database & tabel |
| `sample_dataset_100.sql` | SQL insert 100 data sampel |

## 🔗 Menu Navigasi
- **Home** → `index.php` — Jalankan pengujian
- **Lihat Hasil** → `hasil.php` — Tabel hasil
- **Lihat Grafik** → `grafik.php` — Visualisasi

## 💡 Tips & Trik

### Untuk Reset Database
```sql
TRUNCATE TABLE hasil_running;
TRUNCATE TABLE dataset_nilai;
```

### Untuk Melihat Data Langsung
```sql
SELECT COUNT(*) FROM dataset_nilai;
SELECT * FROM dataset_nilai LIMIT 10;
SELECT * FROM hasil_running ORDER BY created_at DESC;
```

### Untuk Optimalkan Generator
Edit `insert_dataset.php` untuk mengubah:
- Total data: ubah `$total = 10000;`
- Distribusi: ubah nilai percentage di `$counts`

## 🐛 Troubleshooting

**Error: "Database connection error"**
- Pastikan MySQL sudah berjalan di Laragon
- Check username/password di `koneksi.php`

**Error: "Not enough data in dataset_nilai"**
- Jalankan `sample_dataset_100.sql` atau `insert_dataset.php` dulu

**Grafik tidak muncul**
- Check browser console (F12 → Console tab)
- Pastikan ada data di tabel `hasil_running`

**Timeout pada dataset besar**
- Untuk sekarang, batasi ke 1000 atau 2000
- Atau tunggu lebih lama saat running

## ✅ Checklist Setup

- [ ] Laragon sudah diinstall & berjalan
- [ ] Apache & MySQL aktif di Laragon
- [ ] Folder `TUBES-AKA` di `C:\laragon\www\`
- [ ] `create_tables.sql` sudah dijalankan
- [ ] Database `algoritma_pengurutan` terbuat
- [ ] Tabel `dataset_nilai` & `hasil_running` terbuat
- [ ] Data sampel sudah di-insert (100 atau 10.000)
- [ ] Bisa buka `http://localhost/TUBES-AKA/index.php`
- [ ] Pengujian berjalan dan hasil muncul

---

**Dibuat:** Desember 2025  
**Bahasa:** PHP 8 Native + MySQL + Chart.js  
**Framework:** Tidak ada (Pure PHP)
