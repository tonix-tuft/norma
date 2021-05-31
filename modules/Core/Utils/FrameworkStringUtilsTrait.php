<?php

/*
 * Copyright (c) 2021 Anton Bagdatyev (Tonix)
 * 
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Norma\Core\Utils;

/**
 * A trait containing useful methods for working with strings.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
trait FrameworkStringUtilsTrait {
    
    /**
     * Implementation of sprintf with associative keys as arguments.
     * 
     * @param string $format The format of the string, as in PHP's `sprintf()` function.
     * @param array $args An associative array with the arguments to interpolate.
     * @param bool $errorOnArgNotFound Throw an error or not if one argument is missing. By default, does not throw an error.
     * @return string The formatted string.
     */
    public function sprintfn($format, array $args = [], $errorOnArgNotFound = FALSE) {
        // Map of argument names to their corresponding sprintf numeric argument value.
        $arg_nums = array_keys($args);
        array_unshift($arg_nums, 0);
        $arg_nums = array_flip(array_slice($arg_nums, 1, NULL, true));

        $charNotWithinString = NULL;
        
        // Find the next named argument. each search starts at the end of the previous replacement.
        $namedKeyRegex = '([a-zA-Z_]\w*)';
        for ($pos = 0; preg_match('/(?<=%)'.$namedKeyRegex.'(?=\$)/', $format, $match, PREG_OFFSET_CAPTURE, $pos);) {
            $arg_pos = $match[0][1];
            $arg_len = strlen($match[0][0]);
            $arg_key = $match[1][0];

            // Replace the named argument with the corresponding numeric one.
            $replace = NULL;
            if (!array_key_exists($arg_key, $arg_nums)) {
                // Programmer did not supply a value for the named argument found in the format string.
                if ($errorOnArgNotFound) {
                    user_error("sprintfn(): Missing argument '${arg_key}'", E_USER_WARNING);
                    return false;
                }
                if (is_null($charNotWithinString)) {
                    $charNotWithinString = $this->generateRandomMD5ChunkNotWithinString($format);
                }
                $replace = $charNotWithinString . $arg_key . $charNotWithinString;
            }
            else {
                $replace = $arg_nums[$arg_key];
            }
            
            $format = substr_replace($format, $replace, $arg_pos, $arg_len);
            $pos = $arg_pos + strlen($replace); // Skip to end of replacement for next iteration.
        }
        if (!empty($charNotWithinString)) {
            $format = preg_replace(
                    '/%'.
                        '('.
                            preg_quote($charNotWithinString, '/').
                            $namedKeyRegex.
                            preg_quote($charNotWithinString, '/').
                        ')'.
                    '\$/',
                    '\1',
                    $format
            );
            $returnStrTmp = vsprintf($format, array_values($args));
            $returnStr = preg_replace(
                    '/'.
                        preg_quote($charNotWithinString, '/').
                        '('.$namedKeyRegex.')'.
                        preg_quote($charNotWithinString, '/').
                    '/',
                    '%\1$',
                    $returnStrTmp 
            );
        }
        else {
            $returnStr = vsprintf($format, array_values($args));
        }
        return $returnStr;
    }
    
    /**
     * Generate a random MD5 chunk which doesn't appear within the string given as parameter.
     * 
     * @param string $str The string.
     * @return string An MD5 chunk which is a string which doesn't appear within the given string, i.e. is not a substring of the string given as parameter.
     */
    public function generateRandomMD5ChunkNotWithinString($str) {
        return $this->generateRandomHashChunkNotWithinString($str, 'md5');
    }

    /**
     * Generate a random hash chunk which doesn't appear within the string given as parameter, using the given optional hash function.
     * 
     * @param string $str The string.
     * @param callable The hashing function to use. Optional. Defaults to 'sha'.
     * @return string An hash chunk which is a string which doesn't appear within the given string, i.e. is not a substring of the string given as parameter.
     */
    public function generateRandomHashChunkNotWithinString($str, $hashFunc = 'sha1') {
        $hash = $hashFunc($str);
        $i = 4;
        while (strpos($str, $hash) !== FALSE) {
            $hash = $hashFunc($this->generateRandomString($i++));
        }
        return $hash;
    }
    
    /**
     * Generates a random string of the given length which contains the following ASCII characters:
     * 
     *      - ASCII characters of the range 97-122 (upper and lower bounds included);
     *      - ASCII characters of the range 65-09 (upper and lower bounds included);
     *      - digits from 0 to 9.
     * 
     * @param int $length The length of the string.
     * @return string The randomly generated string.
     */
    public function generateRandomString($length) {
        static $whitelist = [];
        if (empty($whitelist)) {
            $whitelist = array_merge(
                array_map(function($charASCIICode) {
                    return chr($charASCIICode);
                }, range(97, 122)),
                array_map(function($charASCIICode) {
                    return chr($charASCIICode);
                }, range(65, 90)),
                range(0,9)
            );
        }
        $str = '';
        $count = count($whitelist) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $whitelist[rand(0, $count)];
        }
        return $str;
    }
    
    /**
     * Replaces the first occurrence of `$needle` within `$haystack` with the string `$replace`.
     * 
     * @param string $haystack The string where to search for `$needle` and replace it with `$replace`.
     * @param string $needle The substring to replace.
     * @param string $replace The replace string
     * @return string The new string or the same string if `$needle` is not found.
     */
    public function strReplaceFirstOccurrence($haystack, $needle, $replace) {
        $newString = $haystack;
        $pos = strpos($haystack, $needle);
        if ($pos !== false) {
            $newString = substr_replace($haystack, $replace, $pos, strlen($needle));
        }
        return $newString;
    }
    
    
    /**
     * Tests whether the `$haystack` string starts with the `$needle` substring.
     * 
     * @param string $haystack The string.
     * @param string $needle The substring.
     * @return bool True if the string starts with the given substring.
     */
    public function strStartsWith($haystack, $needle) {
        return strpos($haystack, $needle) === 0;
    }
    
    /**
     * Randomly picks the character of `$str` and constructs a new string up to a maximum length of `$maxLength` characters.
     * 
     * @param string $str The input string.
     * @param int $maxLength The maximum length of the string to return.
     * @return string The new randomly constructed string.
     */
    private function randomlyPickCharacters($str, $maxLength) {
        $ret = '';
        $alreadyUsed = [];
        $len = strlen($str);
        
        if ($len < $maxLength) {
            $maxLength = $len;
        }
        for ($i = 0; $i < $maxLength; $i++) {
            $r = rand(0, $maxLength - 1);
            while (!empty($alreadyUsed[$r])) {
                $r = rand(0, $maxLength - 1);
            }
            $alreadyUsed[$r] = true;
            $ret .= $str[$r];
        }
        return $ret;
    }
    
}
