<?php

class Solution {

    /**
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersection($nums1, $nums2) {
        $set1 = [];
        $result = [];

        foreach ($nums1 as $num) {
            $set1[$num] = true;
        }

        foreach ($nums2 as $num) {
            if (isset($set1[$num])) {
                $result[] = $num;
                unset($set1[$num]);
            }
        }

        return $result;
    }
}