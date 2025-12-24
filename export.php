<?php
/**
 * export.php - Export hasil pengujian ke CSV
 * 
 * Script ini mengekspor data hasil pengujian dari database ke file CSV
 * untuk keperluan analisis dan laporan.
 */

require_once __DIR__ . '/koneksi.php';

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="hasil_pengujian_' . date('Y-m-d_His') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8 (for Excel compatibility)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write CSV header
fputcsv($output, [
    'ID',
    'Ukuran Data',
    'Waktu Selection Sort (detik)',
    'Waktu Insertion Sort (detik)',
    'Selisih Waktu (detik)',
    'Lebih Cepat',
    'Persentase Perbedaan (%)',
    'Timestamp'
], ';');

// Fetch data from database
try {
    $res = $conn->query('SELECT * FROM hasil_running ORDER BY id DESC');
    
    if (!$res) {
        throw new Exception('Error querying hasil_running: ' . $conn->error);
    }
    
    $rowCount = 0;
    while ($row = $res->fetch_assoc()) {
        $sel = (float)$row['waktu_selection'];
        $ins = (float)$row['waktu_insertion'];
        $diff = abs($sel - $ins);
        $faster = ($sel < $ins) ? 'Selection Sort' : (($ins < $sel) ? 'Insertion Sort' : 'Sama');
        
        // Calculate percentage difference
        $minTime = min($sel, $ins);
        $percent = $minTime > 0 ? ($diff / $minTime) * 100 : 0;
        
        // Write row to CSV
        fputcsv($output, [
            $row['id'],
            $row['ukuran_data'],
            number_format($sel, 6, '.', ''),
            number_format($ins, 6, '.', ''),
            number_format($diff, 6, '.', ''),
            $faster,
            number_format($percent, 2, '.', ''),
            $row['created_at']
        ], ';');
        
        $rowCount++;
    }
    
    // Add summary row
    fputcsv($output, [], ';'); // Empty row
    fputcsv($output, ['SUMMARY'], ';');
    fputcsv($output, ['Total Records', $rowCount], ';');
    
} catch (Exception $e) {
    // If error, output error message
    fputcsv($output, ['ERROR', $e->getMessage()], ';');
}

fclose($output);
exit;
?>

