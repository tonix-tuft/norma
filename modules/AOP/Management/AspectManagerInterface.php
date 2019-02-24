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

namespace Norma\AOP\Management;

/**
 * The interface of an aspect manager lets creating aspects
 * and calling their methods easily.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface AspectManagerInterface {
    
    /**
     * Gets the instance of an aspect.
     * 
     * @param string $aspect A string representing the aspect component to create.
     * @return object An instance representing the aspect.
     */
    public function getAspect($aspect);
    
    /**
     * Calls a method on a previously created aspect and returns its result.
     * 
     * @param object $aspect The aspect.
     * @param string $method The name of the method of the aspect to call.
     * @param array $paramsMapInjection An optional map of parameters where the key is the name of the parameter and the value is the parameter
     *                                                          itself to use when calling the method.
     *                                                          This is particularly useful if the underlying implementation of this method uses a DI container.
     * @return mixed The result of the method execution.
     */
    public function callAspectMethod($aspect, $method, $paramsMapInjection = []);
    
}
