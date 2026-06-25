<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @return NULL
     */
    function moveZeroes(&$nums) {
        $insertPos = 0;
        $n = count($nums);

        for ($i = 0; $i < $n; $i++) {
            if ($nums[$i] !== 0) {
                if ($i !== $insertPos) {
                    $temp = $nums[$insertPos];
                    $nums[$insertPos] = $nums[$i];
                    $nums[$i] = $temp;
                }
                $insertPos++;
            }
        }
    }
}