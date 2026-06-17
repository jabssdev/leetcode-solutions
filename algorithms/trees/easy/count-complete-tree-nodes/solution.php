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
     * @return Integer
     */
    function countNodes($root) {
        if ($root === null) {
            return 0;
        }

        $leftHeight = $this->getLeftHeight($root);
        $rightHeight = $this->getRightHeight($root);

        if ($leftHeight === $rightHeight) {
            return (1 << $leftHeight) - 1;
        }

        return 1 + $this->countNodes($root->left) + $this->countNodes($root->right);
    }

    private function getLeftHeight($node) {
        $height = 0;

        while ($node !== null) {
            $height++;
            $node = $node->left;
        }

        return $height;
    }

    private function getRightHeight($node) {
        $height = 0;

        while ($node !== null) {
            $height++;
            $node = $node->right;
        }

        return $height;
    }
}