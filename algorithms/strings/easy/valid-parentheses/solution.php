<?php
class Solution {
    
    private const BRACKET_MAP = [
        ')' => '(',
        ']' => '[',
        '}' => '{',
    ];

    /**
     * @param string $s
     * @return bool
     */
    public function isValid(string $s): bool {
        $length = strlen($s);

        if ($length % 2 !== 0) {
            return false;
        }

        $stack = [];

        for ($i = 0; $i < $length; $i++) {
            $char = $s[$i];

            if (isset(self::BRACKET_MAP[$char])) {
                $topElement = !empty($stack) ? array_pop($stack) : '#';
                
                if ($topElement !== self::BRACKET_MAP[$char]) {
                    return false;
                }
            } else {
                $stack[] = $char;
            }
        }

        return empty($stack);
    }
}