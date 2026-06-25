<?php

class Solution {

    /**
     * @param Integer[] $g
     * @param Integer[] $s
     * @return Integer
     */
    function findContentChildren($g, $s) {
        sort($g);
        sort($s);
        
        $childI = 0;
        $cookieJ = 0;
        $numChildren = count($g);
        $numCookies = count($s);
        
        while ($childI < $numChildren && $cookieJ < $numCookies) {
            if ($s[$cookieJ] >= $g[$childI]) {
                $childI++;
            }
            $cookieJ++;
        }
        
        return $childI;
    }
}