/**
 * @param {string} s
 * @param {string} t
 * @return {boolean}
 */
var isIsomorphic = function (s, t) {
	const n = s.length;
	const sMap = new Array(128).fill(0);
	const tMap = new Array(128).fill(0);

	for (let i = 0; i < n; i++) {
		const sVal = s.charCodeAt(i);
		const tVal = t.charCodeAt(i);

		if (sMap[sVal] !== tMap[tVal]) {
			return false;
		}

		sMap[sVal] = i + 1;
		tMap[tVal] = i + 1;
	}

	return true;
};
