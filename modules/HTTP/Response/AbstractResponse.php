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

namespace Norma\HTTP\Response;

use Norma\HTTP\AbstractMessage;
use Norma\HTTP\Response\ResponseInterface;
use Norma\HTTP\HTTPStatusCodeEnum;
use Norma\HTTP\CookieInterface;
use Psr\Http\Message\StreamInterface;

/**
 * An abstract HTTP response.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
abstract class AbstractResponse extends AbstractMessage implements ResponseInterface {
    
    /**
     * IANA minimum HTTP status code value.
     * 
     * @source https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     */
    const IANA_MIN_STATUS_CODE = 100;
    
    /**
     * IANA maximum HTTP status code value.
     * 
     * @source https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     */
    const IANA_MAX_STATUS_CODE = 599;
    
    /**
     * @var int
     */
    protected $statusCode;
    
    /**
     * @var string
     */
    protected $reasonPhrase;
    
    /**
     * @var array<CookieInterface>
     */
    protected $cookies;
    
    /**
     * Construct a new response.
     * 
     * @param StreamInterface $body The body stream of the response.
     * @param int $statusCode The status code.
     * @param array $headers An array of headers.
     * @throws \InvalidArgumentException If the given status code is invalid or if the stream of the body cannot be constructed
     *                                                            because of invalid arguments supplied during its construction.
     * @throws \RuntimeException If the stream of the body cannot be constructed for some reason.
     */
    public function __construct(StreamInterface $body, $statusCode = HTTPStatusCodeEnum::OK, array $headers = []) {
        $this->throwExceptionIfInvalidStatusCode($statusCode);
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $this->getDefaultReasonPhraseForStatusCode($statusCode);
        $this->body = $body;
        $this->setHeaders($headers);
        $this->cookies = [];
    }
    
    /**
     * Throws an exception if the given status code is invalid.
     * 
     * @source https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * 
     * @param int $code The status code.
     * @return void
     * @throws \InvalidArgumentException If the given status code is invalid.
     */
    protected function throwExceptionIfInvalidStatusCode($code) {
        if (
            !isset(HTTPStatusCodeEnum::$texts[$code])
            &&
            (
                $code < self::IANA_MIN_STATUS_CODE
                ||
                $code > self::IANA_MAX_STATUS_CODE
            )
        ) {
            throw new \InvalidArgumentException(sprintf('Invalid status code "%s".', $code));
        }
    }
    
    /**
     * Get the default reason phrase for the given status code.
     * 
     * @param int $code The status code.
     * @return string The default reason phrase or an empty string if a default reason phrase for the given status code is missing.
     */
    protected function getDefaultReasonPhraseForStatusCode($code) {
        $reasonPhrase = '';
        if (isset(HTTPStatusCodeEnum::$texts[$code])) {
            $reasonPhrase = HTTPStatusCodeEnum::$texts[$code];
        }
        return $reasonPhrase;
    }
    
    /**
     * Forces setting a status of the response.
     * Use only internally because PSR-7 messages MUST guarantee immutability.
     *
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the provided status code;
     *                                                 if none is provided, implementations MAY
     *                                                 use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    protected function setStatus($code, $reasonPhrase = '') {
        $this->throwExceptionIfInvalidStatusCode($code);
        $this->statusCode = $code;
        
        if (!empty($reasonPhrase)) {
            $this->reasonPhrase = $reasonPhrase;
        }
        else {
            $this->reasonPhrase = $this->getDefaultReasonPhraseForStatusCode($code);
        }
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '') {
        $clone = $this->cloneThis();
        $clone->setStatus($code, $reasonPhrase);
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase() {
        return $this->reasonPhrase ?? '';
    }
    
    /**
     * {@inheritdoc}
     */
    public function withCookie(CookieInterface $cookie) {
        $clone = $this->cloneThis();
        $clone->cookies = array_merge($clone->cookies, [$cookie]);
        
        return $clone;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCookies() {
        return $this->cookies;
    }

    /**
     * {@inheritdoc}
     */
    public function send() {
        $this->throwExceptionIfHeadersAlreadySent();
        $this->sendHTTPHeaders();
        $this->sendHTTPCookies();
        $this->sendHTTPStatusCode();
        $this->sendHTTPBody();
    }
    
    /**
     * Throws an exception if HTTP headers have already been sent.
     * 
     * @throws HeadersAlreadySentException If the HTTP headers have already been sent.
     */
    protected function throwExceptionIfHeadersAlreadySent() {
        $file = '';
        $line = 0;
        if (headers_sent($file, $line)) {
            throw new HeadersAlreadySentException(sprintf('HTTP headers have already been sent in file "%s" at line "%s".', $file, $line));
        }
    }
    
    /**
     * Tests if the underlying body stream is empty or its size is unknown.
     * 
     * @return bool TRUE if the underlying body stream is empty or its size is unknown, FALSE otherwise.
     */
    protected function isBodyEmptyOrSizeUnknown() {
        $size = $this->body->getSize();
        return !is_int($size) || $size <= 0;
    }
    
    /**
     * Send the HTTP headers of the response.
     * 
     * @return void
     */
    abstract protected function sendHTTPHeaders();
    
    /**
     * Send the HTTP cookies of the response.
     * 
     * @return void
     */
    abstract protected function sendHTTPCookies();
    
    /**
     * Send the HTTP status code of the response.
     * 
     * @return void
     */
    abstract protected function sendHTTPStatusCode();
    
    /**
     * Send the HTTP body of the response.
     * 
     * @return void
     */
    abstract protected function sendHTTPBody();
    
}
