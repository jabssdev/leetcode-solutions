<?php

class Solution {

    /**
     * @param String $haystack
     * @param String $needle
     * @return Integer
     */
    function strStr($haystack, $needle) {
        $hLen = strlen($haystack);
        $nLen = strlen($needle);

        if ($nLen === 0) return 0;

        for ($i = 0; $i <= $hLen - $nLen; $i++) {
            for ($j = 0; $j < $nLen; $j++) {
                if ($haystack[$i + $j] !== $needle[$j]) {
                    break;
                }

                if ($j === $nLen - 1) {
                    return $i;
                }
            }
        }

        return -1;
    }
}