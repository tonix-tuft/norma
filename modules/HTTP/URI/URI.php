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

namespace Norma\HTTP\URI;

use Psr\Http\Message\UriInterface;
use Norma\HTTP\SchemeStandardPortMap;

/**
 * The implementation of a PSR-7 URI.
 *
 * @source http://php.net/manual/en/function.parse-url.php#114817
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 * @author <lauris () lauris ! lv> (http://php.net/manual/en/function.parse-url.php#114817)
 */
class URI implements UriInterface {
    
    /**
     * A regular expression pattern used to match a valid scheme.
     */
    const SCHEME_REGEX = '^[a-zA-Z][a-zA-Z0-9+.-]*$';

    /**
     * @var string
     */
    protected $scheme;
    
    /**
     * @var string
     */
    protected $host;
    
    /**
     * @var string
     */
    protected $user;
    
    /**
     * @var string
     */
    protected $password;
    
    /**
     * @var int|null
     */
    protected $port;
    
    /**
     * @var string
     */
    protected $path;
    
    /**
     * @var string
     */
    protected $query;
    
    /**
     * @var string
     */
    protected $fragment;
    
    /**
     * Constructs a new URI.
     * 
     * @param string|null $URIString A string representing the URI, or NULL, if the URI is intended to be built on the way.
     * @throws \InvalidArgumentException If the passed URI string is malformed and therefore cannot be parsed.
     */
    public function __construct($URIString = NULL) {
        if (!is_null($URIString)) {
            $this->parseURI($URIString);
        }
        else {
            $this->parseURI('');
        }
    }
        
    /**
     * Parses a URI and extrapolates its parts. This method is used internally to set up the initial state of the object.
     * 
     * @param string $URIString The URI string.
     * @return void
     * @throws \InvalidArgumentException If the passed URI string is malformed and therefore cannot be parsed.
     */
    protected function parseURI($URIString) {
        $URIParts = $this->UTF8ParseURI($URIString);
        $this->valorizeFieldsFromURIParts($URIParts);
    }
    
    /**
     * Parses a URI using `parse_url`, with additional UTF-8 support.
     * 
     * @see http://php.net/manual/en/function.parse-url.php
     * @source http://php.net/manual/en/function.parse-url.php#114817
     * 
     * @param string $URIString The URI string.
     * @return array An array with the same structure as the one returned by `parse_url`.
     * @throws \InvalidArgumentException If the passed URI string is malformed and therefore cannot be parsed.
     */
    public function UTF8ParseURI($URIString) {
        $encodedURIString = $this->encodeUTF8URIString($URIString);
        
        $URIParts = parse_url($encodedURIString);
        if ($URIParts === FALSE) {
            throw new \InvalidArgumentException(sprintf('Could not parse URI "%s".', $URIString));
        }
        
        foreach($URIParts as $name => $value) {
            $URIParts[$name] = rawurldecode($value);
        }
        return $URIParts;
    }
    
    /**
     * Encodes a URI with UTF-8 support.
     * 
     * @source http://php.net/manual/en/function.parse-url.php#114817
     * 
     * @param string $URIString The URI string.
     * @return string The URI encoded with UTF-8 support.
     */
    protected function encodeUTF8URIString($URIString) {
        return preg_replace_callback('~[^:/@?&=#]+~u', function ($matches) {
                return rawurlencode($matches[0]);
        }, $URIString);
    }
    
    /**
     * Valorizes the internal fields of this URI using the given URI parts.
     * 
     * @see http://php.net/manual/en/function.parse-url.php
     * 
     * @param array $URIParts An array specifying the parts of the URI, with a structure compatible
     *                                           with the one returned by `parse_url`.
     * @return void
     */
    protected function valorizeFieldsFromURIParts($URIParts) {
        // Host.
        $this->valorizeHostFieldFromURIParts($URIParts);
        
        // User.
        $this->user = $URIParts['user'] ?? '';
        
        // Password.
        $this->password = $URIParts['pass'] ?? '';
        
        // Scheme and port.
        $this->valorizeSchemeAndPortFieldsFromURIParts($URIParts);
        
        // Path.
        $this->valorizePathFieldFromURIParts($URIParts);
        
        // Query.
        $this->valorizeQueryFieldFromURIParts($URIParts);
        
        // Fragment.
        $this->valorizeFragmentFieldFromURIParts($URIParts);
    }
    
    /**
     * Valorizes the URI host from the given URI parts.
     * 
     * @see http://php.net/manual/en/function.parse-url.php
     * 
     * @param array $URIParts An array specifying the parts of the URI, with a structure compatible
     *                                       with the one returned by `parse_url`.
     * @throws \InvalidArgumentException For invalid hostnames.
     * @return void
     */
    protected function valorizeHostFieldFromURIParts($URIParts) {
        $this->host = strtolower($URIParts['host'] ?? '');
        $this->throwExceptionIfInvalidHost($this->host);
    }
    
