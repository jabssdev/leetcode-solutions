<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    function findDisappearedNumbers($nums) {
        $result = [];
        $n = count($nums);
        
        for ($i = 0; $i < $n; $i++) {
            $index = abs($nums[$i]) - 1;
            
            if ($nums[$index] > 0) {
                $nums[$index] = -$nums[$index];
            }
        }
        
        for ($i = 0; $i < $n; $i++) {
            if ($nums[$i] > 0) {
                $result[] = $i + 1;
            }
        }
        
        return $result;
    }
}