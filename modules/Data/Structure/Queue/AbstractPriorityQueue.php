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

namespace Norma\Data\Structure\Queue;

use Norma\Data\Structure\Queue\PriorityQueueInterface;
use Norma\Algorithm\Sorting\BuiltinQuicksort;
use Norma\Algorithm\Sorting\SortingAlgorithmInterface;

/**
 * An abstract base class for priority queues which uses arrays internally.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
abstract class AbstractPriorityQueue implements PriorityQueueInterface {
    
    /**
     * @var int
     */
    protected $extractFlags;
    
    /**
     * @var int
     */
    protected $iterationMode;
    
    /**
     * @var int
     */
    protected $currentIndex;
    
    /**
     * @var SortingAlgorithmInterface 
     */
    protected $sortingAlgorithm;
    
    /**
     * @var array
     */
    protected $items;
    
    /**
     * @var int
     */
    protected $count;
    
    /**
     * @var int
     */
    protected $i;
    
    /**
     * Constructs a new priority queue.
     */
    public function __construct() {
        $this->sortingAlgorithm = $this->makeSortingAlgorithm();
        $this->items = [];
        $this->extractFlags = PriorityQueueInterface::EXTRACT_FLAG_DATA;
        $this->iterationMode = PriorityQueueInterface::ITERATION_MODE_DELETE;
        $this->count = 0;
        $this->i = 0;
    }
    
    /**
     * Factory method for the sorting algorithm to use for the sorting of the items of the queue in accordance to their priority.
     * 
     * @return SortingAlgorithmInterface The sorting algorithm to use.
     */
    protected function makeSortingAlgorithm(): SortingAlgorithmInterface {
        return new BuiltinQuicksort();
    }
    
    /**
     * Gets an item at the given index.
     * 
     * @param int $index The index of the item.
     * @return mixed The value or priority (or both) of the extracted item, depending on the extract flag.
     */
    protected function itemAtIndex($index) {
        $item = $this->items[$index];
        if ($this->extractFlags === PriorityQueueInterface::EXTRACT_FLAG_DATA) {
            return $item['data'];
        }
        else if ($this->extractFlags === PriorityQueueInterface::EXTRACT_FLAG_PRIORITY) {
            return $item['priority'];
        }
        else {
            unset($item['i']);
            return $item;
        }
    }
    
    /**
     * Throws an exception if the queue is empty.
     * 
     * @return void
     * @throws \RuntimeException If the queue is empty.
     */
    protected function throwExceptionIfEmpty() {
        if (empty($this->items)) {
            throw new \RuntimeException('The priority queue is empty.');
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function bottom() {
        $this->throwExceptionIfEmpty();
        return $this->itemAtIndex($this->count - 1);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int {
        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function current() {
        if ($this->iterationMode === PriorityQueueInterface::ITERATION_MODE_DELETE) {
            $item = $this->extract();
            $this->currentIndex--;
            return $item;
        }
        return $this->itemAtIndex($this->currentIndex);
    }

    /**
     * {@inheritdoc}
     */
    public function extract() {
        $item = $this->top();
        array_shift($this->items);
        $this->count--;
        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtractFlags(): int {
        return $this->extractFlags;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterationMode(): int {
        return $this->iterationMode;
    }

    /**
     * {@inheritdoc}
     */
    public function insert($value, $priority): bool {
        $this->items[] = [
            'data' => $value,
            'priority' => $priority,
            'i' => ++$this->i
        ];
        $this->count++;
        
        $this->sortingAlgorithm->sort($this->items, function($a, $b) {
            $priorityComparation = $this->compare($b['priority'], $a['priority']);
            if ($priorityComparation === 0) {
                return $a['i'] <=> $b['i'];
            }
            return $priorityComparation;
        });
        
        return TRUE;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool {
        return empty($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function key() {
        return $this->currentIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function next() {
        $this->currentIndex++;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind() {
        reset($this->items);
        $this->currentIndex = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtractFlags(int $flags) {
        $this->extractFlags = $flags;
    }

    /**
     * {@inheritdoc}
     */
    public function setIterationMode(int $mode) {
        $this->iterationMode = $mode;
    }

    /**
     * {@inheritdoc}
     */
    public function top() {
        $this->throwExceptionIfEmpty();
        return $this->itemAtIndex(0);
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool {
        return isset($this->items[$this->currentIndex]);
    }

}
