<?php
// about.php - penjelasan proyek
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>About - Analisis Efisiensi Algoritma Pengurutan</title>
    <style>
        body{font-family:Segoe UI, Tahoma, Geneva, Verdana, sans-serif;background:#f6f8fb;padding:30px}
        .card{max-width:900px;margin:0 auto;background:#fff;padding:24px;border-radius:8px;box-shadow:0 8px 30px rgba(0,0,0,0.08)}
        h1{color:#333}p{color:#444;line-height:1.6}
        .section{margin-top:18px}
        pre{background:#f4f6fb;padding:12px;border-radius:6px;overflow:auto}
        a.back{display:inline-block;margin-top:16px;padding:10px 14px;background:#667eea;color:#fff;border-radius:6px;text-decoration:none}
    </style>
</head>
<body>
    <div class="card">
        <h1>About</h1>

        <div class="section">
            <strong>1. Judul</strong>
            <p>Analisis Efisiensi Algoritma Pengurutan: Perbandingan Selection Sort (Iteratif) dan Insertion Sort (Rekursif) pada Pengurutan Data Nilai Mahasiswa.</p>
        </div>

        <div class="section">
            <strong>2. Deskripsi Studi Kasus</strong>
            <p>Proyek ini mengukur waktu eksekusi dua algoritma pengurutan sederhana menggunakan dataset nilai mahasiswa realistis (rentang 40–100). Tujuannya adalah membandingkan performa (running time) dan memperlihatkan perbedaan perilaku antara pendekatan iteratif dan rekursif pada ukuran dataset berbeda.</p>
        </div>

        <div class="section">
            <strong>3. Dataset yang Digunakan</strong>
            <p>Data tersimpan di tabel <code>dataset_nilai</code> (MySQL). Kolom: <code>id INT AUTO_INCREMENT</code>, <code>nilai INT</code>. Nilai di-generate mengikuti distribusi realistis kampus Indonesia:</p>
            <ul>
                <li>40–55: rendah (~15%)</li>
                <li>56–70: sedang (~35%)</li>
                <li>71–85: baik (~35%)</li>
                <li>86–100: sangat baik (~15%)</li>
            </ul>
            <p>Untuk eksperimen, tersedia generator 10.000 data (`insert_dataset.php`) dan contoh 100 data (`sample_dataset_100.sql`).</p>
        </div>

        <div class="section">
            <strong>4. Algoritma yang Digunakan</strong>
            <p>Implementasi dan ringkasan singkat dari algoritma yang dibandingkan dalam proyek ini.</p>

            <h4>🔧 Algoritma yang Dibandingkan</h4>
            <h5>Selection Sort (Iteratif)</h5>
            <ul>
                <li><strong>Metode:</strong> Iteratif — mencari elemen minimum pada sisa array lalu menukarnya ke posisi saat ini.</li>
                <li><strong>Kompleksitas:</strong> O(n²) untuk best, average, dan worst case.</li>
                <li><strong>Stabil:</strong> Tidak (bukan stable sort).</li>
                <li><strong>Karakteristik:</strong> Konsisten dan prediktif — performa tidak tergantung pada input awal.</li>
            </ul>

            <h5>Insertion Sort (Rekursif)</h5>
            <ul>
                <li><strong>Metode:</strong> Rekursif — urutkan n-1 elemen, lalu sisipkan elemen ke-n pada posisi yang benar.</li>
                <li><strong>Best case:</strong> O(n) (ketika data sudah hampir terurut).</li>
                <li><strong>Worst / Average case:</strong> O(n²).</li>
                <li><strong>Stabil:</strong> Ya (stable sort).</li>
                <li><strong>Karakteristik:</strong> Adaptive — lebih efisien pada data yang sudah sebagian terurut; menggunakan call stack sehingga membutuhkan ruang tambahan O(n) pada rekursi.</li>
            </ul>
        </div>

        <div class="section">
            <strong>Panduan Singkat Penggunaan</strong>
            <ol>
                <li>Jalankan Laragon (Apache + MySQL).</li>
                <li>Buat DB dan tabel dengan <code>create_tables.sql</code> di phpMyAdmin.</li>
                <li>Isi data sampel via <code>sample_dataset_100.sql</code> atau jalankan <code>php insert_dataset.php</code> untuk 10.000 nilai.</li>
                <li>Buka <a href="/TUBES-AKA/index.php">index.php</a>, pilih ukuran dataset dan algoritma, lalu jalankan pengujian.</li>
            </ol>
        </div>

        <a class="back" href="index.php">← Kembali</a>
    </div>
</body>
</html>
