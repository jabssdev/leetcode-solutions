<?php

class MyStack {
    private SplQueue $queue;

    /**
     */
    function __construct() {
        $this->queue = new SplQueue();
    }
  
    /**
     * @param Integer $x
     * @return NULL
     */
    function push($x) {
        $size = $this->queue->count();
        
        $this->queue->enqueue($x);
        
        while ($size > 0) {
            $this->queue->enqueue($this->queue->dequeue());
            $size--;
        }
    }
  
    /**
     * @return Integer
     */
    function pop() {
        return $this->queue->dequeue();
    }
  
    /**
     * @return Integer
     */
    function top() {
        return $this->queue->bottom();
    }
  
    /**
     * @return Boolean
     */
    function empty() {
        return $this->queue->isEmpty();
    }
}

/**
 * Your MyStack object will be instantiated and called as such:
 * $obj = MyStack();
 * $obj->push($x);
 * $ret_2 = $obj->pop();
 * $ret_3 = $obj->top();
 * $ret_4 = $obj->empty();
 */