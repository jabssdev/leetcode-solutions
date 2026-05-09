/**
 * @param {number} columnNumber
 * @return {string}
 */
var convertToTitle = function (columnNumber) {
	let result = [];

	while (columnNumber > 0) {
		columnNumber--;
		const remainder = columnNumber % 26;
		result.push(String.fromCharCode(65 + remainder));
		columnNumber = Math.floor(columnNumber / 26);
	}

	return result.reverse().join("");
};
