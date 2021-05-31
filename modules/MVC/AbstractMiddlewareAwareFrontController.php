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

namespace Norma\MVC;

use Norma\HTTP\Request\Server\ServerRequestInterface;
use Norma\DI\AbstractDependencyInjectionContainer;
use Norma\MVC\Routing\RequestRouterInterface;
use Norma\MVC\Routing\RequestRouteInterface;
use Norma\MVC\Routing\Constraint\RequestRouteConstraintInterface;
use Norma\HTTP\Response\ResponseInterface;
use Norma\HTTP\Middleware\MiddlewareLayerInterface;
use Norma\HTTP\Response\Wrapper\ImmediateResponseWithThrowableWrapperInterface;
use Norma\HTTP\Response\Wrapper\ImmediateResponseWrapperInterface;
use Norma\HTTP\HTTPStatusCodeEnum;
use Norma\Middleware\MiddlewareLayerExecutorTrait;

/**
 * An abstract front controller which is also middleware-aware.
 * This default Norma's front controller is the only component which has access to the application's DI container.
 * Other components should not set the container as a dependency but should declare their dependencies explicitly
 * in the constructor, in their methods (MVC controllers) or eventually using Norma annotations.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
abstract class AbstractMiddlewareAwareFrontController implements FrontControllerInterface {
    
    use MiddlewareLayerExecutorTrait;

    /**
     * @var RequestRouterInterface
     */
    protected $router;
    
    /**
     * @var AbstractDependencyInjectionContainer
     */
    protected $container;
    
    /**
     * @var ServerRequestInterface
     */
    protected $request;
    
    /**
     * @var ResponseInterface
     */
    protected $response;
    
    /**
     * @var MiddlewareLayerInterface
     */
    protected $middlewareLayer;
    
    /**
     * Constructs a new front controller.
     * 
     * @param RequestRouterInterface $router A request router.
     * @param AbstractDependencyInjectionContainer $container The application's DI container.
     * @param ServerRequestInterface $request The server request.
     * @param ResponseInterface $response An initial response to use.
     * @param MiddlewareLayerInterface $middlewareLayer The middleware layer.
     */
    public function __construct(RequestRouterInterface $router, AbstractDependencyInjectionContainer $container, ServerRequestInterface $request, ResponseInterface $response, MiddlewareLayerInterface $middlewareLayer) {
        $this->router = $router;
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;
        $this->middlewareLayer = $middlewareLayer;
    }
    
    /**
     * Handles the return value of a middleware.
     * 
     * @param mixed $middlewareReturnValue The middleware's return value. Could be NULL, an array of {@link ServerRequestInterface} or {@link ResponseInterface}
     *                                                                 or a {@link ServerRequestInterface} or {@link ResponseInterface}.
     *                                                                 If FALSE is returned, then the execution of the current middleware layer will stop.
     * @return void
     * @throws \UnexpectedValueException In case of an unexpected value.
     */
    protected function handleMiddlewareReturnValue($middlewareReturnValue) {
        if (is_null($middlewareReturnValue)) {
            return;
        }
        if (is_array($middlewareReturnValue)) {
            return $this->handleMiddlewareArrayReturnValue($middlewareReturnValue);
        }
        else {
            return $this->handleMiddlewareSingleReturnValue($middlewareReturnValue);
        }
    }
    
    /**
     * Handles a middleware's array return value.
     * 
     * @param array $middlewareArrayReturnValue A middleware's array return value.
     * @return void
     * @throws \UnexpectedValueException In case of an unexpected value.
     */
    protected function handleMiddlewareArrayReturnValue($middlewareArrayReturnValue) {
        foreach ($middlewareArrayReturnValue as $middlewareSingleReturnValue) {
            $this->handleMiddlewareSingleReturnValue($middlewareSingleReturnValue);
        }
    }
    
    /**
     * Handles a middleware's return value.
     * 
     * @param ServerRequestInterface|ResponseInterface $middlewareSingleReturnValue A middleware's return value, either a server request or a response
     *                                                                                                                                  which should override the current one.
     * @return void|bool FALSE may be returned to instruct the caller to terminate the request handling process and to immediately send the response built so far to the client.
     * @throws \UnexpectedValueException In case of an unexpected value.
     * @throws \Throwable In case the given return value is an immediate response wrapper with a throwable error or exception.
     */
    protected function handleMiddlewareSingleReturnValue($middlewareSingleReturnValue) {
        if ($middlewareSingleReturnValue instanceof ImmediateResponseWithThrowableWrapperInterface) {
            $this->response = $middlewareSingleReturnValue->getResponse();
            
            // A response with a throwable wrapper causes the throwable to be thrown.
            throw $middlewareSingleReturnValue->getThrowable();
        }
        else if ($middlewareSingleReturnValue instanceof ImmediateResponseWrapperInterface) {
            $this->response = $middlewareSingleReturnValue->getResponse();
            
            // FALSE instructs the front controller to terminate the request handling process and send the response built so far.
            return false;
        }
        else if ($middlewareSingleReturnValue instanceof ServerRequestInterface) {
            $this->request = $this->overrideContainerServerRequest($middlewareSingleReturnValue);
        }
        else if ($middlewareSingleReturnValue instanceof ResponseInterface) {
            $this->response = $middlewareSingleReturnValue;
        }
        else {
            throw new \UnexpectedValueException(
                sprintf('Unexpected middleware return value of type "%s".', 
                    is_object($middlewareSingleReturnValue) ? get_class($middlewareSingleReturnValue) : gettype($middlewareSingleReturnValue)
                )
            );
        }
    }
    
    /**
     * Overrides the server request memorized within the container.
     * 
     * @param ServerRequestInterface $request The request.
     */
    protected function overrideContainerServerRequest(ServerRequestInterface $request) {
        $this->container->addConfig([
            'norma' => [
                'framework' => [
                    'http' => [
                        ServerRequestInterface::class => $request
                    ]
                ]
            ]
        ]);
        return $request;
    }
    
    /**
     * {@inheritdoc}
     */
    public function handleTermination() {
        // Norma's middleware layer 9.
        $this->executeMiddlewareLayerAfterSendingResponse($this->request, $this->response);
    }
    
    /**
     * {@inheritdoc}
     */
    public function run(ServerRequestInterface $request) {
        try {
            $this->request = $this->overrideContainerServerRequest($request);
            
            // Norma's middleware layer 2 (layer 1 is the error middleware).
            if ($this->handleMiddlewareReturnValue($this->executeMiddlewareLayerBeforeRouteMatchingProcess($this->request, $this->response)) !== false) {
                $route = $this->router->getMatchingRoute($this->request);

                // Norma's middleware layer 3.
                if ($this->handleMiddlewareReturnValue($this->executeMiddlewareLayerAfterRouteMatchingProcess($this->request, $this->response, $route)) !== false) {
                    if (!is_null($route)) {
                        $controllerComponent = $route->getController();
                        $controllerAction = $route->getControllerAction();
                        $controller = $this->container->get($controllerComponent);
                        $params = $this->router->getMatchedRouteParams();

                        // Norma's middleware layer 4.
                        if ($this->handleMiddlewareReturnValue($this->executeMiddlewareLayerBeforeControllerExecution($this->request, $this->response, $route, $controller, $controllerAction)) !== false) {
                            $controllerResponse = $this->container->call([$controller, $controllerAction], $params);
                            if ($controllerResponse instanceof ResponseInterface) {
                                $this->response = $controllerResponse;
                            }
                            else {
                                throw new \UnexpectedValueException(
                                    sprintf('The action "%s" of the controller "%s" must return a response object of type "%s".', 
                                        $controllerAction,
                                        get_class($controller),
                                        ResponseInterface::class
                                    )
                                );
                            }

                            // Norma's middleware layer 5.
                            $this->handleMiddlewareReturnValue($this->executeMiddlewareLayerAfterControllerExecution($this->request, $this->response, $route, $controller, $controllerAction));   
                        }
                    }
                    else {
                        $partiallyUnmatchedRoutes = $this->router->getPartiallyUnmatchedRoutes();
                        $executeLayerWhenNoMatchingRoute = true;
                        if (!empty($partiallyUnmatchedRoutes)) {
                            foreach ($partiallyUnmatchedRoutes as $route) {
                                $routeConstraint = $this->router->getUnsatisfiedConstraintForRoute($route);
                                if ($routeConstraint instanceof RequestRouteConstraintInterface) {
                                    $executeLayerWhenNoMatchingRoute = false;
                                    
                                    // Norma's middleware layer 6.
                                    $this->handleMiddlewareReturnValue($this->executeMiddlewareLayerWhenPartiallyUnmatchedRoute($this->request, $this->response, $this->router, $route, $routeConstraint));
                                }
                            }
                        }
                        
                        if ($executeLayerWhenNoMatchingRoute) {
                            // Norma's middleware layer 7.
                            $this->handleMiddlewareReturnValue($this->executeMiddlewareLayerWhenNoMatchingRoute($this->request, $this->response, $this->router));
                        }
                    }
                }
            }
            $this->sendResponse($this->request, $this->response);
        }
        catch (\Throwable $e) {
            // Uncaught exceptions will be handled by the Norma's global error capturer which in turn
            // will call {@link FrontControllerInterface::handleThrowable()}.
            throw $e;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function handleThrowable(\Throwable $e) {
        /*
         * In case there's a fatal error (e.g. an out of memory or maximum execution time fatal error during development)
         * or if an exception or an error occurs during the sending phase of the response and output buffering is disabled,
         * it would be impossible to set additional headers or override existent ones as they would already
         * have been sent.
         * 
         * In such cases, the front controller will not handle this exception or error as it could not resend a response.
         */
        if (!headers_sent()) {
            if (((int) ($this->response->getStatusCode() / 100)) === 2) {
                $this->response = $this->response->withStatus(HTTPStatusCodeEnum::INTERNAL_SERVER_ERROR, HTTPStatusCodeEnum::$texts[HTTPStatusCodeEnum::INTERNAL_SERVER_ERROR]);
            }
            $this->handleMiddlewareReturnValue($this->executeMiddlewareLayerOnThrowable($this->request, $this->response, $e));
            $this->sendResponse($this->request, $this->response);
        }
    }
    
    /**
     * Send the response for the given request.
     * 
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The response to send.
     * @return void
     */
    protected function sendResponse(ServerRequestInterface $request, ResponseInterface $response) {
        // Norma's middleware layer 8.
        $this->response = $this->executeMiddlewareLayerBeforeSendingResponse($request, $response);
        $this->response->send();
    }
    
    /**
     * Executes the Norma's middleware layer 1, i.e. when an uncaught exception or error is thrown.
     * 
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The current response to use.
     * @param \Throwable $e The exception which caused the execution of this middleware layer.
     * @return mixed The return value may be one of the following:
     *                          - void: If nothing is returned, nothing will be overridden;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWithThrowableWrapperInterface: The front controller will stop its execution immediately and the response
     *                                                                                                                                                                      built so far will be used and the throwable associated with the wrapper will
     *                                                                                                                                                                      be thrown;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWrapperInterface: The front controller will stop its execution immediately and the response built so far will be sent;
     *                          - Norma\HTTP\Request\Server\ServerRequestInterface: A server request which would override the original one and be used further by the application;
     *                          - Norma\HTTP\Response\ResponseInterface: A server response which should be used but which can be replaced further on by the application;
     *                          - array: An array combining the previous values, in any order, which would override the current server request and response.
     *                                       If the returned array contains two or more values of the same type (e.g. two server requests), the latest value in order is the one to use and the previous one
     *                                       will be ignored by the framework.
     */
    protected abstract function executeMiddlewareLayerOnThrowable(ServerRequestInterface $request, ResponseInterface $response, \Throwable $e);
    
    /**
     * Executes the Norma's middleware layer 2, i.e. before the route matching process.
     * 
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The current response to use.
     * @return mixed The return value may be one of the following:
     *                          - void: If nothing is returned, nothing will be overridden;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWithThrowableWrapperInterface: The front controller will stop its execution immediately and the response
     *                                                                                                                                                                      built so far will be used and the throwable associated with the wrapper will
     *                                                                                                                                                                      be thrown;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWrapperInterface: The front controller will stop its execution immediately and the response built so far will be sent;
     *                          - Norma\HTTP\Request\Server\ServerRequestInterface: A server request which would override the original one and be used further by the application;
     *                          - Norma\HTTP\Response\ResponseInterface: A server response which should be used but which can be replaced further on by the application;
     *                          - array: An array combining the previous values, in any order, which would override the current server request and response.
     *                                       If the returned array contains two or more values of the same type (e.g. two server requests), the latest value in order is the one to use and the previous one
     *                                       will be ignored by the framework.
     */
    protected abstract function executeMiddlewareLayerBeforeRouteMatchingProcess(ServerRequestInterface $request, ResponseInterface $response);
    
    /**
     * Executes the Norma's middleware layer 3, i.e. after the route matching process.
     * 
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The current response to use.
     * @param RequestRouteInterface|null $route The route matching the request. Null if there's no route which matches.
     * @return mixed The return value may be one of the following:
     *                          - void: If nothing is returned, nothing will be overridden;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWithThrowableWrapperInterface: The front controller will stop its execution immediately and the response
     *                                                                                                                                                                      built so far will be used and the throwable associated with the wrapper will
     *                                                                                                                                                                      be thrown;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWrapperInterface: The front controller will stop its execution immediately and the response built so far will be sent;
     *                          - Norma\HTTP\Request\Server\ServerRequestInterface: A server request which would override the original one and be used further by the application.
     *                          - Norma\HTTP\Response\ResponseInterface: A server response which should be used but which can be replaced further on by the application.
     *                          - array: An array combining the previous values, in any order, which would override the current server request and response.
     *                                       If the returned array contains two or more values of the same type (e.g. two server requests), the latest value in order is the one to use and the previous one
     *                                       will be ignored by the framework.
     */
    protected abstract function executeMiddlewareLayerAfterRouteMatchingProcess(ServerRequestInterface $request, ResponseInterface $response, $route);
    
    /**
     * Executes the Norma's middleware layer 4, i.e. after a route matched the request and before executing the corresponding controller.
     * 
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The current response to use.
     * @param RequestRouteInterface $route The route matching the request.
     * @param object $controller The controller instance associated with the route.
     * @param string|null $controllerAction The name of the controller's action. Null is a possible value too, in which case the controller is assumed
     *                                                             to be invokable through its `__invoke()` method.
     * @return mixed The return value may be one of the following:
     *                          - void: If nothing is returned, nothing will be overridden;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWithThrowableWrapperInterface: The front controller will stop its execution immediately and the response
     *                                                                                                                                                                      built so far will be used and the throwable associated with the wrapper will
     *                                                                                                                                                                      be thrown;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWrapperInterface: The front controller will stop its execution immediately and the response built so far will be sent;
     *                          - Norma\HTTP\Request\Server\ServerRequestInterface: A server request which would override the original one and be used further by the application.
     *                          - Norma\HTTP\Response\ResponseInterface: A server response which should be used but which can be replaced further on by the application.
     *                          - array: An array combining the previous values, in any order, which would override the current server request and response.
     *                                       If the returned array contains two or more values of the same type (e.g. two server requests), the latest value in order is the one to use and the previous one
     *                                       will be ignored by the framework.
     */
    protected abstract function executeMiddlewareLayerBeforeControllerExecution(ServerRequestInterface $request, ResponseInterface $response, RequestRouteInterface $route, $controller, $controllerAction);
    
    /**
     * Executes the Norma's middleware layer 5, i.e. after a route matched the request and after executing the corresponding controller.
     * 
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The response returned by the controller.
     * @param RequestRouteInterface $route The route matching the request.
     * @param object $controller The controller instance associated with the route.
     * @param string|null $controllerAction The name of the controller's action. Null is a possible value too, in which case the controller is assumed
     *                                                             to be invokable through its `__invoke()` method.
     * @return mixed The return value may be one of the following:
     *                          - void: If nothing is returned, nothing will be overridden;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWithThrowableWrapperInterface: The front controller will stop its execution immediately and the response
     *                                                                                                                                                                      built so far will be used and the throwable associated with the wrapper will
     *                                                                                                                                                                      be thrown;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWrapperInterface: The front controller will stop its execution immediately and the response built so far will be sent;
     *                          - Norma\HTTP\Request\Server\ServerRequestInterface: A server request which would override the original one and be used further by the application.
     *                          - Norma\HTTP\Response\ResponseInterface: A server response which should be used but which can be replaced further on by the application.
     *                          - array: An array combining the previous values, in any order, which would override the current server request and response.
     *                                       If the returned array contains two or more values of the same type (e.g. two server requests), the latest value in order is the one to use and the previous one
     *                                       will be ignored by the framework.
     */
    protected abstract function executeMiddlewareLayerAfterControllerExecution(ServerRequestInterface $request, ResponseInterface $response, RequestRouteInterface $route, $controller, $controllerAction);
    
    /**
     * Executes the Norma's middleware layer 6, i.e. after the route matching process when there is a partially unmatched route (i.e. a matching route which has an unsatisfied constraint).
     * 
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The current response to use.
     * @param RequestRouterInterface $router The request router.
     * @param RequestRouteInterface $route The partially unmatched route.
     * @param RequestRouteConstraintInterface $routeConstraint The unsatisfied constraint.
     * @return mixed The return value may be one of the following:
     *                          - void: If nothing is returned, nothing will be overridden;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWithThrowableWrapperInterface: The front controller will stop its execution immediately and the response
     *                                                                                                                                                                      built so far will be used and the throwable associated with the wrapper will
     *                                                                                                                                                                      be thrown;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWrapperInterface: The front controller will stop its execution immediately and the response built so far will be sent;
     *                          - Norma\HTTP\Request\Server\ServerRequestInterface: A server request which would override the original one and be used further by the application.
     *                          - Norma\HTTP\Response\ResponseInterface: A server response which should be used but which can be replaced further on by the application.
     *                          - array: An array combining the previous values, in any order, which would override the current server request and response.
     *                                       If the returned array contains two or more values of the same type (e.g. two server requests), the latest value in order is the one to use and the previous one
     *                                       will be ignored by the framework.
     */
    protected abstract function executeMiddlewareLayerWhenPartiallyUnmatchedRoute(ServerRequestInterface $request, ResponseInterface $response, RequestRouterInterface $router, RequestRouteInterface $route, RequestRouteConstraintInterface $routeConstraint);
    
    /**
     * Executes the Norma's middleware layer 7, i.e. after the route matching process when there isn't a route which matches the request.
     * 
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The current response to use.
     * @param RequestRouterInterface $router The request router.
     * @return mixed The return value may be one of the following:
     *                          - void: If nothing is returned, nothing will be overridden;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWithThrowableWrapperInterface: The front controller will stop its execution immediately and the response
     *                                                                                                                                                                      built so far will be used and the throwable associated with the wrapper will
     *                                                                                                                                                                      be thrown;
     *                          - Norma\HTTP\Response\Wrapper\ImmediateResponseWrapperInterface: The front controller will stop its execution immediately and the response built so far will be sent;
     *                          - Norma\HTTP\Request\Server\ServerRequestInterface: A server request which would override the original one and be used further by the application.
     *                          - Norma\HTTP\Response\ResponseInterface: A server response which should be used but which can be replaced further on by the application.
     *                          - array: An array combining the previous values, in any order, which would override the current server request and response.
     *                                       If the returned array contains two or more values of the same type (e.g. two server requests), the latest value in order is the one to use and the previous one
     *                                       will be ignored by the framework.
     */
    protected abstract function executeMiddlewareLayerWhenNoMatchingRoute(ServerRequestInterface $request, ResponseInterface $response, RequestRouterInterface $router);
    
    /**
     * Executes the Norma's middleware layer 8, i.e. before sending the final response to the client.
     * 
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The response returned so far.
     * @return ResponseInterface The final response to send to the client.
     */
    protected abstract function executeMiddlewareLayerBeforeSendingResponse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
    
    /**
     * Executes the Norma's middleware layer 9, i.e. after sending the response to the client.
     * 
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The response sent to the client.
     * @return void
     */
    protected abstract function executeMiddlewareLayerAfterSendingResponse(ServerRequestInterface $request, ResponseInterface $response);
    
}
