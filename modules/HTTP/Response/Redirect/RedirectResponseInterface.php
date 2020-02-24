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

namespace Norma\HTTP\Response\Redirect;

use Norma\HTTP\Response\ResponseInterface;

/**
 * The interface of a redirect response which conforms with the PSR-7 specification.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface RedirectResponseInterface extends ResponseInterface {
    
    /**
     * Get the redirect URI associated with this redirect response.
     * 
     * @return string The redirect URI of this response.
     */
    public function getRedirectURI();
    
    /**
     * Return an instance with the given redirect URI.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated redirect URI.
     * 
     * @param UriInterface|string $redirectURI The redirect URI.
     * @return static
     */
    public function withRedirectURI($redirectURI);
    
}
