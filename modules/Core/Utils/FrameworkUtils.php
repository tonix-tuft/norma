<?php

/*
 * Copyright (c) 2019 Anton Bagdatyev (Tonix-Tuft)
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

use Norma\Core\Utils\FrameworkArrayUtilsTrait;
use Norma\Core\Utils\FrameworkStringUtilsTrait;

/**
 * FrameworkUtils defines common methods which can be used across different modules.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class FrameworkUtils {
    
    use FrameworkArrayUtilsTrait;
    use FrameworkStringUtilsTrait;
    
    /**
     * Tests whether a variable is a PHP closure.
     * 
     * @param mixed $mixed A PHP variable.
     * @return bool True if the variable is a closure, false otherwise.
     */
    public function isAnonymousFunction($mixed) {
        return (is_callable($mixed) && ($mixed instanceof \Closure));
    }
    
    /**
     * Creates a file creating also intermediate missing directories.
     * 
     * @param string $filename The name of the file to create.
     * @param string $contents The contents of the file to create.
     * @return bool True if the file was successfully created, false otherwise.
     */
    public function filePutContentsMissingDirectories($filename, $contents) {
        $dirname = dirname($filename);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, TRUE);
        }
        return file_put_contents($filename, $contents);
    }
    
    /**
     * Deletes all the files within a given path which have all a common prefix.
     * 
     * @param string $prefix The prefix.
     * @param string $path The path.
     * @param array $excludePaths Filenames to exclude when deleting the files which have a common prefix.
     * @return void
     */
    public function deleteFilesStartingWith($prefix, $path, $excludePaths = []) {
        $iterator = new \RegexIterator(new \DirectoryIterator($path), '#^'.preg_quote($prefix, '#').'[^/]+$#', \RegexIterator::GET_MATCH);
        foreach ($iterator as $entry) {
            $filename = $entry[0];
            $pathToDelete = $path . DIRECTORY_SEPARATOR . $filename;
            if (!in_array($pathToDelete, $excludePaths)) {
                unlink($pathToDelete);
            }
        }
    }
    
    /**
     * Deletes a directory recursively deleting inner directories and nested files.
     * 
     * @param string $dir The directory to delete.
     * @return void
     */
    public function deleteDirectory($dir) {
        if (empty($dir) || $dir === DIRECTORY_SEPARATOR) {
            return;
        }
        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }
    
    /**
     * Checks whether a type is an abstract class or an interface.
     * 
     * @param string $type The type to check.
     * @return bool True if the type is an abstract class or an interface.
     */
    public function isAbstractClassOrInterface($type) {
        try {
            return interface_exists($type) ||
                      (class_exists($type) && (new \ReflectionClass($type))->isAbstract());
        }
        catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Checks whether all the keys given as parameter exist in an array. Each subsequent key is checked in an
     * inner dimension of the given array.
     * 
     * @param array $array An array
     * @param string|int $keys Variadic argument of the keys to check if they all exist in the given array.
     * @return bool True if all the keys exist, each key on the corresponding dimension of the array, false otherwise.
     */
    public function arrayKeysExist($array, ...$keys) {
        $current = $array;
        if (empty($keys)) {
            return false;
        }
        foreach ($keys as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return false;
            }
            $current = $current[$key];
        }
        return true;
    }
    
    /**
     * Tests whether an array is an associative array.
     * 
     * @param array $array The array.
     * @return bool True if the array is an associative array, false otherwise.
     */
    public function isAssocArray($array) {
        return array_keys($array) !== range(0, count($array) - 1);
    }
    
    /**
     * Returns the maximum depth level of an array, from 0 to n.
     * 
     * @param mixed $array An array or object to be iterated upon.
     * @return int The maximum depth of the array.
     */
    public function maxDepth($array) {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($array), \RecursiveIteratorIterator::CHILD_FIRST);
        $iterator->rewind();
        $maxDepth = 0;
        foreach ($iterator as $v) {
            $depth = $iterator->getDepth();
            if ($depth > $maxDepth) {
                $maxDepth = $depth;
            }
        }
        return $maxDepth;
    }
    
    /**
     * Given an array with any dimension, loosens its contents in order to obtain a new
     * array where each element is an array containing the leaf value at index 0 and the array
     * of keys (i.e. the path) which led to that leaf value (in order).
     * 
     * @param array $array The array.
     * @return array The resulting array.
     */
    public function loosenMultiDimensionalArrayPathForEachVal($array) {
        return $this->privateLoosenInternalMultiDimensionalArrayPathForEachVal($array);
    }
    
    /**
     * Adds keys and a leaf value to an array. Each key is added as an inner key of the previous one,
     * until the last argument is reached and therefore the leaf value is added.
     * 
     * @param array $array The array.
     * @param mixed $args Keys and last value to add as a leaf.
     * @return void
     */
    public function arrayMultiDim(&$array, ...$args) {
        return $this->privateArrayMultiDim($array, ...$args);
    }
    
    /**
     * Gets the first key of an array.
     * 
     * @param array An array.
     * @return mixed The array's first key.
     */
    public function arrayFirstKey($array) {
        foreach ($array as $key => $val) { break; }
        return $key;
    }
    
    /**
     * Gets the first value of an array.
     * 
     * @param array An array.
     * @return mixed The array's first value.
     */
    public function arrayFirstVal() {
        foreach ($array as $val) { break; }
        return $val;
    }
    
    /**
     * PHP `range` function using of PHP generators.
     * 
     * @param mixed $start First value of the sequence.
     * @param mixed $limit The sequence is ended upon reaching the end value.
     * @param number $step If a step value is given, it will be used as the increment between elements in the sequence.
     *                                     Step should be given as a positive number, but it can also be negative if and only if start is greater than or equal to limit.
     *                                     If not specified, step will default to 1.
     *                                     If `$step` is negative, and `$start` equals to `$limit`, the value `$start` = `$limit` would be returned once.
     * @throws \LogicException If the step parameter is invalid.
     */
    public function xrange($start, $limit, $step = 1) {
        if ($start < $limit) {
            if ($step <= 0) {
                throw new \LogicException('Step must be positive when start is less than limit.');
            }

            for ($i = $start; $i <= $limit; $i += $step) {
                yield $i;
            }
        }
        else {
            if ($step >= 0) {
                throw new \LogicException('Step must be negative when start is greater than or equal to limit.');
            }

            for ($i = $start; $i >= $limit; $i += $step) {
                yield $i;
            }
        }
    }

}
