<?php

class Solution {

    /**
     * @param Integer $n
     * @return Integer
     */
    function climbStairs($n) {
        if ($n <= 1) {
            return 1;
        }

        $prev = 1;
        $curr = 1;

        for ($i = 2; $i <= $n; $i++) {
            $temp = $curr;
            $curr = $prev + $curr;
            $prev = $temp;
        }

        return $curr;
    }
}