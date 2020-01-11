<?php

/*
 * Copyright (c) 2020 Anton Bagdatyev (Tonix-Tuft)
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

namespace Norma\Data\Structure\ListCollection;

use Norma\Data\Structure\ListCollection\LinkedListNodeInterface;
use Norma\Data\Structure\ListCollection\ListNodeInterface;

/**
 * The interface of a node of a linked list which has a pointer to its next node.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface ForwardLinkedListNodeInterface extends LinkedListNodeInterface {
    
    /**
     * Gets the next node pointed by this one.
     * 
     * @return mixed The next node or NULL if none exists.
     */
    public function next();
    
    /**
     * Sets the next node pointed by this node.
     * 
     * @param ListNodeInterface|null $node The node. NULL can be passed to instruct the node that it does not have a next node.
     * @return void
     */
    public function setNext(ListNodeInterface $node = NULL);
    
}
