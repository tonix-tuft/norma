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

/**
 * Interface of a sorting algorithm.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface SortingAlgorithmInterface {
    
    /**
     * Sorts an array using the given comparator.
     * The array MUST be reindexed starting from `0` up to `n - 1`, where `n` is the number of elements in the array.
     * 
     * @param array $array The array. The original array MUST be sorted and changed (reindexed).
     * @param callable|null $comparator An optional custom comparator function.
     *                                                        The comparator function is guaranteed to receive two elements of the array as arguments
     *                                                        and it must return an integer less than, equal to, or greater than zero if the first argument
     *                                                        is considered to be respectively less than, equal to, or greater than the second.
     *                                                        If NULL is given, implementors MUST compare items in ascending order directly comparing
     *                                                        the elements of the array using comparison operators (`>`, `=>`, `<=`, `<`).
     * @return void
     * @throws \InvalidArgumentException If the given comparator function is not NULL and is not a callable.
     */
    public function sort(array &$array, $comparator = NULL);
    
    /**
     * Sorts an array using the given comparator maintaining index association.
     * The array MUST not be reindexed.
     * 
     * @param array $array The array. The original array MUST be sorted and its indices/keys for the respective values MUST remain the same.
     * @param callable|null $comparator An optional custom comparator function.
     *                                                        The comparator function is guaranteed to receive two elements of the array as arguments
     *                                                        and it must return an integer less than, equal to, or greater than zero if the first argument
     *                                                        is considered to be respectively less than, equal to, or greater than the second.
     *                                                        If NULL is given, implementors MUST compare items in ascending order directly comparing
     *                                                        the elements of the array using comparison operators (`>`, `=>`, `<=`, `<`).
     * @return void
     * @throws \InvalidArgumentException If the given comparator function is not NULL and is not a callable.
     */
    public function asort(array &$array, $comparator = NULL);
    
    /**
     * Sorts an array by its keys using the given comparator.
     * 
     * @param array $array The array.
     * @param callable|null $comparator An optional custom comparator function.
     *                                                        The comparator function is guaranteed to receive two keys of the array as arguments
     *                                                        and it must return an integer less than, equal to, or greater than zero if the first argument
     *                                                        is considered to be respectively less than, equal to, or greater than the second.
     *                                                        If NULL is given, implementors MUST compare items in ascending order directly comparing
     *                                                        the keys of the array using comparison operators (`>`, `=>`, `<=`, `<`).
     * @return void
     * @throws \InvalidArgumentException If the given comparator function is not NULL and is not a callable.
     */
    public function ksort(array &$array, $comparator = NULL);
    
}
