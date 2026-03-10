<?php

class Solution {

    /**
     * @param Integer[] $nums
     * @param Integer $target
     * @return Integer[]
     */
    function twoSum($nums, $target) {
        $map = [];
        foreach ($nums as $i => $num) {
            $diff = $target - $num;
            if (isset($map[$diff])) {
                return [$map[$diff], $i];
            }
            $map[$num] = $i;
        }
        return [];
    }
}