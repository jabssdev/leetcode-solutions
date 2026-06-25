<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer
     */
    function findMaxConsecutiveOnes($nums) {
        $maxCount = 0;
        $currentCount = 0;

        foreach ($nums as $num) {
            if ($num === 1) {
                $currentCount++;
                if ($currentCount > $maxCount) {
                    $maxCount = $currentCount;
                }
            } else {
                $currentCount = 0;
            }
        }

        return $maxCount;
    }
}