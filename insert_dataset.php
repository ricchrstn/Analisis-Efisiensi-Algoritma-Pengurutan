<?php
/**
 * insert_dataset.php - Generate 10,000 realistic student scores and insert into DB
 * 
 * Script ini menghasilkan data nilai mahasiswa dengan distribusi realistis
 * dan menyimpannya ke database untuk keperluan pengujian algoritma.
 * 
 * Distribusi:
 * - 40-55 (rendah): 15%
 * - 56-70 (sedang): 35%
 * - 71-85 (baik): 35%
 * - 86-100 (sangat baik): 15%
 * 
 * Usage: php insert_dataset.php
 */

require_once __DIR__ . '/koneksi.php';

/**
 * Generate random number in range
 * 
 * @param int $min Minimum value
 * @param int $max Maximum value
 * @return int Random number
 */
function rand_range($min, $max) {
    return rand($min, $max);
}

$total = 10000;
$values = [];

// Calculate distribution counts
$counts = [
    'low' => (int)($total * 0.15),
    'mid' => (int)($total * 0.35),
    'good' => (int)($total * 0.35),
    'high' => $total - ((int)($total * 0.15) + (int)($total * 0.35) + (int)($total * 0.35)),
];

// Generate values according to distribution
for ($i = 0; $i < $counts['low']; $i++) $values[] = rand_range(40, 55);
for ($i = 0; $i < $counts['mid']; $i++) $values[] = rand_range(56, 70);
for ($i = 0; $i < $counts['good']; $i++) $values[] = rand_range(71, 85);
for ($i = 0; $i < $counts['high']; $i++) $values[] = rand_range(86, 100);

// Shuffle to randomize order
shuffle($values);

try {
    // Truncate table and insert
    if (!$conn->query('TRUNCATE TABLE dataset_nilai')) {
        throw new Exception('Error truncating table: ' . $conn->error);
    }

    $stmt = $conn->prepare('INSERT INTO dataset_nilai (nilai) VALUES (?)');
    if (!$stmt) {
        throw new Exception('Error preparing statement: ' . $conn->error);
    }
    
    $stmt->bind_param('i', $val);

    $conn->begin_transaction();
    foreach ($values as $val) {
        if (!$stmt->execute()) {
            throw new Exception('Error executing insert: ' . $stmt->error);
        }
    }
    $conn->commit();

    echo "✓ Successfully inserted " . count($values) . " records into dataset_nilai.\n";
    echo "  Distribution:\n";
    echo "  - Low (40-55): " . $counts['low'] . " records\n";
    echo "  - Mid (56-70): " . $counts['mid'] . " records\n";
    echo "  - Good (71-85): " . $counts['good'] . " records\n";
    echo "  - High (86-100): " . $counts['high'] . " records\n";
    
} catch (Exception $e) {
    if ($conn->in_transaction) {
        $conn->rollback();
    }
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

?>
