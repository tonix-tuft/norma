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

use Norma\AOP\Autoloading\AOPAutoloaderWrapperFactoryInterface;
use Norma\AOP\Autoloading\AOPAutoloaderWrapper;
use Norma\AOP\FS\AOPEligiblePathDeterminerInterface;
use Norma\AOP\Stream\AOPFilenameStreamFilterRewriterInterface;
use Composer\Autoload\ClassLoader;

/**
 * The implementation of an AOP autoloader wrapper factory.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class AOPAutoloaderWrapperFactory implements AOPAutoloaderWrapperFactoryInterface {
    
    /**
     * @var AOPEligiblePathDeterminerInterface
     */
    protected $AOPEligiblePathDeterminer;
    
    /**
     * @var AOPFilenameStreamFilterRewriterInterface
     */
    protected $AOPFilenameStreamFilterRewriterInterface;
    
    /**
     * Constructs a new factory.
     * 
     * @param AOPEligiblePathDeterminerInterface $AOPEligiblePathDeterminer 
     * @param AOPEligiblePathDeterminerInterface $AOPEligiblePathDeterminer An AOP eligible path determiner.
     * @param AOPFilenameStreamFilterRewriterInterface $AOPFilenameStreamFilterRewriter An AOP stream filter rewriter.
     * @param AOPFilenameStreamFilterRewriterInterface $AOPFilenameStreamFilterRewriter
     */
    public function __construct(AOPEligiblePathDeterminerInterface $AOPEligiblePathDeterminer, AOPFilenameStreamFilterRewriterInterface $AOPFilenameStreamFilterRewriter) {
        $this->AOPEligiblePathDeterminer = $AOPEligiblePathDeterminer;
        $this->AOPFilenameStreamFilterRewriter = $AOPFilenameStreamFilterRewriter;
    }
    
    /**
     * {@inheritdoc}
     */
    public function makeAOPAutoloaderWrapper(ClassLoader $classLoader): AOPAutoloaderWrapperInterface {
        return new AOPAutoloaderWrapper($classLoader, $this->AOPEligiblePathDeterminer, $this->AOPFilenameStreamFilterRewriter);
    }

}
