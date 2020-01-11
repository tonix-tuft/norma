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

namespace Norma\Middleware;

use Norma\DI\AbstractDependencyInjectionContainer;
use Norma\Middleware\MiddlewareLayerInterface;
use Norma\Middleware\MiddlewareFilterTrait;

/**
 * A trait for classes which execute middlewares.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
trait MiddlewareLayerExecutorTrait {
    
    use MiddlewareFilterTrait;
    
    /**
     * Executes the given middleware components.
     * 
     * @param array $middlewareComponents An array of middleware components to execute.
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @param array $params The array of parameters to unbox and pass to the middleware's `execute` method.
     * @return void|bool FALSE may be returned to instruct the caller to stop the execution (e.g. for web applications
     *                              it can be used to instruct the caller to terminate the request handling process and to immediately
     *                              send the response built so far to the client).
     */
    protected function executeMiddlewareComponents($middlewareComponents, AbstractDependencyInjectionContainer $container, $params = []) {
        foreach ($middlewareComponents as $middlewareComponent) {
            if (!$middlewareComponent) {
                // Falsy components are ignored (conditional middlewares).
                continue;
            }
            $middleware = $container->get($middlewareComponent);
            $ret = $this->handleMiddlewareReturnValue($middleware->execute(...$params));
            if ($ret === false) {
                return $ret;
            }
        }
    }
    
    /**
     * Executes a middleware layer with the given parameters.
     * 
     * @param int $middlewareLayerCode The integer code of the middleware layer to execute.
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @param array $params The array of parameters to unbox and pass to the middleware's `execute` method.
     * @return void|bool FALSE may be returned to instruct the caller to stop the execution (e.g. for web applications
     *                              it can be used to instruct the caller to terminate the request handling process and to immediately
     *                              send the response built so far to the client).
     */
    protected function executeMiddlewareLayer(MiddlewareLayerInterface $middlewareLayer, $middlewareLayerCode, AbstractDependencyInjectionContainer $container, $params = []) {
        $middlewares = $middlewareLayer->get($middlewareLayerCode);
        return $this->executeMiddlewareComponents($middlewares, $container, $params);
    }
    
    /**
     * Executes a middleware layer with the given parameters and "context" object.
     * 
     * @param int $middlewareLayer The integer code of the middleware layer to execute.
     * @param object $object The "context" object.
     * @param array $params The array of parameters to unbox and pass to each middleware's `execute` method.
     * @return void|bool FALSE may be returned to instruct the caller to stop the execution (e.g. for web applications
     *                              it can be used to instruct the caller to terminate the request handling process and to immediately
     *                              send the response built so far to the client).
     */
    protected function executeMiddlewareLayerWithContext(MiddlewareLayerInterface $middlewareLayer, $middlewareLayerCode, $object, AbstractDependencyInjectionContainer $container, $params = []) {
        $middlewares = $middlewareLayer->get($middlewareLayerCode);
        $middlewaresToExecute = $this->filterMiddlewaresWithContext($middlewares, $object);
        return $this->executeMiddlewareComponents($middlewaresToExecute, $container, $params);
    }
    
    /**
     * Handles the return value of a middleware.
     * 
     * @param mixed $middlewareReturnValue The middleware's return value. If FALSE is returned, then the execution of the current middleware layer will stop.
     * @return void
     */
    protected function handleMiddlewareReturnValue($middlewareReturnValue) {
        return $middlewareReturnValue;
    }
    
}
