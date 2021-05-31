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

use Norma\HTTP\Response\PSR7\PSR7ResponseAdapterInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A PSR-7 HTTP response adapter factory interface.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface PSR7ResponseAdapterFactoryInterface {
   
    /**
     * Makes a new Norma's response adapter from the given PSR-7 response.
     * 
     * @param ResponseInterface A PSR-7 response.
     * @return PSR7ResponseAdapterInterface The adapted response.
     */
    public function makeResponseFromResponse(ResponseInterface $response): PSR7ResponseAdapterInterface;
    
    /**
     * Makes a new Norma's streamed response adapter from the given PSR-7 response.
     * 
     * @param ResponseInterface A PSR-7 response.
     * @return PSR7StreamedResponseAdapterInterface The adapted streamed response.
     */
    public function makeStreamedResponseFromResponse(ResponseInterface $response): PSR7StreamedResponseAdapterInterface;
    
}
