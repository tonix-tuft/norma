<?php

/*
 * Copyright (c) 2020 Anton Bagdatyev (Tonix)
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
 * A class which implements the logic of a simple file locking system.
 * 
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class SimpleLockFile {
    
    const DEFAULT_LOCK_FILE_IDENTIFIER = 'lockfile';
    
    /**
     * @var string
     */
    protected $baseDir;
    
    /**
     * @var string
     */
    protected $lockFileIdentifier;
    
    /**
     * @var string
     */
    protected $lockFilePath;
    
    /**
     * Constructs a new `SimpleLockFile` object.
     * 
     * @param string $baseDir The base directory of the lock file.
     * @param string $lockFileIdentifier The lockfile prefix identifier.
     */
    public function __construct($baseDir, $lockFileIdentifier = self::DEFAULT_LOCK_FILE_IDENTIFIER) {
        $this->baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->lockFileIdentifier = self::DEFAULT_LOCK_FILE_IDENTIFIER . '_' . pathinfo(basename($lockFileIdentifier), PATHINFO_FILENAME);
        $this->lockFilePath = $this->baseDir . $this->lockFileIdentifier;
    }
    
    /**
     * Creates a lock file.
     * 
     * @param string $contents The contents of the lock file.
     * @return bool True if the file was successfully created. False otherwise.
     */
    public function create($contents = NULL) {
        return file_put_contents($this->lockFilePath, is_null($contents) ? getmypid() : $contents);
    }
    
    /**
     * Tests whether a lock file exists.
     * 
     * @return bool True if it exists, false otherwise.
     */
    public function exists() {
        return file_exists($this->lockFilePath);
    }
    
    /**
     * Clears an existent lock file.
     * 
     * @return bool True if the lock file was successfully deleted, false otherwise.
     */
    public function clear() {
        return unlink($this->lockFilePath);
    }
    
    /**
     * Gets the PID or contents of the lock file (in case the lock file was created with different contents).
     * 
     * @return string The content of the lock file.
     */
    public function getPID() {
        return file_get_contents($this->lockFilePath);
    }
    
}
