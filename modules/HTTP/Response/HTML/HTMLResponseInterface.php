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

namespace Norma\HTTP\Response\HTML;

use Norma\HTTP\Response\ResponseInterface;

/**
 * The interface of an HTML response which conforms with the PSR-7 specification.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
interface HTMLResponseInterface extends ResponseInterface {
    
    /**
     * Get the HTML content associated with this HTML response.
     * 
     * @return string The HTML content of this response.
     */
    public function getHTMLContent();
    
    /**
     * Return an instance with the given HTML content.
     * This method SHOULD NOT update the underlying body stream of the response.
     * If the HTML content has already been written to the underlying body stream of the response then the
     * HTML content added through this method SHOULD NOT be used for the response.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated HTML content.
     * 
     * @param string $HTMLContent The HTML content.
     * @return static
     */
    public function withHTMLContent($HTMLContent);
    
}
