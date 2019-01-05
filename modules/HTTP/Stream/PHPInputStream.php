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

use Norma\HTTP\Stream\InputStreamInterface;
use Norma\HTTP\Stream\Stream;
use Norma\HTTP\Request\Server\ServerRequestInterface;
use Norma\Core\Utils\FrameworkIOUtilsTrait;

/**
 * An implementation of a PHP input stream.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class PHPInputStream extends Stream implements InputStreamInterface {
    
    use FrameworkIOUtilsTrait;
    
    /**
     * @var ServerRequestInterface
     */
    protected $serverRequest;
    
    /**
     * @var string
     */
    protected $tempFile;
    
    /**
     * Constructs a new PHP input stream.
     * 
     * @param ServerRequestInterface The server request.
     * @throws \Exception If the stream could not be constructed for some reason.
     */
    public function __construct(ServerRequestInterface $serverRequest) {
        $this->serverRequest = $serverRequest;
        
        $inputStreamResource = fopen('php://input', 'rb');
        $this->tempFile = $this->tempFile();
        $destStreamResource = fopen($this->tempFile, 'w+b');
        if (stream_copy_to_stream($inputStreamResource, $destStreamResource) === FALSE) {
            throw new \RuntimeException('php://input stream could not be copied to file with unique filename to make the stream seekable.');
        }
        fclose($inputStreamResource);
        fclose($destStreamResource);
        
        parent::__construct($this->tempFile, 'rb');
    }
    
    /**
     * Destructs this PHP input stream.
     */
    public function __destruct() {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSize() {
        $bodySize = parent::getSize();
        if ($bodySize !== NULL) {
            return $bodySize;
        }
        
        $contentLength = $this->serverRequest->getHeaderLine('Content-Length');
        if (!empty($contentLength)) {
            return (int) $contentLength;
        }
        return null;
    }
    
}
