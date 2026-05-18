/**
 * @param {number} x
 * @return {number}
 */
var mySqrt = function (x) {
	if (x < 2) {
		return x;
	}

	let left = 1;
	let right = Math.floor(x / 2);
	let ans = 0;

	while (left <= right) {
		const mid = left + Math.floor((right - left) / 2);

		if (mid <= x / mid) {
			ans = mid;
			left = mid + 1;
		} else {
			right = mid - 1;
		}
	}

	return ans;
};
