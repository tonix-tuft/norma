<?php

/*
 * Copyright (c) 2020 Anton Bagdatyev (Tonix)
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

namespace Norma\HTTP\Response\Range;

use Norma\HTTP\Response\StreamedResponseInterface;
use Norma\HTTP\Request\Server\ServerRequestInterface;

/**
 * A streamed response which is also aware of the HTTP `Range` request header
 * of a range request made to a server and which behaves consistently with the RFC 7233.
 * 
 * @source https://httpwg.org/specs/rfc7233.html
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface RangeRequestAwareStreamedResponseInterface extends StreamedResponseInterface {
    
    /**
     * Return an instance associated with the specified request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated request.
     * 
     * @param ServerRequestInterface The request.
     * @return static
     */
    public function withRequest(ServerRequestInterface $request);
    
    /**
     * Gets the request associated with this range-aware response.
     * 
     * @return ServerRequestInterface|null The request associated with this range-aware response or NULL if none is associated.
     */
    public function getRequest();
    
}