    /**
     * Valorizes the URI host from the given URI parts.
     * 
     * @see http://php.net/manual/en/function.parse-url.php
     * 
     * @param array $URIParts An array specifying the parts of the URI, with a structure compatible
     *                                       with the one returned by `parse_url`.
     * @return void
     * @throws \InvalidArgumentException For invalid or unsupported schemes.
     * @throws \InvalidArgumentException For invalid ports.
     */
    protected function valorizeSchemeAndPortFieldsFromURIParts($URIParts) {
        // Scheme.
        $scheme = strtolower($URIParts['scheme'] ?? '');
        if (!empty($scheme)) {
            $this->throwExceptionIfInvalidScheme($scheme);
        }
        $this->scheme = $scheme;
        
        // Port. Depends on scheme also.
        $port = null;
        if (array_key_exists('port', $URIParts)) {
            $port = $URIParts['port'];
        }
        if (!is_null($port)) {
            $this->throwExceptionIfInvalidPort($port);
            $isStandard = $this->isPortForSchemeStandard($port, $this->scheme);
            if ($isStandard === TRUE) {
                $port = null;
            }   
        }
        $this->port = $port;
    }
    
    /**
     * Throws an exception if the given scheme is invalid.
     * 
     * @param string $scheme The scheme.
     * @return void
     * @throws \InvalidArgumentException For invalid or unsupported schemes.
     */
    protected function throwExceptionIfInvalidScheme($scheme) {
        /*
         * Per RFC 3986:
         * 
         * scheme      = ALPHA *( ALPHA / DIGIT / "+" / "-" / "." )
         * 
         * @source https://tools.ietf.org/html/rfc3986#section-3.1
         */
        if (!preg_match('/' . self::SCHEME_REGEX . '/', $scheme)) {
            throw new \InvalidArgumentException(sprintf('Invalid scheme "%s"', $scheme));
        }
    }
    
    /**
     * Throws an exception if the given host is invalid.
     * 
     * @param string $host The host.
     * @return void
     * @throws \InvalidArgumentException For invalid hostnames.
     */
    protected function throwExceptionIfInvalidHost($host) {
        if (!is_string($host)) {
            throw new \InvalidArgumentException('URI host must be a string.');
        }
    }
    
    /**
     * Throws an exception if the given port is invalid.
     * 
     * @param int $port The port.
     * @return void
     * @throws \InvalidArgumentException For invalid ports.
     */
    protected function throwExceptionIfInvalidPort($port) {
        if (!is_int($port) || $port < 1 || $port > 65535) {
            throw new \InvalidArgumentException(sprintf('Invalid URI port "%d", must be an integer within the range 1-65535.', $port));
        }
    }
    
    /**
     * Throws an exception if the given path is invalid.
     * 
     * @param string $path The path.
     * @return void
     * @throws \InvalidArgumentException For invalid paths.
     */
    protected function throwExceptionIfInvalidPath($path) {
        if (!is_string($path)) {
            throw new \InvalidArgumentException('URI path must be a string.');
        }
    }
    
    /**
     * Throws an exception if the given query string is invalid.
     * 
     * @param string $query The query string.
     * @return void
     * @throws \InvalidArgumentException For invalid query strings.
     */
    protected function throwExceptionIfInvalidQuery($query) {
        if (!is_string($query)) {
            throw new \InvalidArgumentException('URI query must be a string.');
        }
    }
    
    /**
     * Valorizes the URI path from the given URI parts.
     * 
     * @see http://php.net/manual/en/function.parse-url.php
     * 
     * @param array $URIParts An array specifying the parts of the URI, with a structure compatible
     *                                       with the one returned by `parse_url`.
     * @throws \InvalidArgumentException For invalid paths.
     * @return void
     */
    protected function valorizePathFieldFromURIParts($URIParts) {
        $path = '';
        if (array_key_exists('path', $URIParts)) {
            $pathToEncode = $URIParts['path'];
            
            $this->throwExceptionIfInvalidPath($pathToEncode);
            
            $path = implode('/', array_map(function($pathPart) {
                return rawurlencode(rawurldecode($pathPart));
            }, explode('/', $pathToEncode ?? '')));
        }
        $this->path = $path;
    }
    
    /**
     * Valorizes the URI query from the given URI parts.
     * 
     * @see http://php.net/manual/en/function.parse-url.php
     * 
     * @param array $URIParts An array specifying the parts of the URI, with a structure compatible
     *                                       with the one returned by `parse_url`.
     * @throws \InvalidArgumentException For invalid query strings.
     * @return void
     */
    protected function valorizeQueryFieldFromURIParts($URIParts) {
        $query = '';
        
        if (array_key_exists('query', $URIParts)) {
            $queryToEncode = $URIParts['query'];
            
            $this->throwExceptionIfInvalidQuery($queryToEncode);
            
            $explodeParams = explode('&', $queryToEncode);
            $encodedKeyVals = [];
            foreach ($explodeParams as $explodeParam) {
                $explodeKeyVal = explode('=', $explodeParam, 2);
                $key = $explodeKeyVal[0];
                $value = NULL;
                if (count($explodeKeyVal) > 1) {
                    $value = $explodeKeyVal[1];
                }
                $encodedKeyVals[] = rawurlencode(rawurldecode($key)) . 
                        (
                            $value !== NULL
                            ?
                            '=' . rawurlencode(rawurldecode($value))
                            :
                            ''
                        )
                ;
            }
            $query = implode('&', $encodedKeyVals);
        }
        $this->query = $query;
    }
    
