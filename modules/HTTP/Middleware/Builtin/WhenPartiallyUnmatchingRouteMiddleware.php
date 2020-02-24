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

namespace Norma\HTTP\Middleware\Builtin;

use Norma\HTTP\Request\Server\ServerRequestInterface;
use Norma\HTTP\Response\ResponseInterface;
use Norma\MVC\Routing\RequestRouterInterface;
use Norma\MVC\Routing\RequestRouteInterface;
use Norma\MVC\Routing\Constraint\RequestRouteConstraintInterface;
use Norma\HTTP\Response\Wrapper\ResponseWrapperFactoryInterface;
use Norma\MVC\Routing\RoutingException;
use Norma\HTTP\HTTPStatusCodeEnum;

/**
 * A builtin middleware to execute when there is a partially unmatching route.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class WhenPartiallyUnmatchingRouteMiddleware {
    
    /**
     * @var ResponseWrapperFactoryInterface
     */
    protected $responseWrapperFactory;
    
    /**
     * Constructs a new middleware.
     * 
     * @param ResponseWrapperFactoryInterface A response wrapper factory.
     */
    public function __construct(ResponseWrapperFactoryInterface $responseWrapperFactory) {
        $this->responseWrapperFactory = $responseWrapperFactory;
    }
    
    /**
     * Executes this middleware.
     * 
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The response built so far.
     * @param RequestRouterInterface $router The request router.
     * @param RequestRouteInterface $route The partially unmatched route.
     * @param RequestRouteConstraintInterface $routeConstraint The unsatisfied constraint.
     * @return ImmediateResponseWithThrowableWrapperInterface An immediate response wrapper with a bound throwable error or exception.
     */
    public function execute(ServerRequestInterface $request, ResponseInterface $response, RequestRouterInterface $router, RequestRouteInterface $route, RequestRouteConstraintInterface $routeConstraint) {
        return $this->responseWrapperFactory->makeImmediateResponseWithThrowableWrapper(
            $response->withStatus(HTTPStatusCodeEnum::BAD_REQUEST, HTTPStatusCodeEnum::$texts[HTTPStatusCodeEnum::BAD_REQUEST]),
            new RoutingException(sprintf('The route with name "%1$s" which matches the request has an unsatisfied constraint of type "%2$s."',
                $route->getName(),
                get_class($routeConstraint)
            ))
        );
    }
    
}
