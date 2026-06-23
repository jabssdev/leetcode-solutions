<?php

class Solution {

    /**
     * @param String $s
     * @param String $t
     * @return Boolean
     */
    function isAnagram($s, $t) {
        if (mb_strlen($s) !== mb_strlen($t)) return false;

        $counts = [];
        $sChars = mb_str_split($s);
        $tChars = mb_str_split($t);
        $length = count($sChars);

        for ($i = 0; $i < $length; $i++) {
            $counts[$sChars[$i]] = ($counts[$sChars[$i]] ?? 0) + 1;
            $counts[$tChars[$i]] = ($counts[$tChars[$i]] ?? 0) - 1;
        }

        foreach ($counts as $count) {
            if ($count !== 0) return false;
        }

        return true;
    }
}