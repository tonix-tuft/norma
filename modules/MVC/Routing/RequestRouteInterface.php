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

namespace Norma\MVC\Routing;

/**
 * The interface of the request route of a request router.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface RequestRouteInterface {
   
    /**
     * Returns the controller associated with this route.
     * 
     * @return string The controller.
     */
    public function getController();
    
    /**
     * Sets the controller associated with this route.
     * 
     * @param string $controller The controller.
     * @return void
     */
    public function setController($controller);
    
    /**
     * Returns the action of the controller associated with this route.
     * 
     * @return string|null The action.
     */
    public function getControllerAction();
    
    /**
     * Returns the name of this route.
     * 
     * @return string The name of the route.
     */
    public function getName();
    
    /**
     * Returns the pattern of this route.
     * 
     * @return string The pattern of the route.
     */
    public function getPattern();
    
    /**
     * Returns the constraints of this route.
     * 
     * @return RequestRouteConstraintsInterface The constraints.
     */
    public function getConstraints();
    
}
