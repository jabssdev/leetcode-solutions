<?php

class Solution
{
  private $buffer = [];
  private $bufferIndex = 0;
  private $bufferSize = 0;

  /**
   * @param String
   * @param Integer
   * @return Integer
   */
  function read($buf, $n)
  {
    $totalCharsRead = 0;

    while ($totalCharsRead < $n) {
      if ($this->bufferIndex >= $this->bufferSize) {
        $this->buffer = array_fill(0, 4, '');
        $this->bufferSize = read4($this->buffer);
        $this->bufferIndex = 0;

        if ($this->bufferSize === 0) {
          break;
        }
      }

      while ($totalCharsRead < $n && $this->bufferIndex < $this->bufferSize) {
        $buf[] = $this->buffer[$this->bufferIndex];
        $totalCharsRead++;
        $this->bufferIndex++;
      }
    }

    return $totalCharsRead;
  }
}