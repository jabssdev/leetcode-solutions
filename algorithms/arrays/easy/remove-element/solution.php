<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @param Integer $val
     * @return Integer
     */
    function removeElement(&$nums, $val) {
        $length = count($nums);
        $k = 0;

        for ($i = 0; $i < $length; $i++) {
            if ($nums[$i] !== $val) {
                $nums[$k] = $nums[$i];
                $k++;
            }
        }

        return $k;
    }
}