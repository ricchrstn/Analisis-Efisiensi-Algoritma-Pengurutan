<?php
/**
 * test_sorting.php - Unit tests untuk algoritma sorting
 * 
 * Script ini melakukan pengujian unit untuk memastikan algoritma
 * Selection Sort dan Insertion Sort bekerja dengan benar.
 */

require_once __DIR__ . '/sorting.php';

// Colors for terminal output
$GREEN = "\033[32m";
$RED = "\033[31m";
$YELLOW = "\033[33m";
$RESET = "\033[0m";
$BOLD = "\033[1m";

$tests_passed = 0;
$tests_failed = 0;
$total_tests = 0;

/**
 * Assert function untuk testing
 */
function assert_test($condition, $message) {
    global $tests_passed, $tests_failed, $total_tests, $GREEN, $RED, $RESET, $BOLD;
    $total_tests++;
    
    if ($condition) {
        $tests_passed++;
        echo "{$GREEN}✓{$RESET} {$message}\n";
        return true;
    } else {
        $tests_failed++;
        echo "{$RED}✗{$RESET} {$message}\n";
        return false;
    }
}

/**
 * Test array equality
 */
function arrays_equal($a, $b) {
    if (count($a) !== count($b)) return false;
    sort($a);
    sort($b);
    return $a === $b;
}

echo "{$BOLD}=== Unit Testing Algoritma Sorting ==={$RESET}\n\n";

// Test 1: Selection Sort - Basic test
echo "{$BOLD}1. Testing Selection Sort (Basic){$RESET}\n";
$test1 = [64, 34, 25, 12, 22, 11, 90];
$expected1 = [11, 12, 22, 25, 34, 64, 90];
$result1 = selection_sort($test1);
assert_test($result1 === $expected1, "Selection Sort: [64, 34, 25, 12, 22, 11, 90]");

// Test 2: Selection Sort - Already sorted
echo "\n{$BOLD}2. Testing Selection Sort (Already Sorted){$RESET}\n";
$test2 = [1, 2, 3, 4, 5];
$expected2 = [1, 2, 3, 4, 5];
$result2 = selection_sort($test2);
assert_test($result2 === $expected2, "Selection Sort: Already sorted array");

// Test 3: Selection Sort - Reverse sorted
echo "\n{$BOLD}3. Testing Selection Sort (Reverse Sorted){$RESET}\n";
$test3 = [5, 4, 3, 2, 1];
$expected3 = [1, 2, 3, 4, 5];
$result3 = selection_sort($test3);
assert_test($result3 === $expected3, "Selection Sort: Reverse sorted array");

// Test 4: Selection Sort - Single element
echo "\n{$BOLD}4. Testing Selection Sort (Single Element){$RESET}\n";
$test4 = [42];
$expected4 = [42];
$result4 = selection_sort($test4);
assert_test($result4 === $expected4, "Selection Sort: Single element");

// Test 5: Selection Sort - Duplicate values
echo "\n{$BOLD}5. Testing Selection Sort (Duplicate Values){$RESET}\n";
$test5 = [3, 1, 3, 2, 1];
$expected5 = [1, 1, 2, 3, 3];
$result5 = selection_sort($test5);
assert_test($result5 === $expected5, "Selection Sort: Array with duplicates");

// Test 6: Insertion Sort - Basic test
echo "\n{$BOLD}6. Testing Insertion Sort Recursive (Basic){$RESET}\n";
$test6 = [64, 34, 25, 12, 22, 11, 90];
$expected6 = [11, 12, 22, 25, 34, 64, 90];
$result6 = insertion_sort_recursive($test6);
assert_test($result6 === $expected6, "Insertion Sort: [64, 34, 25, 12, 22, 11, 90]");

// Test 7: Insertion Sort - Already sorted
echo "\n{$BOLD}7. Testing Insertion Sort Recursive (Already Sorted){$RESET}\n";
$test7 = [1, 2, 3, 4, 5];
$expected7 = [1, 2, 3, 4, 5];
$result7 = insertion_sort_recursive($test7);
assert_test($result7 === $expected7, "Insertion Sort: Already sorted array");

// Test 8: Insertion Sort - Reverse sorted
echo "\n{$BOLD}8. Testing Insertion Sort Recursive (Reverse Sorted){$RESET}\n";
$test8 = [5, 4, 3, 2, 1];
$expected8 = [1, 2, 3, 4, 5];
$result8 = insertion_sort_recursive($test8);
assert_test($result8 === $expected8, "Insertion Sort: Reverse sorted array");

// Test 9: Insertion Sort - Single element
echo "\n{$BOLD}9. Testing Insertion Sort Recursive (Single Element){$RESET}\n";
$test9 = [42];
$expected9 = [42];
$result9 = insertion_sort_recursive($test9);
assert_test($result9 === $expected9, "Insertion Sort: Single element");

// Test 10: Insertion Sort - Duplicate values
echo "\n{$BOLD}10. Testing Insertion Sort Recursive (Duplicate Values){$RESET}\n";
$test10 = [3, 1, 3, 2, 1];
$expected10 = [1, 1, 2, 3, 3];
$result10 = insertion_sort_recursive($test10);
assert_test($result10 === $expected10, "Insertion Sort: Array with duplicates");

// Test 11: Both algorithms produce same result
echo "\n{$BOLD}11. Testing Consistency (Both Algorithms){$RESET}\n";
$test11 = [45, 23, 78, 12, 56, 34, 90, 1, 67];
$result11_sel = selection_sort($test11);
$result11_ins = insertion_sort_recursive($test11);
assert_test($result11_sel === $result11_ins, "Both algorithms produce same result");

// Test 12: Empty array
echo "\n{$BOLD}12. Testing Edge Case (Empty Array){$RESET}\n";
$test12 = [];
$expected12 = [];
$result12_sel = selection_sort($test12);
$result12_ins = insertion_sort_recursive($test12);
assert_test($result12_sel === $expected12 && $result12_ins === $expected12, "Empty array handling");

// Test 13: Large array (100 elements)
echo "\n{$BOLD}13. Testing Performance (Large Array - 100 elements){$RESET}\n";
$test13 = [];
for ($i = 0; $i < 100; $i++) {
    $test13[] = rand(1, 1000);
}
$expected13 = $test13;
sort($expected13);
$result13_sel = selection_sort($test13);
$result13_ins = insertion_sort_recursive($test13);
assert_test($result13_sel === $expected13 && $result13_ins === $expected13, "Large array (100 elements)");

// Summary
echo "\n{$BOLD}=== Test Summary ==={$RESET}\n";
echo "Total Tests: {$total_tests}\n";
echo "{$GREEN}Passed: {$tests_passed}{$RESET}\n";
if ($tests_failed > 0) {
    echo "{$RED}Failed: {$tests_failed}{$RESET}\n";
} else {
    echo "{$GREEN}Failed: 0{$RESET}\n";
}

if ($tests_failed === 0) {
    echo "\n{$BOLD}{$GREEN}✓ All tests passed!{$RESET}\n";
    exit(0);
} else {
    echo "\n{$BOLD}{$RED}✗ Some tests failed!{$RESET}\n";
    exit(1);
}
?>

