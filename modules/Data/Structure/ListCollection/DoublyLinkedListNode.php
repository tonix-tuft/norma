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

namespace Norma\Data\Structure\ListCollection;

use Norma\Data\Structure\ListCollection\DoublyLinkedListNodeInterface;
use Norma\Data\Structure\ListCollection\ListNodeInterface;

/**
 * The implementation of a node of a doubly linked list.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class DoublyLinkedListNode implements DoublyLinkedListNodeInterface {
    
    /**
     * @var mixed
     */
    protected $element;
    
    /**
     * @var ListNodeInterface|null
     */
    protected $next;
    
    /**
     * @var ListNodeInterface|null
     */
    protected $prev;
    
    /**
     * Constructs a new node with the given element.
     * 
     * @param mixed $element The element of the node.
     * @param ListNodeInterface $prev The previous node of the node or NULL if none.
     * @param ListNodeInterface $next The next node of the node or NULL if none.
     */
    public function __construct($element, ListNodeInterface $prev = NULL, ListNodeInterface $next = NULL) {
        $this->element = $element;
        $this->prev = $prev;
        $this->next = $next;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getElement() {
        return $this->element;
    }

    /**
     * {@inheritdoc}
     */
    public function next() {
        return $this->next;
    }

    /**
     * {@inheritdoc}
     */
    public function prev() {
        return $this->prev;
    }

    /**
     * {@inheritdoc}
     */
    public function setElement($element) {
        $this->element = $element;
    }

    /**
     * {@inheritdoc}
     */
    public function setNext(ListNodeInterface $node = NULL) {
        $this->next = $node;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrev(ListNodeInterface $node = NULL) {
        $this->prev = $node;
    }

}
