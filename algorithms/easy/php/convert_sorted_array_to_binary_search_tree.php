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
     * @param Integer[] $nums
     * @return TreeNode
     */
    function sortedArrayToBST($nums) {
        if (empty($nums)) {
            return null;
        }
        
        $mid = floor(count($nums) / 2);
        $root = new TreeNode($nums[$mid]);
        $root->left = $this->sortedArrayToBST(array_slice($nums, 0, $mid));
        $root->right = $this->sortedArrayToBST(array_slice($nums, $mid + 1));
        return $root;
    }
}