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

namespace Norma\HTTP\Stream;

use Psr\Http\Message\StreamInterface;

/**
 * An implementation of a Norma's stream.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class Stream implements StreamInterface {
    
    /**
     * @var null|resource
     */
    protected $streamResource;
    
    /**
     * Constructs a new stream instance.
     * 
     * @param resource|string $streamResource The stream resource. Either a resource handle or a string specifying a resource to open.
     *                                                                    Defaults to PHP's `php://temp` stream.
     * @param string $mode The mode of the stream. Defaults to 'w+b' (therefore a readable and writable stream with binary mode enabled).
     *                                      If a resource handle for `$streamResource` is given then this parameter will be ignored.
     * @throws \InvalidArgumentException If the provided `$streamResource` is not a string and is not a resource handle identifying the stream.
     * @throws \RuntimeException If `$streamResource` is a string identifying the stream resource handle to open and the stream resource cannot be opened for some reason.
     */
    public function __construct($streamResource = 'php://temp', $mode = 'w+b') {
        if (is_string($streamResource)) {
            $this->streamResource = $this->openStreamResource($streamResource, $mode);
        }
        else if (is_resource($streamResource)) {
            $this->streamResource = $streamResource;
        }
        else {
            throw new \InvalidArgumentException('Invalid stream resource provided, must be either a string or a resource handle.');
        }
    }
    
    /**
     * Opens a new stream resource.
     * 
     * @param string $streamResource The string identifying a stream resource handle to open.
     * @param string $mode The mode for the stream to use.
     * @return resource The opened stream resource handle.
     * @throws \RuntimeException If the stream cannot be opened for some reason.
     */
    protected function openStreamResource($streamResource, $mode) {
        $fopen = @fopen($streamResource, $mode);
        if ($fopen === false) {
            throw new \RuntimeException('Could not open stream.');
        }
        return $fopen;
    }
    
    /**
     * Throws an exception if the stream is not writable.
     * 
     * @return void
     * @throws \RuntimeException If the stream is not writable.
     */
    protected function throwExceptionIfNotWritableStream() {
        if (!$this->isWritable()) {
            throw new \RuntimeException('Stream is not writable.');
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function __toString() {
        try {
            $this->rewind();
            return $this->getContents();
        }
        catch (\Throwable $e) {
            // PHP's `__toString()` method MUST not throw any exception.
            trigger_error(get_class($this) . "::__toString(): Caught $e", E_USER_ERROR);
            return '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close() {
        $streamResource = $this->detach();
        if (is_resource($streamResource)) {
            fclose($streamResource);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function detach() {
        $streamResource = $this->streamResource;
        $this->streamResource = null;
        if (is_resource($streamResource)) {
            return $streamResource;
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize() {
        $size = null;
        if (is_resource($this->streamResource)) {
            $streamResourceStat = fstat($this->streamResource);
            $size = $streamResourceStat['size'] ?? null;
        }
        return $size;
    }

    /**
     * {@inheritdoc}
     */
    public function tell() {
        if (!is_resource($this->streamResource)) {
            throw new \RuntimeException('Stream resource is not set.');
        }
        
        $pos = ftell($this->streamResource);
        if (is_int($pos)) {
            return $pos;
        }
        throw new \RuntimeException('Could not tell the current offset into the file stream.');
    }

    /**
     * {@inheritdoc}
     */
    public function eof() {
        return !is_resource($this->streamResource) || feof($this->streamResource);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable() {
        $metaSeekable = $this->getMetadata('seekable');
        return $metaSeekable === true;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET) {
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is unseekable.');
        }
        $fseek = fseek($this->streamResource, $offset, $whence);
        if ($fseek !== 0) {
            $fseekWhencesMap = [
                SEEK_SET => 'SEEK_SET',
                SEEK_CUR => 'SEEK_CUR',
                SEEK_END => 'SEEK_END'
            ];
            throw new \RuntimeException(
                    sprintf('fseek(%3$s): Error while seeking within stream using offset "%1$s" and whence "%2$s".'),
                    $offset,
                    (isset($fseekWhencesMap[$whence]) ? $fseekWhencesMap[$whence] : $whence),
                    $fseek
            );
        }
        return $fseek;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind() {
        $this->seek(0);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable() {
        if (!is_resource($this->streamResource)) {
            return false;
        }
        $modeMetadata = $this->getMetadata('mode');
        return (
            strpos($modeMetadata, '+') !== false
            || strpos($modeMetadata, 'w') !== false
            || strpos($modeMetadata, 'a') !== false
            || strpos($modeMetadata, 'x') !== false
            || strpos($modeMetadata, 'c') !== false
        );
    }

    /**
     * {@inheritdoc}
     */
    public function write($string) {
        $this->throwExceptionIfNotWritableStream();
        
        $fwrite = fwrite($this->streamResource, $string);
        if ($fwrite === false) {
            throw new \RuntimeException('Could not write to stream.');
        }
        return $fwrite;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable() {
        if (!is_resource($this->streamResource)) {
            return false;
        }
        $modeMetadata = $this->getMetadata('mode');
        return (
            strpos($modeMetadata, '+') !== false
            || strpos($modeMetadata, 'r') !== false
        );
    }

    /**
     * {@inheritdoc}
     */
    public function read($length) {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream is not readable.');
        }
        
        $fread = fread($this->streamResource, $length);
        if ($fread === false) {
            throw new \RuntimeException('Could not read stream.');
        }
        return $fread;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents() {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Could not get the remaining contents of stream. The stream is not readable.');
        }
        
        $contents = stream_get_contents($this->streamResource);
        if ($contents === false) {
            throw new \RuntimeException('Could not get the remaining contents of stream.');
        }
        return $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null) {
        $keyIsNull = $key === null;
        $return = $keyIsNull ? [] : null;
        if (is_resource($this->streamResource)) {
            $metadata = stream_get_meta_data($this->streamResource);
            if ($keyIsNull) {
                $return = $metadata;
            }
            else if (isset($metadata[$key])) {
                $return = $metadata[$key];
            }
        }
        return $return;
    }

}
