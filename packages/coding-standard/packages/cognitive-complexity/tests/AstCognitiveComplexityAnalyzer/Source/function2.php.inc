<?php

function sumOfPrimes(int $max): int {
    $total = 0;
    for ($i = 1; $i <= $max; ++$i) { // operation: +1, nesting: +0)
        for ($j = 2; $j < $i; ++$j) { // operation: +1, nesting: +1
            if ($i % $j == 0) { // operation: +1, nesting: +2
                continue;
            }
        }
        $total += $i;
    }

    return $total;
}

// Cognitive Complexity 6
