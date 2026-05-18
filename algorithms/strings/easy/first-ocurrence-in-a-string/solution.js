/**
 * @param {string} haystack
 * @param {string} needle
 * @return {number}
 */
var strStr = function (haystack, needle) {
	const hLen = haystack.length;
	const nLen = needle.length;

	for (let i = 0; i < hLen - nLen + 1; i++) {
		if (haystack.substring(i, i + nLen) === needle) {
			return i;
		}
	}

	return -1;
};
