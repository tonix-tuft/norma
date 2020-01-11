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

namespace Norma\HTTP\Request\Server;

use Norma\HTTP\Request\Server\ServerRequestFactoryInterface;
use Norma\HTTP\Request\Server\ServerRequestInterface;
use Norma\HTTP\Request\Server\ServerRequest;
use Norma\HTTP\Upload\UploadedFilesArrayNormalizerInterface;
use Norma\HTTP\Request\Server\ServerRequestHeadersParserInterface;
use Norma\HTTP\Request\Server\ServerRequestProtocolVersionParserInterface;
use Norma\HTTP\Request\CookieHeaderParserInterface;
use Norma\HTTP\Request\RequestMethodEnum;
use Norma\HTTP\URI\URIParserInterface;
use Norma\HTTP\Stream\InputStreamFactoryInterface;
use Norma\Core\Env\EnvInterface;

/**
 * The implementation of a server request factory.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class ServerRequestFactory implements ServerRequestFactoryInterface {
    
    /**
     * @var UploadedFilesArrayNormalizerInterface
     */
    protected $uploadedFilesNormalizer;
    
    /**
     * @var ServerRequestHeadersParserInterface
     */
    protected $headersParser;
    
    /**
     * @var ServerRequestProtocolVersionParserInterface
     */
    protected $protocolVersionParser;
    
    /**
     * @var CookieHeaderParserInterface
     */
    protected $cookieHeaderParser;
    
    /**
     * @var URIParserInterface
     */
    protected $URIParser;
    
    /**
     * @var InputStreamFactoryInterface
     */
    protected $inputStreamFactory;
    
    /**
     * @var EnvInterface
     */
    protected $env;
    
    /**
     * Constructs a new factory.
     * 
     * @param UploadedFilesArrayNormalizerInterface $uploadedFilesNormalizer An uploaded files normalizer.
     * @param ServerRequestHeadersParserInterface $headersParser A server request headers parser.
     * @param ServerRequestProtocolVersionParserInterface $protocolVersionParser A server request protocol version parser.
     * @param URIParserInterface $URIParser A URI parser interface.
     * @param InputStreamFactoryInterface $inputStreamFactory An input stream factory.
     * @param EnvInterface $env The environment.
     */
    public function __construct(UploadedFilesArrayNormalizerInterface $uploadedFilesNormalizer, ServerRequestHeadersParserInterface $headersParser,
            ServerRequestProtocolVersionParserInterface $protocolVersionParser, CookieHeaderParserInterface $cookieHeaderParser,
            URIParserInterface $URIParser, InputStreamFactoryInterface $inputStreamFactory, EnvInterface $env
    ) {
        $this->uploadedFilesNormalizer = $uploadedFilesNormalizer;
        $this->headersParser = $headersParser;
        $this->protocolVersionParser = $protocolVersionParser;
        $this->cookieHeaderParser = $cookieHeaderParser;
        $this->URIParser = $URIParser;
        $this->inputStreamFactory = $inputStreamFactory;
        $this->env = $env;
    }
    
    /**
     * {@inheritdoc}
     */
    public function makeRequest($server = NULL, $files = NULL, $queryParams = NULL, $body = NULL, $parsedBody = NULL, $cookies = NULL): ServerRequestInterface {
        // Server params.
        $serverParams = $server ?? $_SERVER ?? [];
        $serverParams['NORMA_APP_URI_BASE_PATH'] = $this->env->get('NORMA_APP_URI_BASE_PATH');
        
        // Headers.
        $headers = $this->headersParser->parseHeadersFromServerParams($serverParams);
        
        // Request method.
        $method = $serverParams['REQUEST_METHOD'] ?? RequestMethodEnum::GET;
        
        // Base instance.
        $serverRequest = new ServerRequest($serverParams, NULL, $method, $headers);
        
        // Uploaded files.
        $uploadedFiles = $this->uploadedFilesNormalizer->normalize($files ?? $_FILES ?? []);
        
        // Parsed body.
        $parsedBody = $parsedBody ?? $_POST ?? null;
        
        // Query params.
        $queryParams = $queryParams ?? $_GET ?? [];
        
        // URI.
        $URI = $this->URIParser->parseURI($serverRequest, $parsedBody, $queryParams);
        
        // Version.
        $serverRequestServerParams = $serverRequest->getServerParams();
        $version = $this->protocolVersionParser->parseProtocolVersionFromServerParams($serverRequestServerParams);
        
        // Cookies.
        $cookieParams = $cookies ?? $_COOKIE ?? [];
        if ($serverRequest->hasHeader('Cookie')) {
            $cookieHeaderLine = $serverRequest->getHeaderLine('Cookie');
            $parsedCookies = $this->cookieHeaderParser->parseCookieFromCookieHeader($cookieHeaderLine);
            $cookieParams = array_merge($cookieParams, $parsedCookies);
        }
        
        $serverRequestBeforeBody = $serverRequest->withUploadedFiles($uploadedFiles)
                ->withUri($URI)
                ->withQueryParams($queryParams)
                ->withParsedBody($parsedBody)
                ->withCookieParams($cookieParams)
                ->withProtocolVersion($version)
        ;
        
        // Body stream.
        $bodyStream = $body ?? $this->inputStreamFactory->makeFromServerRequest($serverRequestBeforeBody);
        
        $finalServerRequest = $serverRequestBeforeBody->withBody($bodyStream);
        
        return $finalServerRequest;
    }
    
}
