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

/**
 * The marker (tag) interface of a list.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface ListInterface extends \Iterator {
    
    /**
     * Pushes an element at the end of the list.
     * 
     * @param mixed $element The element.
     * @return ListNodeInterface The node representing the element.
     */
    public function push($element): ListNodeInterface;
    
    /**
     * Adds an element at the beginning of the list.
     * 
     * @param mixed $element The element.
     * @return ListNodeInterface The node representing the element.
     */
    public function unshift($element): ListNodeInterface;
    
    /**
     * Gets the first node of the list.
     * 
     * @return ListNodeInterface|null The node representing the element or NULL if none exists.
     */
    public function first(): ListNodeInterface;
    
    /**
     * Gets the last node of the list.
     * 
     * @return ListNodeInterface The node representing the element or NULL if none exists.
     */
    public function last(): ListNodeInterface;
    
    /**
     * Removes an element from the list given the node representing that element.
     * 
     * @param ListNodeInterface $node The node to remove.
     * @return void
     * @throws \OutOfBoundsException If the given node does not exist in the list.
     */
    public function remove(ListNodeInterface $node);
    
    /**
     * Insert a new element after the given node.
     * 
     * @param ListNodeInterface $node The node after which to insert the element.
     * @param mixed $element The element.
     * @return ListNodeInterface The new inserted node representing the element.
     * @throws \OutOfBoundsException If the given node does not exist in the list.
     */
    public function insertAfter(ListNodeInterface $node, $element): ListNodeInterface;
    
    /**
     * Insert a new element before the given node.
     * 
     * @param ListNodeInterface $node The node before which to insert the element.
     * @param mixed $element The element.
     * @return ListNodeInterface The new inserted node representing the element.
     * @throws \OutOfBoundsException If the given node does not exist in the list.
     */
    public function insertBefore(ListNodeInterface $node, $element): ListNodeInterface;
    
    /**
     * Returns the number of elements in the list.
     * 
     * @return int The number of elements in the list.
     */
    public function count(): int;
    
    /**
     * Tests if the list is empty.
     * 
     * @return bool TRUE if the list is empty, FALSE otherwise.
     */
    public function isEmpty(): bool;
    
    /**
     * Tests if the given node exists in the list.
     * 
     * @param ListNodeInterface $node The node.
     * @return bool TRUE if the node exists, FALSE otherwise.
     */
    public function has(ListNodeInterface $node): bool;
    
}
