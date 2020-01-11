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

namespace Norma\HTTP\Stream;

use Psr\Http\Message\StreamInterface;

/**
 * The general interface of a stream factory.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface StreamFactoryInterface {
    
    /**
     * Makes a new stream using the given mode.
     * 
     * @param string $mode The mode to use for the resource of the stream.
     * @return StreamInterface The stream.
     * @throws \InvalidArgumentException If an invalid argument is provided when constructing the stream.
     * @throws \RuntimeException If the stream cannot be constructed for some reason.
     */
    public function makeFromMode($mode): StreamInterface;
    
    /**
     * Makes a new stream using the given filename and mode.
     * 
     * @param string $filename The filename.
     * @param StreamInterface $mode The mode to use for the resource of the stream.
     * @return StreamInterface The stream.
     * @throws \InvalidArgumentException If an invalid argument is provided when constructing the stream.
     * @throws \RuntimeException If the stream cannot be constructed for some reason.
     */
    public function makeFromFilenameAndMode($filename, $mode): StreamInterface;
    
}
