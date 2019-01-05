<?php

/*
 * Copyright (c) 2019 Anton Bagdatyev (Tonix-Tuft)
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

use Norma\HTTP\Response\Response;
use Norma\HTTP\Stream\ReadableWritableStreamFactoryInterface;
use Norma\HTTP\HTTPStatusCodeEnum;
use Norma\HTTP\Response\Plain\PlainResponseInterface;

/**
 * A class which represents an HTTP plain text response which conforms with the PSR-7 specification.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class PlainResponse extends Response implements PlainResponseInterface {
    
    /**
     * @var ReadableWritableStreamFactoryInterface
     */
    protected $readableWritableStreamFactory;
    
    /**
     * @var string
     */
    protected $plainContent;
    
    /**
     * Constructs a new plain text response.
     * 
     * @param ReadableWritableStreamFactoryInterface $readableWritableStreamFactory A readable and writable stream factory.
     * @param string $plainContent The plain text content of the response. Can either be the complete plain text content or a part of it.
     * @param int $statusCode The status code.
     * @param array $headers An array of headers.
     * @throws \InvalidArgumentException If the given status code is invalid or if the stream of the body cannot be constructed
     *                                                            because of invalid arguments supplied during its construction.
     * @throws \RuntimeException If the stream of the body cannot be constructed for some reason.
     */
    public function __construct(ReadableWritableStreamFactoryInterface $readableWritableStreamFactory, $plainContent = '', $statusCode = HTTPStatusCodeEnum::OK, array $headers = []) {
        $this->readableWritableStreamFactory = $readableWritableStreamFactory;
        $body = $this->readableWritableStreamFactory->make();
        
        $this->plainContent = $plainContent;
        
        parent::__construct($body, $statusCode, $headers);
        
        $this->setHeaderIfNone('Content-Type', 'text/plain; charset=utf-8');
    }
    
    /**
     * Writes the plain text content to the body of this plain text response.
     * By default, this implementation writes the plain text content only if the body stream of the response
     * is empty. If the body stream of the response is not empty, then nothing is written.
     * 
     * @return void
     * @throws \RuntimeException On failure of the writing process.
     */
    protected function writePlainText() {
        if ($this->isBodyEmptyOrSizeUnknown()) {
            $this->body->write($this->plainContent);
            $this->body->rewind();   
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function sendHTTPBody() {
        // Write the plain text content before sending the response.
        $this->writePlainText();
        parent::sendHTTPBody();
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainContent() {
        return $this->plainContent;
    }

    /**
     * {@inheritdoc}
     */
    public function withPlainContent($plainContent) {
        $clone = $this->cloneThis();
        $clone->plainContent = $plainContent;
        return $clone;
    }

}
