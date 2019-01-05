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

namespace Norma\Core\Autoloading;

/**
 * Autoloader interface.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface AutoloaderInterface {
    
    /**
     * Registers the autoloader.
     * 
     * @return void
     */
    public function register();
    
    /**
     * Unegisters the autoloader.
     * 
     * @return void
     */
    public function unregister();
    
    /**
     * Autoloads a resource (class, interface or PHP trait). If the resoure doesn't exist, nothing happens.
     * 
     * @param string $class The fully qualified class name.  The term "class" refers to classes, interfaces, traits,
     *                                    and other similar structures.
     * @return void
     */
    public function autoload($class);
    
    /**
     * Adds namespaces. A namespace is a namespace prefix associated with a base directory within whom 
     * the resources are located.
     * 
     * @param array $namespaces Each argument must be an array with a namespace prefix at index 0 and
     *                                              the associated base directory at index 1.
     * @return void
     */
    public function addNamespaces(/* array $namespace, array $namespace, ... */);
    
    /**
     * Adds a namespace.
     * 
     * @param string $namespacePrefix The namespace prefix associated with a base directory. The namespace
     *                                                      prefix may be an empty string, and if it is, then the autoloader should
     *                                                      map the namespace to the directory structure inside of the backend directory
     *                                                      codebase of the framework.
     * @param string $baseDirectory The base directory within whom the resources are located.
     * @param bool $prepend If true, prepend the base directory to the stack instead of appending it.
     *                                      this causes the base directory to be searched first rather than last.
     * @return void
     */
    public function addNamespace($namespacePrefix, $baseDirectory, $prepend = false);
 
}