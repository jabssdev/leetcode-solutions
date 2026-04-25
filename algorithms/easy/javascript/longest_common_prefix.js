/**
 * @param {string[]} strs
 * @return {string}
 */
var longestCommonPrefix = function (strs) {
	if (!strs || strs.length === 0) {
		return "";
	}

	const sortedStrs = [...strs].sort();

	const firstStr = sortedStrs[0];
	const lastStr = sortedStrs[sortedStrs.length - 1];

	for (let i = 0; i < firstStr.length; i++) {
		if (firstStr[i] !== lastStr[i]) {
			return firstStr.substring(0, i);
		}
	}

	return firstStr;
};
