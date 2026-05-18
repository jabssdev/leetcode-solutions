<?php

class Solution {

    /**
     * @param Integer $n
     * @return Boolean
     */
    function isHappy($n) {
        $slow = $n;
        $fast = $this->getNext($n);

        while ($fast !== 1 && $slow !== $fast) {
            $slow = $this->getNext($slow);
            $fast = $this->getNext($this->getNext($fast));
        }

        return $fast === 1;
    }

    function getNext($n) {
        $sum = 0;
        while ($n > 0) {
            $digit = $n % 10;
            $sum += $digit * $digit;
            $n = (int)($n / 10);
        }
        return $sum;
    }
}