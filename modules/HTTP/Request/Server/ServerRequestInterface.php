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

namespace Norma\HTTP\Request\Server;

use Psr\Http\Message\ServerRequestInterface as PSR7ServerRequestInterface;
use Norma\HTTP\Request\RequestInterface;

/**
 * The representation of an incoming request on the server (i.e. how the request is seen server-side).
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface ServerRequestInterface extends PSR7ServerRequestInterface, RequestInterface {
    
    /**
     * Get the application's request URI path, which is what the client is asking for.
     * The request URI path MUST be decoded, but encoded slashes (i.e. `%2F`) MUST remain encoded
     * to facilitate the routing path matching process and let the client code differentiate between
     * slashes used as a path separator and encoded ones not used as a path separator.
     *
     * @return string The application's request URI path.
     */
    public function getAppRequestURIPath();
    
    /**
     * Get the value of the `Content-Type` header of this request.
     * 
     * @return string|null The `Content-Type` header of this request.
     */
    public function getContentType();
    
    /**
     * Get the value of a server parameter.
     * 
     * @param string $param The server parameter key.
     * @return mixed The value. NULL is returned when the parameter key does not exist or
     *                         if the value of the parameter key is effectively `null`.
     */
    public function getServerParam($param);
    
}
