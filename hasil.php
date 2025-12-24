<?php
/**
 * hasil.php - Tampilkan tabel hasil pengujian dengan filter dan statistik
 */

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/error_handler.php';

// Get filter parameters
$filter_size = isset($_GET['size']) ? (int)$_GET['size'] : null;
$limit = isset($_GET['limit']) ? max(10, min(200, (int)$_GET['limit'])) : 50;

// Build query with filter
$query = 'SELECT * FROM hasil_running WHERE 1=1';
$params = [];
$types = '';

if ($filter_size !== null && $filter_size > 0) {
    $query .= ' AND ukuran_data = ?';
    $params[] = $filter_size;
    $types .= 'i';
}

$query .= ' ORDER BY id DESC LIMIT ?';
$params[] = $limit;
$types .= 'i';

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    show_error('Database Error', 'Error querying hasil_running: ' . $stmt->error);
    exit;
}

$res = $stmt->get_result();
$results = [];
$stats = [
    'total' => 0,
    'selection_avg' => 0,
    'insertion_avg' => 0,
    'selection_min' => PHP_FLOAT_MAX,
    'insertion_min' => PHP_FLOAT_MAX,
    'selection_max' => 0,
    'insertion_max' => 0,
    'selection_wins' => 0,
    'insertion_wins' => 0
];

while ($row = $res->fetch_assoc()) {
    $results[] = $row;
    $sel = (float)$row['waktu_selection'];
    $ins = (float)$row['waktu_insertion'];
    
    $stats['total']++;
    $stats['selection_avg'] += $sel;
    $stats['insertion_avg'] += $ins;
    $stats['selection_min'] = min($stats['selection_min'], $sel);
    $stats['insertion_min'] = min($stats['insertion_min'], $ins);
    $stats['selection_max'] = max($stats['selection_max'], $sel);
    $stats['insertion_max'] = max($stats['insertion_max'], $ins);
    
    if ($sel < $ins) $stats['selection_wins']++;
    elseif ($ins < $sel) $stats['insertion_wins']++;
}

if ($stats['total'] > 0) {
    $stats['selection_avg'] /= $stats['total'];
    $stats['insertion_avg'] /= $stats['total'];
}

// Get available sizes for filter
$sizeRes = $conn->query('SELECT DISTINCT ukuran_data FROM hasil_running ORDER BY ukuran_data ASC');
$availableSizes = [];
while ($s = $sizeRes->fetch_assoc()) {
    $availableSizes[] = $s['ukuran_data'];
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Running - Analisis Efisiensi Algoritma</title>
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

        .filter-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 2px solid #667eea;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-form label {
            font-weight: bold;
            color: #333;
        }

        .filter-form select, .filter-form input {
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .filter-form button {
            padding: 8px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .filter-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
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
            <h1>📊 Hasil Running Semua Pengujian</h1>
            <p>Analisis Efisiensi Algoritma Pengurutan</p>
        </header>

        <div class="content">
            <!-- Filter Section -->
            <div class="section">
                <h2>🔍 Filter & Pencarian</h2>
                <div class="filter-box">
                    <form method="get" action="hasil.php" class="filter-form">
                        <label for="size">Filter Ukuran Data:</label>
                        <select name="size" id="size">
                            <option value="">Semua Ukuran</option>
                            <?php foreach ($availableSizes as $size): ?>
                                <option value="<?= $size ?>" <?= $filter_size == $size ? 'selected' : '' ?>>
                                    <?= number_format($size) ?> data
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="limit">Jumlah Data:</label>
                        <select name="limit" id="limit">
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                            <option value="200" <?= $limit == 200 ? 'selected' : '' ?>>200</option>
                        </select>

                        <button type="submit">🔍 Terapkan Filter</button>
                        <a href="hasil.php" style="padding: 8px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">🔄 Reset</a>
                    </form>
                </div>
            </div>

            <!-- Statistics Section -->
            <?php if ($stats['total'] > 0): ?>
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
                    <div class="stat-card">
                        <div class="label">Selection Menang</div>
                        <div class="value"><?= $stats['selection_wins'] ?>x</div>
                    </div>
                    <div class="stat-card">
                        <div class="label">Insertion Menang</div>
                        <div class="value"><?= $stats['insertion_wins'] ?>x</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Results Table -->
            <div class="section">
                <h2>📋 Tabel Hasil</h2>
                
                <?php if (empty($results)): ?>
                    <div class="empty-state">
                        <p>Belum ada data pengujian.</p>
                        <p style="font-size: 0.9em; margin-top: 10px;">Jalankan pengujian di halaman <a href="index.php" style="color: #667eea;">Home</a> untuk melihat hasil.</p>
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
                                <th>Persentase</th>
                                <th>Waktu Pengujian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): ?>
                                <?php
                                    $sel = (float)$row['waktu_selection'];
                                    $ins = (float)$row['waktu_insertion'];
                                    $diff = abs($sel - $ins);
                                    $faster = ($sel < $ins) ? 'Selection' : (($ins < $sel) ? 'Insertion' : 'Sama');
                                    $faster_class = ($sel < $ins) ? 'faster' : (($ins < $sel) ? 'slower' : '');
                                    $minTime = min($sel, $ins);
                                    $percent = $minTime > 0 ? ($diff / $minTime) * 100 : 0;
                                ?>
                                <tr>
                                    <td><strong><?= $row['id'] ?></strong></td>
                                    <td><?= number_format($row['ukuran_data']) ?></td>
                                    <td><?= number_format($sel, 6) ?></td>
                                    <td><?= number_format($ins, 6) ?></td>
                                    <td><?= number_format($diff, 6) ?></td>
                                    <td><span class="<?= $faster_class ?>"><?= $faster ?></span></td>
                                    <td><?= number_format($percent, 2) ?>%</td>
                                    <td><?= $row['created_at'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Button Group -->
            <div class="button-group">
                <a href="index.php">🏠 Kembali ke Home</a>
                <a href="grafik.php">📈 Lihat Grafik</a>
                <a href="export.php" class="btn-export">📥 Export ke CSV</a>
            </div>
        </div>

        <footer>
            <p>Analisis Efisiensi Algoritma Pengurutan | PHP Native + MySQL | Desember 2025</p>
        </footer>
    </div>
</body>
</html>
