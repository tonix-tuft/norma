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

namespace Norma\Data\Structure\ListCollection;

use Norma\Data\Structure\ListCollection\ListInterface;

/**
 * The interface of a doubly linked list.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface DoublyLinkedListInterface extends ListInterface {
    
    /**
     * LIFO iteration mode (stack style).
     */
    const ITERATION_MODE_LIFO = 1;
    
    /**
     * FIFO iteration mode (queue style).
     */
    const ITERATION_MODE_FIFO = 2;
    
    /**
     * Sets the iteration mode of the doubly linked list.
     * 
     * @param int $mode The iteration mode, i.e. either one or the other:
     *                                - Norma\Data\Structure\ListCollection\DoublyLinkedListInterface::ITERATION_MODE_LIFO (Stack style);
     *                                - Norma\Data\Structure\ListCollection\DoublyLinkedListInterface::ITERATION_MODE_FIFO (Queue style);
     * @return void
     */
    public function setIterationMode(int $mode);
    
    /**
     * Gets the iteration mode of the doubly linked list.
     * 
     * @return int The iteration mode.
     */
    public function getIterationMode();
    
}
