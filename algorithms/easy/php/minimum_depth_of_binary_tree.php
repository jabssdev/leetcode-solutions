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
    function minDepth($root) {
        if (!$root) return 0;

        $queue = new SplQueue();
        $queue->enqueue($root);
        $depth = 1;

        while (!$queue->isEmpty()) {
            $levelSize = $queue->count();

            for ($i = 0; $i < $levelSize; $i++) {
                $node = $queue->dequeue();

                if (!$node->left && !$node->right) return $depth;

                if ($node->left) $queue->enqueue($node->left);
                if ($node->right) $queue->enqueue($node->right);
            }
            $depth++;
        }

        return $depth;
    }
}