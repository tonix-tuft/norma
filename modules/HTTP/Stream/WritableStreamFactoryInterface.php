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

namespace Norma\HTTP\Stream;

use Psr\Http\Message\StreamInterface;

/**
 * The interface of a factory which creates writable streams.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface WritableStreamFactoryInterface {
    
    /**
     * Makes a new stream from the given filename.
     * 
     * @param string $filename The filename.
     * @return StreamInterface A writable stream for the given filename.
     * @throws \InvalidArgumentException If an invalid argument is provided when constructing the stream.
     * @throws \RuntimeException If the stream cannot be constructed for some reason.
     */
    public function makeFromFilename($filename): StreamInterface;
    
    /**
     * Makes a new empty stream.
     * 
     * @return StreamInterface A writable empty stream for the given filename.
     * @throws \InvalidArgumentException If an invalid argument is provided when constructing the stream.
     * @throws \RuntimeException If the stream cannot be constructed for some reason.
     */
    public function make(): StreamInterface;
    
}
