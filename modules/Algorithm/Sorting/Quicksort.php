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

namespace Norma\Algorithm\Sorting;

use Norma\Algorithm\Sorting\AbstractSortingAlgorithm;

/**
 * An implementation of a Quicksort sorting algorithm.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class Quicksort extends AbstractSortingAlgorithm {
    
    /**
     * {@inheritdoc}
     */
    public function sort(array &$array, $comparator = NULL) {
        $this->quicksort($array, $comparator);
    }

    /**
     * {@inheritdoc}
     */
    public function asort(array &$array, $comparator = NULL) {
        $keys = array_keys($array);
        
        $comparatorFn = $this->comparator($comparator);
        
        $this->quicksort($keys, function($keyA, $keyB) use ($comparatorFn, &$array) {
            $valueA = $array[$keyA];
            $valueB = $array[$keyB];
            return $comparatorFn($valueA, $valueB);
        }, TRUE);
        
        $keys = array_flip($keys);
        foreach ($keys as $key => &$i) {
            $i = $array[$key];
        }
        $array = $keys;
    }
    
    /**
     * {@inheritdoc}
     */
    public function ksort(array &$array, $comparator = NULL) {
        $keys = array_keys($array);
        
        $comparatorFn = $this->comparator($comparator);
        
        $this->sort($keys, $comparatorFn);
        
        $keys = array_flip($keys);
        foreach ($keys as $key => &$i) {
            $i = $array[$key];
        }
        $array = $keys;
    }
    
    /**
     * Sorts an array using the Quicksort algorithm.
     * 
     * @see http://alienryderflex.com/quicksort/
     * 
     * @param array $array Array to sort.
     * @param callable|null $comparator Comparator function of NULL if the default comparator should be used.
     * @param bool $arrayIsAlready0BasedWithLinearIndices TRUE if it is known that the array is already 0-based with linear indices
     *                                                                                       (i.e. a standard non-associative array), FALSE otherwise.
     * @return void
     * @throws \InvalidArgumentException If the given $comparator is not NULL and is not a callable.
     */
    protected function quicksort(&$array, $comparator = NULL, $arrayIsAlready0BasedWithLinearIndices = FALSE) {
        if (empty($array)) {
            $array = [];
            return;
        }
        
        $count = count($array);
        
        if ($count == 1) {
             $array = array_values($array);
             return;
        }
        else if (!$arrayIsAlready0BasedWithLinearIndices) {
            $array = array_values($array);
        }
        
        $comparatorFn = $this->comparator($comparator);
        
        // Two arrays used as stacks for partition indices to avoid recursion.
        $nextPartitionStartIndexStack = [];
        $nextPartitionEndIndexStack = [];
        $currentPartitionIndex = 0;
        
        $nextPartitionStartIndexStack[$currentPartitionIndex] = 0;
        $nextPartitionEndIndexStack[$currentPartitionIndex] = $count - 1;
        
        while ($currentPartitionIndex >= 0) {
            $leftIndex = $nextPartitionStartIndexStack[$currentPartitionIndex];
            $rightIndex = $nextPartitionEndIndexStack[$currentPartitionIndex];
            
            if ($leftIndex < $rightIndex) {
                // Picking a random pivot for this partition.
                $pivotIndex = rand($leftIndex, $rightIndex);
                $leftmostKey = $leftIndex;
                $pivot = $array[$pivotIndex];

                // Swap leftmost element with pivot.
                list($array[$pivotIndex], $array[$leftmostKey]) = [$array[$leftmostKey], $array[$pivotIndex]];

                while ($leftIndex < $rightIndex) {
                    while ($comparatorFn($array[$rightIndex], $pivot) >= 0 && $leftIndex < $rightIndex) {
                        $rightIndex--;
                    }
                    if ($leftIndex < $rightIndex) {
                        $array[$leftIndex] = $array[$rightIndex];
                        $leftIndex++;
                    }
                    
                    while ($comparatorFn($array[$leftIndex], $pivot) <= 0 && $leftIndex < $rightIndex) {
                        $leftIndex++;
                    }
                    if ($leftIndex < $rightIndex) {
                        $array[$rightIndex] = $array[$leftIndex];
                        $rightIndex--;
                    }
                }
                
                $array[$leftIndex] = $pivot;
                $nextPartitionStartIndexStack[$currentPartitionIndex + 1] = $leftIndex + 1;
                $nextPartitionEndIndexStack[$currentPartitionIndex + 1] = $nextPartitionEndIndexStack[$currentPartitionIndex];
                $nextPartitionEndIndexStack[$currentPartitionIndex] = $leftIndex;
                $currentPartitionIndex++;
                
                if (
                    ($nextPartitionEndIndexStack[$currentPartitionIndex] - $nextPartitionStartIndexStack[$currentPartitionIndex])
                    >
                    ($nextPartitionEndIndexStack[$currentPartitionIndex - 1] - $nextPartitionStartIndexStack[$currentPartitionIndex - 1])
                ) {
                    list(
                        $nextPartitionEndIndexStack[$currentPartitionIndex],
                        $nextPartitionStartIndexStack[$currentPartitionIndex],
                        $nextPartitionEndIndexStack[$currentPartitionIndex - 1],
                        $nextPartitionStartIndexStack[$currentPartitionIndex - 1]
                    ) = [
                        $nextPartitionEndIndexStack[$currentPartitionIndex - 1],
                        $nextPartitionStartIndexStack[$currentPartitionIndex - 1],
                        $nextPartitionEndIndexStack[$currentPartitionIndex],
                        $nextPartitionStartIndexStack[$currentPartitionIndex]
                    ];
                }
            }
            else {
                $currentPartitionIndex--;
            }
        }
    }

}
