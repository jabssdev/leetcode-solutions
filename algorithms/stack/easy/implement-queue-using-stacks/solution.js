var MyQueue = function () {
	this.inputStack = [];
	this.outputStack = [];
};

/**
 * @param {number} x
 * @return {void}
 */
MyQueue.prototype.push = function (x) {
	this.inputStack.push(x);
};

/**
 * @return {number}
 */
MyQueue.prototype.pop = function () {
	this._moveElements();
	return this.outputStack.pop();
};

/**
 * @return {number}
 */
MyQueue.prototype.peek = function () {
	this._moveElements();
	return this.outputStack[this.outputStack.length - 1];
};

/**
 * @return {boolean}
 */
MyQueue.prototype.empty = function () {
	return this.inputStack.length === 0 && this.outputStack.length === 0;
};

/**
 * Helper function to move elements from input stack to output stack
 */
MyQueue.prototype._moveElements = function () {
	if (this.outputStack.length === 0) {
		while (this.inputStack.length > 0) {
			this.outputStack.push(this.inputStack.pop());
		}
	}
};

/**
 * Your MyQueue object will be instantiated and called as such:
 * var obj = new MyQueue()
 * obj.push(x)
 * var param_2 = obj.pop()
 * var param_3 = obj.peek()
 * var param_4 = obj.empty()
 */
