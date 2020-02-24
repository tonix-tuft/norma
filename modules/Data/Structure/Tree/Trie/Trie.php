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

namespace Norma\Data\Structure\Tree\Trie;

use Norma\Data\Structure\Tree\Trie\TrieInterface;
use Norma\Data\Structure\Tree\Trie\TrieNodeInterface;
use Norma\Data\Structure\Tree\Trie\TrieTrait;

/**
 * The implementation of a trie tree.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class Trie implements TrieInterface {
    
    use TrieTrait {
        startTrieNodeFromEventualTrieNode as protected trieTraitStartTrieNodeFromEventualTrieNode;
    }
    
    /**
     * @var TrieNodeInterface
     */
    protected $rootNode;
    
    /**
     * Constructs a new trie tree.
     */
    public function __construct() {
        $this->rootNode = $this->makeNewTrieNode();
    }
    
    public function __destruct() {
        var_dump('Deallocating trie');
    }
    
    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool {
        return isset($this->rootNode[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset) {
        return $this->rootNode[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value) {
        $this->rootNode[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset) {
        unset($this->rootNode[$offset]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAllNestingBranches(): \Iterator {
        // TODO
        yield from [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRootNode(): TrieNodeInterface {
        return $this->rootNode;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function startTrieNodeFromEventualTrieNode($trieNode = NULL): TrieNodeInterface {
        if ($trieNode === NULL) {
            $startTrieNode = $this->rootNode;
        }
        else {
            $startTrieNode = $trieNode;
        }
        return $this->trieTraitStartTrieNodeFromEventualTrieNode($trieNode);
    }

}
