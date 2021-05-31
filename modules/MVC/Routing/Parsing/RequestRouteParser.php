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

namespace Norma\MVC\Routing\Parsing;

use Norma\Core\Utils\FrameworkUtils;
use Norma\MVC\Routing\Constraint\Parsing\RequestRouteConstraintsParserInterface;
use Norma\MVC\Routing\RequestRouteFactoryInterface;
use Norma\MVC\Routing\RequestRouteInterface;

/**
 * An implementation of a request route parser.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class RequestRouteParser implements RequestRouteParserInterface {
    
    const CONTROLLER_ACTION_SEPARATOR = '->';
    
    const ROUTE_REGEX_PATTERN_DELIMITER = '#';
    
    const SIMPLE_SLUG_REGEX_PATTERN = '[^/]+';
    
    const VALID_NAMED_SUBPATTERN_PATTERN = '[_A-Za-z]\w{0,31}';
    
    const REGEX_SLUG_PATTERN = '~
    (?=
        (?P<whole_slug>
          (?:[{][{])
          (?P<slug>' . self::VALID_NAMED_SUBPATTERN_PATTERN . ')
          (?:[}])
          (?:
            (?:
              (?:
                 (?P<slug_pattern_delimiter>[^}])
                 (?P<slug_pattern>
                    [\s\S]+?
                 )
                 (?P=slug_pattern_delimiter)
                 (?P<slug_pattern_modifiers>.*?)
               )
               (?:[}])
            )
          )
        )
    )
    ~x';
    
    const SIMPLE_SLUG_PATTERN = '~
    (?=
       (?P<whole_slug>
         (?<![{])
         (?:[{]{1})
         (?P<slug>' . self::VALID_NAMED_SUBPATTERN_PATTERN . ')
         (?:[}]{1})
         (?![}])
       )
    )
    ~x';
    
    /**
     * @var FrameworkUtils
     */
    protected $utils;
    
    /**
     * @var RequestRouteConstraintsParserInterface
     */
    protected $requestRouteConstraintsParser;
    
    /**
     * @var RequestRouteFactoryInterface
     */
    protected $requestRouteFactory;

    /**
     * Constructs a new request route parser.
     * 
     * @param FrameworkUtils $utils Framework's utility object.
     * @param RequestRouteConstraintsParserInterface $requestRouteConstraintsParser A request route constraints parser.
     */
    public function __construct(FrameworkUtils $utils, RequestRouteConstraintsParserInterface $requestRouteConstraintsParser, RequestRouteFactoryInterface $requestRouteFactory) {
        $this->utils = $utils;
        $this->requestRouteConstraintsParser = $requestRouteConstraintsParser;
        $this->requestRouteFactory = $requestRouteFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function parse(array $route): RequestRouteInterface {
        if (!empty($route['name'])) {
            $routeName = $route['name'];
            unset($route['name']);
        }
        else if (!empty($route[0])) {
            $routeName = $route[0];
            unset($route[0]);
            $route = array_values($route);
        }
        else {
            $routeName = '';
        }
        
        if ($this->utils->isAssocArray($route)) {
            return $this->parseAssocRoute($route, $routeName);
        }
        else {
            return $this->parseRoute($route, $routeName);
        }
    }
    
    /**
     * Parse a route defined as an associative array.
     * 
     * @param array $route An associative array defining the structure of a route.
     * @param string $routeName The name of the route.
     * @return RequestRouteInterface The parsed route.
     */
    protected function parseAssocRoute(array $route, $routeName) {
        return $this->parseRoute([
                $route['pattern'] ?? '',
                $route['controller'] ?? '',
                $route['constraints'] ?? []
            ],
            $routeName
        );
    }
    
    /**
     * Parse a route defined as an indexed array.
     * 
     * @param array $route An indexed array defining the structure of a route.
     * @param string $routeName The name of the route.
     * @return RequestRouteInterface The parsed route.
     */
    protected function parseRoute(array $route, $routeName) {
        $pattern = $this->parseRoutePattern($route[0] ?? '');
        $controller = $route[1] ?? '';
        $constraints = $route[2] ?? NULL;
        
        $controllerExplode = explode(self::CONTROLLER_ACTION_SEPARATOR, $controller);
        $controllerAction = NULL;
        if (count($controllerExplode) > 1) {
            $controllerName = $controllerExplode[0];
            $controllerAction = $controllerExplode[1];
        }
        else {
            $controllerName = $controllerExplode[0];
        }
        
        $requestRouteConstraints = $this->requestRouteConstraintsParser->parse($constraints);
        
        return $this->requestRouteFactory->makeRoute($routeName, $pattern, $controllerName, $controllerAction, $requestRouteConstraints);
    }

    /**
     * {@inheritdoc}
     */
    public function parseAll(array $data): array {
        $routes = [];
        foreach ($data as $routeName => $route) {
            if (is_string($routeName)) {
                $route['name'] = $routeName;
            }
            $route = $this->parse($route);
            $routes[] = $route;
        }
        return $routes;
    }
    
    /**
     * Parses a route pattern and returns a valid regular expression for the pattern.
     * 
     * @param string $routePattern The route pattern.
     * @return string The route's regex.
     */
    protected function parseRoutePattern($routePattern) {
        /*
         * @see https://regex101.com/r/tg2ffX/57 Regex for a single slug with regex pattern.
         * @see https://regex101.com/r/8ToYqq/3 Regex for a single simple slug.
         */
        
        // 1. Parse regex slugs.
        // 2. Parse simple slugs.
        $slugPatterns = [
            self::REGEX_SLUG_PATTERN,
            self::SIMPLE_SLUG_PATTERN
        ];
        $blocked = [];
        foreach ($slugPatterns as $slugPattern) {
            $matches = [];
            $pregMatchOffset = 0;
            while (preg_match($slugPattern, $routePattern, $matches, PREG_OFFSET_CAPTURE, $pregMatchOffset)) {
                $wholeSlugOffset = $matches['whole_slug'][1];
                $wholeSlugLength = strlen($matches['whole_slug'][0]);
                $wholeSlugOffsetEnd = ($wholeSlugOffset + $wholeSlugLength) - 1;
                
                $pregMatchOffset = $wholeSlugOffset + 1;
                
                $alreadyBlocked = FALSE;
                foreach ($blocked as $block) {
                    if (
                        (
                          $wholeSlugOffset >= $block['offset_range']['start']
                          &&
                          $wholeSlugOffset <= $block['offset_range']['end']
                        )
                        ||
                        (
                          $wholeSlugOffsetEnd >= $block['offset_range']['start']
                          &&
                          $wholeSlugOffsetEnd <= $block['offset_range']['end']
                        )
                    ) {
                        $alreadyBlocked = TRUE;
                    }
                }
                
                if ($alreadyBlocked) {
                    $matches = [];
                    continue;
                }
                
                if (!empty($matches['slug_pattern'][0])) {
                    $regexPattern = $matches['slug_pattern'][0];
                }
                else {
                    $regexPattern = self::SIMPLE_SLUG_REGEX_PATTERN;
                }
                $subpattern = '(?P<'.$matches['slug'][0].'>'.$regexPattern.')';
                
                if (!empty($matches['slug_pattern_modifiers'][0])) {
                    $modifiers = $matches['slug_pattern_modifiers'][0];
                    $subpattern = '(?' . $modifiers . ':' . $subpattern . ')';
                }
                
                $blocked[$wholeSlugOffset] = [
                    'offset_range' => [
                        'start' => $wholeSlugOffset,
                        'end' => $wholeSlugOffsetEnd
                    ],
                    'subpattern' => $subpattern
                ];
                
                $matches = [];
            }
        }
        
        $transpiledPattern = $routePattern;
        krsort($blocked, SORT_NUMERIC);
        foreach ($blocked as $block) {
            $transpiledPattern = substr_replace(
                    $transpiledPattern,
                    $block['subpattern'],
                    $block['offset_range']['start'],
                    ($block['offset_range']['end'] - $block['offset_range']['start']) + 1
            );
        }
        
        $routeRegex = self::ROUTE_REGEX_PATTERN_DELIMITER . 
                '^' . 
                $transpiledPattern .
                '$' . 
                self::ROUTE_REGEX_PATTERN_DELIMITER .
                $this->defaultRouteRegexModifiers();
        
        return $routeRegex;
    }
    
    /**
     * Returns the default modifiers to use to construct the parsed route regex.
     * 
     * @return string The modifiers as used with PHP's `preg_*` functions.
     */
    protected function defaultRouteRegexModifiers() {
        return 'ux';
    }

}