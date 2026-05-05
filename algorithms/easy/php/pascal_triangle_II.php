<?php

class Solution {

    /**
     * @param Integer $rowIndex
     * @return Integer[]
     */
    function getRow($rowIndex) {
        $row = [1];
        
        for ($i = 1; $i <= $rowIndex; $i++) {
            $row[] = (int) round($row[$i - 1] * ($rowIndex - $i + 1) / $i);
        }
        
        return $row;
    }
}