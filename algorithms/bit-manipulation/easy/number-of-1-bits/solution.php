<?php

class Solution {

    /**
     * @param Integer $n
     * @return Integer
     */
    function hammingWeight($n) {
        $count = 0;

        while ($n !== 0) {
            $n &= $n - 1;
            $count++;
        }

        return $count;
    }
}