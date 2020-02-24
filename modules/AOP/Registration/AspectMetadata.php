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

namespace Norma\AOP\Registration;

use Norma\AOP\Registration\AspectMetadataInterface;

/**
 * Implementation of aspect metadata.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class AspectMetadata implements AspectMetadataInterface {
    
    /**
     * @var string
     */
    protected $aspect;
    
    /**
     * @var array
     */
    protected $pointcuts;
    
    /**
     * @var array
     */
    protected $pointcutAdvicesMap;
    
    /**
     * Constructs a new aspect metadata.
     * 
     * @param string $aspect The aspect.
     * @param array $pointcuts The aspect's pointcuts, indexed by the name of the pointcut.
     * @param array $pointcutAdvicesMap A map where each key is the name of a pointcut of the aspect and each value
     *                                                          is a bidimensional array having an advice type as the key and an array of advices to execute
     *                                                          should that pointcut match as the value.
     */
    public function __construct($aspect, $pointcuts, $pointcutAdvicesMap) {
        $this->aspect = $aspect;
        $this->pointcuts = $pointcuts;
        $this->pointcutAdvicesMap = $pointcutAdvicesMap;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAspect() {
        return $this->aspect;
    }

    /**
     * {@inheritdoc}
     */
    public function getPointcutAdvicesMap() {
        return $this->pointcutAdvicesMap;
    }

    /**
     * {@inheritdoc}
     */
    public function getPointcuts() {
        return $this->pointcuts;
    }

}
