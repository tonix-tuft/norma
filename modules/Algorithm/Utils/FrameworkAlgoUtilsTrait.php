<?php

/*
 * Copyright (c) 2020 Anton Bagdatyev (Tonix)
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

namespace Norma\Algorithm\Utils;

/**
 * A trait containing useful methods concerning algorithms shared across multiple Norma's framework components.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
trait FrameworkAlgorithmUtilsTrait {
    
    /**
     * Merge the given array of segments and return a new array with groups of overlapping segments merged into one segment.
     * 
     * @param array $segmentsArray The segments array. Each element of the array MUST be an array of two elements:
     *                                                  the starting value of the segment at index 0 and the ending value of the segment at index 1.
     *                                                  The starting value SHOULD be less than or equal to the ending value.
     *                                                  If the starting value is greater than the ending value, these two values are switched before merging the segment.
     * @return array An array with the same structure as `$segmentsArray` is returned, though having overlapping segments merged into a single segment.
     */
    private function mergeSegments($segmentsArray) {
        if (empty($segmentsArray)) {
            return [];
        }
        
        foreach ($segmentsArray as &$segmentRef) {
            if ($segmentRef[0] > $segmentRef[1]) {
                $end = $segmentRef[1];
                $segmentRef[1] = $segmentRef[0];
                $segmentRef[0] = $end;
            }
        }
        
        usort($segmentsArray, function($a, $b) {
            return $a[0] - $b[0];
        });
        $first = array_shift($segmentsArray);
        $stack = new \SplStack();
        $stack->push($first);
        
        foreach ($segmentsArray as $segment) {
            $lastPushedSegment = $stack->offsetGet(0);            
            
            if ($segment[0] > $lastPushedSegment[1]) {
                $stack->push($segment);
            }
            else {
                $mergedSegment = [
                    $lastPushedSegment[0]
                ];
                if ($segment[1] > $lastPushedSegment[1]) {
                    $mergedSegment[1] = $segment[1];
                }
                else {
                    $mergedSegment[1] = $lastPushedSegment[1];
                }
                $stack->pop();
                $stack->push($mergedSegment);
            }
        }
        
        $merged = [];
        $c = $stack->count();
        $j = 0;
        for ($i = $c - 1; $i >= 0; $i--) {
            $merged[$j] = $stack->offsetGet($i);
            $j++;
        }
        return $merged;
    }
    
}
