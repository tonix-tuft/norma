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

namespace Norma\HTTP\Response\XML;

use Norma\HTTP\Response\ResponseInterface;

/**
 * The interface of an XML response which conforms with the PSR-7 specification.
 * 
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
interface XMLResponseInterface extends ResponseInterface {
    
    /**
     * Get the XML content associated with this XML response.
     * 
     * @return string The XML content of this response.
     */
    public function getXMLContent();
    
    /**
     * Return an instance with the given XML content.
     * This method SHOULD NOT update the underlying body stream of the response.
     * If the XML content has already been written to the underlying body stream of the response then the
     * XML content added through this method SHOULD NOT be used for the response.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated XML content.
     * 
     * @param string $XMLContent The XML content.
     * @return static
     */
    public function withXMLContent($XMLContent);
    
}
