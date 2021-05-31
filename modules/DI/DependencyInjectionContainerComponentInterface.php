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

namespace Norma\DI;

/**
 * Interface of a DI container's component.
 * 
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface DependencyInjectionContainerComponentInterface {
    
    /**
     * Gets the component.
     * 
     * @return mixed The component.
     */
    public function getComponent();
    
    /**
     * Sets the component.
     * 
     * @param mixed The component.
     * @return void
     */
    public function setComponent($component);
    
    /**
     * Tests whether the component is lazy or not.
     * 
     * @return bool True if the component is lazy, false otherwise.
     */
    public function isLazy();
    
    /**
     * Sets whether the component is lazy or not.
     * 
     * @param bool $lazy Whether the component should be lazy or not.
     * @return void
     */
    public function setLazy($lazy);
    
    /**
     * Gets the scope of a component.
     * 
     * @return int The int code of the scope of the component (a value of the enum-like class {@link DependencyInjectionContainerComponentScopeEnum}.
     */
    public function getScope();
    
    /**
     * Sets the scope of a component.
     * 
     * @param int $scope The int code of the scope of the component (a value of the enum-like class {@link DependencyInjectionContainerComponentScopeEnum}.
     */
    public function setScope($scope);
    
}
