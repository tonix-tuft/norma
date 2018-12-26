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

namespace Norma\DI;

/**
 * An implementation of a DI container's component.
 * 
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class DependencyInjectionContainerComponent implements DependencyInjectionContainerComponentInterface{
    
    /**
     * @var string The component.
     */
    protected $component;
    
    /**
     * @var bool Whether it is lazy or not.
     */
    protected $lazy;
    
    /**
     * @var int The int code of the scope of the component (a value of the enum-like class {@link DependencyInjectionContainerComponentScopeEnum}.
     */
    protected $scope;
    
    /**
     * Constructs a new DI component from the given component.
     * 
     * @param mixed $component The component.
     */
    public function __construct($component) {
        $this->component = $component;
        $this->lazy = FALSE;
        $this->scope = FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function getComponent() {
        return $this->component;
    }

    /**
     * {@inheritdoc}
     */
    public function isLazy() {
        return $this->lazy;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getScope() {
        return $this->scope;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setLazy($lazy) {
        $this->lazy = $lazy;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setScope($scope) {
        $this->scope = $scope;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setComponent($component) {
        $this->component = $component;
        return $this;
    }

}
