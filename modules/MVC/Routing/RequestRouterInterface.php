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

use Norma\HTTP\Request\Server\ServerRequestInterface;

/**
 * Interface of a router.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface RequestRouterInterface {
    
    /**
     * Add a route to this router.
     * 
     * @param array $route The route to add, which is an array defining the structure of a route.
     * @return void
     */
    public function addRoute(array $route);

    /**
     * Add all the routes given as parameter to this router.
     * 
     * @param array $routes An array of routes. Each element is an array which defines the structure of a route.
     * @return void
     */
    public function addRoutes(array $routes);
    
    /**
     * Get a route matching a request.
     * 
     * @param ServerRequestInterface $request The request.
     * @return RequestRouteInterface|null The route matching the request. Null if there's no route which matches.
     */
    public function getMatchingRoute(ServerRequestInterface $request);
    
    /**
     * Switch the namespace context to use for the routes added within the given callback.
     * 
     * @param string $namespaceContext The new namespace context.
     * @param callable $callback A callback which will be given this router as parameter.
     *                                           Routes added within this callback will all pertain to the namespace
     *                                           context given as the first parameter.
     * @return void
     */
    public function switchNamespaceContext($namespaceContext, $callback);
    
    /**
     * Returns the parameters of the route which matched.
     * 
     * @return array An array where keys are the parameter names and value is the value of the parameter.
     */
    public function getMatchedRouteParams();
    
    /**
     * Returns the route which matched.
     * 
     * @return RequestRouteInterface|null The route matching the request. Null if a route didn't match yet.
     */
    public function getMatchedRoute();
    
    /**
     * Returns the routes which partially unmatched the request. These should be the routes which matched against
     * the request URI but which still didn't match fully the request for some reason (e.g. because a constraint didn't match).
     * 
     * @return array<RequestRouteInterface> An array of routes.
     */
    public function getPartiallyUnmatchedRoutes();
    
    /**
     * Returns the unsatisfied constraint for the route if there is one.
     * 
     * @return RequestRouteConstraintInterface|null The unmatched constraint of the route or null if the route doesn't have a constraint which didn't match.
     */
    public function getUnsatisfiedConstraintForRoute(RequestRouteInterface $route);
    
}
