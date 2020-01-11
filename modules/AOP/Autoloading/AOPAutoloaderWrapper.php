<?php

/*
 * Copyright (c) 2020 Anton Bagdatyev (Tonix-Tuft)
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
use Norma\AOP\FS\AOPEligiblePathDeterminerInterface;
use Norma\AOP\Stream\AOPFilenameStreamFilterRewriterInterface;
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
     * @var AOPEligiblePathDeterminerInterface
     */
    protected $AOPEligiblePathDeterminer;
    
    /**
     * @var AOPFilenameStreamFilterRewriterInterface
     */
    protected $AOPFilenameStreamFilterRewriter;
    
    /**
     * Constructs a new AOP autoloader wrapper.
     * 
     * @param ClassLoader $classLoader The Composer's class loader.
     * @param AOPEligiblePathDeterminerInterface $AOPEligiblePathDeterminer An AOP eligible path determiner.
     * @param AOPFilenameStreamFilterRewriterInterface $AOPFilenameStreamFilterRewriter An AOP filename stream filter rewriter.
     */
    public function __construct(ClassLoader $classLoader, AOPEligiblePathDeterminerInterface $AOPEligiblePathDeterminer, AOPFilenameStreamFilterRewriterInterface $AOPFilenameStreamFilterRewriter) {
        $this->classLoader = $classLoader;
        $this->AOPEligiblePathDeterminer = $AOPEligiblePathDeterminer;
        $this->AOPFilenameStreamFilterRewriter = $AOPFilenameStreamFilterRewriter;
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadClass($class) {
        $fileToInclude = $this->classLoader->findFile($class);
        if ($fileToInclude) {
            $fileToInclude = $this->passThroughAOPLayer($fileToInclude);
            includeFile($fileToInclude);
            return TRUE;
        }
    }
    
    /**
     * Pass the file to include through the AOP layer.
     * 
     * @param string $fileToInclude Path of the file to include.
     * @return string Path of the file to include.
     */
    protected function passThroughAOPLayer($fileToInclude) {
        $realPath = realpath($fileToInclude);
        $pathToReturn = $realPath;
        
        // TODO: Add cache.
        
        if ($this->AOPEligiblePathDeterminer->isPathEligible($pathToReturn)) {
            static $streamFilterWasRegistered = FALSE;
            if (!$streamFilterWasRegistered) {
                $streamFilterWasRegistered = TRUE;
                $this->AOPFilenameStreamFilterRewriter->register();
            }
            $pathToReturn = $this->AOPFilenameStreamFilterRewriter->rewrite($pathToReturn);
        }
        
        return $pathToReturn;
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