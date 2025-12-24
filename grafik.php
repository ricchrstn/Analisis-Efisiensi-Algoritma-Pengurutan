<?php
/**
 * grafik.php - Visualisasi grafik perbandingan waktu running
 */

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/error_handler.php';

$res = $conn->query('SELECT ukuran_data, waktu_selection, waktu_insertion, created_at FROM hasil_running ORDER BY created_at ASC');

if (!$res) {
    show_error('Database Error', 'Error querying hasil_running: ' . $conn->error);
    exit;
}

$labels = [];
$data_sel = [];
$data_ins = [];
$row_count = 0;

while ($r = $res->fetch_assoc()) {
    $labels[] = $r['ukuran_data'] . ' (' . substr($r['created_at'], 11, 5) . ')';
    $data_sel[] = (float)$r['waktu_selection'];
    $data_ins[] = (float)$r['waktu_insertion'];
    $row_count++;
}

// Calculate statistics
$stats = [
    'total' => $row_count,
    'selection_avg' => $row_count > 0 ? array_sum($data_sel) / count($data_sel) : 0,
    'insertion_avg' => $row_count > 0 ? array_sum($data_ins) / count($data_ins) : 0,
    'selection_min' => $row_count > 0 ? min($data_sel) : 0,
    'insertion_min' => $row_count > 0 ? min($data_ins) : 0,
    'selection_max' => $row_count > 0 ? max($data_sel) : 0,
    'insertion_max' => $row_count > 0 ? max($data_ins) : 0,
];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Grafik Perbandingan - Analisis Efisiensi Algoritma</title>
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
        }

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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
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
            font-size: 1.5em;
            font-weight: bold;
            color: #667eea;
        }

        .info-box {
            background: #e7f3fe;
            border-left: 5px solid #2196F3;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .info-box strong { color: #1976D2; }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .button-group a {
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
            display: inline-block;
        }

        .button-group a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .button-group a.btn-export {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

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
            <h1>📈 Grafik Perbandingan Waktu Running</h1>
            <p>Visualisasi Perbandingan Selection Sort vs Insertion Sort</p>
        </header>

        <div class="content">
            <?php if ($row_count === 0): ?>
                <div class="section">
                    <div class="empty-state">
                        <p style="font-size: 1.2em; margin-bottom: 10px;">Belum ada data pengujian.</p>
                        <p>Jalankan pengujian terlebih dahulu di halaman <a href="index.php" style="color: #667eea;">Home</a> untuk melihat visualisasi grafik.</p>
                    </div>
                </div>
            <?php else: ?>
                <!-- Chart Section -->
                <div class="section">
                    <h2>📊 Grafik Line Chart</h2>
                    <div class="chart-container">
                        <canvas id="chart"></canvas>
                    </div>
                </div>

                <!-- Statistics Section -->
                <div class="section">
                    <h2>📈 Statistik</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="label">Total Pengujian</div>
                            <div class="value"><?= number_format($stats['total']) ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="label">Rata-rata Selection</div>
                            <div class="value"><?= number_format($stats['selection_avg'], 6) ?>s</div>
                        </div>
                        <div class="stat-card">
                            <div class="label">Rata-rata Insertion</div>
                            <div class="value"><?= number_format($stats['insertion_avg'], 6) ?>s</div>
                        </div>
                        <div class="stat-card">
                            <div class="label">Min Selection</div>
                            <div class="value"><?= number_format($stats['selection_min'], 6) ?>s</div>
                        </div>
                        <div class="stat-card">
                            <div class="label">Min Insertion</div>
                            <div class="value"><?= number_format($stats['insertion_min'], 6) ?>s</div>
                        </div>
                        <div class="stat-card">
                            <div class="label">Max Selection</div>
                            <div class="value"><?= number_format($stats['selection_max'], 6) ?>s</div>
                        </div>
                        <div class="stat-card">
                            <div class="label">Max Insertion</div>
                            <div class="value"><?= number_format($stats['insertion_max'], 6) ?>s</div>
                        </div>
                    </div>

                    <div class="info-box">
                        <strong>💡 Analisis:</strong> Grafik menunjukkan perbandingan waktu eksekusi antara Selection Sort (Iteratif) dan Insertion Sort (Rekursif). 
                        Garis merah menunjukkan Selection Sort, sedangkan garis biru menunjukkan Insertion Sort.
                    </div>
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

            <!-- Button Group -->
            <div class="button-group">
                <a href="index.php">🏠 Kembali ke Home</a>
                <a href="hasil.php">📊 Lihat Hasil Tabel</a>
                <a href="export.php" class="btn-export">📥 Export ke CSV</a>
            </div>
        </div>

        <footer>
            <p>Analisis Efisiensi Algoritma Pengurutan | PHP Native + MySQL + Chart.js | Desember 2025</p>
        </footer>
    </div>
</body>
</html>
