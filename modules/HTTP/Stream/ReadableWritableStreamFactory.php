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

namespace Norma\HTTP\Stream;

use Norma\HTTP\Stream\ReadableWritableStreamFactoryInterface;
use Norma\HTTP\Stream\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * The implementation of a readable and writable stream factory.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class ReadableWritableStreamFactory implements ReadableWritableStreamFactoryInterface {
    
    /**
     * The mode of a readable and writable stream.
     */
    const STREAM_MODE = 'w+b';
    
    /**
     * @var StreamFactoryInterface
     */
    protected $streamFactory;
    
    /**
     * Constructs a new readable and writable stream.
     * 
     * @param StreamFactoryInterface $streamFactory A stream factory.
     */
    public function __construct(StreamFactoryInterface $streamFactory) {
        $this->streamFactory = $streamFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function make(): StreamInterface {
        return $this->streamFactory->makeFromMode(self::STREAM_MODE);
    }

    /**
     * {@inheritdoc}
     */
    public function makeFromFilename($filename): StreamInterface {
        return $this->streamFactory->makeFromFilenameAndMode($filename, self::STREAM_MODE);
    }

}
