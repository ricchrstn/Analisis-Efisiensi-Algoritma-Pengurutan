<?php
// index.php - Dashboard utama dengan form, tabel hasil, dan grafik
require_once __DIR__ . '/koneksi.php';

$sizes = [1, 10, 20, 50, 100, 200, 500, 1000, 2000, 5000];

// Fetch all results
$res = $conn->query('SELECT * FROM hasil_running ORDER BY id DESC LIMIT 50');
$results = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $results[] = $row;
    }
}

// Prepare data untuk grafik
$labels = [];
$data_sel = [];
$data_ins = [];
$res_chart = $conn->query('SELECT ukuran_data, waktu_selection, waktu_insertion, created_at FROM hasil_running ORDER BY created_at ASC');
if ($res_chart) {
    while ($r = $res_chart->fetch_assoc()) {
        $labels[] = $r['ukuran_data'] . ' (' . substr($r['created_at'], 11, 5) . ')';
        $data_sel[] = (float)$r['waktu_selection'];
        $data_ins[] = (float)$r['waktu_insertion'];
    }
}
$row_count = count($labels);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analisis Efisiensi Algoritma Pengurutan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { 
            max-width: 1400px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 10px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        header h1 { font-size: 2.5em; margin-bottom: 10px; }
        header p { font-size: 1.1em; opacity: 0.95; }
        
        .content { padding: 30px; }
        
        .section { 
            margin-bottom: 40px; 
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 5px solid #667eea;
        }
        
        .section h2 { 
            color: #333; 
            margin-bottom: 20px; 
            font-size: 1.8em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Form Section */
        .form-group {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 20px;
        }
        .form-group label { 
            font-weight: bold; 
            color: #333;
            min-width: 200px;
        }
        .form-group select { 
            padding: 10px 15px; 
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 5px;
            flex: 1;
            max-width: 300px;
        }
        .form-group button { 
            padding: 10px 25px; 
            font-size: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            border: none; 
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        .form-group button:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        /* Table Section */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0;
            background: white;
        }
        table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        table th, table td { 
            padding: 15px; 
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table tbody tr { transition: background 0.2s; }
        table tbody tr:hover { background: #f0f0f0; }
        table tbody tr:nth-child(even) { background: #f8f9fa; }
        
        .faster { 
            color: #28a745; 
            font-weight: bold;
            padding: 5px 10px;
            background: #d4edda;
            border-radius: 3px;
        }
        .slower { 
            color: #dc3545; 
            font-weight: bold;
            padding: 5px 10px;
            background: #f8d7da;
            border-radius: 3px;
        }
        
        /* Chart Section */
        .chart-container { 
            position: relative; 
            width: 100%; 
            height: 500px; 
            margin: 30px 0;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-sizing: border-box;
        }
        canvas { max-height: 100%; }
        
        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #667eea;
            text-align: center;
        }
        .stat-card .label { 
            color: #666; 
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .stat-card .value { 
            font-size: 1.8em; 
            font-weight: bold;
            color: #667eea;
        }
        
        /* Info Box */
        .info-box {
            background: #e7f3fe;
            border-left: 5px solid #2196F3;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-box strong { color: #1976D2; }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .empty-state p { font-size: 1.1em; margin: 10px 0; }
        
        /* Footer */
        footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🎓 Analisis Efisiensi Algoritma Pengurutan</h1>
            <p>Perbandingan Selection Sort (Iteratif) vs Insertion Sort (Rekursif)</p>
            <p style="margin-top:10px"><a href="about.php" style="color:rgba(255,255,255,0.95); text-decoration:underline;">About</a></p>
        </header>

        <div class="content">
            <!-- Section 1: Form Pengujian -->
            <div class="section">
                <h2>⚙️ Jalankan Pengujian</h2>
                <p style="color: #666; margin-bottom: 20px;">Pilih ukuran dataset, algoritma, dan klik tombol untuk menjalankan perbandingan</p>
                
                <form method="post" action="run.php">
                    <div class="form-group">
                        <label for="size">Pilih Ukuran Dataset:</label>
                        <select name="size" id="size">
                            <?php foreach ($sizes as $s): ?>
                                <option value="<?= $s ?>"><?= number_format($s) ?> data</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Pilih Algoritma:</label>
                        <div style="display: flex; gap: 20px;">
                            <label style="display: flex; align-items: center; gap: 8px; margin: 0; min-width: auto; flex: none;">
                                <input type="radio" name="algorithm" value="selection" checked style="width: 18px; height: 18px; cursor: pointer;">
                                <span>Selection Sort (Iteratif)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; margin: 0; min-width: auto; flex: none;">
                                <input type="radio" name="algorithm" value="insertion" style="width: 18px; height: 18px; cursor: pointer;">
                                <span>Insertion Sort (Rekursif)</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; margin: 0; min-width: auto; flex: none;">
                                <input type="radio" name="algorithm" value="both" style="width: 18px; height: 18px; cursor: pointer;">
                                <span>Jalankan Keduanya</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit">▶️ Jalankan Pengujian</button>
                        <a href="delete_history.php" style="padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: all 0.3s; display: inline-block;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(220,53,69,0.4)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">🗑️ Hapus Riwayat</a>
                    </div>
                </form>

                <div class="info-box">
                    <strong>💡 Tips:</strong> Mulai dari dataset kecil (1-100) untuk melihat perbedaan, kemudian naik secara bertahap ke dataset besar. Gunakan "Jalankan Keduanya" untuk perbandingan langsung.
                </div>
            </div>

            <!-- Section 2: Tabel Hasil -->
            <div class="section">
                <h2>📊 Hasil Running Semua Pengujian</h2>
                
                <?php if (empty($results)): ?>
                    <div class="empty-state">
                        <p>Belum ada data pengujian.</p>
                        <p style="font-size: 0.9em;">Jalankan pengujian di atas untuk melihat hasil dan grafik.</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Ukuran Data</th>
                                <th>Selection Sort (s)</th>
                                <th>Insertion Sort (s)</th>
                                <th>Selisih Waktu</th>
                                <th>Lebih Cepat</th>
                                <th>Waktu Pengujian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): ?>
                                <?php
                                    $sel = $row['waktu_selection'];
                                    $ins = $row['waktu_insertion'];
                                    $diff = abs($sel - $ins);
                                    $faster = ($sel < $ins) ? 'Selection' : 'Insertion';
                                    $faster_class = ($sel < $ins) ? 'faster' : 'slower';
                                ?>
                                <tr>
                                    <td><strong><?= $row['id'] ?></strong></td>
                                    <td><?= number_format($row['ukuran_data']) ?></td>
                                    <td><?= number_format($sel, 6) ?></td>
                                    <td><?= number_format($ins, 6) ?></td>
                                    <td><?= number_format($diff, 6) ?></td>
                                    <td><span class="<?= $faster_class ?>"><?= $faster ?></span></td>
                                    <td><?= $row['created_at'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Stats -->
                    <h3 style="margin-top: 30px; color: #333;">📈 Statistik</h3>
                    <div class="stats">
                        <div class="stat-card">
                            <div class="label">Total Pengujian</div>
                            <div class="value"><?= count($results) ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="label">Rata-rata Selection</div>
                            <div class="value"><?= number_format(array_sum(array_column($results, 'waktu_selection')) / count($results), 6) ?>s</div>
                        </div>
                        <div class="stat-card">
                            <div class="label">Rata-rata Insertion</div>
                            <div class="value"><?= number_format(array_sum(array_column($results, 'waktu_insertion')) / count($results), 6) ?>s</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Section 3: Grafik -->
            <div class="section">
                <h2>📈 Grafik Perbandingan Waktu Running</h2>
                
                <?php if ($row_count === 0): ?>
                    <div class="empty-state">
                        <p>Belum ada data untuk ditampilkan dalam grafik.</p>
                        <p style="font-size: 0.9em;">Jalankan beberapa pengujian di atas untuk melihat visualisasi.</p>
                    </div>
                <?php else: ?>
                    <div class="chart-container">
                        <canvas id="chart"></canvas>
                    </div>

                    <script>
                        const labels = <?= json_encode($labels) ?>;
                        const dataSel = <?= json_encode($data_sel) ?>;
                        const dataIns = <?= json_encode($data_ins) ?>;

                        const ctx = document.getElementById('chart').getContext('2d');
                        const chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Selection Sort (Iteratif)',
                                        data: dataSel,
                                        borderColor: 'rgba(255,99,132,1)',
                                        backgroundColor: 'rgba(255,99,132,0.1)',
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 6,
                                        pointBackgroundColor: 'rgba(255,99,132,1)',
                                        pointBorderColor: 'white',
                                        pointBorderWidth: 2,
                                    },
                                    {
                                        label: 'Insertion Sort (Rekursif)',
                                        data: dataIns,
                                        borderColor: 'rgba(54,162,235,1)',
                                        backgroundColor: 'rgba(54,162,235,0.1)',
                                        fill: true,
                                        tension: 0.4,
                                        pointRadius: 6,
                                        pointBackgroundColor: 'rgba(54,162,235,1)',
                                        pointBorderColor: 'white',
                                        pointBorderWidth: 2,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {mode: 'index', intersect: false},
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Perbandingan Waktu Eksekusi (detik)',
                                        font: { size: 16, weight: 'bold' }
                                    },
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: {
                                            font: { size: 14 },
                                            padding: 20,
                                            usePointStyle: true,
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        type: 'linear',
                                        display: true,
                                        title: {
                                            display: true,
                                            text: 'Waktu (detik)',
                                            font: { size: 14, weight: 'bold' }
                                        }
                                    },
                                    x: {
                                        display: true,
                                        title: {
                                            display: true,
                                            text: 'Ukuran Data (Waktu Pengujian)',
                                            font: { size: 14, weight: 'bold' }
                                        }
                                    }
                                }
                            }
                        });
                    </script>
                <?php endif; ?>
            </div>
        </div>

        <footer>
            <p>Analisis Efisiensi Algoritma Pengurutan | PHP Native + MySQL + Chart.js | Desember 2025</p>
        </footer>
    </div>
</body>
</html>
