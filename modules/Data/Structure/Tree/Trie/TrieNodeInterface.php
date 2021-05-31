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

namespace Norma\Data\Structure\Tree\Trie;

/**
 * The interface of a trie node.
 * 
 * Implementors MUST assure that array access operations on this objects are bound to the trie tree
 * which created the trie node represented by this interface.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface TrieNodeInterface extends \ArrayAccess {
    
    /**
     * Gets the value of the trie node.
     * 
     * @return mixed The value of the trie node. `NULL` MUST be returned in case no value has been set on the trie node.
     */
    public function getValue();
    
    /**
     * Sets the value of the trie node.
     * 
     * @param mixed $value The value of the trie node.
     * @return void
     */
    public function setValue($value);
    
    /**
     * Tests if the trie node is a leaf node.
     * 
     * @return bool TRUE if it is a leaf, FALSE otherwise.
     */
    public function isLeaf();
    
}
