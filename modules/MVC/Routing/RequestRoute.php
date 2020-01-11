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

namespace Norma\MVC\Routing;

use Norma\MVC\Routing\Constraint\RequestRouteConstraintsInterface;

/**
 * The implementation of a request route.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class RequestRoute implements RequestRouteInterface {
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $controller;
    
    /**
     * @var string
     */
    protected $controllerAction;
    
    /**
     * @var RequestRouteConstraintsInterface
     */
    protected $constraints;
    
    /**
     * Construct a new request route.
     * 
     * @param string $name The name of the route.
     * @param string $pattern The pattern of the route.
     * @param string $controller The controller associated with the route.
     * @param string|null $controllerAction The action of the controller associated with the route.
     * @param RequestRouteConstraintsInterface $requestRouteConstraints Constraints for the route.
     */
    public function __construct($name, $pattern, $controller, $controllerAction, RequestRouteConstraintsInterface $requestRouteConstraints) {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->controller = $controller;
        $this->controllerAction = $controllerAction;
        $this->constraints = $requestRouteConstraints;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * {@inheritdoc}
     */
    public function getControllerAction() {
        return $this->controllerAction;
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPattern() {
        return $this->pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints() {
        return $this->constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function setController($controller) {
        $this->controller = $controller;
    }

}