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

/**
 * An interface which provides useful methods which lets to identify whether
 * a URI host is a valid `uri-host` as per RFC 3986 (`host`) or not and which `uri-host` is it.
 * 
 * RFC 3986 defines `host` (`uri-host`) as:
 * 
 *      host = IP-literal / IPv4address / reg-name
 * 
 * Implementations should implement the methods of this interface which lets client code
 * to determine whether a given URI host is valid and event let them know which kind of URI host
 * it is (`IP-literal`, `IPv4address` or `reg-name`).
 * 
 * @source https://tools.ietf.org/html/rfc3986#section-3.2.2
 * 
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
interface RFC3986URIHostValidatorInterface {
    
    /**
     * Tests whether the given URI host is valid. This method MUST return TRUE if and only if the given string
     * is a valid `IP-literal`, a valid`IPv4address` or a valid `reg-name`, as defined by RFC 3986.
     * If this method returns TRUE, then one of the methods {@link RFC3986URIHostValidatorInterface::isValidIPLiteral()},
     * {@link RFC3986URIHostValidatorInterface::isValidIPv4Address()} or
     * {@link RFC3986URIHostValidatorInterface::isValidRegName()} MUST also return TRUE.
     * 
     * @param string $URIHost The string to test.
     * @return bool True if the given string is a valid URI host.
     */
    public function isValidURIHost($URIHost);
    
    /**
     * Tests whether the given URI host is a valid IP literal, as defined by RFC 3986 (`IP-literal`).
     * This method MUST return TRUE if and only if the given string is a valid `IP-literal`.
     * If this method returns TRUE, then {@link RFC3986URIHostValidatorInterface::isValidURIHost()}
     * MUST also return TRUE for the same URI host.
     * 
     * @param string $URIHost The string to test.
     * @return bool True if the given string is a valid `IP-literal`, as per RFC 3986.
     */
    public function isValidIPLiteral($URIHost);
    
    /**
     * Tests whether the given URI host is a valid IPv4 address, as defined by RFC 3986 (`IPv4address`).
     * This method MUST return TRUE if and only if the given string is a valid `IPv4address`.
     * If this method returns TRUE, then {@link RFC3986URIHostValidatorInterface::isValidURIHost()}
     * MUST also return TRUE for the same URI host.
     * 
     * @param string $URIHost The string to test.
     * @return bool True if the given string is a valid `IPv4address`, as per RFC 3986.
     */
    public function isValidIPv4Address($URIHost);
    
    /**
     * Tests whether the given URI host is a valid reg-name, as defined by RFC 3986 (`reg-name`).
     * This method MUST return TRUE if and only if the given string is a valid `reg-name`.
     * If this method returns TRUE, then {@link RFC3986URIHostValidatorInterface::isValidURIHost()}
     * MUST also return TRUE for the same URI host.
     * 
     * @param string $URIHost The string to test.
     * @return bool True if the given string is a valid `reg-name`, as per RFC 3986.
     */
    public function isValidRegName($URIHost);
    
}
