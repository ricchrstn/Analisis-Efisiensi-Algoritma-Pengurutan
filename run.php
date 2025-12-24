<?php
/**
 * run.php - Fetch dataset, run sorting algorithms, record times
 * 
 * This script handles the execution of sorting algorithms and records their performance.
 * Includes input validation, SQL injection protection, and stack overflow protection.
 */

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/sorting.php';
require_once __DIR__ . '/error_handler.php';

set_time_limit(0);

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Input validation - Size
$size = isset($_POST['size']) ? (int)$_POST['size'] : 100;
$size = max(1, min(10000, $size)); // Batasi antara 1-10000

// Input validation - Algorithm
$allowed_algorithms = ['selection', 'insertion', 'both'];
$algorithm = isset($_POST['algorithm']) && in_array($_POST['algorithm'], $allowed_algorithms) 
    ? $_POST['algorithm'] 
    : 'both';

$sizeSafe = $size;
$error_message = null;

// Fetch random sample using prepared statement (SQL injection protection)
try {
    // Check available data first
    $checkStmt = $conn->prepare('SELECT COUNT(*) as total FROM dataset_nilai');
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $totalAvailable = $checkResult->fetch_assoc()['total'];
    
    if ($totalAvailable < $sizeSafe) {
        $error_message = "Data tidak cukup di database. Tersedia: $totalAvailable, Diperlukan: $sizeSafe. Silakan jalankan insert_dataset.php terlebih dahulu.";
    } else {
        // Use prepared statement for LIMIT to prevent SQL injection
        $stmt = $conn->prepare('SELECT id, nilai FROM dataset_nilai ORDER BY RAND() LIMIT ?');
        $stmt->bind_param('i', $sizeSafe);
        
        if (!$stmt->execute()) {
            throw new Exception('Error querying dataset_nilai: ' . $stmt->error);
        }
        
        $res = $stmt->get_result();
        
        $data = [];
        $mahasiswa = [];
        while ($row = $res->fetch_assoc()) {
            $data[] = (int)$row['nilai'];
            $mahasiswa[] = ['id' => $row['id'], 'nilai' => $row['nilai']];
        }
        
        if (count($data) < $sizeSafe) {
            $error_message = "Data tidak cukup. Ditemukan: " . count($data) . ", Diperlukan: $sizeSafe.";
        }
    }
} catch (Exception $e) {
    show_error('Database Error', $e->getMessage());
    exit;
}

// If error, show error page
if ($error_message) {
    show_error('Data Tidak Cukup', $error_message);
    exit;
}

$results = [];
$time_selection = 0;
$time_insertion = 0;

// Stack overflow protection for recursive insertion sort
$MAX_RECURSIVE_SIZE = 3000;
if (($algorithm === 'insertion' || $algorithm === 'both') && $sizeSafe > $MAX_RECURSIVE_SIZE) {
    show_error(
        'Ukuran Dataset Terlalu Besar', 
        "Insertion Sort (Rekursif) tidak disarankan untuk dataset > $MAX_RECURSIVE_SIZE karena risiko stack overflow. Ukuran saat ini: $sizeSafe. Silakan gunakan dataset lebih kecil atau pilih Selection Sort saja."
    );
    exit;
}

// Run Selection Sort (iterative) jika dipilih
if ($algorithm === 'selection' || $algorithm === 'both') {
    try {
        $arr1 = $data;
        $t0 = microtime(true);
        $sorted1 = selection_sort($arr1);
        $t1 = microtime(true);
        $time_selection = $t1 - $t0;
        
        $results['selection'] = [
            'time' => $time_selection,
            'sorted' => $sorted1
        ];
    } catch (Exception $e) {
        show_error('Selection Sort Error', 'Terjadi kesalahan saat menjalankan Selection Sort: ' . $e->getMessage());
        exit;
    }
}

// Run Insertion Sort (recursive) jika dipilih
if ($algorithm === 'insertion' || $algorithm === 'both') {
    try {
        $arr2 = $data;
        $t2 = microtime(true);
        $sorted2 = insertion_sort_recursive($arr2);
        $t3 = microtime(true);
        $time_insertion = $t3 - $t2;
        
        $results['insertion'] = [
            'time' => $time_insertion,
            'sorted' => $sorted2
        ];
    } catch (Exception $e) {
        show_error('Insertion Sort Error', 'Terjadi kesalahan saat menjalankan Insertion Sort: ' . $e->getMessage());
        exit;
    }
}

