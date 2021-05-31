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

namespace Norma\HTTP;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Norma\HTTP\HTTPProtocolVersionEnum;

/**
 * A base abstract class which implements the PSR-7 `MessageInterface`.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
abstract class AbstractMessage implements MessageInterface {
    
    /**
     * A regular expression pattern used to match a valid header name.
     */
    const HEADER_NAME_REGEX = '^[-!#$%&\'*+.^_`|~0-9a-zA-Z]+$';
    
    /**
     * A regular expression pattern used to match a valid header value line folding sequence.
     * 
     * Per RFC 7230:
     * 
     *  obs-fold       = CRLF 1*( SP / HTAB )
     * 
     * @source https://tools.ietf.org/html/rfc7230#section-3.2
     */
    const HEADER_VALUE_LINE_FOLDING_REGEX = '(?:\r\n[ \t]+)';

    /**
     * A regular expression pattern used to match a valid header value.
     * 
     * Per RFC 7230 and RFC 5234:
     * 
     *  field-value    = *( field-content / obs-fold )
     *  field-content  = field-vchar [ 1*( SP / HTAB ) field-vchar ]
     *  field-vchar = VCHAR / obs-text
     *  obs-text       = %x80-FF
     *  obs-fold       = CRLF 1*( SP / HTAB )
     *                        ; obsolete line folding
     *                        ; see Section 3.2.4
     *  VCHAR          =  %x21-7E
     *                       ; visible (printing) characters
     * 
     * NOTE: the resulting regex slightly differs from the regex which would result from the RFC's ABNF for header field values
     *            otherwise the resulting regex will not match a header value where a single printable non-whitespace character
     *            is between two whitespace characters.
     * 
     * @source https://tools.ietf.org/html/rfc7230#section-3.2
     * @source https://tools.ietf.org/html/rfc7230#section-3.2.4
     * @source https://tools.ietf.org/html/rfc5234#appendix-B.1
     */
    const HEADER_VALUE_REGEX = '^(?:(?:((?:(?:[\x21-\x7E])|(?:[\x80-\xFF]|[ ])))(?:[ \t]+(?1))?)|(?:'.self::HEADER_VALUE_LINE_FOLDING_REGEX.'))*$';
    
    /**
     * @var string
     */
    protected $protocolVersion = HTTPProtocolVersionEnum::V1_1_CODE;
    
    /**
     * @var array<string, array<string>>
     */
    protected $headers = [];
    
    /**
     * @var array<string, string>
     */
    protected $headerNameLowerCaseMap = [];
    
    /**
     * @var StreamInterface
     */
    protected $body;
    
    /**
     * Clones this message.
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
     * Forces setting a header internally modifying the state of the message.
     * Use only internally because PSR-7 messages MUST guarantee immutability.
     * 
     * @param string $headerName The header name.
     * @param string|array $headerValue The header value.
     * @return static The current instance of this message.
     * @throws \InvalidArgumentException If a header name or one of its values is invalid.
     */
    protected function setHeader($headerName, $headerValue) {
        if (!is_array($headerValue)) {
            $headerValue = [$headerValue];
        }
        $this->throwExceptionIfInvalidHeaderNameOrValues($headerName, $headerValue);
        
        $lowerCaseHeaderName = strtolower($headerName);
        
        if (isset($this->headerNameLowerCaseMap[$lowerCaseHeaderName])) {
            $previousHeaderName = $this->headerNameLowerCaseMap[$lowerCaseHeaderName];
            unset($this->headerNameLowerCaseMap[$lowerCaseHeaderName]);
            unset($this->headers[$previousHeaderName]);
        }
        
        $this->headerNameLowerCaseMap[$lowerCaseHeaderName] = $headerName;
        $this->headers[$headerName] = $headerValue;
        
        return $this;
    }
    
    /**
     * Forces the unsetting of a header internally modifying the state of the message.
     * Use only internally because PSR-7 messages MUST guarantee immutability.
     * 
     * @param string $headerName The header name to unset.
     * @return static The current instance of this message.
     */
    protected function unsetHeader($headerName) {
        $lowerCaseHeaderName = strtolower($headerName);
        if (isset($this->headerNameLowerCaseMap[$lowerCaseHeaderName])) {
            $headerNameToUnset = $this->headerNameLowerCaseMap[$lowerCaseHeaderName];
            unset($this->headers[$headerNameToUnset]);
            unset($this->headerNameLowerCaseMap[$lowerCaseHeaderName]);
        }
        return $this;
    }
    
    /**
     * Forces setting headers internally modifying the state of the message.
     * Use only internally because PSR-7 messages MUST guarantee immutability.
     * 
     * @param array $headers An array of headers to set. Each key of the array MUST be a valid header name
     *                                      and the corresponding value must be either an array of values or a single string value
     *                                      which represents the header value to set for that header.
     * @return static The current instance of this message.
     * @throws \InvalidArgumentException If a header has an invalid header name or one of its values is invalid.
     */
    protected function setHeaders($headers) {
        foreach ($headers as $headerName => $headerValue) {
            $this->setHeader($headerName, $headerValue);
        }
        return $this;
    }
    
    /**
     * Forces setting a header if not already set.
     * Use only internally because PSR-7 messages MUST guarantee immutability.
     *
     * @param string $headerName The header name.
     * @param string|array $headerValue The header value.
     * @return static The current instance of this message.
     * @throws \InvalidArgumentException If a header name or one of its values is invalid.
     */
    protected function setHeaderIfNone($headerName, $headerValue) {
        if (!$this->hasHeader($headerName)) {
            $this->setHeader($headerName, $headerValue);
        }
        return $this;
    }
    
    /**
     * Throws an invalid argument exception if the given header name or value is invalid.
     * 
     * @param string $name Case-insensitive header field name.
     * @param array<string> $values Header values.
     * @return void
     * @throws \InvalidArgumentException If a header name or one of its values is invalid.
     */
    protected function throwExceptionIfInvalidHeaderNameOrValues($name, array $values) {
        $this->throwExceptionIfInvalidHeaderName($name);
        $this->throwExceptionIfInvalidHeaderValues($values);
    }
    
    /**
     * Throws an invalid argument exception if the given header name is invalid.
     * 
     * @source https://stackoverflow.com/questions/19028068/illegal-characters-in-http-headers#answer-44652895
     * 
     * @param string $name Case-insensitive header field name.
     * @return void
     * @throws \InvalidArgumentException If a header name is invalid.
     */
    protected function throwExceptionIfInvalidHeaderName($name) {
        /*
         * Per RFC 7230:
         * 
         *  field-name     = token
         * 
         *  token          = 1*tchar
         * 
         *  tchar          = "!" / "#" / "$" / "%" / "&" / "'" / "*"
         *                         / "+" / "-" / "." / "^" / "_" / "`" / "|" / "~"
         *                         / DIGIT / ALPHA
         *                      ; any VCHAR, except delimiters
         * 
         * @source https://tools.ietf.org/html/rfc7230#section-3.2
         * @source https://tools.ietf.org/html/rfc7230#section-3.2.6
         */
        if (!preg_match('/' . self::HEADER_NAME_REGEX . '/', $name)) {
            throw new \InvalidArgumentException(sprintf('The header name "%s" is not valid.', $name));
        }
    }
    
    /**
     * Throws an invalid argument exception if one of the given header values is invalid.
     * 
     * @param array<string> $values Header values.
     * @return void
     * @throws \InvalidArgumentException If one of the header values is invalid.
     */
    protected function throwExceptionIfInvalidHeaderValues(array $values) {
        foreach ($values as $value) {
            $this->throwExceptionIfInvalidHeaderValue($value);
        }
    }
    
    /**
     * Throws an invalid argument exception if the given header value is invalid.
     * 
     * @param string $value The header value.
     * @return void
     * @throws \InvalidArgumentException If a header name is invalid.
     */
    protected function throwExceptionIfInvalidHeaderValue($value) {
        if (!preg_match('/'.self::HEADER_VALUE_REGEX.'/', $value)) {
            throw new \InvalidArgumentException(sprintf('The header value "%s" is not valid.', $value));
        }
    }
    
    /**
     * Tests whether a header value is valid and has line folding.
     * 
     * @param string $headerName Header.
     * @return bool True if the header value is valid and line folded, false otherwise.
     */
    public function isValidAndHasLineFolding($headerName) {
        /*
         * Historically, HTTP header field values could be extended over
         * multiple lines by preceding each extra line with at least one space
         * or horizontal tab (obs-fold).  This specification deprecates such
         * line folding except within the message/http media type
         * (Section 8.3.1).  A sender MUST NOT generate a message that includes
         * line folding (i.e., that has any field-value that contains a match to
         * the obs-fold rule) unless the message is intended for packaging
         * within the message/http media type.
         * 
         * @source https://tools.ietf.org/html/rfc7230#section-3.2.4
         */
        $values = $this->getHeader($headerName);
        if (!empty($values)) {
            foreach ($values as $value) {
                try {
                    $this->throwExceptionIfInvalidHeaderValue($value);
                    if (preg_match('/'.self::HEADER_VALUE_LINE_FOLDING_REGEX.'/', $value)) {
                        return TRUE;
                    }
                }
                catch (\InvalidArgumentException $ex) {
                    return FALSE;
                }
            }
        }
        return FALSE;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion() {
        return $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version) {
        $clone = $this->cloneThis();
        $clone->protocolVersion = $version;
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name) {
        $lowerCaseHeaderName = strtolower($name);
        return isset($this->headerNameLowerCaseMap[$lowerCaseHeaderName]) && 
            isset($this->headers[$this->headerNameLowerCaseMap[$lowerCaseHeaderName]]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name) {
        if (!$this->hasHeader($name)) {
            return [];
        }
        
        $lowerCaseHeaderName = strtolower($name);
        return $this->headers[$this->headerNameLowerCaseMap[$lowerCaseHeaderName]];
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name) {
        return implode(',', $this->getHeader($name));
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value) {
        if (!is_array($value)) {
            $values = [$value];
        }
        else {
            $values = $value;
        }
        
        $this->throwExceptionIfInvalidHeaderNameOrValues($name, $values);
        
        $clone = $this->cloneThis();
        $lowerCaseHeaderName = strtolower($name);
        if (isset($clone->headerNameLowerCaseMap[$lowerCaseHeaderName])) {
            $header = $clone->headerNameLowerCaseMap[$lowerCaseHeaderName];
            unset($clone->headers[$header]);
        }
        $clone->headerNameLowerCaseMap[$lowerCaseHeaderName] = $name;
        $clone->headers[$name] = $values;
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value) {
        $thisHeader = $this->getHeader($name);
        
        $clone = $this->withHeader($name, $value);
        $cloneHeader = $clone->getHeader($name);
        
        $clone->headers[$name] = array_merge($thisHeader, $cloneHeader);
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name) {
        $clone = $this->cloneThis();
        $lowerCaseHeaderName = strtolower($name);
        
        if ($clone->hasHeader($name)) {
            $headerName = $clone->headerNameLowerCaseMap[$lowerCaseHeaderName];
            unset($clone->headers[$headerName]);
            unset($clone->headerNameLowerCaseMap[$lowerCaseHeaderName]);
        }
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body) {
        $clone = $this->cloneThis();
        $clone->body = $body;
        return $clone;
    }
    
}
