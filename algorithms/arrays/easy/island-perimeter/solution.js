/**
 * @param {number[][]} grid
 * @return {number}
 */
var islandPerimeter = function (grid) {
	let perimeter = 0;
	const rows = grid.length;
	const cols = grid[0].length;

	for (let r = 0; r < rows; r++) {
		for (let c = 0; c < cols; c++) {
			if (grid[r][c] === 1) {
				perimeter += 4;

				if (r > 0 && grid[r - 1][c] === 1) {
					perimeter -= 2;
				}

				if (c > 0 && grid[r][c - 1] === 1) {
					perimeter -= 2;
				}
			}
		}
	}

	return perimeter;
};
