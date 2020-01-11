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

namespace Norma\HTTP\Response\JSON;

use Norma\HTTP\Response\Response;
use Norma\Data\Serialization\JSON\JSONEncoderInterface;
use Norma\HTTP\HTTPStatusCodeEnum;
use Norma\HTTP\Response\JSON\JSONResponseInterface;
use Norma\HTTP\Stream\ReadableWritableStreamFactoryInterface;

/**
 * A class representing an HTTP JSON response message which conforms with the PSR-7 specification.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class JSONResponse extends Response implements JSONResponseInterface {
    
    /**
     * Default JSON encoding options.
     * 
     * @source http://php.net/manual/en/json.constants.php
     */
    const DEFAULT_JSON_ENCODING_OPTIONS = JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT|JSON_UNESCAPED_SLASHES;
    
    /**
     * @var JSONEncoderInterface
     */
    protected $JSONEncoder;
    
    /**
     * @var ReadableWritableStreamFactoryInterface
     */
    protected $readableWritableStreamFactory;

    /**
     * @var mixed
     */
    protected $data;
    
    /**
     * @var int
     */
    protected $options;
    
    /**
     * @var int
     */
    protected $depth;
    
    /**
     * Constructs a new JSON response.
     * 
     * @param JSONEncoderInterface A JSON encoder.
     * @param ReadableWritableStreamFactoryInterface $readableWritableStreamFactory A readable and writable stream factory used with the given data to create the JSON body stream of the response.
     * @param mixed $data The data to convert to JSON. The data will be written to the body stream of the response if the body stream is empty when sending the response.
     * @param int $statusCode The status code.
     * @param array $headers An array of headers.
     * @param int Optional options.
     * @param int $depth Optional maximum depth.
     * @throws \InvalidArgumentException If the given status code is invalid or if the stream of the body cannot be constructed
     *                                                            because of invalid arguments supplied during its construction.
     * @throws \RuntimeException If the stream of the body cannot be constructed for some reason.
     * @throws \Norma\Data\Serialization\JSON\JSONException If the provided data cannot be encoded into a JSON payload.
     */
    public function __construct(JSONEncoderInterface $JSONEncoder, ReadableWritableStreamFactoryInterface $readableWritableStreamFactory, $data = NULL, $statusCode = HTTPStatusCodeEnum::OK, array $headers = [], $options = self::DEFAULT_JSON_ENCODING_OPTIONS, $depth = 512) {
        $this->JSONEncoder = $JSONEncoder;
        $this->readableWritableStreamFactory = $readableWritableStreamFactory;
        $this->data = $data;
        $this->options = $options;
        $this->depth = $depth;
        
        $body = $this->readableWritableStreamFactory->make();
        
        parent::__construct($body, $statusCode, $headers);
        
        $this->setHeaderIfNone('Content-Type', 'application/json');
    }
    
    /**
     * Converts the data to JSON content and writes it to the body of this JSON response.
     * By default, this implementation writes the data converted to JSON only if the body stream of the response
     * is empty. If the body stream of the response is not empty, then nothing is written.
     * 
     * @return void
     * @throws \Norma\Data\Serialization\JSON\JSONException If the data of this response cannot be encoded into a JSON payload.
     * @throws \RuntimeException On failure of the writing process.
     */
    protected function writeJSON() {
        if ($this->isBodyEmptyOrSizeUnknown()) {
            $JSONPayload = $this->JSONEncoder->encode($this->data, $this->options, $this->depth);
            $this->body->write($JSONPayload);
            $this->body->rewind();   
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function sendHTTPBody() {
        // Write the JSON payload before sending the response.
        $this->writeJSON();
        parent::sendHTTPBody();
    }

    /**
     * {@inheritdoc}
     */
    public function getData() {
        return $this->data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function withData($data) {
        $clone = $this->cloneThis();
        $clone->data = $data;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withOptions($options) {
        $clone = $this->cloneThis();
        $clone->options = $options;
        return $clone;
    }

}
