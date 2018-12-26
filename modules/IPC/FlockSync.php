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

namespace Norma\IPC;

/**
 * A component which implements an advisory lock system using the PHP `flock` function 
 * which can be used to synchronize the access to a shared resource using lock files.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class FlockSync {
    
    /**
     * Synchronize the execution of a code specified by `$callback`.
     * 
     * @param string $lockFile The name of a file which will be used as a shared lock file among processes.
     * @param callable|null $callback A callable to call which will be the synchronizes code to execute.
     * @param string $lockFileContents The optional contents to write within the lock file.
     * @param bool $nonBlocking Whether the process which is waiting to acquire the lock held by another process
     *                                             should wait or not. By default it will wait.
     * @param callable|null $nonBlockingCallback In case `$nonBlocking` is true, `$nonBlockingCallback` will be executed
     *                                                                       if the exclusive lock cannot be acquired.
     * @return void
     */
    public function synchronize($lockFile, $callback, $lockFileContents = NULL, $nonBlocking = FALSE, $nonBlockingCallback = NULL) {
        $fp = fopen($lockFile, "w");
        $exclusiveLockOperation = LOCK_EX;
        if (flock($fp, $nonBlocking ? $exclusiveLockOperation | LOCK_NB : $exclusiveLockOperation)) {
            $closure = \Closure::bind(function() use (&$fp) {
                if (is_resource($fp) && get_resource_type($fp) === 'file') {
                    $this->unlockHandle();
                }
            }, $this, self::class);
            register_shutdown_function($closure);
            if (!is_null($lockFileContents)) {
                fwrite($fp, $lockFileContents);
            }
            
            try {
                $callback();
            }
            catch (\Throwable $e) {
                throw $e;
            }
            
            $this->unlockHandle($fp);
        }
        else if ($nonBlocking && is_callable($nonBlockingCallback)) {
            $nonBlockingCallback();
        }
    }
    
    /**
     * Unlocks a previously locked resource.
     * 
     * @param resource $fp
     * @return void
     */
    protected function unlockHandle($fp) {
        flock($fp, LOCK_UN);
        fclose($fp);
    }
    
}