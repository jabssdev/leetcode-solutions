<?php

class Solution {

    /**
     * @param String $s
     * @return Boolean
     */
    function isPalindrome($s) {
        $left = 0;
        $right = strlen($s) - 1;

        while ($left < $right) {
            if (!ctype_alnum($s[$left])) {
                $left++;
            } elseif (!ctype_alnum($s[$right])) {
                $right--;
            } else {
                if (strtolower($s[$left]) !== strtolower($s[$right])) {
                    return false;
                }
                $left++;
                $right--;
            }
        }

        return true;
    }
}
