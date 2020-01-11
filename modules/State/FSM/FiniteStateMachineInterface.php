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

namespace Norma\State\FSM;

use Norma\State\FSM\DistributedTransitionLogicFiniteStateMachineInterface;

/**
 * The interface of a generic FSM (Finite-State Machine) with distributed transition logic.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface FiniteStateMachineInterface extends DistributedTransitionLogicFiniteStateMachineInterface {
    
    /**
     * Executes an iteration of this state machine with optional input.
     * 
     * @param mixed $input The input data for the current iteration or NULL if input is not required for the state machine to work properly.
     * @return void
     */
    public function process($input = NULL);
    
    /**
     * Registers a callable to execute when data are set with {@link DistributedTransitionLogicFiniteStateMachineInterface::setData()}
     * to the corresponding key.
     * 
     * @param string $key The key of the data.
     * @param callable $callable The callable to execute. Implementors MUST pass the following parameters to the callable:
     *                                           - `$data` (mixed): The new data set with {@link DistributedTransitionLogicFiniteStateMachineInterface::setData()};
     *                                           - `$oldData` (mixed): The old data corresponding to the given key;
     * @return void
     */
    public function onData($key, callable $callable);
    
}