// Save to DB - Fix: Save once when "both", not twice
try {
    $now = date('Y-m-d H:i:s');
    $stmt = $conn->prepare('INSERT INTO hasil_running (ukuran_data, waktu_selection, waktu_insertion, created_at) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('idds', $sizeSafe, $time_selection, $time_insertion, $now);
    
    if (!$stmt->execute()) {
        throw new Exception('Error saving result: ' . $stmt->error);
    }
} catch (Exception $e) {
    // Log error but don't stop execution
    error_log('Failed to save result to database: ' . $e->getMessage());
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Pengujian</title>
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
        header h1 { font-size: 2em; margin-bottom: 10px; }
        
        .content { padding: 30px; }
        
        .section { 
            margin-bottom: 30px; 
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 5px solid #667eea;
        }
        
        .section h2 { 
            color: #333; 
            margin-bottom: 15px; 
            font-size: 1.5em;
        }

        .result-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 2px solid #667eea;
        }

        .result-box h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .time-display {
            font-size: 1.8em;
            font-weight: bold;
            color: #667eea;
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border-radius: 5px;
            margin: 10px 0;
        }

        .comparison {
            background: #e7f3fe;
            padding: 15px;
            border-radius: 5px;
            border-left: 5px solid #2196F3;
            margin: 20px 0;
        }

        .faster { color: #28a745; font-weight: bold; }
        .slower { color: #dc3545; font-weight: bold; }

        .data-section {
            margin-top: 30px;
        }

        .data-section h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            max-height: 400px;
            overflow-y: auto;
        }

        .data-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: sticky;
            top: 0;
        }

        .data-table th, .data-table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .data-table tbody tr:hover {
            background: #f0f0f0;
        }

        .sorted-data {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 8px;
            margin-top: 15px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .sorted-value {
            padding: 10px;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 4px;
            font-weight: bold;
        }

        .button-group {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .button-group a {
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .button-group a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
            <h1>✅ Hasil Pengujian</h1>
            <p>Ukuran Dataset: <?= number_format($sizeSafe) ?> data | Algoritma: 
                <?php 
                    if ($algorithm === 'both') echo 'Selection Sort & Insertion Sort';
                    elseif ($algorithm === 'selection') echo 'Selection Sort (Iteratif)';
                    else echo 'Insertion Sort (Rekursif)';
                ?>
            </p>
        </header>

        <div class="content">
            <!-- Hasil Waktu -->
            <div class="section">
                <h2>⏱️ Waktu Eksekusi</h2>
                
                <?php if (isset($results['selection'])): ?>
                    <div class="result-box">
                        <h3>🔴 Selection Sort (Iteratif)</h3>
                        <div class="time-display">
                            <?= number_format($results['selection']['time'], 6) ?> detik
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($results['insertion'])): ?>
                    <div class="result-box">
                        <h3>🔵 Insertion Sort (Rekursif)</h3>
                        <div class="time-display">
                            <?= number_format($results['insertion']['time'], 6) ?> detik
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($results['selection']) && isset($results['insertion'])): ?>
                    <div class="comparison">
                        <h3 style="margin-top: 0;">📊 Perbandingan</h3>
                        <?php 
                            $sel = $results['selection']['time'];
                            $ins = $results['insertion']['time'];
                            $diff = abs($sel - $ins);
                            $percent = ($diff / min($sel, $ins)) * 100;
                        ?>
                        <?php if ($sel < $ins): ?>
                            <p><span class="faster">Selection Sort lebih cepat</span> sebesar <?= number_format($percent, 2) ?>% (selisih: <?= number_format($diff, 6) ?>s)</p>
                        <?php elseif ($ins < $sel): ?>
                            <p><span class="faster">Insertion Sort lebih cepat</span> sebesar <?= number_format($percent, 2) ?>% (selisih: <?= number_format($diff, 6) ?>s)</p>
                        <?php else: ?>
                            <p>Kedua algoritma memiliki waktu yang sama</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Data Mahasiswa yang Digunakan -->
            <div class="data-section">
                <h2>👥 Data Mahasiswa yang Digunakan</h2>
                <p style="color: #666; margin-bottom: 15px;">Berikut adalah <?= count($mahasiswa) ?> data nilai mahasiswa yang digunakan dalam pengujian:</p>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#No</th>
                            <th>ID Data</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mahasiswa as $idx => $mhs): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><?= $mhs['id'] ?></td>
                                <td><strong><?= $mhs['nilai'] ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Data Terurut -->
            <?php if (isset($results['selection'])): ?>
                <div class="data-section">
                    <h2>🔴 Data Terurut - Selection Sort</h2>
                    <p style="color: #666; margin-bottom: 15px;">Urutan nilai dari kecil ke besar:</p>
                    <div class="sorted-data">
                        <?php foreach ($results['selection']['sorted'] as $val): ?>
                            <div class="sorted-value"><?= $val ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($results['insertion'])): ?>
                <div class="data-section">
                    <h2>🔵 Data Terurut - Insertion Sort</h2>
                    <p style="color: #666; margin-bottom: 15px;">Urutan nilai dari kecil ke besar:</p>
                    <div class="sorted-data">
                        <?php foreach ($results['insertion']['sorted'] as $val): ?>
                            <div class="sorted-value"><?= $val ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Button Group -->
            <div class="button-group">
                <a href="index.php">🏠 Kembali ke Home</a>
            </div>
        </div>

        <footer>
            <p>Analisis Efisiensi Algoritma Pengurutan | PHP Native + MySQL | Desember 2025</p>
        </footer>
    </div>
</body>
</html>
