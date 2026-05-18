/**
 * @param {number} n
 * @return {number}
 */
var climbStairs = function (n) {
	let prev = 1;
	let curr = 1;

	for (let i = 2; i <= n; i++) {
		const next = prev + curr;
		prev = curr;
		curr = next;
	}

	return curr;
};
