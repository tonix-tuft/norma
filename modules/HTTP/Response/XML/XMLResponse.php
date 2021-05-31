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

namespace Norma\HTTP\Response\XML;

use Norma\HTTP\Response\Response;
use Norma\HTTP\Stream\ReadableWritableStreamFactoryInterface;
use Norma\HTTP\HTTPStatusCodeEnum;
use Norma\HTTP\Response\XML\XMLResponseInterface;

/**
 * A class which represents an HTTP XML response which conforms with the PSR-7 specification.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class XMLResponse extends Response implements XMLResponseInterface {
    
    /**
     * @var ReadableWritableStreamFactoryInterface
     */
    protected $readableWritableStreamFactory;
    
    /** 
     * @var string
     */
    protected $XMLContent;
    
    /**
     * Constructs a new XML response.
     * 
     * @param ReadableWritableStreamFactoryInterface $readableWritableStreamFactory A readable and writable stream factory.
     * @param string $XMLContent The initial XML content of the response. Can either be the complete XML content or a part of it.
     * @param int $statusCode The status code.
     * @param array $headers An array of headers.
     * @throws \InvalidArgumentException If the given status code is invalid or if the stream of the body cannot be constructed
     *                                                            because of invalid arguments supplied during its construction.
     * @throws \RuntimeException If the stream of the body cannot be constructed for some reason.
     */
    public function __construct(ReadableWritableStreamFactoryInterface $readableWritableStreamFactory, $XMLContent = '', $statusCode = HTTPStatusCodeEnum::OK, array $headers = []) {
        $this->readableWritableStreamFactory = $readableWritableStreamFactory;
        $body = $this->readableWritableStreamFactory->make();
        
        $this->XMLContent = $XMLContent;
        
        parent::__construct($body, $statusCode, $headers);
        
        $this->setHeaderIfNone('Content-Type', 'application/xml; charset=utf-8');
    }
    
    /**
     * Writes the XML content to the body of this XML response.
     * By default, this implementation writes the XML content only if the body stream of the response
     * is empty. If the body stream of the response is not empty, then nothing is written.
     * 
     * @return void
     * @throws \RuntimeException On failure of the writing process.
     */
    protected function writeXML() {
        if ($this->isBodyEmptyOrSizeUnknown()) {
            $this->body->write($this->XMLContent);
            $this->body->rewind();   
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function sendHTTPBody() {
        // Write the XML content before sending the response.
        $this->writeXML();
        parent::sendHTTPBody();
    }

    /**
     * {@inheritdoc}
     */
    public function getXMLContent() {
        return $this->XMLContent;
    }

    /**
     * {@inheritdoc}
     */
    public function withXMLContent($XMLContent) {
        $clone = $this->cloneThis();
        $clone->XMLContent = $XMLContent;
        return $clone;
    }
    
}
