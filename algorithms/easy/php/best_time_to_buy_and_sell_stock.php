<?php

class Solution {

    /**
     * @param Integer[] $prices
     * @return Integer
     */
    function maxProfit($prices) {
        $minPrice = PHP_INT_MAX;
        $maxProfit = 0;

        foreach ($prices as $price) {
            if ($price < $minPrice) $minPrice = $price;
            elseif ($price - $minPrice > $maxProfit) $maxProfit = $price - $minPrice;
        }

        return $maxProfit;
    }
}