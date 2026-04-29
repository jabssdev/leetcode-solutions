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
     * @param TreeNode $root
     * @return Integer[]
     */
    function inorderTraversal($root) {
        $result = [];
        $stack = [];

        while ($root || !empty($stack)) {
            while ($root) {
                array_push($stack, $root);
                $root = $root->left;
            }

            $root = array_pop($stack);
            $result[] = $root->val;
            $root = $root->right;
        }

        return $result;
    }
}