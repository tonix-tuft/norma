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

namespace Norma\HTTP\Response\Wrapper;

use Norma\HTTP\Response\ResponseInterface;
use Norma\HTTP\Response\Wrapper\ResponseWrapperTrait;

/**
 * A trait for Norma responses' wrappers.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
trait NormaResponseWrapperTrait {
    
    use ResponseWrapperTrait;
    
    /**
     * @var ResponseInterface
     */
    protected $response;
   
    /**
     * Constructs a new wrapper.
     * 
     * @param ResponseInterface $response A Norma response.
     */
    public function __construct(ResponseInterface $response) {
        $this->response = $response;
        $this->protocolVersion = $response->getProtocolVersion();
        parent::__construct($response->getBody(), $response->getStatusCode(), $response->getHeaders());
    }
    
    /**
     * Return the underlying Norma response associated with the wrapper.
     * 
     * @return ResponseInterface The response.
     */
    public function getResponse() {
        return $this->response;
    }
    
    /**
     * Return a wrapper with the specified response.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated response.
     * 
     * @param ResponseInterface $response A response.
     * @return static
     */
    public function withResponse(ResponseInterface $response) {
        return new self($response);
    }
    
}
