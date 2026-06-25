/**
 * @param {number[]} g
 * @param {number[]} s
 * @return {number}
 */
var findContentChildren = function (g, s) {
	g.sort((a, b) => a - b);
	s.sort((a, b) => a - b);

	let childI = 0;
	let cookieJ = 0;

	while (childI < g.length && cookieJ < s.length) {
		if (s[cookieJ] >= g[childI]) {
			childI++;
		}
		cookieJ++;
	}

	return childI;
};
