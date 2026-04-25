<?php

class Solution {
    /**
     * @param string[] $strs
     * @return string
     */
    public function longestCommonPrefix(array $strs): string {
        if (empty($strs)) {
            return "";
        }

        sort($strs);

        $firstStr = $strs[0];
        $lastStr = $strs[count($strs) - 1];
        
        $length = strlen($firstStr);

        for ($i = 0; $i < $length; $i++) {
            if ($firstStr[$i] !== $lastStr[$i]) {
                return substr($firstStr, 0, $i);
            }
        }

        return $firstStr;
    }
}