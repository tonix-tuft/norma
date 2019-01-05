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

use Norma\MVC\Routing\Constraint\RequestRouteConstraintsInterface;
use Norma\MVC\Routing\RequestRouteInterface;

/**
 * A request route factory interface of the Norma's routing module.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface RequestRouteFactoryInterface {
   
    /**
     * Makes a new request route.
     * 
     * @param string $routeName The name of the route.
     * @param string $pattern The pattern of the route.
     * @param string $controller The name of the controller associated with the route.
     * @param string|null $controllerAction The name of the controller's action. Null is a possible value too, in which case the controller is assumed
     *                                                             to be invokable through its `__invoke()` method.
     * @param RequestRouteConstraintsInterface $requestRouteConstraints The constraints of the route.
     * @return RequestRouteInterface The new request route.
     */
    public function makeRoute($routeName, $pattern, $controller, $controllerAction, $requestRouteConstraints); 
    
}
