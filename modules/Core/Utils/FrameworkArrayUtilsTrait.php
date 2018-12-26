<?php

/*
 * Copyright (c) 2018 Anton Bagdatyev
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
 * A trait containing useful methods concerning arrays shared across multiple Norma's framework components.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
trait FrameworkArrayUtilsTrait {
    
    /**
     * Given an array with any dimension, loosens its contents in order to obtain a new
     * array where each element is an array containing the leaf value at index 0 and the array
     * of keys (i.e. the path) which led to that leaf value (in order).
     * 
     * @param array $array The array.
     * @return array The resulting array.
     */
    private function privateLoosenInternalMultiDimensionalArrayPathForEachVal($array) {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array), \RecursiveIteratorIterator::CHILD_FIRST);
        $iterator->rewind();
        $res = [];
        $j = 0;
        foreach ($iterator as $v) {
            $j++;
            $depth = $iterator->getDepth();
            for ($path = [], $i = 0, $z = $depth; $i <= $z; $i++) {
                $path[] = $iterator->getSubIterator($i)->key();
            }
            $leaf = $array;
            foreach ($path as $pathKey) {
                $leaf = $leaf[$pathKey];
            }
            if (!is_array($leaf) || (count($leaf) === 0)) {
                $res[] = [$v, $path];
            }
        }
        return $res;
    }
    
    /**
     * Adds keys and a leaf value to an array. Each key is added as an inner key of the previous one,
     * until the last argument is reached and therefore the leaf value is added.
     * 
     * @param array $array The array.
     * @param mixed $args Keys and last value to add as a leaf.
     * @return void
     */
    private function privateArrayMultiDim(&$array, ...$args) {
        $value = array_pop($args);
        $keys = $args;

        $l = count($args) - 1;
        $i = 0;
        $curr = &$array;
        while ($i < $l) {
            $key = $keys[$i];
            if (!isset($curr[$key])) {
                $curr[$key] = [];
            }
            $curr = &$curr[$key];
            $i++;
        }
        $curr[$keys[$i]] = $value;
    }
    
    /**
     * Given an array and an array of keys, traverses the array and returns its inner value
     * retrieved using each subsequent element of the keys array as an inner key of the given array.
     * 
     * @param array $array The array where the nested value should be.
     * @param array $nested The array of keys to use to traverse the array. If one of the keys of this array does not exist within the given `$array` array
     *                                     NULL will be returned. If this argument is omitted, then the original array will be returned.
     * @return mixed The value.
     */
    private function privateNestedArrayValue(array $array, $nested = []) {
        $ret = $array;
        foreach ($nested as $key) {
            if (!isset($ret[$key])) {
                return NULL;
            }
            $ret = $ret[$key];
        }
        return $ret;
    }
    
}
