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

namespace Norma\HTTP\Middleware\Builtin;

use Norma\HTTP\HTTPStatusCodeEnum;
use Norma\MVC\Routing\RoutingException;
use Norma\HTTP\Response\ResponseInterface;
use Norma\MVC\Routing\RequestRouterInterface;
use Norma\HTTP\Request\Server\ServerRequestInterface;
use Norma\HTTP\Response\Wrapper\ResponseWrapperFactoryInterface;

/**
 * A builtin middleware to execute when there isn't a matching route.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class WhenNoMatchingRouteMiddleware {
    
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
     * @return ImmediateResponseWithThrowableWrapperInterface An immediate response wrapper with a bound throwable error or exception.
     */
    public function execute(ServerRequestInterface $request, ResponseInterface $response, RequestRouterInterface $router) {
        return $this->responseWrapperFactory->makeImmediateResponseWithThrowableWrapper(
            $response->withStatus(HTTPStatusCodeEnum::NOT_FOUND, HTTPStatusCodeEnum::$texts[HTTPStatusCodeEnum::NOT_FOUND]),
            new RoutingException("There isn't a route which matches the request.")
        );
    }
    
}
