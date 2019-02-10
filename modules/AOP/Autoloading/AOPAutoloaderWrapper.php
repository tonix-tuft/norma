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

namespace Norma\AOP\Autoloading;

use Norma\AOP\Autoloading\AOPAutoloaderWrapperInterface;
use Composer\Autoload\ClassLoader;

/**
 * The implementation of an AOP autoloader wrapper.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class AOPAutoloaderWrapper extends ClassLoader implements AOPAutoloaderWrapperInterface {
    
    /**
     * @var ClassLoader
     */
    protected $classLoader;
    
    /**
     * Constructs a new AOP autoloader wrapper.
     * 
     * @param ClassLoader $classLoader The wrapped class loader.
     */
    public function __construct(ClassLoader $classLoader) {
        $this->classLoader = $classLoader;
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadClass($class) {
        // TODO: complete
        $fileToInclude = $this->classLoader->findFile($class);
        if ($fileToInclude) {
            includeFile($fileToInclude);
            return TRUE;
        }
    }

}

/**
 * Includes a filename (scope isolated, like the Composer's {@link ClassLoader}).
 * 
 * @param string $file Filename to include.
 */
function includeFile($file) {
    if (function_exists('Composer\Autoload\includeFile')) {
        \Composer\Autoload\includeFile($file);
    }
    else {
        include $file;
    }
}