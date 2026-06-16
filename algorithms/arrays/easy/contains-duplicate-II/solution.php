<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @param Integer $k
     * @return Boolean
     */
    function containsNearbyDuplicate($nums, $k) {
        $window = [];
        $length = count($nums);

        for ($i = 0; $i < $length; $i++) {
            $num = $nums[$i];

            if (isset($window[$num])) {
                return true;
            }

            $window[$num] = true;

            if (count($window) > $k) {
                unset($window[$nums[$i - $k]]);
            }
        }

        return false;
    }
}