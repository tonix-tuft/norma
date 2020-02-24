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

namespace Norma\HTTP\Response\Void;

use Norma\HTTP\Response\Response;
use Norma\HTTP\HTTPStatusCodeEnum;
use Norma\HTTP\Stream\ReadableStreamFactoryInterface;
use Norma\HTTP\Response\Void\VoidResponseInterface;

/**
 * A class which represents an HTTP response which doesn't have any content and conforms with the PSR-7 specification.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class VoidResponse extends Response implements VoidResponseInterface {
    
    /**
     * Constructs a new void response.
     * 
     * @param ReadableStreamFactoryInterface $readableStreamFactory A readable stream factory.
     * @param int $statusCode The status code.
     * @param array $headers An array of headers.
     * @throws \InvalidArgumentException If the given status code is invalid or if the stream of the body cannot be constructed
     *                                                            because of invalid arguments supplied during its construction.
     * @throws \RuntimeException If the stream of the body cannot be constructed for some reason.
     */
    public function __construct(ReadableStreamFactoryInterface $readableStreamFactory, $statusCode = HTTPStatusCodeEnum::NO_CONTENT, array $headers = []) {
        $body = $readableStreamFactory->make();
        parent::__construct($body, $statusCode, $headers);
    }
    
}
