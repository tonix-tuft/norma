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

namespace Norma\HTTP\Response\Wrapper;

use Norma\HTTP\Response\ResponseInterface;
use Norma\HTTP\Response\StreamedResponseInterface;
use Norma\HTTP\Response\Wrapper\ImmediateResponseWrapperInterface;
use Norma\HTTP\Response\Wrapper\ImmediateResponseWithThrowableWrapperInterface;

/**
 * Description of ResponseWrapperFactory
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class ResponseWrapperFactory implements ResponseWrapperFactoryInterface {
    
    /**
     * {@inheritdoc}
     */
    public function makeImmediateResponseWrapper(ResponseInterface $response): ImmediateResponseWrapperInterface {
        if ($response instanceof StreamedResponseInterface) {
            return new ImmediateStreamedResponseWrapper($response);
        }
        else {
            return new ImmediateResponseWrapper($response);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function makeImmediateResponseWithThrowableWrapper(ResponseInterface $response, \Throwable $throwable): ImmediateResponseWithThrowableWrapperInterface {
        if ($response instanceof StreamedResponseInterface) {
            $responseWrapper = new ImmediateStreamedResponseWithThrowableWrapper($response);
        }
        else {
            $responseWrapper = new ImmediateResponseWithThrowableWrapper($response);
        }
        return $responseWrapper->withThrowable($throwable);
    }

}
