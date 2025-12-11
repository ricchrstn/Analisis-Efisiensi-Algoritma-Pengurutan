<?php
require_once __DIR__ . '/koneksi.php';

$res = $conn->query('SELECT ukuran_data, waktu_selection, waktu_insertion, created_at FROM hasil_running ORDER BY created_at ASC');

if (!$res) {
    die('Error querying hasil_running: ' . $conn->error);
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
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Grafik Perbandingan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h2 { color: #333; }
        .chart-container { position: relative; width: 90%; height: 500px; margin: 20px 0; }
        a { margin-right: 15px; text-decoration: none; color: #0066cc; }
        a:hover { text-decoration: underline; }
        .info { background-color: #e7f3fe; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h2>Grafik Perbandingan Waktu Running</h2>
    
    <?php if ($row_count === 0): ?>
        <div class="info">
            <strong>Belum ada data pengujian.</strong><br>
            Jalankan pengujian terlebih dahulu di halaman <a href="index.php">Home</a>.
        </div>
    <?php else: ?>
        <div class="chart-container">
            <canvas id="chart"></canvas>
        </div>

        <div class="info">
            <strong>Statistik:</strong><br>
            - Total pengujian: <?= $row_count ?><br>
            - Rata-rata Selection: <?= number_format(array_sum($data_sel) / count($data_sel), 6) ?>s<br>
            - Rata-rata Insertion: <?= number_format(array_sum($data_ins) / count($data_ins), 6) ?>s
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
                            label: 'Selection Sort (Iteratif) - Waktu (detik)',
                            data: dataSel,
                            borderColor: 'rgba(255,99,132,1)',
                            backgroundColor: 'rgba(255,99,132,0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(255,99,132,1)',
                        },
                        {
                            label: 'Insertion Sort (Rekursif) - Waktu (detik)',
                            data: dataIns,
                            borderColor: 'rgba(54,162,235,1)',
                            backgroundColor: 'rgba(54,162,235,0.1)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(54,162,235,1)',
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
                            text: 'Perbandingan Waktu Eksekusi: Selection Sort vs Insertion Sort'
                        },
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            title: {
                                display: true,
                                text: 'Waktu (detik)'
                            }
                        },
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Ukuran Data (Waktu Pengujian)'
                            }
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>

    <h3>Menu</h3>
    <p>
        <a href="index.php">← Kembali ke Home</a> | <a href="hasil.php">📊 Lihat Hasil Tabel</a>
    </p>
</body>
</html>
