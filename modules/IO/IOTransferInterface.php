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

use Psr\Http\Message\StreamInterface;

/**
 * The interface of an IO transfer object which lets transfer contents from one source to another easily.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface IOTransferInterface {
    
    /**
     * Transfer the contents of a file to another file up to a maximum length of `$maxLength` (if given).
     * 
     * @param string $sourceFilename The source filename.
     * @param string $destFilename The destination filename. The destination file SHOULD be truncated.
     * @param int|null $maxLength The maximum length to transfer from the source file to the destination file, in bytes.
     *                                                If NULL (default), all the contents of the source file MUST be transfered to the destination file.
     * @throws \RuntimeException If the transfer fails.
     */
    public function fromFileToFile($sourceFilename, $destFilename, $maxLength = NULL);
    
    /**
     * Transfer the contents of a stream to another stream up to a maximum length of `$maxLength` (if given).
     * 
     * @param StreamInterface $source The source stream.
     * @param StreamInterface $dest The destination stream.
     * @param int|null $maxLength The maximum length to transfer from the source stream to the destination stream, in bytes.
     *                                                If NULL (default), all the contents of the source stream MUST be transfered to the destination file.
     * @throws \RuntimeException If the transfer fails.
     */
    public function fromStreamToStream(StreamInterface $source, StreamInterface $dest, $maxLength = NULL);
    
    /**
     * Set the chunk size in bytes to use when transferring contents.
     * 
     * @param int $chunkSize The size of a single chunk to use when reading from the source.
     * @return static
     */
    public function setChunkSize(int $chunkSize);
    
}
