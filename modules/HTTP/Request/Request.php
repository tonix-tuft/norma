<?php

/*
 * Copyright (c) 2018 Anton Bagdatyev
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

namespace Norma\HTTP\Request;

use Psr\Http\Message\UriInterface;
use Norma\HTTP\AbstractMessage;
use Norma\HTTP\Request\RequestInterface;
use Norma\HTTP\Request\RequestMethodEnum;
use Norma\HTTP\URI\URI;

/**
 * The implementation of an outgoing, client-side request.
 * 
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class Request extends AbstractMessage implements RequestInterface {
    
    /**
     * @var string|false
     */
    protected $requestTarget = FALSE;
    
    /**
     * @var string
     */
    protected $method;
    
    /**
     * @var UriInterface
     */
    protected $URI = NULL;
    
    /**
     * Constructs a new request.
     * 
     * @param UriInterface|null $URI The URI.
     * @param string|null $method The method.
     * @param array The request headers.
     * @throws \InvalidArgumentException If the given HTTP method is invalid or another of the given parameters is invalid (e.g. `$headers`).
     */
    public function __construct(UriInterface $URI = NULL, $method = NULL, $headers = []) {
        $this->URI = $URI !== NULL ? $URI : new URI();
        
        if ($method !== NULL) {
            $this->throwExceptionIfInvalidMethod($method);
        }
        else {
            // Fallback to GET as the default HTTP method.
            $method = RequestMethodEnum::GET;
        }
        $this->method = $method;
        
        $this->setHeaders($headers);
        
        $hostFromURI = $this->getHostFromURI();
        if (!$this->hasHeader('Host') && !empty($hostFromURI)) {
            $this->setHeader('Host', $hostFromURI);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getHostFromURI() {
        return $this->getHostFromGivenURI($this->URI);
    }
    
    /**
     * Return the host from the given URI.
     * 
     * @param UriInterface $URI The URI.
     * @return string The host of the URI.
     */
    protected function getHostFromGivenURI(UriInterface $URI) {
        $host = $URI->getHost();
        if (!empty($host) && !empty($URI->getPort())) {
            $host .= ':' . $URI->getPort();
        }
        return $host;
    }
    
    /**
     * Throws an exception if the given HTTP method is invalid.
     * 
     * @param string $method An HTTP method.
     * @throws \InvalidArgumentException If the given HTTP method is invalid.
     */
    protected function throwExceptionIfInvalidMethod($method) {
        $methods = array_flip(RequestMethodEnum::toKeyVal());
        if (!isset($methods[$method])) {
            throw new \InvalidArgumentException(sprintf('The HTTP method "%s" is not valid.'), $method);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTarget() {
        /*
         * A request target can be one of four different forms:
         * 
         * - origin-form, which is the path and query string (if present) of the URI;
         * - absolute-form, which is an absolute URI;
         * - authority-form, which is the authority portion of the URI (user-info, if present; host; and port, if non-standard);
         * - asterisk-form, which is the string *.
         * 
         * URI structure:
         *  <scheme>://<authority>[/<path>][?<query string>][#<fragment>]
         * 
         *  <scheme> = http / https; for HTTP requests
         *  <authority> = [user-info@]host[:port]
         *  user-info = user[:pass]
         * 
         * Per RFC 7230:
         * 
         *  request-target = origin-form
         *                               / absolute-form
         *                               / authority-form
         *                               / asterisk-form
         * 
         * @source https://mwop.net/blog/2015-01-26-psr-7-by-example.html
         * @source https://tools.ietf.org/html/rfc7230#section-5.3
         */
        if ($this->requestTarget !== FALSE) {
            return $this->requestTarget;
        }
        
        $URI = $this->getUri();
        $originFormRequestTarget = $URI->getPath();
        $URIQuery = $URI->getQuery();
        if (!empty($URIQuery)) {
            $originFormRequestTarget .= '?' . $URIQuery;
        }
        
        if (!empty($originFormRequestTarget)) {
            return $originFormRequestTarget;
        }
        else {
            return '/';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget) {
        /*
         * @source https://www.ietf.org/rfc/rfc3986.txt
         * @source https://stackoverflow.com/questions/5442658/spaces-in-urls#answer-5442677
         */
        $requestTargetWithEncodedSpaces = preg_replace('/\s/', '%20', $requestTarget);
        $clone = $this->cloneThis();
        $clone->requestTarget = $requestTargetWithEncodedSpaces;
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function withMethod($method) {
        $this->throwExceptionIfInvalidMethod($method);
        $clone = $this->cloneThis();
        $clone->method = $method;
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri() {
        return $this->URI;
    }

    /**
     * {@inheritdoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false) {
        $clone = $this->cloneThis();
        $clone->URI = $uri;
        
        $hostFromURI = $clone->getHostFromURI();
        if (!$preserveHost) {
            if (!empty($hostFromURI)) {
                if ($clone->hasHeader('Host')) {
                    $cloneHostHeader = $clone->headerNameLowerCaseMap['host'];
                    $clone->headers[$cloneHostHeader] = [$hostFromURI];
                }
                else {
                    $clone->setHeader('Host', $hostFromURI);
                }
            }
        }
        else if (empty($clone->getHeader('Host'))) {
            if (!empty($hostFromURI)) {
                $clone->setHeader('Host', $hostFromURI);
            }
        }
        
        return $clone;
    }
    
}
