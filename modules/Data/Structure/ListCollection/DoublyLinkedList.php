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

namespace Norma\Data\Structure\ListCollection;

use Norma\Data\Structure\ListCollection\ListNodeInterface;
use Norma\Data\Structure\ListCollection\DoublyLinkedListNodeInterface;

/**
 * The implementation of a doubly linked list with `O(1)` time for insertions and removals.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class DoublyLinkedList implements DoublyLinkedListInterface {
    
    /**
     * @var ListNodeInterface|null
     */
    protected $head;

    /**
     * @var ListNodeInterface|null
     */
    protected $tail;
    
    /**
     * @var \SplObjectStorage
     */
    protected $listMap;
    
    /**
     * @var int
     */
    protected $currentPosition;
    
    /**
     * @var ListNodeInterface|null
     */
    protected $current;
    
    /**
     * @var int
     */
    protected $iterationMode;
    
    /**
     * Constructs a new list.
     */
    public function __construct() {
        $this->head = NULL;
        $this->tail = NULL;
        $this->current = NULL;
        $this->listMap = new \SplObjectStorage();
        $this->currentPosition = 0;
    }
    
    /**
     * Throws an exception if the given node does not exist.
     * 
     * @param ListNodeInterface $node The node for which to throw the exception if it does not exist.
     * @throws \OutOfBoundsException The exception to throw if the node is not in the list.
     * @return void
     */
    protected function throwExceptionIfNodeDoesNotExist(ListNodeInterface $node) {
        if (!$this->has($node)) {
            throw new \OutOfBoundsException(sprintf('The node of type "%s" does not exist in the list.', get_class($node)));
        }
    }
    
    /**
     * Factory method which creates a new doubly linked list node for the given element.
     * 
     * @param mixed $element The element.
     * @return DoublyLinkedListNodeInterface The new node.
     */
    protected function makeNewNode($element): DoublyLinkedListNodeInterface {
        return new DoublyLinkedListNode($element);
    }
    
    /**
     * {@inheritdoc}
     */
    public function count(): int {
        return $this->listMap->count();
    }

    /**
     * {@inheritdoc}
     */
    public function first(): ListNodeInterface {
        if ($this->has($this->head)) {
            return $this->listMap[$this->head];
        }
        return NULL;
    }

    /**
     * {@inheritdoc}
     */
    public function insertAfter(ListNodeInterface $node, $element): ListNodeInterface {
        $this->throwExceptionIfNodeDoesNotExist($node);
        
        /* @var $doublyLinkedListNode DoublyLinkedListNodeInterface */
        $doublyLinkedListNode = $this->listMap[$node];
        $next = $doublyLinkedListNode->next();
        
        $newNode = $this->makeNewNode($element);
        $newNode->setPrev($doublyLinkedListNode);
        $newNode->setNext($next);
        
        $doublyLinkedListNode->setNext($newNode);
        
        $this->listMap[$newNode] = $newNode;
        
        return $newNode;
    }

    /**
     * {@inheritdoc}
     */
    public function insertBefore(ListNodeInterface $node, $element): ListNodeInterface {
        $this->throwExceptionIfNodeDoesNotExist($node);
        
        /* @var $doublyLinkedListNode DoublyLinkedListNodeInterface */
        $doublyLinkedListNode = $this->listMap[$node];
        $prev = $doublyLinkedListNode->prev();
        
        $newNode = $this->makeNewNode($element);
        $newNode->setNext($doublyLinkedListNode);
        $newNode->setPrev($prev);
        
        $doublyLinkedListNode->setPrev($newNode);
        
        $this->listMap[$newNode] = $newNode;
        
        return $newNode;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool {
        return $this->count() <= 0;
    }

    /**
     * {@inheritdoc}
     */
    public function last(): ListNodeInterface {
        if ($this->has($this->tail)) {
            return $this->listMap[$this->tail];
        }
        return NULL;
    }

    /**
     * {@inheritdoc}
     */
    public function push($element): ListNodeInterface {
        $newNode = $this->makeNewNode($element);
        
        if ($this->has($this->tail)) {
            /* @var $tailNode DoublyLinkedListNodeInterface */
            $tailNode = $this->listMap[$this->tail];
            $tailNode->setNext($newNode);
            $newNode->setPrev($tailNode);
        }
        $this->tail = $newNode;
        
        if (!$this->has($this->head)) {
            $this->head = $newNode;
        }
        
        $this->listMap[$this->tail] = $this->tail;
        
        return $newNode;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ListNodeInterface $node) {
        $this->throwExceptionIfNodeDoesNotExist($node);
        
        /* @var $doublyLinkedListNode DoublyLinkedListNodeInterface */
        $doublyLinkedListNode = $this->listMap[$node];
        
        /* @var $doublyLinkedListNode DoublyLinkedListNodeInterface|null */
        $next = $doublyLinkedListNode->next();
        
        /* @var $doublyLinkedListNode DoublyLinkedListNodeInterface|null */
        $prev = $doublyLinkedListNode->prev();
        
        if ($prev !== NULL) {
            $prev->setNext($next);
        }
        else if ($node === $this->head) {
            $this->head = $next;
        }
        
        if ($next !== NULL) {
            $next->setPrev($prev);
        }
        else if ($node === $this->tail) {
            $this->tail = $prev;
        }
        
        unset($this->listMap[$node]);
    }

    /**
     * {@inheritdoc}
     */
    public function unshift($element): ListNodeInterface {
        $newNode = $this->makeNewNode($element);
        
        if ($this->has($this->head)) {
            /* @var $headNode DoublyLinkedListNodeInterface */
            $headNode = $this->listMap[$this->head];
            $headNode->setPrev($newNode);
            $newNode->setNext($headNode);
        }
        $this->head = $newNode;
        
        if (!$this->has($this->tail)) {
            $this->tail = $newNode;
        }
        
        $this->listMap[$this->head] = $this->head;
        
        return $newNode;
    }

    /**
     * {@inheritdoc}
     */
    public function has(ListNodeInterface $node): bool {
        return isset($this->listMap[$node]);
    }

    /**
     * {@inheritdoc}
     */
    public function current() {
        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function key() {
        return $this->currentPosition;
    }

    /**
     * {@inheritdoc}
     */
    public function next() {
        $this->current = 
                $this->iterationMode === \SplDoublyLinkedList::IT_MODE_FIFO ?
                $this->current->next()
                :
                $this->current->prev();
        $this->currentPosition++;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind() {
        $this->current =
                $this->iterationMode === \SplDoublyLinkedList::IT_MODE_FIFO ?
                $this->head
                :
                $this->tail;
        $this->currentPosition = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool {
        return $this->has($this->current);
    }

    /**
     * {@inheritdoc}
     */
    public function setIterationMode($mode) {
        $this->iterationMode = $mode;
    }

}
