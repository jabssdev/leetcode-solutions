<?php

/**
 * Definition for a singly-linked list.
 * class ListNode {
 *     public $val = 0;
 *     public $next = null;
 *     function __construct($val = 0, $next = null) {
 *         $this->val = $val;
 *         $this->next = $next;
 *     }
 * }
 */
class Solution {

    /**
     * @param ListNode $head
     * @return Boolean
     */
    function isPalindrome($head) {
        if ($head === null || $head->next === null) {
            return true;
        }

        $slow = $head;
        $fast = $head;
        
        while ($fast !== null && $fast->next !== null) {
            $slow = $slow->next;
            $fast = $fast->next->next;
        }

        $prev = null;
        $curr = $slow;
        
        while ($curr !== null) {
            $nextTemp = $curr->next;
            $curr->next = $prev;
            $prev = $curr;
            $curr = $nextTemp;
        }

        $left = $head;
        $right = $prev;
        
        while ($right !== null) {
            if ($left->val !== $right->val) {
                return false;
            }
            $left = $left->next;
            $right = $right->next;
        }

        return true;
    }
}