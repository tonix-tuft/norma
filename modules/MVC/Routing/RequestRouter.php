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

use Norma\HTTP\Request\Server\ServerRequestInterface;
use Norma\MVC\Routing\Parsing\RequestRouteParserInterface;

/**
 * A request router implementation.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class RequestRouter implements RequestRouterInterface {
    
    /**
     * @var string
     */
    protected $currentNamespaceContext;
    
    /**
     * @var RequestRouteParserInterface
     */
    protected $routeParser;
    
    /**
     * @var array<string, RequestRouteInterface>
     */
    protected $routes;
    
    /**
     * @var array
     */
    protected $priorityRouteNameMap;
    
    /**
     * @var array
     */
    protected $matchedRouteParams;
    
    /**
     * @var RequestRouteInterface
     */
    protected $matchedRoute = null;
    
    /**
     * @var array<RequestRouteInterface>
     */
    protected $partiallyUnmatchedRoutes;
    
    /**
     * @var array<string, RequestRouteConstraintInterface>
     */
    protected $routeNameUnmatchedContstraintMap;
    
    /**
     * Construct a new request router.
     * 
     * @param RequestRouteParserInterface $routeParser The parser.
     */
    public function __construct(RequestRouteParserInterface $routeParser) {
        $this->currentNamespaceContext = '';
        $this->routeParser = $routeParser;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchingRoute(ServerRequestInterface $request) {
        $this->partiallyUnmatchedRoutes = [];
        $this->routeNameUnmatchedContstraintMap = [];
        
        $appRequestURI = $request->getAppRequestURIPath();
        ksort($this->priorityRouteNameMap, SORT_NUMERIC);

        foreach ($this->priorityRouteNameMap as $routeNames) {
            foreach ($routeNames as $routeName) {
                /* @var $route RequestRouteInterface */
                $route = $this->routes[$routeName];
                $pattern = $route->getPattern();
                $matches = [];
                if (preg_match($pattern, $appRequestURI, $matches)) {
                    $constraints = $route->getConstraints();
                    $atleastOneRouteConstraintIsNotSatisfied = false;
                    foreach ($constraints as $constraint) {
                        if (!$constraint->isSatisfied($request)) {
                            $this->partiallyUnmatchedRoutes[] = $route;
                            $this->routeNameUnmatchedContstraintMap[$route->getName()] = $constraint;
                            $atleastOneRouteConstraintIsNotSatisfied = true;
                            break;
                        }
                    }
                    if ($atleastOneRouteConstraintIsNotSatisfied) {
                        continue;
                    }
                    else {
                        $keys = array_keys($matches);
                        foreach ($keys as $key) {
                            if (is_int($key)) {
                                unset($matches[$key]);
                            }
                        }
                        $this->matchedRouteParams = $matches;
                        $this->matchedRoute = $route;
                        return $route;
                    }
                }
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function addRoute(array $route) {
        $requestRoute = $this->routeParser->parse($route);
        $this->addRouteInternally($requestRoute);
    }

    /**
     * {@inheritdoc}
     */
    public function switchNamespaceContext($namespaceContext, $callback) {
        $trimNamespaceContext = trim($namespaceContext, '\\');
        $previousNamespaceContext = $this->currentNamespaceContext;
        $this->currentNamespaceContext = empty($this->currentNamespaceContext) ?
                $trimNamespaceContext
                :
                $this->currentNamespaceContext . '\\' . $trimNamespaceContext;
        $routes = $callback($this) ?? [];
        $this->addRoutes($routes);
        $this->currentNamespaceContext = $previousNamespaceContext;
    }

    /**
     * {@inheritdoc}
     */
    public function addRoutes(array $routes) {
        $requestRoutes = $this->routeParser->parseAll($routes);
        foreach ($requestRoutes as $requestRoute) {
            $this->addRouteInternally($requestRoute);
        }
    }

    /**
     * Adds a route to the internal data structure of this router.
     * 
     * @param RequestRouteInterface The route to add.
     * @return void
     */
    protected function addRouteInternally(RequestRouteInterface $requestRoute) {
        $routeName = $requestRoute->getName();
        if (empty($routeName)) {
            throw new RoutingException('There\'s a route without a name. All routes must have a unique name.');
        }
        if (isset($this->routes[$routeName])) {
            throw new RoutingException(sprintf('Duplicate route name "%1$s".', $routeName));
        }
        $pattern = $requestRoute->getPattern();
        $this->indexPriorityRouteNameMap($routeName, $pattern);
        $this->contextualizeRoute($requestRoute);
        $this->routes[$routeName] = $requestRoute;
    }
    
    /**
     * Constualize a route using the current router namespace context, if needed.
     * 
     * @param RequestRouteInterface $route The route.
     * @return void
     */
    protected function contextualizeRoute(RequestRouteInterface $route) {
        $controller = $route->getController();
        if (!empty($this->currentNamespaceContext) && strpos($controller, '\\') !== 0) {
            $route->setController($this->currentNamespaceContext . '\\' . rtrim($controller, '\\'));
        }
    }
    
    /**
     * Indexes the internal priority route name map of this router.
     * 
     * @param string $routeName The name of the route.
     * @param string $pattern The regex pattern of the route.
     * @return void
     */
    protected function indexPriorityRouteNameMap($routeName, $pattern) {
        $priority =  substr_count($pattern, '/');
        $this->priorityRouteNameMap[$priority][] = $routeName;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchedRouteParams() {
        return $this->matchedRouteParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchedRoute() {
        return $this->matchedRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function getPartiallyUnmatchedRoutes() {
        return $this->partiallyUnmatchedRoutes;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnsatisfiedConstraintForRoute(RequestRouteInterface $route) {
        $routeName = $route->getName();
        if (!empty($this->routeNameUnmatchedContstraintMap[$routeName])) {
            return $this->routeNameUnmatchedContstraintMap[$routeName];
        }
        return null;
    }

}
