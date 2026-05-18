<?php

class Solution {

    /**
     * @param Integer $x
     * @return Integer
     */
    function mySqrt($x) {
        if ($x < 2) return $x;

        $left = 1;
        $right = intdiv($x, 2);
        $ans = 0;

        while ($left <= $right) {
            $mid = $left + intdiv($right - $left, 2);

            if ($mid <= $x / $mid) {
                $ans = $mid;
                $left = $mid + 1;
            } else {
                $right = $mid - 1;
            }
        }

        return $ans;    
    }
}