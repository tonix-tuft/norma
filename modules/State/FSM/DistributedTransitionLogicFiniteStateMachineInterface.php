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

namespace Norma\State\FSM;

use Norma\State\FSM\StateInterface;

/**
 * A Finite-State Machine as seen by a state, therefore allowing distributed transition logic.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface DistributedTransitionLogicFiniteStateMachineInterface {
    
    /**
     * Changes the state of the state machine.
     * 
     * @param string|StateInterface $state The next state.
     * @return void
     */
    public function setState($state);
    
    /**
     * Sets data to the state machine.
     * 
     * @param string $key The key of the data.
     * @param mixed $data The data to pass to the state machine.
     *                                   If a callable is given, it MUST be called with an `$oldData` parameter containing the old data corresponding
     *                                   to the key or NULL if no data to the corresponding key have been set yet. The return value of the callable
     *                                   MUST be used as the value to set.
     * @return void
     */
    public function setData($key, $data);
    
    /**
     * Retrieves data from the state machine.
     * 
     * @param string $key The key of the data to retrieve.
     * @return mixed The data corresponding to the given key. NULL if no data is defined for the given key.
     */
    public function getData($key);
    
}
