<?php

class Solution
{
  /**
   * @param Integer $x
   * @return Boolean
   */
  function isPalindrome($x)
  {
    if ($x < 0 || ($x % 10 === 0 && $x !== 0)) {
      return false;
    }

    $revertedNumber = 0;

    while ($x > $revertedNumber) {
      $revertedNumber = ($revertedNumber * 10) + ($x % 10);

      $x = (int)($x / 10);
    }

    return $x === $revertedNumber || $x === (int)($revertedNumber / 10);
  }
}
