/**
 * @param {string} s
 * @param {string} t
 * @return {boolean}
 */
var isAnagram = function (s, t) {
	if (s.length !== t.length) return false;

	const countMap = new Map();

	for (const char of s) {
		countMap.set(char, (countMap.get(char) || 0) + 1);
	}

	for (const char of t) {
		if (!countMap.has(char)) return false;
		countMap.set(char, countMap.get(char) - 1);
		if (countMap.get(char) < 0) return false;
	}

	return true;
};
