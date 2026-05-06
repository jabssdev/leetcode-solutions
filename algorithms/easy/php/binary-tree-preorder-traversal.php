<?php

/**
 * Definition for a binary tree node.
 * class TreeNode {
 *     public $val = null;
 *     public $left = null;
 *     public $right = null;
 *     function __construct($val = 0, $left = null, $right = null) {
 *         $this->val = $val;
 *         $this->left = $left;
 *         $this->right = $right;
 *     }
 * }
 */
class Solution {

    /**
     * @param TreeNode|null $root
     * @return Integer[]
     */
    function preorderTraversal($root) {
        $result = [];
        if ($root === null) return $result;

        $stack = [$root];

        while (!empty($stack)) {
            $node = array_pop($stack);
            $result[] = $node->val;

            if ($node->right !== null) $stack[] = $node->right;
            if ($node->left !== null) $stack[] = $node->left;
        }

        return $result;
    }
}