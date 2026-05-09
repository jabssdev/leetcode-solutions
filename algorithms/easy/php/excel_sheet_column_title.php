<?php

class Solution {

    /**
     * @param Integer $columnNumber
     * @return String
     */
    function convertToTitle($columnNumber) {
        $result = '';

        while ($columnNumber > 0) {
            $columnNumber--;
            $remainder = $columnNumber % 26;
            $result = chr(65 + $remainder) . $result;
            $columnNumber = intdiv($columnNumber, 26);
        }

        return $result;
    }
}