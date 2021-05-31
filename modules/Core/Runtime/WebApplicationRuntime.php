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

namespace Norma\Core\Runtime;

use Norma\Core\Runtime\AbstractRuntime;
use Norma\DI\AbstractDependencyInjectionContainer;
use Norma\MVC\FrontControllerInterface;
use Norma\MVC\Routing\RequestRouterInterface;
use Norma\HTTP\Request\Server\ServerRequestInterface;
use Norma\HTTP\Middleware\MiddlewareLayerInterface;

/**
 * The implementation of a web application runtime.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class WebApplicationRuntime extends AbstractRuntime {
    
    /**
     * {@inheritdoc}
     */
    protected function onContainerConfigured(AbstractDependencyInjectionContainer $container) {
        // Web application middlewares configuration.
        $this->configureWebApplicationMiddlewares($container);

        // Routing configuration.
        $this->configureRouting($container);
    }

    /**
     * {@inheritdoc}
     */
    protected function executeApplication(AbstractDependencyInjectionContainer $container) {
        /* @var $frontController Norma\MVC\FrontControllerInterface */
        $frontController = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'mvc', 'controller', FrontControllerInterface::class]));
        
        /* @var $request Norma\HTTP\Request\Server\ServerRequestInterface */
        $request = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'http', ServerRequestInterface::class]));

        $frontController->run($request);
    }
    
    /**
     * Configures the routing of the application.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @return void
     */
    protected function configureRouting(AbstractDependencyInjectionContainer $container) {
        /* @var $router RequestRouterInterface */
        $router = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'mvc', 'routing', RequestRouterInterface::class]));
        $routingConfigCallable = (require_once $this->normaDir . '/app/config/runtime-' . $this->runtime . '/routes.php') ?? function() {};
        $routingConfig = $container->call($routingConfigCallable);
        $router->addRoutes($routingConfig ?? []);
    }
    
    /**
     * Configures the web application's middlewares.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container
     * @return void
     */
    protected function configureWebApplicationMiddlewares(AbstractDependencyInjectionContainer $container) {
        /*
         * The Norma framework defines the following middleware layers for a web application:
         * 
         *      1. A middleware executed whenever an error occurs (unhandled fatal error wrapped within an exception, uncaught exception);
         *      2. A middleware which is executed before there is a controller response and before the route matching process begins;
         *      3. A middleware which is executed before there is a controller response and after the route matching process;
         *      4. A middleware which is executed before there is a controller response and after the route matching process when there is a matching route;
         *      5. A middleware which is executed after the controller execution when there is a controller response and therefore after the route matching process when there is a matching route;
         *      6. A middleware which is executed when there is a partially unmatched route and therefore an unsatisfied route's constraint;
         *      7. A middleware which is executed when there isn't a matching route;
         *      8. An "after" middleware which is always executed after the route matching process and before sending the response;
         *      9. An "after" middleware which is always executed after sending the response;
         *  
         * The workflow of a Norma web application:
         * 
         *                                                                                                                                    At any time there could be an error, i.e. a `Throwable` `Error` or uncaught `Exception` or a PHP error which is in turn wrapped within an exception instance.
         *  <---------------------------------- Middleware 1 (error)                           If there's an error, the Norma's special error middleware is executed.
         *                                                                                                                                    ===========================================================================================================================================
         *  1. A request arrives                                                                                                    \ ---> This happens always.
         *    <--------------------------------- Middleware 2 (before)                           \ ---> This happens always, if there's a 1st middleware layer defined, it is always executed provided that there isn't an error.
         *  2. Route matching process:                                                                                           \ ---> Depends on what the above middleware layer returned. If the above layer of middlewares didn't stop the execution at this point, the route matching process always starts, provided that there wasn't an error yet.
         *    <--------------------------------- Middleware 3 (before)                              \ ---> Depends on what the above middleware layers returned. If the above layers of middlewares didn't stop execution, always, provided that there wasn't an error yet.
         *      2.1. A route matched:                                                                                                   \ ---> If then a route matches...
         *           <--------------------------------- Middleware 4 (before)                          \ ---> Depends on what the above middleware layers returned. If the above layers of middlewares didn't stop the execution, always, provided that a route matched and that there wasn't an error yet.
         *             3. Controller execution:                                                                                           | ---> Depends on what the above middleware layers returned. If the above layers of middlewares didn't stop the execution, always, provided that a route matched and that there wasn't an error yet.
         *           <--------------------------------- Middleware 5 (after)                             / ---> Depends on what the above middleware layers returned. If the above layers of middlewares didn't stop the execution, always, provided that a route matched and that there wasn't an error yet.
         *      2.2. No matching route:                                                                                                / ---> If no route matches...
         *           2.2.1 There's a partially unmatched route (unsatisfied constraint):                        / ---> The route is partially unmatched and has an unsatisfied constraint...
         *                <--------------------------------- Middleware 6 (after)                    / ---> Depends on what the above middleware layers returned. If the above layers of middlewares didn't stop the execution, always, provided that a route didn't match, it has an unsatisfied constraint and that there wasn't an error yet.
         *           2.2.2 A route didn't match at all:                                                                         / ---> If the route didn't match at all...
         *                <--------------------------------- Middleware 7 (after)                 / ---> Depends on what the above middleware layers returned. If the above layers of middlewares didn't stop the execution, always, provided that a route didn't match at all and that there wasn't an error yet.
         *    <--------------------------------- Middleware 8 (after)                            / ---> Depends on what the above middleware layers returned. If the above layers of middlewares didn't stop the execution, always, provided that there wasn't an error yet.
         *  4. Response sending (always)                                                                                   / ---> This happens always, i.e. a response is always sent to the client. All errors and uncaught exceptions handled within the 1st middleware layer must generate a response too.
         *    <--------------------------------- Middleware 9 (after)                         / ---> Depends on what the above middleware layers returned. If the above layers of middlewares didn't stop the execution, always, provided that there wasn't an error yet.
         * 
         */
        $middlewareLayer = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'http', 'middleware', MiddlewareLayerInterface::class]));
        $this->registerMiddlewares(
            $container,
            $middlewareLayer,
            $this->normaDir . '/app/config/runtime-' . $this->runtime . '/middlewares.php'
        );
        $this->registerMiddlewares(
            $container,
            $middlewareLayer,
            $this->normaDir . '/app/config/runtime-' . $this->runtime . '/' . $this->environment . '/middlewares.php',
            TRUE
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function onThrowable(\Throwable $e, AbstractDependencyInjectionContainer $container) {
        /* @var $frontController Norma\MVC\FrontControllerInterface */
        $frontController = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'mvc', 'controller', FrontControllerInterface::class]));
        $frontController->handleThrowable($e);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function onTermination(AbstractDependencyInjectionContainer $container) {
        $frontController = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'mvc', 'controller', FrontControllerInterface::class]));
        $frontController->handleTermination();
    }

}
