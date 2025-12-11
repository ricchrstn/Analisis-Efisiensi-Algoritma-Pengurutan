<?php
// sorting.php - implement Selection Sort (iterative) and Insertion Sort (recursive)

function selection_sort(array $arr): array {
    $n = count($arr);
    for ($i = 0; $i < $n - 1; $i++) {
        $minIdx = $i;
        for ($j = $i + 1; $j < $n; $j++) {
            if ($arr[$j] < $arr[$minIdx]) {
                $minIdx = $j;
            }
        }
        if ($minIdx !== $i) {
            $tmp = $arr[$i];
            $arr[$i] = $arr[$minIdx];
            $arr[$minIdx] = $tmp;
        }
    }
    return $arr;
}

function insertion_sort_recursive(array $arr, ?int $n = null): array {
    if ($n === null) $n = count($arr);
    if ($n <= 1) return $arr;

    // Sort first n-1 elements
    $arr = insertion_sort_recursive($arr, $n - 1);

    // Insert nth element into correct position
    $last = $arr[$n - 1];
    $j = $n - 2;
    while ($j >= 0 && $arr[$j] > $last) {
        $arr[$j + 1] = $arr[$j];
        $j--;
    }
    $arr[$j + 1] = $last;
    return $arr;
}

?>
