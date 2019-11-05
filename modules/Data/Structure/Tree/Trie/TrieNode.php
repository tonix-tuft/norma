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

namespace Norma\Data\Structure\Tree\Trie;

use Norma\Data\Structure\Tree\Trie\TrieNodeInterface;
use Norma\Data\Structure\Tree\Trie\TrieTrait;

/**
 * A trie node.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class TrieNode implements TrieNodeInterface {
    
    use TrieTrait;
    
    /**
     * @var mixed
     */
    protected $value;
    
    /**
     * @var array
     */
    protected $children;
    
    /**
     * Constructor.
     * 
     * @param mixed $value The value of this trie node.
     */
    public function __construct($value = NULL) {
        $this->value = $value;
        $this->children = [];
    }
    
    public function __destruct() {
        var_dump('Deallocating trie node');
    }
    
    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool {
        if (!$this->isInTraversingProcess()) {
            return $this->exists($offset, $this);
        }
        
        return array_key_exists($offset, $this->children);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset) {
        if (!$this->isInTraversingProcess()) {
            return $this->get($offset, $this);
        }
        
        return $this->children[$offset] ?? NULL;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value) {
        if (!$this->isInTraversingProcess()) {
            $this->set($offset, $this, $value);
            return;
        }
        
        if (!array_key_exists($offset, $this->children)) {
            $this->children[$offset] = $this->makeNewTrieNode();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset) {
        if (!$this->isInTraversingProcess()) {
            $this->unset($offset, $this);
            return;
        }
        
        unset($this->children[$offset]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getValue() {
        return $this->value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setValue($value) {
        $this->value = $value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isLeaf() {
        return empty($this->children);
    }
    
}
