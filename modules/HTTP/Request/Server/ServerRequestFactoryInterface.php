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

use Norma\HTTP\Request\Server\ServerRequestInterface;

/**
 * The interface of a server request factory.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface ServerRequestFactoryInterface {
    
    /**
     * Makes a new server request.
     * 
     * @param array|null $server The server request server params. Typically `$_SERVER`.
     * @param array|null $files An array of uploaded files. Typically `$_FILES`.
     * @param array|null $queryParams The query params. Typically `$_GET`.
     * @param Psr\Http\Message\StreamInterface|null $body The stream representing the body of the request.
     * @param array|null $parsedBody The parsed body. Typically `$_POST`.
     * @param array|null $cookies The cookies. Typically `$_COOKIE`.
     * @return ServerRequestInterface The server request.
     * @throws \InvalidArgumentException if during the construction of the server request there is an invalid argument.
     */
    public function makeRequest($server = NULL, $files = NULL, $queryParams = NULL, $body = NULL, $parsedBody = NULL, $cookies = NULL): ServerRequestInterface;
    
}
