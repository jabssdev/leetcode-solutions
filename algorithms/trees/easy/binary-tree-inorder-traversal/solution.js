/**
 * Definition for a binary tree node.
 * function TreeNode(val, left, right) {
 *     this.val = (val===undefined ? 0 : val)
 *     this.left = (left===undefined ? null : left)
 *     this.right = (right===undefined ? null : right)
 * }
 */
/**
 * @param {TreeNode} root
 * @return {number[]}
 */
var inorderTraversal = function (root) {
	const result = [];
	const stack = [];

	while (root || stack.length > 0) {
		while (root) {
			stack.push(root);
			root = root.left;
		}

		root = stack.pop();
		result.push(root.val);
		root = root.right;
	}

	return result;
};
