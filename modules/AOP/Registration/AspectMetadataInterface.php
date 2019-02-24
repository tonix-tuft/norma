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

namespace Norma\AOP\Registration;

/**
 * An interface representing the metadata of an aspect.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface AspectMetadataInterface {
    
    /**
     * Gets the aspect.
     * 
     * @return string The aspect.
     */
    public function getAspect();
    
    /**
     * Gets the pointcuts. The key MUST be the name of the pointcut,
     * which can also be retrieved through the pointcut itself using {@link Norma\AOP\Pointcut\PointcutInterface::getName()}.
     * 
     * @return array The pointcuts.
     */
    public function getPointcuts();
    
    /**
     * Gets the pointcut advices map.
     * The returned array MUST have the following structure: each key of the array must be the name of a pointcut
     * which can also be retrieved through the pointcut itself using {@link Norma\AOP\Pointcut\PointcutInterface::getName()}
     * and each value is a bidimensional array where the key represents the type of advice ({@link Norma\AOP\Advice\AdviceTypeEnum})
     * and the value is an array of method names representing the advices of the aspect to execute should the pointcut match.
     * 
     * @return array The pointcut advices map.
     */
    public function getPointcutAdvicesMap();
    
}
