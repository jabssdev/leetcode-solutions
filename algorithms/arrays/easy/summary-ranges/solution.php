<?php 

class Solution {

    /**
     * @param Integer[] $nums
     * @return String[]
     */
    function summaryRanges($nums) {
        $result = [];
        $n = count($nums);
        
        if ($n === 0) {
            return $result;
        }

        $start = $nums[0];

        for ($i = 1; $i <= $n; $i++) {
            
            if ($i === $n || $nums[$i] !== $nums[$i - 1] + 1) {
                
                if ($start === $nums[$i - 1]) {
                    $result[] = (string)$start;
                } else {
                    $result[] = $start . "->" . $nums[$i - 1];
                }
                
                if ($i < $n) {
                    $start = $nums[$i];
                }
            }
        }

        return $result;
    }
}