/**
 * @param {string} s
 * @return {boolean}
 */
var isValid = function (s) {
	if (s.length % 2 !== 0) {
		return false;
	}

	const stack = [];
	const map = {
		")": "(",
		"]": "[",
		"}": "{",
	};

	for (const char of s) {
		if (map[char]) {
			const topElement = stack.length > 0 ? stack.pop() : "#";
			if (topElement !== map[char]) {
				return false;
			}
		} else {
			stack.push(char);
		}
	}

	return stack.length === 0;
};
