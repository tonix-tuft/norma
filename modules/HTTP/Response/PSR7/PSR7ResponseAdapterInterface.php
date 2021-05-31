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

namespace Norma\HTTP\Response\PSR7;

use Norma\HTTP\Response\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PSR7ResponseInterface;

/**
 * The interface of a PSR-7 HTTP response adapter.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface PSR7ResponseAdapterInterface extends ResponseInterface {
    
    /**
     * Return the underlying PSR-7 response associated with the adapter.
     * 
     * @return \Psr\Http\Message\ResponseInterface The PSR-7 response.
     */
    public function getResponse();
    
    /**
     * Return an adapter with the specified response.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated response.
     * 
     * @param \Psr\Http\Message\ResponseInterface $response A PSR-7 response.
     * @return static
     */
    public function withResponse(PSR7ResponseInterface $response);
    
}
