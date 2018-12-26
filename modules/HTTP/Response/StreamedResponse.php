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

namespace Norma\HTTP\Response;

use Norma\HTTP\Response\Response;
use Norma\HTTP\Response\StreamedResponseInterface;
use Psr\Http\Message\StreamInterface;
use Norma\HTTP\HTTPStatusCodeEnum;

/**
 * A streamed response which conforms with the PSR-7 specification.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class StreamedResponse extends Response implements StreamedResponseInterface {
    
    /**
     * @var int
     */
    protected $bufferSize;
    
    /**
     * Construct a new response.
     * 
     * @param StreamInterface $body The body stream of the response.
     * @param int $statusCode The status code.
     * @param array $headers An array of headers.
     * @param int $bufferSize The buffer size to read for each chunk when sending the body of the response, in bytes.
     * @throws \InvalidArgumentException If the given status code is invalid or if the stream of the body cannot be constructed
     *                                                            because of invalid arguments supplied during its construction.
     * @throws \RuntimeException If the stream of the body cannot be constructed for some reason.
     */
    public function __construct(StreamInterface $body, $statusCode = HTTPStatusCodeEnum::OK, array $headers = [], $bufferSize = 8192) {
        $this->bufferSize = $bufferSize;
        
        parent::__construct($body, $statusCode, $headers);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function sendHTTPBody() {
        $this->body->rewind();
        while (!$this->body->eof()) {
            $buffer = $this->body->read($this->bufferSize);
            echo $buffer;
        }
        $this->body->close();
    }

    /**
     * {@inheritdoc}
     */
    public function getBufferSize() {
        return $this->bufferSize;
    }

    /**
     * {@inheritdoc}
     */
    public function withBufferSize(int $bufferSize) {
        $clone = $this->cloneThis();
        $clone->bufferSize = $bufferSize;
        
        return $clone;
    }

}
