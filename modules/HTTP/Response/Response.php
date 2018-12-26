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

namespace Norma\HTTP\Response;

use Norma\HTTP\Response\AbstractResponse;

/**
 * The implementation of a response class.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class Response extends AbstractResponse {
    
    /**
     * {@inheritdoc}
     */
    protected function sendHTTPHeaders() {
        $statusCode = $this->getStatusCode();
        $headers = $this->getHeaders();
        foreach ($headers as $header => $values) {
            $headerNameLower = strtolower($header);
            $replace = $headerNameLower === 'set-cookie' ? false : true;
            foreach ($values as $value) {
                header(sprintf('%s: %s', $header, $value), $replace, $statusCode);
                $replace = false;
            }
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function sendHTTPCookies() {
        $cookies = $this->getCookies();
        foreach ($cookies as $cookie) {
            /* @var $cookie Norma\HTTP\CookieInterface */
            $options = $cookie->getOptions();
            $name = $cookie->getName();
            $value = $cookie->getValue();
            if (is_array($options)) {
                setcookie($name, $value, $options);
            }
            else {
                setcookie($name, $value, $cookie->getExpire(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHTTPOnly());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function sendHTTPStatusCode() {
        $reasonPhrase = $this->getReasonPhrase();
        $statusCode = $this->getStatusCode();
        $protocolVersion = $this->getProtocolVersion();
        $statusLine = sprintf('HTTP/%s %d%s', $protocolVersion, $statusCode, (!empty($reasonPhrase) ? (' ' . $reasonPhrase) : ''));
        header(
            $statusLine,
            true,
            $statusCode
        );
    }
    
    /**
     * {@inheritdoc}
     */
    protected function sendHTTPBody() {
        $body = $this->getBody();
        echo ((string) $body);
        $body->close();
    }

}
