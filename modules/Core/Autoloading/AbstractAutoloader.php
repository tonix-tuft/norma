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

namespace Norma\Core\Autoloading;

/**
 * An abstract autoloader.
 * 
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
abstract class AbstractAutoloader implements AutoloaderInterface {
    
    const AUTOLOAD_METHOD = 'autoload';
    
    /**
     * Namespace separator.
     * 
     * @var string
     */
    protected $namespaceSeparator = '\\';
    
    /**
     * File extension for classes autoloaded by this autoloader.
     *
     * @var string
     */
    protected $fileExtension = '.php';

    /**
     * {@inheritdoc}
     */
    public function register() {
        spl_autoload_register([$this, static::AUTOLOAD_METHOD]);
    }

    /**
     * {@inheritdoc}
     */
    public function unregister() {
        spl_autoload_unregister([$this, static::AUTOLOAD_METHOD]);
    }

    /**
     * Sets the namespace separator used by classes in the namespace of this autoloader.
     * 
     * @param string $sep The separator to use.
     */
    public function setNamespaceSeparator($sep) {
        $this->namespaceSeparator = $sep;
    }
    /**
     * Gets the namespace seperator used by classes in the namespace of this autoloader.
     *
     * @return void
     */
    public function getNamespaceSeparator() {
        return $this->namespaceSeparator;
    }    
    
    /**
     * Sets the file extension of class files in the namespace of this autoloader.
     * 
     * @param string $fileExtension
     */
    public function setFileExtension($fileExtension) {
        $this->fileExtension = $fileExtension;
    }
    /**
     * Gets the file extension of class files in the namespace of this autoloader.
     *
     * @return string $fileExtension
     */
    public function getFileExtension() {
        return $this->fileExtension;
    }

    /**
     * Requires a file, if it exists.
     * 
     * @param string $fileName The name of the file to require if it exists.
     * @return bool True if the file exists and is required successfully, false otherwise.
     */
    protected function requireFile($fileName) {
        if (file_exists($fileName)) {
            require_once $fileName;
            return true;
        }
        return false;
    }
    
}

