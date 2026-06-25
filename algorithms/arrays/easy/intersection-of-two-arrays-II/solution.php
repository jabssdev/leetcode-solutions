<?php

class Solution {

    /**
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersect($nums1, $nums2) {
        if (count($nums1) > count($nums2)) {
            return $this->intersect($nums2, $nums1);
        }

        $counts = [];
        $result = [];

        foreach ($nums1 as $num) {
            $counts[$num] = ($counts[$num] ?? 0) + 1;
        }

        foreach ($nums2 as $num) {
            if (isset($counts[$num]) && $counts[$num] > 0) {
                $result[] = $num;
                $counts[$num]--;
            }
        }

        return $result;
    }
}