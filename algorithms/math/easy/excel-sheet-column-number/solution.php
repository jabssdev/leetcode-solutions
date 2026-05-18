<?php

class Solution {

    /**
     * @param String $columnTitle
     * @return Integer
     */
    function titleToNumber($columnTitle) {
        $result = 0;
        $n = strlen($columnTitle);
        $baseCode = ord('A') - 1;

        for ($i = 0; $i < $n; $i++) {
            $value = ord($columnTitle[$i]) - $baseCode;
            $result = $result * 26 + $value;
        }

        return $result;
    }
}