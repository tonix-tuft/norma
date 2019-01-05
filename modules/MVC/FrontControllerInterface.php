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

namespace Norma\MVC;

use Norma\HTTP\Request\Server\ServerRequestInterface;

/**
 * The interface of a front controller.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface FrontControllerInterface {
    
    /**
     * Run this front controller on the given request.
     * 
     * @param ServerRequestInterface $request The request.
     * @return void
     * @throws \Throwable If an exception or error occurs while processing the request.
     */
    public function run(ServerRequestInterface $request);
    
    /**
     * Handle an uncaught throwable.
     * As the method {@link FrontControllerInterface::run()} is the entry point method of an application
     * running within the web runtime, the front controller MUST give the caller an interface for handling uncaught exceptions and errors.
     * Implementors MUST implement this method where they handle such uncaught exceptions and errors.
     * Implementors MUST also be very careful when implementing this method because they SHOULD NOT throw exceptions and errors
     * within this method, otherwise they could compromise the work of an exception handler set to handle uncaught exceptions and errors
     * which in turn calls this method.
     * 
     * @param \Throwable $e A throwable error or exception.
     * @return void
     */
    public function handleThrowable(\Throwable $e);
    
    /**
     * Handles the termination of the application.
     * 
     * @return void
     */
    public function handleTermination();
    
}
