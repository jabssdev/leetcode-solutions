<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function missingNumber($nums) {
        $n = count($nums);
        
        $missing = $n;
        
        for ($i = 0; $i < $n; $i++) {
            $missing ^= $i ^ $nums[$i];
        }
        
        return $missing;
    }
}