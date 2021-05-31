<?php

/*
 * Copyright (c) 2021 Anton Bagdatyev (Tonix)
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

namespace Norma\Data\Structure\Map;

use Norma\Data\Structure\ListCollection\ListInterface;
use Norma\Data\Structure\ListCollection\DoublyLinkedList;
use Norma\Data\Structure\ListCollection\ListNodeInterface;

/**
 * A map which maps objects to data maintaining the order in which the entries were put into it.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class LinkedObjectMap implements ObjectMapInterface {
    
    /**
     * @var \SplObjectStorage
     */
    protected $map;
    
    /**
     * @var ListInterface
     */
    protected $list;
    
    /**
     * @var int
     */
    protected $currentPosition;
    
    /**
     * Constructs a new map.
     */
    public function __construct() {
        $this->map = $this->makeMap();
        $this->list = $this->makeNodesList();
        $this->currentPosition = 0;
    }
    
    /**
     * Factory method which creates a new internal map for objects as keys.
     * 
     * @return \SplObjectStorage A new map.
     */
    protected function makeMap() {
        return new \SplObjectStorage();
    }
    
    /**
     * Factory method which creates a list to use for the nodes of the map.
     * 
     * @return ListInterface A new list.
     */
    protected function makeNodesList(): ListInterface {
        return new DoublyLinkedList();
    }
    
    /**
     * {@inheritdoc}
     */
    public function count(): int {
        return $this->map->count();
    }

    /**
     * {@inheritdoc}
     */
    public function current() {
        $current = $this->list->current();
        $nodeElement = $current->getElement();
        return $nodeElement['key'];
    }

    /**
     * {@inheritdoc}
     */
    public function get($object) {
        if ($this->has($object)) {
            /* @var $node ListNodeInterface */
            $node = $this->map[$object];
            $element = $node->getElement();
            return $element['data'];
        }
        return NULL;
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
        $this->list->next();
        $this->currentPosition++;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($object) {
        if ($this->has($object)) {
            $node = $this->map[$object];
            $this->list->remove($node);
            unset($this->map[$object]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind() {
        $this->list->rewind();
        $this->currentPosition = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function set($object, $data) {
        if (!$this->has($object)) {
            $node = $this->list->push([
                'key' => $object,
                'data' => $data
            ]);
            $this->map[$object] = $node;
        }
        else {
            $node = $this->map[$object];
            
            /* @var $node ListNodeInterface */
            $node->setElement([
                'key' => $object,
                'data' => $data
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool {
        return $this->list->valid();
    }
    
    /**
     * {@inheritdoc}
     */
    public function has($object) {
        return isset($this->map[$object]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool {
        return $this->has($offset);
    }
    
    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset) {
        return $this->get($offset);
    }
    
    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }
    
    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset) {
        $this->remove($offset);
    }

}
