/**
 * @param {number[]} nums
 * @param {number} k
 * @return {boolean}
 */
var containsNearbyDuplicate = function (nums, k) {
	const windowSet = new Set();

	for (let i = 0; i < nums.length; i++) {
		if (windowSet.has(nums[i])) {
			return true;
		}

		windowSet.add(nums[i]);

		if (windowSet.size > k) {
			windowSet.delete(nums[i - k]);
		}
	}

	return false;
};
