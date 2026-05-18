/**
 * @param {string} columnTitle
 * @return {number}
 */
var titleToNumber = function (columnTitle) {
	let result = 0;
	const baseCode = "A".charCodeAt(0) - 1;

	for (const char of columnTitle) {
		const value = char.charCodeAt(0) - baseCode;
		result = result * 26 + value;
	}

	return result;
};
