<?php

class Solution {

    /**
     * @param String $a
     * @param String $b
     * @return String
     */
    function addBinary($a, $b) {
        $i = strlen($a) - 1;
        $j = strlen($b) - 1;
        $carry = 0;
        $result = [];

        while ($i >= 0 || $j >= 0 || $carry > 0) {
            $digitA = ($i >= 0) ? (int)$a[$i] : 0;
            $digitB = ($j >= 0) ? (int)$b[$j] : 0;

            $sum = $digitA + $digitB + $carry;
            
            $result[] = $sum % 2;
            $carry = (int)($sum / 2);

            $i--;
            $j--;
        }

        return implode("", array_reverse($result));
    }
}