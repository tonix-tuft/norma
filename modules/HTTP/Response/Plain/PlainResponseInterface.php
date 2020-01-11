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

namespace Norma\HTTP\Response\Plain;

use Norma\HTTP\Response\ResponseInterface;

/**
 * The interface of a plain text response which conforms with the PSR-7 specification.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface PlainResponseInterface extends ResponseInterface {
    
    /**
     * Get the plain text content associated with this plain text response.
     * 
     * @return string The plain text content of this response.
     */
    public function getPlainContent();
    
    /**
     * Return an instance with the given plain text content.
     * This method SHOULD NOT update the underlying body stream of the response.
     * If the plain text content has already been written to the underlying body stream of the response then the
     * plain text content added through this method SHOULD NOT be used for the response.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated plain text content.
     * 
     * @param string $plainContent The plain text content.
     * @return static
     */
    public function withPlainContent($plainContent);
    
}
