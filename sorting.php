<?php
/**
 * sorting.php - Implementasi Selection Sort (iteratif) dan Insertion Sort (rekursif)
 * 
 * File ini berisi implementasi dua algoritma pengurutan yang digunakan untuk
 * perbandingan performa dalam proyek analisis efisiensi algoritma.
 */

/**
 * Selection Sort (Iteratif)
 * 
 * Algoritma pengurutan yang mencari elemen terkecil dari sisa array
 * dan menukarnya dengan elemen pada posisi saat ini.
 * 
 * @param array $arr Array yang akan diurutkan (ascending)
 * @return array Array yang sudah terurut
 * @complexity O(n²) untuk semua kasus (best, average, worst)
 * @space O(1) - in-place sorting
 * @stability Tidak stabil (unstable)
 */
function selection_sort(array $arr): array {
    $n = count($arr);
    
    // Iterasi melalui array
    for ($i = 0; $i < $n - 1; $i++) {
        // Cari index elemen terkecil dari sisa array
        $minIdx = $i;
        for ($j = $i + 1; $j < $n; $j++) {
            if ($arr[$j] < $arr[$minIdx]) {
                $minIdx = $j;
            }
        }
        
        // Tukar elemen terkecil dengan elemen di posisi i
        if ($minIdx !== $i) {
            $tmp = $arr[$i];
            $arr[$i] = $arr[$minIdx];
            $arr[$minIdx] = $tmp;
        }
    }
    
    return $arr;
}

/**
 * Insertion Sort (Rekursif)
 * 
 * Algoritma pengurutan yang menggunakan pendekatan rekursif.
 * Mengurutkan n-1 elemen pertama secara rekursif, kemudian menyisipkan
 * elemen ke-n ke posisi yang benar.
 * 
 * @param array $arr Array yang akan diurutkan (ascending)
 * @param int|null $n Ukuran array (default: null, akan dihitung otomatis)
 * @return array Array yang sudah terurut
 * @complexity O(n) best case, O(n²) average & worst case
 * @space O(n) - karena menggunakan call stack rekursi
 * @stability Stabil (stable sort)
 * @throws Exception Jika terjadi stack overflow pada dataset sangat besar
 */
function insertion_sort_recursive(array $arr, ?int $n = null): array {
    // Inisialisasi n jika belum di-set
    if ($n === null) {
        $n = count($arr);
    }
    
    // Base case: array dengan 1 elemen atau kurang sudah terurut
    if ($n <= 1) {
        return $arr;
    }

    // Rekursi: urutkan n-1 elemen pertama
    $arr = insertion_sort_recursive($arr, $n - 1);

    // Insert elemen ke-n ke posisi yang benar
    $last = $arr[$n - 1];
    $j = $n - 2;
    
    // Geser elemen yang lebih besar ke kanan
    while ($j >= 0 && $arr[$j] > $last) {
        $arr[$j + 1] = $arr[$j];
        $j--;
    }
    
    // Sisipkan elemen ke posisi yang benar
    $arr[$j + 1] = $last;
    
    return $arr;
}

?>
