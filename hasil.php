<?php
require_once __DIR__ . '/koneksi.php';

$res = $conn->query('SELECT * FROM hasil_running ORDER BY id DESC LIMIT 50');

if (!$res) {
    die('Error querying hasil_running: ' . $conn->error);
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Running</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        table, th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        a { margin-right: 15px; text-decoration: none; color: #0066cc; }
        a:hover { text-decoration: underline; }
        .faster { color: green; font-weight: bold; }
        .slower { color: red; }
    </style>
</head>
<body>
    <h2>Hasil Running Semua Pengujian</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Ukuran Data</th>
            <th>Waktu Selection (s)</th>
            <th>Waktu Insertion (s)</th>
            <th>Selisih Waktu</th>
            <th>Lebih Cepat</th>
            <th>Timestamp</th>
        </tr>
        <?php while ($row = $res->fetch_assoc()): ?>
            <?php
                $sel = $row['waktu_selection'];
                $ins = $row['waktu_insertion'];
                $diff = abs($sel - $ins);
                $faster = ($sel < $ins) ? 'Selection' : 'Insertion';
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= number_format($row['ukuran_data']) ?></td>
                <td><?= number_format($sel, 6) ?></td>
                <td><?= number_format($ins, 6) ?></td>
                <td><?= number_format($diff, 6) ?></td>
                <td><span class="<?= ($sel < $ins) ? 'faster' : 'slower' ?>"><?= $faster ?></span></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h3>Menu</h3>
    <p>
        <a href="index.php">← Kembali ke Home</a> | <a href="grafik.php">📈 Lihat Grafik</a>
    </p>
</body>
</html>
