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

namespace Norma\IO;

use Norma\IO\IOTransferInterface;
use Norma\HTTP\Stream\ReadableStreamFactoryInterface;
use Norma\HTTP\Stream\WritableStreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * An implementation of the IO transfer interface.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class IOTransfer implements IOTransferInterface {
    
    /**
     * @var ReadableStreamFactoryInterface
     */
    protected $readableStreamFactory;
    
    /**
     * @var WritableStreamFactoryInterface
     */
    protected $writableStreamFactory;
    
    /**
     * @var int
     */
    protected $chunkSize = NULL;
    
    /**
     * Constructs a new IO transfer.
     * 
     * @param ReadableStreamFactoryInterface $readableStreamFactory A readable stream factory.
     * @param WritableStreamFactoryInterface $writableStreamFactory A writable stream factory.
     */
    public function __construct(ReadableStreamFactoryInterface $readableStreamFactory, WritableStreamFactoryInterface $writableStreamFactory) {
        $this->readableStreamFactory = $readableStreamFactory;
        $this->writableStreamFactory = $writableStreamFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function fromFileToFile($sourceFilename, $destFilename, $maxLength = NULL) {
        $readableStream = $this->readableStreamFactory->makeFromFilename($sourceFilename);
        $writableStream = $this->writableStreamFactory->makeFromFilename($destFilename);
        
        $this->fromStreamToStream($readableStream, $writableStream, $maxLength);
        
        $readableStream->close();
        $writableStream->close();
    }

    /**
     * {@inheritdoc}
     */
    public function fromStreamToStream(StreamInterface$source, StreamInterface $dest, $maxLength = NULL) {
        $readableStream = $source;
        $writableStream = $dest;
        
        $readableStream->rewind();
        
        $chunk = $this->chunkSize ?? 8192;
        $toWrite = $maxLength <= 0 ? NULL : $maxLength;
        while (!$readableStream->eof()
            && 
            (is_null($toWrite) || $toWrite >= $chunk)
        ) {
            $buffer = $readableStream->read($chunk);
            $writableStream->write($buffer);
            $toWrite -= strlen($buffer);
        }
        if ($toWrite > 0 && !$readableStream->eof()) {
            $writableStream->write($readableStream->read($toWrite));
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setChunkSize(int $chunkSize) {
        $this->chunkSize = $chunkSize;
        return $this;
    }

}
