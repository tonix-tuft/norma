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

namespace Norma\Data\Structure\Queue;

use Norma\Data\Structure\Queue\AbstractPriorityQueue;

/**
 * An integer priority queue implementation, i.e. where priorities are integers.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class IntegerPriorityQueue extends AbstractPriorityQueue {
    
    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException If the given priority is not an integer.
     */
    public function insert($value, $priority): bool {
        if (!is_int($priority)) {
            throw new \InvalidArgumentException(sprintf('The priority argument must of type "int", "%s" given.',
                is_object($priority) ? get_class($priority) : gettype($priority)
            ));
        }
        return parent::insert($value, $priority);
    }
    
    /**
     * {@inheritdoc}
     */
    public function compare($priority1, $priority2): int {
        return $priority1 <=> $priority2;
    }

}
