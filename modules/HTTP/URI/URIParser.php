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

namespace Norma\HTTP\URI;

use Norma\HTTP\URI\URIParserInterface;
use Psr\Http\Message\UriInterface;
use Norma\HTTP\Request\Server\ServerRequestInterface;
use Norma\HTTP\Authentication\AuthorizationHeaderParserFactoryInterface;
use Norma\HTTP\URI\RFC3986URIHostValidatorInterface;
use Norma\HTTP\URI\URIFragmentIdentifierInterface;

/**
 * The implementation of a URI parser.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class URIParser implements URIParserInterface {
    
    /**
     * @var AuthorizationHeaderParserFactoryInterface 
     */
    protected $authorizationHeaderParserFactory;
    
    /**
     * @var RFC3986URIHostValidatorInterface
     */
    protected $RFC3986URIHostValidator;
    
    /**
     * @var URIFragmentIdentifierInterface 
     */
    protected $URIFragmentIdentifier;
    
    /**
     * @var UriInterface
     */
    protected $URI;
 
    /**
     * Constructs a new parser.
     * 
     * @param AuthorizationHeaderParserFactoryInterface $authorizationHeaderParserFactory An authorization header parser factory.
     * @param RFC3986URIHostValidatorInterface A `uri-host` RFC 3986 validator.
     * @param URIFragmentIdentifierInterface A URI fragment parsed body identifier.
     * @param UriInterface $URI A URI to use to populate the final URI to return from the parsed data.
     */
    public function __construct(AuthorizationHeaderParserFactoryInterface $authorizationHeaderParserFactory,
            RFC3986URIHostValidatorInterface $RFC3986URIHostValidator,
            URIFragmentIdentifierInterface $URIFragmentIdentifier,
            UriInterface $URI
    ) {
        $this->authorizationHeaderParserFactory = $authorizationHeaderParserFactory;
        $this->RFC3986URIHostValidator = $RFC3986URIHostValidator;
        $this->URIFragmentIdentifier = $URIFragmentIdentifier;
        $this->URI = $URI;
    }
    
    /**
     * {@inheritdoc}
     */
    public function parseURI(ServerRequestInterface $serverRequest, $parsedBody = NULL, $queryParams = NULL): UriInterface {
        // Base URI.
        $URI = $this->URI;
        
        // URI scheme.
        $scheme = $this->parseSchemeFromServerRequest($serverRequest);
        
        // User info.
        list($user, $password) = $this->parseUserInfoFromServerRequest($serverRequest);
        
        // Host and port.
        list($host, $port) = $this->parseHostAndPortFromServerRequest($serverRequest);
        
        // Path.
        $path = $this->parsePathFromServerRequest($serverRequest);
        
        // Query string.
        $query = $this->parseQueryFromServerRequest($serverRequest);
        
        // Fragment.
        $fragment = $this->parseFragmentFromServerRequestAndParsedBody($serverRequest, $parsedBody, $queryParams);
        
        $finalURI = $URI->withScheme($scheme)
                ->withUserInfo($user, $password)
                ->withHost($host)
                ->withPort($port)
                ->withPath($path)
                ->withQuery($query)
                ->withFragment($fragment)
        ;
        
        return $finalURI;
    }
    
    /**
     * Gets a value from the given server request URI.
     * 
     * @param ServerRequestInterface $serverRequest The server request.
     * @param string $method The method to call on the server request URI. MUST be a method of the {@link UriInterface}.
     * @return mixed The value after calling `$method` on the server request URI of the given server request.
     */
    protected function getServerRequestURIValue(ServerRequestInterface $serverRequest, $method) {
        $currentServerRequestURI = $serverRequest->getUri();
        $currentServerRequestURI->{$method}();
    }
    
    /**
     * Parses a URI scheme from the given server request.
     * 
     * @param ServerRequestInterface $serverRequest The server request.
     * @return string The URI scheme. Implementors MAY override this method and return an empty string if the URI scheme cannot be determined.
     *                         Otherwise the scheme will default to `http` if `https` is not used.
     */
    protected function parseSchemeFromServerRequest(ServerRequestInterface $serverRequest) {
        $scheme = '';

        $currentScheme = $this->getServerRequestURIValue($serverRequest, 'getScheme');
        if (!empty($currentScheme)) {
            $scheme = $currentScheme;
        }
        else {
            $serverParams = $serverRequest->getServerParams();
            $xForwardedProtoHeaderValue = $serverRequest->getHeaderLine('X-Forwarded-Proto');
            $scheme = (
                    (!empty($serverParams['HTTPS']) && strtolower($serverParams['HTTPS']) !== 'off')
                    ||
                    (strtolower($xForwardedProtoHeaderValue) === 'https')
                )
                ?
                'https'
                :
                'http';
        }
        
        return $scheme;
    }
    
    /**
     * Parses the user info from the given server request.
     * 
     * @param ServerRequestInterface $serverRequest The server request.
     * @return array An array with the user name at index 0 (MAY be an empty string if no user name info is present)
     *                       and the password at index 1 (May be null), so that `list()` can be used with this return value.
     *                       to set both user name and password to different variables.
     */
    protected function parseUserInfoFromServerRequest(ServerRequestInterface $serverRequest) {
        $user = '';
        $password = null;
        
        $currentUserInfo = $this->getServerRequestURIValue($serverRequest, 'getUserInfo');
        if (!empty($currentUserInfo)) {
            $explode = explode(':', $currentUserInfo, 2);
            if (!isset($explode[1])) {
                $explode[1] = null;
            }
            return $explode;
        }
        else {
            if ($serverRequest->hasHeader('Authorization')) {
                $authorizationHeaderValue = $serverRequest->getHeaderLine('Authorization');
                
                /* @var $authorizationHeaderParser Norma\HTTP\Authentication\AuthorizationHeaderCredentialsParserInterface */
                $authorizationHeaderParser = $this->authorizationHeaderParserFactory->makeAuthorizationHeaderCredentialsParser($authorizationHeaderValue);
                list($authenticationUser, $authenticationPassword) = $authorizationHeaderParser->parseUserAndPassword();
                $user = $authenticationUser;
                $password = $authenticationPassword;
            }
        }
        
        return [$user, $password];
    }
    
    /**
     * Parses the host from the given server request and scheme.
     * 
     * @source https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.23 Specifies that the port number should be included only if it is not the default port of the protocol being used.
     * @source https://stackoverflow.com/questions/6768793/get-the-full-url-in-php#answer-8891890 Provides insights on the `X-Forwarded-Host` header.
     * @source https://tools.ietf.org/html/rfc2732.html Provides examples of IPv6 used in URLs.
     * 
     * @param ServerRequestInterface $serverRequest The server request.
     * @param bool $useXForwardedHost Whether or not to use the `X-Forwarded-Host` header for the host if there is a valid one. Defaults to TRUE.
     * @param bool $useXForwardedPort Whether or not to use the `X-Forwarded-Port` header for the port if there is a valid one. Defaults to TRUE.
     * @return string The host. An empty string is returned if the host cannot be determined.
     */
    protected function parseHostAndPortFromServerRequest(ServerRequestInterface $serverRequest,
            $useXForwardedHost = TRUE, $useXForwardedPort = TRUE
    ) {
        
        /*
         * Per RFC 7230, which defines the `Host` header:
         * 
         *      Host = uri-host [ ":" port ]
         * 
         * `uri-host` is defined in RFC 3986, section 3.2.2:
         * 
         *      uri-host = <host, see [RFC3986], Section 3.2.2>
         * 
         *      host = IP-literal / IPv4address / reg-name
         * 
         *      IP-literal = "[" ( IPv6address / IPvFuture  ) "]"
         * 
         *      IPv4address = dec-octet "." dec-octet "." dec-octet "." dec-octet
         * 
         *      reg-name = *( unreserved / pct-encoded / sub-delims )
         * 
         *      dec-octet = DIGIT                           ; 0-9
         *                          / %x31-39 DIGIT         ; 10-99
         *                          / "1" 2DIGIT                 ; 100-199
         *                          / "2" %x30-34 DIGIT    ; 200-249
         *                          / "25" %x30-35            ; 250-255
         * 
         *      IPv6address = 6( h16 ":" ) ls32
         *                             / "::" 5( h16 ":" ) ls32
         *                             / [ h16 ] "::" 4( h16 ":" ) ls32
         *                             / [ *1( h16 ":" ) h16 ] "::" 3( h16 ":" ) ls32
         *                             / [ *2( h16 ":" ) h16 ] "::" 2( h16 ":" ) ls32
         *                             / [ *3( h16 ":" ) h16 ] "::" h16 ":" ls32
         *                             / [ *4( h16 ":" ) h16 ] "::" ls32
         *                             / [ *5( h16 ":" ) h16 ] "::" h16
         *                             / [ *6( h16 ":" ) h16 ] "::"
         * 
         *      ls32 = ( h16 ":" h16 ) / IPv4address
         *                 ; least-significant 32 bits of address
         * 
         *      h16 = 1*4HEXDIG
         *                 ; 16 bits of address represented in hexadecimal
         * 
         *      IPvFuture = "v" 1*HEXDIG "." 1*( unreserved / sub-delims / ":" )
         *  
         *      unreserved = ALPHA / DIGIT / "-" / "." / "_" / "~"
         * 
         *      sub-delims = "!" / "$" / "&" / "'" / "(" / ")"
         *                              / "*" / "+" / "," / ";" / "="
         * 
         *      pct-encoded = "%" HEXDIG HEXDIG
         * 
         *      HEXDIG = DIGIT / "A" / "B" / "C" / "D" / "E" / "F"
         * 
         * @source https://tools.ietf.org/html/rfc7230#section-5.4
         * @source https://tools.ietf.org/html/rfc3986#section-3.2.2
         */
        $host = '';
        $port = null;
        
        $overrideHost = TRUE;
        $overridePort = TRUE;
        
        // Server request URI host and port.
        $currentHost = $this->getServerRequestURIValue($serverRequest, 'getHost');
        $currentPort = $this->getServerRequestURIValue($serverRequest, 'getPort');
        if (!empty($currentHost)) {
            $overrideHost = FALSE;
            $host = $currentHost;
        }
        if (is_int($currentPort)) {
            $overridePort = FALSE;
            $port = $currentPort;
        }
        if (!$overrideHost && !$overridePort) {
            // Both host and port existed in the server request URI.
            return [$host, $port];
        }
        
        // `X-Forwarded-Host` and `X-Forwarded-Port` headers.
        if ($overrideHost && $useXForwardedHost && $serverRequest->hasHeader('X-Forwarded-Host')) {
            $xForwardedHost = $serverRequest->getHeaderLine('X-Forwarded-Host');
            if (!empty($xForwardedHost)) {
                $overrideHost = FALSE;
                $host = $xForwardedHost;
            }
        }
        if ($overridePort && $useXForwardedPort && $serverRequest->hasHeader('X-Forwarded-Port')) {
            $xForwardedPort = (string) $serverRequest->getHeaderLine('X-Forwarded-Port');
            if (ctype_digit($xForwardedPort)) {
                $overridePort = FALSE;
                $port = $xForwardedPort;
            }
        }
        
        // Host and port determined from the `Host` header.
        // Host header may be a hostname (e.g.: `localhost`, `www.domain.com`), an IPv4 (e.g.: `127.0.0.1` for localhost) or an IP literal (e.g.: `[::ffff:7f00:1]` or `[::1]` for localhost).
        if (($overrideHost || $overridePort) && $serverRequest->hasHeader('Host')) {
            $hostHeader = $serverRequest->getHeaderLine('Host');
            if (!empty($hostHeader)) {
                $match = [];
                $hostHeaderHost = $hostHeader;
                if (preg_match('/^(?P<host>.+):(?P<port>\d+)$/', $hostHeader, $match)) {
                    $hostHeaderHost = $match['host'];
                    $port = $overridePort ? $match['port'] : $port;
                }
                
                if ($overrideHost) {
                    $overrideHost = FALSE;
                    $host = $hostHeaderHost;
                }
            }
        }
        
        // Server name.
        if ($overrideHost) {
            $serverName = $serverRequest->getServerParam('SERVER_NAME');
            if (!empty($serverName)) {
                $overrideHost = FALSE;
                $possibleIPLiteral = '[' . $serverName . ']';
                if ($this->RFC3986URIHostValidator->isValidIPLiteral($possibleIPLiteral)) {
                    $host = $possibleIPLiteral;
                }
                else {
                    $host = $serverName;
                }
            }
            if ($overrideHost) {
                $serverAddress = $serverRequest->getServerParam('SERVER_ADDR');
                if (!empty($serverAddress)) {
                    $overrideHost = FALSE;
                    $possibleIPLiteral = '[' . $serverAddress . ']';
                    if ($this->RFC3986URIHostValidator->isValidIPLiteral($possibleIPLiteral)) {
                        $host = $possibleIPLiteral;
                    }
                    else {
                        $host = $serverAddress;
                    }
                }
            }
        }
        
        // Server port.
        if ($overridePort) {
            $serverPort = (string) $serverRequest->getServerParam('SERVER_PORT');
            if (ctype_digit($serverPort)) {
                $port = $overridePort ? $serverPort : $port;
            }
        }
        
        if (!$this->RFC3986URIHostValidator->isValidURIHost($host)) {
            // Reset host if invalid (the host is not a valid reg-name, IPv4 or IP literal, i.e. it doesn't conform with the RFC 3986).
            $host = '';
        }
        
        return [$host, (int) $port];
    }
    
    /**
     * Parses the path from the given server request.
     * 
     * @param ServerRequestInterface $serverRequest The server request.
     * @return string The path.
     */
    public function parsePathFromServerRequest(ServerRequestInterface $serverRequest) {
        $path = '';
        
        $currentPath = $this->getServerRequestURIValue($serverRequest, 'getPath');
        if (!empty($currentPath)) {
            $path = $currentPath;
        }
        else {
            $serverRequestParams = $serverRequest->getServerParams();
            if (!empty($serverRequestParams['REQUEST_URI'])) {
                $path = explode('?', $serverRequestParams['REQUEST_URI'], 2)[0];
                $path = preg_replace('~^(?:[^:/]+)://(?:[^/]+)~', '', $path);
            }
            else {
                if (!empty($serverRequestParams['ORIG_PATH_INFO'])) {
                    $path = $serverRequestParams['ORIG_PATH_INFO'];
                }
            }   
        }
        
        return $path;
    }
    
    /**
     * Parses the query string from the given server request.
     * 
     * @param ServerRequestInterface $serverRequest The server request.
     * @return string The query string.
     */
    public function parseQueryFromServerRequest(ServerRequestInterface $serverRequest) {
        $query = '';
        $currentQuery = $this->getServerRequestURIValue($serverRequest, 'getQuery');
        if (!empty($currentQuery)) {
            $query = $currentQuery;
        }
        else {
            $serverQueryString = $serverRequest->getServerParam('QUERY_STRING');
            if (!empty($serverQueryString)) {
                $query = $serverQueryString;
            }
        }
        return $query;
    }
    
    /**
     * Parses the URI fragment from the given server request, parsed body and query params.
     * 
     * @param ServerRequestInterface $serverRequest The server request.
     * @param array|null $parsedBody The parsed body. Typically `$_POST`.
     * @param array|null $queryParams The query params. Typically `$_GET`.
     * @return string The URI fragment.
     */
    public function parseFragmentFromServerRequestAndParsedBody(ServerRequestInterface $serverRequest, $parsedBody = NULL, $queryParams = NULL) {
        $fragment = '';
        $currentFragment = $this->getServerRequestURIValue($serverRequest, 'getFragment');
        if (!empty($currentFragment)) {
            $fragment = $currentFragment;
        }
        else if (
            !empty($parsedBody)
            ||
            !empty($queryParams)
        ) {
            $field = $this->URIFragmentIdentifier->get();
            if (!empty($parsedBody[$field])) {
                $fragment = ltrim($parsedBody[$field], '#');
            }
            else if (!empty($queryParams[$field])) {
                $fragment = ltrim($queryParams[$field], '#');
            }
        }
        
        return $fragment;
    }

}
