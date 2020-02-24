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

namespace Norma\MVC\Routing\Constraint;

/**
 * An implementation of the {@link RequestRouteConstraintsInterface}
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class RequestRouteConstraints implements RequestRouteConstraintsInterface {
   
    /**
     * @var array<RequestRouteConstraintInterface>
     */
    protected $constraints = [];
    
    /**
     * @var int
     */
    protected $i = 0;
    
    /**
     * {@inheritdoc}
     */
    public function current() {
        return $this->constraints[$this->i];
    }
    
    /**
     * {@inheritdoc}
     */
    public function key() {
        return $this->i;
    }

    /**
     * {@inheritdoc}
     */
    public function next() {
        $this->i++;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind() {
        $this->i = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function valid() {
        return isset($this->constraints[$this->i]);
    }

    /**
     * Adds a constraint.
     * 
     * @param RequestRouteConstraintInterface $constraint The constraint to add
     * @return void
     */
    public function add(RequestRouteConstraintInterface $constraint) {
        $this->constraints[] = $constraint;
    }

}
