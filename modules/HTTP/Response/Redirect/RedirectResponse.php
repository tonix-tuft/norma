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

namespace Norma\HTTP\Response\Redirect;

use Norma\HTTP\Response\Response;
use Norma\HTTP\HTTPStatusCodeEnum;
use Norma\HTTP\Stream\ReadableWritableStreamFactoryInterface;
use Norma\HTTP\Response\Redirect\RedirectResponseInterface;

/**
 * A class which represents an HTTP redirect response message which conforms with the PSR-7 specification.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class RedirectResponse extends Response implements RedirectResponseInterface {
    
    /**
     * @var ReadableWritableStreamFactoryInterface
     */
    protected $readableWritableStreamFactory;
   
    /**
     * @var string
     */
    protected $redirectURI;
    
    /**
     * Constructs a new HTML response.
     * 
     * @param ReadableWritableStreamFactoryInterface A readable and writable stream factory.
     * @param UriInterface|string $redirectURI The URI where to redirect the client.
     * @param int $statusCode The status code.
     * @param array $headers An array of headers.
     * @throws \InvalidArgumentException If the given status code is invalid or if the stream of the body cannot be constructed
     *                                                            because of invalid arguments supplied during its construction.
     * @throws \RuntimeException If the stream of the body cannot be constructed for some reason.
     */
    public function __construct(ReadableWritableStreamFactoryInterface $readableWritableStreamFactory, $redirectURI = '', $statusCode = HTTPStatusCodeEnum::FOUND, array $headers = []) {        
        $this->readableWritableStreamFactory = $readableWritableStreamFactory;
        $body = $this->readableWritableStreamFactory->make();
        
        $this->setHeader('Location', (string) $redirectURI);
        parent::__construct($body, $statusCode, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectURI() {
        return ((string) $this->redirectURI);
    }

    /**
     * {@inheritdoc}
     */
    public function withRedirectURI($redirectURI) {
        $clone = $this->cloneThis();
        $clone->redirectURI = $redirectURI;
        $clone->setHeader('Location', (string) $redirectURI);
        return $clone;
    }

}
