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
     * @return Boolean
     */
    function isSymmetric($root) {
        if (!$root) {
            return true;
        }
        return $this->isMirror($root->left, $root->right);
    }

    function isMirror($left, $right) {
        if (!$left && !$right) {
            return true;
        }
        if (!$left || !$right || $left->val !== $right->val) {
            return false;
        }
        return $this->isMirror($left->left, $right->right) && $this->isMirror($left->right, $right->left);
    }
}