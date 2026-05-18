<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function removeDuplicates(&$nums) {
        $length = count($nums);

        if ($length === 0) {
            return 0;
        }

        $k = 0;

        for ($i = 1; $i < $length; $i++) {
            if ($nums[$i] !== $nums[$k]) {
                $k++;
                $nums[$k] = $nums[$i];
            }
        }

        return $k + 1;
    }
}