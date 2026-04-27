<?php

class Solution {

    /**
     * @param String $s
     * @return Integer
     */
    function lengthOfLastWord($s) {
        $length = 0;
        $i = strlen($s) - 1;

        while ($i >= 0 && $s[$i] === ' ') {
            $i--;
        }

        while ($i >= 0 && $s[$i] !== ' ') {
            $length++;
            $i--;
        }

        return $length;
    }
}