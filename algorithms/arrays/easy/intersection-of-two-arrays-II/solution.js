/**
 * @param {number[]} nums1
 * @param {number[]} nums2
 * @return {number[]}
 */
var intersect = function (nums1, nums2) {
	if (nums1.length > nums2.length) {
		return intersect(nums2, nums1);
	}

	const counts = new Map();
	const result = [];

	for (const num of nums1) {
		counts.set(num, (counts.get(num) || 0) + 1);
	}
	for (const num of nums2) {
		const count = counts.get(num);

		if (count > 0) {
			result.push(num);
			counts.set(num, count - 1);
		}
	}

	return result;
};
