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

namespace Norma\Data\Structure\Tree\Trie;

/**
 * A trait for tries.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
trait TrieTrait {
    
    /**
     * @var bool
     */
    protected $isInTraversingProcess = FALSE;

    /**
     * {@link Norma\Data\Structure\Tree\Trie\TrieInterface::set()}
     */
    public function set($branch, $trieNode = NULL, $value = NULL) {
        $startTrieNode = $this->startTrieNodeFromEventualTrieNode($trieNode);
        $this->isInTraversingProcess = TRUE;
        $branches = $this->normalizeBranch($branch);
        
        $leaves = [];
        
        $c = 0;
        foreach ($branches as $branchToSet) {
            $c++;
            $len = $this->branchLength($branchToSet);
            
            /* @var $currentTrieNode TrieNodeInterface */
            $currentTrieNode = $startTrieNode;
            for ($i = 0; $i < $len; $i++) {
                $key = $branchToSet[$i];
                $currentTrieNode[$key] = NULL;
                $currentTrieNode = $currentTrieNode[$key];
            }
            $currentTrieNode->setValue($value);
            $leaves[] = $currentTrieNode;
        }
        
        $this->isInTraversingProcess = FALSE;
        
        if ($c == 0) {
            // No leaves, returning the starting trie node.
            return $startTrieNode;
        }
        else if ($c == 1) {
            // There's only one leaf (therefore only one branch was set).
            return $leaves[0];
        }
        else {
            // More than one branch was set on the trie, returning the leaves of these branches.
            return $leaves;
        }
    }
    
    /**
     * {@link Norma\Data\Structure\Tree\Trie\TrieInterface::get()}
     */
    public function get($branch, $trieNode = NULL) {
        $startTrieNode = $this->startTrieNodeFromEventualTrieNode($trieNode);
        $this->isInTraversingProcess = TRUE;
        $branches = $this->normalizeBranch($branch);
        
        $normalizedBranch = $branches[0];
        $len = $this->branchLength($normalizedBranch);
        
        /* @var $currentTrieNode TrieNodeInterface */
        $currentTrieNode = $startTrieNode;
        for ($i = 0; $i < $len; $i++) {
            $key = $normalizedBranch[$i];
            $currentTrieNode = $currentTrieNode[$key];
            if ($currentTrieNode === NULL) {
                $this->isInTraversingProcess = FALSE;
                return NULL;
            }
        }
        
        $this->isInTraversingProcess = FALSE;
        return $currentTrieNode;
    }

    /**
     * {@link Norma\Data\Structure\Tree\Trie\TrieInterface::unset()}
     */
    public function unset($branch, $trieNode = NULL) {
        $startTrieNode = $this->startTrieNodeFromEventualTrieNode($trieNode);
        $this->isInTraversingProcess = TRUE;
        $branches = $this->normalizeBranch($branch);
        
        $normalizedBranch = $branches[0];
        $len = $this->branchLength($normalizedBranch);
        
        /* @var $currentTrieNode TrieNodeInterface */
        $currentTrieNode = $startTrieNode;
        for ($i = 0; $i < $len - 1; $i++) {
            $key = $normalizedBranch[$i];
            $currentTrieNode = $currentTrieNode[$key];
            if ($currentTrieNode === NULL) {
                $this->isInTraversingProcess = FALSE;
                return;
            }
        }
        $lastKey = $normalizedBranch[$i];
        
        unset($currentTrieNode[$lastKey]);
        $this->isInTraversingProcess = FALSE;
    }

    /**
     * {@link Norma\Data\Structure\Tree\Trie\TrieInterface::exists()}
     */
    public function exists($branch, $trieNode = NULL) {
        $node = $this->get($branch, $trieNode);
        return $node instanceof TrieNodeInterface;
    }
    
    /**
     * {@link Norma\Data\Structure\Tree\Trie\TrieInterface::isInTraversingProcess()}
     */
    public function isInTraversingProcess(): bool {
        return $this->isInTraversingProcess;
    }
    
    /**
     * {@link Norma\Data\Structure\Tree\Trie\TrieNodeFactoryInterface::makeNewTrieNode()}
     */
    public function makeNewTrieNode($nodeValue = NULL): TrieNodeInterface {
        $node = new TrieNode($nodeValue);
        return $node;
    }
    
    /**
     * Determines the length of a branch.
     * 
     * @param string|array $branch A branch. Either a string or an array.
     * @return int The length of the branch.
     */
    protected function branchLength($branch) {
        return is_string($branch) ? strlen($branch) : count($branch);
    }
    
    /**
     * Normalizes the given branch or branches array or string returning an array of branches.
     * 
     * @param string|array<mixed>|array<array<mixed>> $branch A branch or multiple branches.
     * @return array<string>|array<array<mixed>> An array containing a single string if a string was given as a branch.
     *                                                                          An array of arrays of strings or integers if the given argument was an array of strings or integers.
     *                                                                          The original branch array if an array of arrays was given.
     */
    protected function normalizeBranch($branch) {
        if (
            is_string($branch)
            ||
            is_array($branch) && !is_array($branch[0])
        ) {
            return [$branch];
        }
        else {
            return $branch;
        }
    }
    
    /**
     * Returns the start trie node on which to start an operation on a branch.
     * 
     * @param TrieNodeInterface|null $trieNode A trie node or NULL.
     * @return TrieNodeInterface The start trie node.
     * @throws \RuntimeException If the given trie node is not a trie node.
     *                                               This method MAY be overridden to provide a fallback trie node in case `$trieNode` is NULL.
     */
    protected function startTrieNodeFromEventualTrieNode($trieNode = NULL): TrieNodeInterface {
        if ($trieNode instanceof TrieNodeInterface) {
            $startTrieNode = $trieNode;
        }
        else {
            throw new \RuntimeException(sprintf('Start trie node was not given to object of type "%s".', get_class($this)));
        }
        return $startTrieNode;
    }
    
}