    /**
     * Valorizes the URI fragment from the given URI parts.
     * 
     * @see http://php.net/manual/en/function.parse-url.php
     * 
     * @param array $URIParts An array specifying the parts of the URI, with a structure compatible
     *                                       with the one returned by `parse_url`.
     * @return void
     */
    protected function valorizeFragmentFieldFromURIParts($URIParts) {
        $fragment = '';
        
        if (!empty($URIParts['fragment'])) {
            $fragmentToEncode = $URIParts['fragment'];
            $fragment = rawurlencode(rawurldecode($fragmentToEncode));
        }
        $this->fragment = $fragment;
    }
    
    /**
     * Tests whether the given port is a standard port for the given scheme or not.
     * 
     * @param int $port The port.
     * @param string $scheme The scheme.
     * @return bool|null TRUE if it is a standard port, FALSE otherwise. If the scheme is unknown then
     *                              NULL is returned.
     */
    public function isPortForSchemeStandard($port, $scheme) {
        if (isset(SchemeStandardPortMap::$lookup[$scheme])) {
            return $port == SchemeStandardPortMap::$lookup[$scheme];
        }
        return NULL;
    }
    
    /**
     * Clones this URI.
     * Note that a clone's protected properties are accessible within this class and eventually subclass object's scope.
     * 
     * @source https://stackoverflow.com/questions/49030812/clone-php-object-and-set-protected-property-on-clone#answer-49031402
     * 
     * @return static
     */
    protected function cloneThis() {
        $clone = clone $this;
        return $clone;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority() {
        if (empty($this->host)) {
            return '';
        }
        $userInfo = $this->getUserInfo();
        $authority = $this->host;
        if (!empty($userInfo)) {
            $authority = $userInfo . '@' . $authority;
        }
        
        if (is_int($this->port)) {
            $isStandardPort = $this->isPortForSchemeStandard($this->port, $this->scheme);
            if ($isStandardPort === FALSE || $isStandardPort === NULL) {
                $authority .= ':' . $this->port;
            }
        }
        
        return $authority;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo() {
        $userInfo = '';
        if (!empty($this->user)) {
            $userInfo = $this->user;
        }
        if (!empty($this->password)) {
            $userInfo .= ':' . $this->password;
        }
        return $userInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment() {
        return $this->fragment;
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme) {
        $clone = $this->cloneThis();
        $clone->valorizeSchemeAndPortFieldsFromURIParts([
            'scheme' => $scheme,
            'port' => $clone->getPort()
        ]);
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null) {
        $clone = $this->cloneThis();
        $clone->user = $user;
        $clone->password = $password ?? '';
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($host) {
        $clone = $this->cloneThis();
        $clone->valorizeHostFieldFromURIParts([
            'host' => $host
        ]);
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port) {
        $clone = $this->cloneThis();
        $clone->valorizeSchemeAndPortFieldsFromURIParts([
            'scheme' => $clone->getScheme(),
            'port' => $port
        ]);
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path) {
        $clone = $this->cloneThis();
        $clone->valorizePathFieldFromURIParts([
            'path' => $path
        ]);
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($query) {
        $clone = $this->cloneThis();
        $clone->valorizeQueryFieldFromURIParts([
            'query' => $query
        ]);
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment) {
        $clone = $this->cloneThis();
        $clone->valorizeFragmentFieldFromURIParts([
            'fragment' => $fragment
        ]);
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() {
        $string = '';
        
        $scheme = $this->getScheme();
        if (!empty($scheme)) {
           $string .= $scheme . ':';
        }
        
        $authority = $this->getAuthority();
        $path = $this->getPath();
        
        if (!empty($authority)) {
            $string .= '//' . $authority;
            
            if (strpos($path, '/') !== 0) {
                // The path is rootless and an authority is present, the path MUST be prefixed by "/".
                $path .= '/';
            }
        }
        else if (strpos($path, '//') === 0) {
            // The path is starting with more than one "/" and no authority is present, the starting slashes MUST be reduced to one.
            $path = '/' . ltrim($path, '/');
        }
        
        $string .= $path;
        
        $query = $this->getQuery();
        if (!empty($query)) {
            $string .= '?' . $query;
        }
        
        $fragment = $this->getFragment();
        if (!empty($fragment)) {
            $string .= '#' . $fragment;
        }
        
        return $string;
    }
    
}
