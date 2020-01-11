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

namespace Norma\HTTP\Response;

use Norma\HTTP\CookieInterface;
use Psr\Http\Message\ResponseInterface as PSR7ResponseInterface;

/**
 * The interface of a response which conforms with the PSR-7 specification.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface ResponseInterface extends PSR7ResponseInterface {
    
    /**
     * Return an instance with the specified cookie.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated cookies including the given cookie to add to the response.
     * 
     * @param CookieInterface The cookie to add to the response.
     * @return static
     */
    public function withCookie(CookieInterface $cookie);
    
    /**
     * Return the cookies to be sent by this response.
     * 
     * @return array<CookieInterface> An array of cookies.
     */
    public function getCookies();
    
    /**
     * Send this response.
     * 
     * @return void
     * @throws \Exception If the response cannot be sent for some reason.
     */
    public function send();
    
}
