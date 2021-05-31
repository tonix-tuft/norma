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

use Norma\Data\Structure\Tree\Trie\TrieNodeInterface;
use Norma\Data\Structure\Tree\Trie\TrieNodeFactoryInterface;

/**
 * The interface of a trie tree.
 * 
 * Implementors MUST assure that the method {@link Norma\Data\Structure\Tree\Trie::set()} behaves in the same way
 * as the direct array access on instances of a trie (which would trigger {@link Norma\Data\Structure\Tree\Trie::offsetSet()})
 * if {@link Norma\Data\Structure\Tree\Trie::set()} is called without a node reference.
 * This means that {@link Norma\Data\Structure\Tree\Trie::offsetSet()} MUST behave like {@link Norma\Data\Structure\Tree\Trie::set()}.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface TrieInterface extends \ArrayAccess, TrieNodeFactoryInterface {
    
    /**
     * Sets a branch on this trie.
     * 
     * @param string|array<mixed>|array<array<mixed>> $branch A branch or multiple branches. This parameter can be:
     * 
     *                                                                                                  - A string, forming a single branch (each character of the string will form a subsequent node of the branch);
     *                                                                                                  - An array of strings or integers (mixed), forming a single branch (each string or integer of the array will form a subsequent node of the branch);
     *                                                                                                  - An array of arrays of strings or integers (mixed), each inner array forming a branch (each string or integer of the inner array
     *                                                                                                     will form a subsequent node of the branch);
     * 
     *                                                                                                  E.g.:
     * 
     *                                                                                                    // Let's assume `$trie` is an instance of an implementation of {@link Norma\Data\Structure\Tree\Trie} and that it is empty
     *                                                                                                    // (has no branches yet).
     *                                                                                                    $trie->set([['a', 'b', 'c'], ['a', 'b', 'cd', 123, 'ef', 456, 'g']]);
     * 
     *                                                                                                  Calling set on the trie with the given array MUST create the following branches:
     * 
     *                                                                                                                            root node
     *                                                                                                                                   |
     *                                                                                                                                   a
     *                                                                                                                                   |
     *                                                                                                                                   b
     *                                                                                                                                  / \
     *                                                                                                     branch 1 -----> c   cb
     *                                                                                                                                       |
     *                                                                                                                                     123
     *                                                                                                                                       |
     *                                                                                                                                      ef
     *                                                                                                                                       |
     *                                                                                                                                     456
     *                                                                                                                                       |
     *                                                                                                                                       g <----- branch 2
     * 
     * @param TrieNodeInterface|null $trieNode A trie node to use as the starting point below which to set the given branch or branches.
     *                                                                   If NULL, implementors MUST use the root node of the trie as the starting node.
     * @param mixed $value An eventual value to associate to each branch or NULL if no value is should be set (a value of NULL will only set the branch
     *                                     if it does not exist and will not override any eventual value if the same branch has been set before with a leaf value).
     * @return TrieNodeInterface|array<TrieNodeInterface> The leaf trie node which may be used as a node reference for further calls to this method.
     *                                                                                      If multiple branches were set, this method MUST return an array with all of the leaf trie nodes for each branch which was set.
     */
    public function set($branch, $trieNode = NULL, $value = NULL);
    
    /**
     * Returns a node of the trie at the given branch.
     * 
     * @param string|array<mixed> $branch A branch. This parameter can be:
     * 
     *                                                                               - A string, forming a single branch (each character of the string will form a subsequent node of the branch);
     *                                                                               - An array of strings or integers (mixed), forming a single branch (each string or integer of the array will form a subsequent node of the branch);
     * 
     * @param TrieNodeInterface|null $trieNode A trie node to use as the starting point below which to retrieve the trie node at the given branch.
     *                                                                   If NULL, implementors MUST use the root node of the trie as the starting node.
     * @return TrieNodeInterface|null The trie node or NULL if the given branch does not exist.
     */
    public function get($branch, $trieNode = NULL);
    
    /**
     * Unsets a node at the given branch removing all the further down branches if the leaf specified in the branch is not a leaf trie node.
     * 
     * If the branch does not exist in the trie, implementors MUST do nothing. This means that they MUST NOT throw any exception, etc... .
     * 
     * @param string|array<mixed> $branch A branch. This parameter can be:
     * 
     *                                                                               - A string, forming a single branch (each character of the string will form a subsequent node of the branch);
     *                                                                               - An array of strings or integers (mixed), forming a single branch (each string or integer of the array will form a subsequent node of the branch);
     * 
     * @param TrieNodeInterface|null $trieNode A trie node to use as the starting point below which to remove the trie node at the given branch.
     *                                                                   If NULL, implementors MUST use the root node of the trie as the starting node.
     * @return void
     */
    public function unset($branch, $trieNode = NULL);
    
    /**
     * Tests if a trie node at the given branch in the trie.
     * 
     * @param string|array<mixed> $branch A branch. This parameter can be:
     * 
     *                                                                               - A string, forming a single branch (each character of the string will form a subsequent node of the branch);
     *                                                                               - An array of strings or integers (mixed), forming a single branch (each string or integer of the array will form a subsequent node of the branch);
     * 
     * @param TrieNodeInterface|null $trieNode A trie node to use as the starting point below which to check if the trie node at the given branch exists.
     *                                                                   If NULL, implementors MUST use the root node of the trie as the starting node.
     * @return bool TRUE if the trie node exists, FALSE otherwise.
     */
    public function exists($branch, $trieNode = NULL);
    
    /**
     * Tests whether the trie is in a traversing process, be it because of a branch being set, unset, etc... .
     * 
     * Implementors of this method MUST return TRUE if the trie is in a traversing process.
     * Otherwise, this method MUST return FALSE.
     * 
     * Implementors of this method MAY find it useful when implementing trie nodes ({@link TrieNodeInterface}).
     * 
     * @return bool TRUE if the trie is in a traversing process, FALSE otherwise.
     */
    public function isInTraversingProcess(): bool;
    
    /**
     * Gets all the nesting branches so far set on the trie.
     * 
     * @return \Iterator The branches of the trie. Each element of the returned iterator is a branch represented with an array
     *                             where each element is in turn a string or an integer forming the key of the node at that level of the branch.
     *                             E.g., for the following trie:
     * 
     *                                                       root node
     *                                                              |
     *                                                              a
     *                                                              |
     *                                                              b
     *                                                             / \
     *                                branch 1 -----> c   cb
     *                                                                  |
     *                                                                123
     *                                                                  |
     *                                                                 ef
     *                                                                  |
     *                                                                456
     *                                                                  |
     *                                                                  g <----- branch 2
     * 
     *                             This method MUST return an iterator leading to the following branches represented by the following arrays:
     *                             
     *                                ['a', 'b', 'c'] // First branch led by the iterator.
     *                                ['a', 'b', 'cd', 123, 'ef', 456, 'g'] // Second branch led by the iterator.
     */
    public function getAllNestingBranches(): \Iterator;
    
    /**
     * Gets a reference to the root node of the trie.
     * 
     * @return TrieNodeInterface The root node.
     */
    public function getRootNode(): TrieNodeInterface;
    
}
