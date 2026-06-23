<?php

class MyQueue {
    private SplStack $inputStack;
    private SplStack $outputStack;

    function __construct() {
        $this->inputStack = new SplStack();
        $this->outputStack = new SplStack();
    }
  
    /**
     * @param Integer $x
     * @return void
     */
    function push(int $x): void {
        $this->inputStack->push($x);
    }
  
    /**
     * @return Integer
     */
    function pop(): int {
        $this->moveElements();
        return $this->outputStack->pop();
    }
  
    /**
     * @return Integer
     */
    function peek(): int {
        $this->moveElements();
        // SplStack usa 'top()' para ver el elemento en la cima
        return $this->outputStack->top();
    }
  
    /**
     * @return Boolean
     */
    function empty(): bool {
        return $this->inputStack->isEmpty() && $this->outputStack->isEmpty();
    }

    /**
     * Método auxiliar privado para trasladar elementos
     */
    private function moveElements(): void {
        if ($this->outputStack->isEmpty()) {
            while (!$this->inputStack->isEmpty()) {
                $this->outputStack->push($this->inputStack->pop());
            }
        }
    }
}

/**
 * Your MyQueue object will be instantiated and called as such:
 * $obj = MyQueue();
 * $obj->push($x);
 * $ret_2 = $obj->pop();
 * $ret_3 = $obj->peek();
 * $ret_4 = $obj->empty();
 */