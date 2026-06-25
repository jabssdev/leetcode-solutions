<?php

class NumArray {
    /**
     * @param Integer[] $nums
     */
    function __construct($nums) {
        $this->prefix = [0];
        $currentSum = 0;
        
        foreach ($nums as $num) {
            $currentSum += $num;
            $this->prefix[] = $currentSum;
        }
    }
  
    /**
     * @param Integer $left
     * @param Integer $right
     * @return Integer
     */
    function sumRange($left, $right) {
        return $this->prefix[$right + 1] - $this->prefix[$left];
    }
}

/**
 * Your NumArray object will be instantiated and called as such:
 * $obj = NumArray($nums);
 * $ret_1 = $obj->sumRange($left, $right);
 */