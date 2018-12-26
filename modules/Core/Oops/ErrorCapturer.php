<?php

/*
 * Copyright (c) 2018 Anton Bagdatyev
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

namespace Norma\Core\Oops;

use Norma\Core\Oops\ErrorCapturerInterface;
use Norma\Core\Oops\ErrorExceptionFactoryInterface;
use Norma\Core\Oops\ErrorExceptionFactory;

/**
 * The implementation of an error capturer.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class ErrorCapturer implements ErrorCapturerInterface {
    
    /**
     * @var bool
     */
    protected $registered;
    
    /**
     * @var array
     */
    protected $callables = [];
    
    /**
     * @var null|ErrorExceptionFactoryInterface
     */
    protected $errorExceptionFactory = null;
    
    /**
     * @var null|callable
     */
    protected $previousThrowableHandler = null;
    
    /**
     * @var null|callable
     */
    protected $previousErrorHandler = null;
    
    /**
     * @var bool
     */
    protected $isShutdown = false;
    
    /**
     * @var bool|string
     */
    protected static $emergencyMemory = false;
    
    /**
     * Registers the error capturer so that it starts to capture errors.
     * 
     * @return void
     */
    protected function register() {
        if (static::$emergencyMemory === false) {
            /*
             * Allocate some memory to deallocate it in case of a memory limit error.
             * 
             * @see https://stackoverflow.com/questions/8440439/safely-catch-a-allowed-memory-size-exhausted-error-in-php#answer-27581958
             */
            static::$emergencyMemory = str_repeat('*', 1024 * 1024);
        }
        
        $this->previousErrorHandler = set_error_handler([$this, 'handleErrorTakingPreviousIntoAccount']);
        
        $this->previousThrowableHandler = set_exception_handler([$this, 'handleUncaughtThrowable']);
        
        register_shutdown_function([$this, 'handleFatalErrorIfOccurred']);
        
        $this->registered = true;
    }
    
    /**
     * Handles an exception by creating it from the triggered E_* error type calling the previous error handler, if set.
     * 
     * @private
     * 
     * @param int $errno The first parameter, errno, contains the level of the error raised, as an integer.
     * @param string $errstr The second parameter, errstr, contains the error message, as a string.
     * @param string $errfile The third parameter is optional, errfile, which contains the filename that the error was raised in, as a string.
     * @param int $errline The fourth parameter is optional, errline, which contains the line number the error was raised at, as an integer.
     * @param array $errcontext The fifth parameter is optional, errcontext, which is an array that points to the active symbol table at
     *                                           the point the error occurred.
     *                                           In other words, errcontext will contain an array of every variable that existed in the scope the error
     *                                           was triggered in. User error handler must not modify error context.
     *                                           WARNING: This parameter has been DEPRECATED as of PHP 7.2.0. Relying on it is highly discouraged.
     *                                                             It is used here only for completeness.
     * @return bool|void If the function returns FALSE then the normal error handler continues.
     */
    public function handleErrorTakingPreviousIntoAccount($errno, $errstr, $errfile, $errline, $errcontext) {
        if ($this->previousErrorHandler) {
            call_user_func($this->previousErrorHandler, $errno, $errstr, $errfile, $errline, $errcontext);
        }
        
        return $this->handleError($errno, $errstr, $errfile, $errline);
    }
    
    /**
     * Handles an uncaught throwable exception or error.
     * 
     * @private
     * 
     * @param \Throwable $e The uncaught throwable exception or error.
     * @return void
     */
    public function handleUncaughtThrowable(\Throwable $e) {
        if ($this->previousThrowableHandler) {
            call_user_func($this->previousThrowableHandler, $e);
        }
        
        $this->handleThrowable($e);
    }
    
    /**
     * Checks for an error which cannot be handled by the handler set through `set_error_handler`
     * and handles it.
     * 
     * @private
     * 
     * @see http://php.net/manual/en/function.set-error-handler.php
     * 
     * @return void
     */
    public function handleFatalErrorIfOccurred() {
        /*
         * Freeing the memory rightaway so there is some SOS memory in case there was a memory limit error.
         */
        static::$emergencyMemory = null;
        $error = error_get_last();
        if (
            $error["type"] == E_ERROR
            ||
            $error["type"] == E_PARSE
            ||
            $error["type"] == E_CORE_ERROR
            ||
            $error["type"] == E_CORE_WARNING
            ||
            $error["type"] == E_COMPILE_ERROR
            ||
            $error["type"] == E_COMPILE_WARNING
            ||
            $error["type"] == E_STRICT
        ) {
            $this->isShutdown = true;
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
    
    /**
     * A factory method which creates an error exception factory.
     * 
     * @return ErrorExceptionFactoryInterface An error exception factory.
     */
    protected function makeErrorExceptionFactory(): ErrorExceptionFactoryInterface {
        if (is_null($this->errorExceptionFactory)) {
            $this->errorExceptionFactory = new ErrorExceptionFactory();
        }
        return $this->errorExceptionFactory;
    }
    
    /**
     * Handles a throwable.
     * 
     * @param \Throwable $e The throwable exception or error to handle.
     */
    protected function handleThrowable(\Throwable $e) {
        foreach ($this->callables as $callable) {
            $callable($e);
        }
        if (!$this->isShutdown) {
            exit(255);
        }
    }
    
    /**
     * Handles an exception by creating it from the triggered E_* error type.
     * 
     * @param int $errno The first parameter, errno, contains the level of the error raised, as an integer.
     * @param string $errstr The second parameter, errstr, contains the error message, as a string.
     * @param string $errfile The third parameter is optional, errfile, which contains the filename that the error was raised in, as a string.
     * @param int $errline The fourth parameter is optional, errline, which contains the line number the error was raised at, as an integer.
     * @return bool|void If the function returns FALSE then the normal error handler continues.
     */
    protected function handleError($errno, $errstr, $errfile, $errline) {
        $errorReporting = error_reporting();
        if (
            // The error was suppressed with the `@` operator
            (0 === $errorReporting)
            ||
            // The error code is not included in error_reporting
            (!($errorReporting & $errno))
        ) {
            return false;
        }

        $errorExceptionFactory = $this->makeErrorExceptionFactory();
        $errorException = $errorExceptionFactory->makeErrorException($errno, $errstr, $errfile, $errline);
        
        $this->handleThrowable($errorException);
    }

    /**
     * {@inheritdoc}
     */
    public function onThrowable(callable $callable) {
        if (!$this->registered) {
            $this->register();
        }
        $this->callables[] = $callable;
    }

}
