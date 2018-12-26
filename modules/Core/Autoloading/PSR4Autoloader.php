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

namespace Norma\Core\Autoloading;

/**
 * PHP PSR-4 autoloader concrete implementation.
 * 
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class PSR4Autoloader extends AbstractAutoloader {

    /**
     * Multidimensional array. The key is a string which represents a namespace prefix
     * and the value is another array of strings, where each string represents a base directory
     * containing classes, interfaces, traits or other similar structures for that namespace.
     *
     * @var array<string, array<string>>
     */
    protected $namespacePrefixes = [];
    
    /**
     * {@inheritdoc}
     */
    public function addNamespace($prefix, $baseDir, $prepend = false) {
        
        // Normalize namespace prefix so that it doesn't have a namespace separator as the first or last character.
        $prefix = trim($prefix, $this->namespaceSeparator);

        // Normalize the base directory so that it doesn't have a trailing directory separator
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR);

        // Initialize the namespace prefix array
        if (isset($this->namespacePrefixes[$prefix]) === false) {
            $this->namespacePrefixes[$prefix] = [];
        }

        // Retain the base directory for the namespace prefix
        if ($prepend) {
            array_unshift($this->namespacePrefixes[$prefix], $baseDir);
        } 
        else {
            array_push($this->namespacePrefixes[$prefix], $baseDir);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function addNamespaces() {
        $namespaces = func_get_args();
        foreach ($namespaces as $namespace) {
            list($prefix, $baseDir) = $namespace;
            $this->addNamespace($prefix, $baseDir);
        }
    }    
    
    /**
     * {@inheritdoc}
     */
    public function autoload($class) {
        // Normalize the fully qualified class name prefix.
        $class = ltrim($class, $this->namespaceSeparator);
        $FQPrefix = $this->namespaceSeparator . $class;
        
        while (($pos = strrpos($FQPrefix, $this->namespaceSeparator)) !== false) {
            // Prefix with the beginning namespace separator without the trailing namespace separator.
            $FQPrefix = substr($FQPrefix, 0, $pos);
            
            // Prefix to search for.
            $prefix = ltrim($FQPrefix, $this->namespaceSeparator);
            
            // Class relative to its current prefix.
            $relativeClass = substr($class, $pos);
            if (isset($this->namespacePrefixes[$prefix]) !== false) {
                foreach ($this->namespacePrefixes[$prefix] as $baseDir) {
                    $fileName = $baseDir . '/' . str_replace($this->namespaceSeparator, '/', $relativeClass) . $this->fileExtension;                    
                    if ($this->requireFile($fileName)) {
                        return;
                    }
                }
            }
        }        
    }

}
