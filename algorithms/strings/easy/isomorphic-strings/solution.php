<?php

class Solution {

    /**
     * @param String $s
     * @param String $t
     * @return Boolean
     */
    function isIsomorphic($s, $t) {
        $n = strlen($s);
        $sMap = array_fill(0, 128, 0);
        $tMap = array_fill(0, 128, 0);

        for ($i = 0; $i < $n; $i++) {
            $sVal = ord($s[$i]);
            $tVal = ord($t[$i]);

            if ($sMap[$sVal] !== $tMap[$tVal]) {
                return false;
            }

            $sMap[$sVal] = $i + 1;
            $tMap[$tVal] = $i + 1;
        }

        return true;
    }
}