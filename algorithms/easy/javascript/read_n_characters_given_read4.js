function read(buf, n) {
	let totalCharsRead = 0;
	let buffer = [];
	let bufferSize = 0;
	let bufferIndex = 0;

	while (totalCharsRead < n) {
		if (bufferIndex >= bufferSize) {
			buffer = new Array(4).fill("");
			bufferSize = read4(buffer);
			bufferIndex = 0;

			if (bufferSize === 0) {
				break;
			}
		}

		while (totalCharsRead < n && bufferIndex < bufferSize) {
			buf[totalCharsRead] = buffer[bufferIndex];
			totalCharsRead++;
			bufferIndex++;
		}
	}

	return totalCharsRead;
}
