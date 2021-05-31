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
use Norma\HTTP\Response\Wrapper\ImmediateResponseWrapperInterface;
use Norma\HTTP\Response\Wrapper\ImmediateResponseWithThrowableWrapperInterface;

/**
 * A factory which creates response wrappers.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface ResponseWrapperFactoryInterface {
    
    /**
     * Makes a new immediate response wrapper from the given Norma response.
     * 
     * @param ResponseInterface $response The response to wrap in an immediate response wrapper.
     * @return ImmediateResponseWrapperInterface An immediate response wrapper.
     */
    public function makeImmediateResponseWrapper(ResponseInterface $response): ImmediateResponseWrapperInterface;
    
    /**
     * Makes a new immediate response wrapper with a bound throwable from the given Norma response and throwable error or exception.
     * 
     * @param ResponseInterface $response The response to wrap in an immediate response wrapper.
     * @param \Throwable A throwable.
     * @return ImmediateResponseWrapperInterface An immediate response wrapper.
     */
    public function makeImmediateResponseWithThrowableWrapper(ResponseInterface $response, \Throwable $throwable): ImmediateResponseWithThrowableWrapperInterface;
    
}
