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
    function isBalanced($root) {
        return $this->checkHeight($root) !== -1;
    }
    
    function checkHeight($node) {
        if (!$node) return 0;
        
        $leftHeight = $this->checkHeight($node->left);
        $rightHeight = $this->checkHeight($node->right);
        
        if ($leftHeight === -1 || $rightHeight === -1 || abs($leftHeight - $rightHeight) > 1) {
            return -1;
        }
        
        return max($leftHeight, $rightHeight) + 1;
    }
}