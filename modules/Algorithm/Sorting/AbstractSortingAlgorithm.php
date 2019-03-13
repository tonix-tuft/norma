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

use Norma\Algorithm\Sorting\SortingAlgorithmInterface;

/**
 * Abstract base class for sorting algorithms.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
abstract class AbstractSortingAlgorithm implements SortingAlgorithmInterface {
    
    /**
     * Default comparator function.
     * 
     * @return callable A comparator callable function which takes two arguments and returns an integer less than,
     *                           equal to, or greater than zero if the first argument is considered to be respectively less than,
     *                           equal to, or greater than the second.
     */
    protected function defaultComparator() {
        return function($a, $b) {
            return $a <=> $b;
        };
    }
    
    /**
     * Obtains a comparator to use for sorting.
     * 
     * @param callable|null $comparator NULL for a default comparator, callable for a custom one.
     * @return callable The callable comparator to use for sorting.
     * @throws \InvalidArgumentException If the given comparator is not NULL and is not a callable.
     */
    protected function comparator($comparator) {
        $comparatorFn = NULL;
        if ($comparator === NULL) {
            $comparatorFn = $this->defaultComparator();
        }
        else {
            $this->throwExceptionIfInvalidComparator($comparator);
            $comparatorFn = $comparator;
        }
        return $comparatorFn;
    }
    
    /**
     * Throws an exception if the given comparator is not callable.
     * 
     * If this method does not throw an exception, the caller is guaranteed that the given
     * comparator is a callable.
     * 
     * @param mixed $comparator The comparator.
     * @return void
     * @throws \InvalidArgumentException If the given comparator is not callable.
     */
    protected function throwExceptionIfInvalidComparator($comparator) {
        if (!is_callable($comparator)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid argument for comparator function. Argument must be NULL to use the default comparator or a valid callable, "%s" given.',
                    is_object($comparator)
                    ?
                    get_class($comparator)
                    :
                    gettype($comparator)
                )
            );
        }
    }
    
}
