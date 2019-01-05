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

namespace Norma\MVC;

use Norma\HTTP\Request\Server\ServerRequestInterface;
use Norma\HTTP\Response\ResponseInterface;
use Norma\MVC\Routing\RequestRouteInterface;
use Norma\HTTP\Middleware\MiddlewareLayerEnum;
use Norma\MVC\Routing\RequestRouterInterface;
use Norma\MVC\Routing\Constraint\RequestRouteConstraintInterface;

/**
 * Front controller implementation.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class FrontController extends AbstractMiddlewareAwareFrontController {
    
    /**
     * {@inheritdoc}
     */
    protected function executeMiddlewareLayerOnThrowable(ServerRequestInterface $request, ResponseInterface $response, \Throwable $e) {
        return $this->executeMiddlewareLayerWithContext($this->middlewareLayer, MiddlewareLayerEnum::ON_THROWABLE, $e, $this->container, func_get_args());
    }
    
    /**
     * {@inheritdoc}
     */
    protected function executeMiddlewareLayerBeforeRouteMatchingProcess(ServerRequestInterface $request, ResponseInterface $response) {
        return $this->executeMiddlewareLayer($this->middlewareLayer, MiddlewareLayerEnum::BEFORE_ROUTE_MATCHING_PROCESS, $this->container, func_get_args());
    }
    
    /**
     * {@inheritdoc}
     */
    protected function executeMiddlewareLayerAfterRouteMatchingProcess(ServerRequestInterface $request, ResponseInterface $response, $route) {
        return $this->executeMiddlewareLayer($this->middlewareLayer, MiddlewareLayerEnum::AFTER_ROUTE_MATCHING_PROCESS, $this->container, func_get_args());
    }
    
    /**
     * {@inheritdoc}
     */
    protected function executeMiddlewareLayerBeforeControllerExecution(ServerRequestInterface $request, ResponseInterface $response, RequestRouteInterface $route, $controller, $controllerAction) {
        return $this->executeMiddlewareLayerWithContext($this->middlewareLayer, MiddlewareLayerEnum::BEFORE_CONTROLLER_EXECUTION, $controller, $this->container, func_get_args());
    }
    
    /**
     * {@inheritdoc}
     */
    protected function executeMiddlewareLayerAfterControllerExecution(ServerRequestInterface $request, ResponseInterface $response, RequestRouteInterface $route, $controller, $controllerAction) {
        return $this->executeMiddlewareLayerWithContext($this->middlewareLayer, MiddlewareLayerEnum::AFTER_CONTROLLER_EXECUTION, $controller, $this->container, func_get_args());
    }
    
    /**
     * {@inheritdoc}
     */
    protected function executeMiddlewareLayerWhenNoMatchingRoute(ServerRequestInterface $request, ResponseInterface $response, RequestRouterInterface $router) {
        return $this->executeMiddlewareLayer($this->middlewareLayer, MiddlewareLayerEnum::WHEN_NO_MATCHING_ROUTE, $this->container, func_get_args());
    }
    
    /**
     * {@inheritdoc}
     */
    protected function executeMiddlewareLayerBeforeSendingResponse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $this->executeMiddlewareLayer($this->middlewareLayer, MiddlewareLayerEnum::BEFORE_SENDING_RESPONSE, $this->container, func_get_args());
        return $this->response;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function executeMiddlewareLayerAfterSendingResponse(ServerRequestInterface $request, ResponseInterface $response) {
        return $this->executeMiddlewareLayer($this->middlewareLayer, MiddlewareLayerEnum::AFTER_SENDING_RESPONSE, $this->container, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    protected function executeMiddlewareLayerWhenPartiallyUnmatchedRoute(ServerRequestInterface $request, ResponseInterface $response, RequestRouterInterface $router, RequestRouteInterface $route, RequestRouteConstraintInterface $routeConstraint) {
        return $this->executeMiddlewareLayer($this->middlewareLayer, MiddlewareLayerEnum::WHEN_PARTIALLY_UNMATCHED_ROUTE, $this->container, func_get_args());
    }
    
}
