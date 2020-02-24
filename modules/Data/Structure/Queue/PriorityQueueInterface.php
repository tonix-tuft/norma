<?php

/*
 * Copyright(c) 2019 Anton Bagdatyev(Tonix)
 * 
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files(the "Software"), to deal in the Software without
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

/**
 * The interface of a priority queue data structure.
 *
 * @author Anton Bagdatyev(Tonix) <antonytuft@gmail.com>
 */
interface PriorityQueueInterface extends \Iterator, \Countable {
    
    /**
     * Extract flag for data only. Default.
     */
    const EXTRACT_FLAG_DATA = 1;
    
    /**
     * Extract flag for priority only.
     */
    const EXTRACT_FLAG_PRIORITY = 2;
    
    /**
     * Extract flag for both data and priority, as an associative array of two keys: `priority` and `data`.
     */
    const EXTRACT_FLAG_BOTH = 3;
    
    /**
     * Iteration mode which instructs the priority queue to delete its items from the queue while iterating. Default.
     */
    const ITERATION_MODE_DELETE = 1;
    
    /**
     * Iteration mode which instructs the priority queue to keep its items in the queue while iterating.
     */
    const ITERATION_MODE_KEEP = 2;
    
    /**
     * Compare priorities in order to place elements correctly.
     * 
     * @param mixed $priority1 The first priority.
     * @param mixed $priority2 The second priority.
     * @return int Positive integer if the first priority is greater than the second priority, 0 if they are equal, negative integer otherwise.
     */
    public function compare($priority1, $priority2): int;

    /**
     * Extracts a item from the top of the priority queue.
     * 
     * @return mixed The value or priority (or both) of the extracted item, depending on the extract flag.
     * @throws \RuntimeException If the queue does not have items to extract.
     */
    public function extract();
    
    /**
     * Gets the flags of extraction.
     * 
     * @return int The value of the extraction flags currently set.
     */
    public function getExtractFlags(): int;
    
    /**
     * Inserts an element in the queue.
     * 
     * @param mixed $value The value to insert.
     * @param mixed $priority The associated priority.
     * @return bool Returns TRUE.
     * @throws \Exception Implementors MAY throw an exception if they need to, e.g. if the priority argument must be of a particular type.
     */
    public function insert($value, $priority): bool;
    
    /**
     * Checks whether the queue is empty or not.
     * 
     * @return bool TRUE if the queue is empty, FALSE otherwise.
     */
    public function isEmpty(): bool;
    
    /**
     * Sets the flags of extraction.
     * 
     * Defines what is extracted by PriorityQueueInterface::current(), PriorityQueueInterface::top(), PriorityQueueInterface::bottom() and PriorityQueueInterface::extract().
     * 
     *     - Norma\Data\Structure\Queue\PriorityQueueInterface::EXTRACT_FLAG_DATA: Extract the data;
     *     - Norma\Data\Structure\Queue\PriorityQueueInterface::EXTRACT_FLAG_PRIORITY: Extract the priority;
     *     - Norma\Data\Structure\Queue\PriorityQueueInterface::EXTRACT_FLAG_BOTH: Extract an associative array containing both, with two keys, `priority` and `data`;
     * 
     * The default mode MUST be `Norma\Data\Structure\Queue\PriorityQueueInterface::EXTRACT_FLAG_DATA`.
     * 
     * @param int $flags The value of the extraction flags.
     * @return void
     */
    public function setExtractFlags(int $flags);
    
    /**
     * Peeks at the item from the top of the queue, i.e. the item with the highest priority.
     * 
     * @return mixed The value or priority (or both) of the top item, depending on the extract flag.
     */
    public function top();
    
    /**
     * Peeks at the item from the bottom of the queue, i.e. the item with the lowest priority.
     * 
     * @return mixed The value or priority (or both) of the bottom item, depending on the extract flag.
     */
    public function bottom();
    
    /**
     * Sets the iteration mode.
     * 
     * Specifies the behavior of the iterator (either one or the other):
     * 
     *     - Norma\Data\Structure\Queue\PriorityQueueInterface::ITERATION_MODE_DELETE: Elements are traversed and deleted by the iterator;
     *     - Norma\Data\Structure\Queue\PriorityQueueInterface::ITERATION_MODE_KEEP: Elements are traversed and kept by the iterator;
     * 
     * The default mode MUST be `Norma\Data\Structure\Queue\PriorityQueueInterface::ITERATION_MODE_DELETE`.
     * 
     * @param int $mode The iteration mode.
     * @return void
     */
    public function setIterationMode(int $mode);
    
    /**
     * Gets the iteration mode.
     * 
     * @return int The iteration mode.
     */
    public function getIterationMode();
    
}
