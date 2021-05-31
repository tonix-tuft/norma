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

namespace Norma\IPC;

/**
 * SimpleLockFileSupervisor helps managing lock files and control program execution flow depending on an existent
 * or missing lock file.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class SimpleLockFileSupervisor {
    
    /**
     * @var SimpleLockFile 
     */
    protected $lockFileObj;
    
    /**
     * Constructs a new `SimpleLockFileSupervisor`.
     * 
     * @param SimpleLockFile $lockFileObj A `SimpleLockFile` to use internally to implement a simple file locking system.
     */
    public function __construct(SimpleLockFile $lockFileObj) {
        $this->lockFileObj = $lockFileObj;
    }
    
    /**
     * Stop script's execution if lock file exists.
     * 
     * @param callable|NULL $beforeExitCallback If a callable is given and the program is about to exit, the callable will be executed before
     *                                                                      exiting and the PID of the lock file will be passed as an argument.
     * @return void
     */
    public function exitIfLockFileExists($beforeExitCallback = NULL) {
        if ($this->lockFileObj->exists()) {
            if (is_callable($beforeExitCallback)) {
                $beforeExitCallback($this->lockFileObj->getPID());
            }
            exit;
        }
    }

    /**
     * Create a lock file if it doesn't exist yet.
     * 
     * @param callable|NULL $afterLockFileCreationCallback Callback to execute upon creation. The result of the creation process will be passed as a parameter.
     * @return void
     */
    public function createLockFileIfLockFileDoesNotExist($afterLockFileCreationCallback = NULL) {
        if (!$this->lockFileObj->exists()) {
            $res = $this->lockFileObj->create();
            if (is_callable($afterLockFileCreationCallback)) {
                $afterLockFileCreationCallback($res);
            }
        }
    }
    
    /**
     * Clear the lock file if it exists.
     * 
     * @param callable|NULL $afterLockFileClearedCallback A callback to execute after an existent lock file is cleared.
     *                                                                                      The result of the clearing process will be passed as an argument.
     * @return void
     */
    public function clearLockFileIfLockFileExists($afterLockFileClearedCallback = NULL) {
        if ($this->lockFileObj->exists()) {
            $res = $this->lockFileObj->clear();
            if (is_callable($afterLockFileClearedCallback)) {
                $afterLockFileClearedCallback($res);
            }
        }
    }
    
    /**
     * Clear the lock file on shutdown if it exists.
     * 
     * @param callable|NULL $shutdownCallback A callback to execute after an existent lock file is cleared on shutdown.
     *                                                                     The callback is given the result of the clearing process as the first argument
     *                                                                     if the lock file existed.
     * @param callable|NULL $errorCallback A callback to execute if there was a PHP error (E_ERROR) during the script's execution.
     *                                                             The result of the PHP function `error_get_last` is given as an argument.
     * @return void
     */
    public function clearLockFileOnShutdownIfLockFileExists($shutdownCallback = NULL, $errorCallback = NULL) {
        $lockFileObj = $this->lockFileObj;
        register_shutdown_function(function() use (&$shutdownCallback, &$errorCallback, &$lockFileObj) {
            $error = error_get_last();
            if (!is_null($error) && $error['type'] === E_ERROR) {
                if (is_callable($errorCallback)) {
                    $errorCallback($error);
                }
            }
            if ($lockFileObj->exists()) {
                $res = $lockFileObj->clear();
                if (is_callable($shutdownCallback)) {
                    $shutdownCallback($res);
                }
            }
        });
    }
    
}
