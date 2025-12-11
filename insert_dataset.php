<?php
// insert_dataset.php - generate 10,000 realistic student scores and insert into DB
require_once __DIR__ . '/koneksi.php';

// Distribution (approx):
// 40-55: 15%
// 56-70: 35%
// 71-85: 35%
// 86-100: 15%

$total = 10000;
$values = [];

function rand_range($min, $max) {
    return rand($min, $max);
}

$counts = [
    'low' => (int)($total * 0.15),
    'mid' => (int)($total * 0.35),
    'good' => (int)($total * 0.35),
    'high' => $total - ((int)($total * 0.15) + (int)($total * 0.35) + (int)($total * 0.35)),
];

for ($i = 0; $i < $counts['low']; $i++) $values[] = rand_range(40, 55);
for ($i = 0; $i < $counts['mid']; $i++) $values[] = rand_range(56, 70);
for ($i = 0; $i < $counts['good']; $i++) $values[] = rand_range(71, 85);
for ($i = 0; $i < $counts['high']; $i++) $values[] = rand_range(86, 100);

shuffle($values);

// Truncate table and insert
$conn->query('TRUNCATE TABLE dataset_nilai');

$stmt = $conn->prepare('INSERT INTO dataset_nilai (nilai) VALUES (?)');
$stmt->bind_param('i', $val);

$conn->begin_transaction();
foreach ($values as $val) {
    $stmt->execute();
}
$conn->commit();

echo "Inserted " . count($values) . " records into dataset_nilai.\n";

?>
