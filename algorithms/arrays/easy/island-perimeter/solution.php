<?php

class Solution {

    /**
     * @param Integer[][] $grid
     * @return Integer
     */
    function islandPerimeter($grid) {
        $perimeter = 0;
        $rows = count($grid);
        $cols = count($grid[0]); 

        for ($r = 0; $r < $rows; $r++) {
            for ($c = 0; $c < $cols; $c++) {
                if ($grid[$r][$c] === 1) {
                    $perimeter += 4;
                    
                    if ($r > 0 && $grid[$r - 1][$c] === 1) {
                        $perimeter -= 2;
                    }
                    
                    if ($c > 0 && $grid[$r][$c - 1] === 1) {
                        $perimeter -= 2;
                    }
                }
            }
        }

        return $perimeter;
    }
}