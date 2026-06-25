<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function thirdMax($nums) {
        $max1 = null;
        $max2 = null;
        $max3 = null;

        foreach ($nums as $num) {
            if ($num === $max1 || $num === $max2 || $num === $max3) {
                continue;
            }

            if ($max1 === null || $num > $max1) {
                $max3 = $max2;
                $max2 = $max1;
                $max1 = $num;
            } elseif ($max2 === null || $num > $max2) {
                $max3 = $max2;
                $max2 = $num;
            } elseif ($max3 === null || $num > $max3) {
                $max3 = $num;
            }
        }

        return $max3 !== null ? $max3 : $max1;
    }
}