<?php

class Solution {

    /**
     * @param Integer $numRows
     * @return Integer[][]
     */
    function generate($numRows) {
        if ($numRows <= 0) return [];

        $triangle = [[1]];

        for ($i = 1; $i < $numRows; $i++) {
            $prevRow = $triangle[$i - 1];
            $newRow = [1];

            for ($j = 1; $j < $i; $j++) {
                $newRow[] = $prevRow[$j - 1] + $prevRow[$j];
            }

            $newRow[] = 1;
            $triangle[] = $newRow;
        }

        return $triangle;
    }
}