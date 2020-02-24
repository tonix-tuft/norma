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

namespace Norma\HTTP\Response\Download;

use Norma\HTTP\Stream\ReadableStreamFactoryInterface;
use Norma\HTTP\Stream\ReadableWritableStreamFactoryInterface;
use Norma\IO\FileDoesNotExistException;
use Norma\Core\Utils\FrameworkIOUtilsTrait;
use Norma\HTTP\HTTPTrait;
use Norma\HTTP\HTTPStatusCodeEnum;
use Norma\HTTP\Response\Range\ByteRangeRequestAwareStreamedResponse;
use Norma\HTTP\Request\Server\ServerRequestInterface;

/**
 * A class representing a file download response which conforms with the PSR-7 specification.
 * 
 * @source https://stackoverflow.com/questions/8485886/force-file-download-with-php-using-header#answer-8485963
 *
 * @author Lawrence Cherone
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class FileDownloadResponse extends ByteRangeRequestAwareStreamedResponse implements FileDownloadResponseInterface {
    
    use HTTPTrait;
    use FrameworkIOUtilsTrait;
    
    /**
     * @var ReadableStreamFactoryInterface
     */
    protected $readableStreamFactory;
    
    /**
     * @var ReadableWritableStreamFactoryInterface
     */
    protected $readableWritableStreamFactory;
    
    /**
     * @var string|null
     */
    protected $filename;
    
    /**
     * @var null|bool
     */
    protected $sendFilename;
    
    /**
     * @var string
     */
    protected $filenameExtension;
    
    /**
     * Construct a new response.
     * 
     * @param ReadableStreamFactoryInterface $readableStreamFactory A readable stream factory to use to create a body stream for the filename.
     * @param ReadableWritableStreamFactoryInterface $readableWritableStreamFactory A readable and writable stream factory to use to create a body stream for the contents of a file.
     * @param string|null $filename The filename to use for the response.
     * @param int $statusCode The status code.
     * @param array $headers An array of headers.
     * @param int $bufferSize The buffer size to read for each chunk when sending the body of the response, in bytes.
     * @param ServerRequestInterface|null $request A server request interface to use to extrapolate requested byte ranges from the `Range` HTTP header.
     * 
     * @throws FileDoesNotExistException If a filename is given and it does not exist.
     * @throws \InvalidArgumentException If the stream of the body cannot be constructed because of invalid arguments supplied during its construction.
     * @throws \RuntimeException If the stream of the body cannot be created for some other reason.
     */
    public function __construct(ReadableStreamFactoryInterface $readableStreamFactory, ReadableWritableStreamFactoryInterface $readableWritableStreamFactory, $filename = NULL,
            $statusCode = HTTPStatusCodeEnum::OK, array $headers = [], $bufferSize = 8192, ServerRequestInterface $request = null) {
        $this->readableStreamFactory = $readableStreamFactory;
        $this->readableWritableStreamFactory = $readableWritableStreamFactory;
        
        if (!is_null($filename)) {
            $this->throwExceptionIfFilenameDoesNotExist($filename);
            $this->sendFilename = TRUE;
            $body = $this->readableStreamFactory->makeFromFilename($filename);
            $this->filenameExtension = pathinfo($filename, PATHINFO_EXTENSION);
        }
        else {
            $body = $this->readableWritableStreamFactory->make();
            $this->filenameExtension = '';
        }
        $this->filename = $filename;
        
        parent::__construct($body, $statusCode, $headers, $bufferSize, $request);
    }
    
    /**
     * Throws an exception if the given filename does not exist.
     * 
     * @param string $filename The filename.
     * @throws FileDoesNotExistException If the given filename does not exist.
     */
    protected function throwExceptionIfFilenameDoesNotExist($filename) {
        if (!$this->fileExists($filename)) {
            throw new FileDoesNotExistException(sprintf('The file "%1$s" does not exist.', $filename));
        }
    }
    
    /**
     * Generates a random filename.
     * 
     * @return string A randomly generated filename.
     */
    protected function generateRandomFilename() {
        return date('Y-m-d_') . $this->generateRandomString(15);
    }
    
    /**
     * {@inheritdoc}
     */
    public function send() {
        if (is_null($this->sendFilename) && $this->isBodyEmptyOrSizeUnknown()) {
            throw new \RuntimeException('File contents not set for the response. Either a filename or a non-empty body must be set.');
        }
        else {
            $filesize = $this->body->getSize();
            if ($this->sendFilename) {
                $filename = $this->filename;
                if (!empty($this->filenameExtension)) {
                    $extension = pathinfo($filename, PATHINFO_EXTENSION);
                    if ($extension !== $this->filenameExtension) {
                        $filename = pathinfo($filename, PATHINFO_FILENAME) . '.' . $this->filenameExtension;
                    }
                }
            }
            else {
                $filename = $this->generateRandomFilename() . '.' . $this->filenameExtension;
            }
            
            $this->setHeaderIfNone('Content-Description', 'File Transfer')
                    ->setHeaderIfNone('Content-Type', 'application/octet-stream')
                    ->setHeaderIfNone('Content-Disposition', 'attachment; filename=' . $this->doubleQuoteEscapeBasename($filename))
                    ->setHeaderIfNone('Content-Transfer-Encoding', 'binary')
                    ->setHeaderIfNone('Connection', 'Keep-Alive')
                    ->setHeaderIfNone('Expires', '0')
                    ->setHeaderIfNone('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                    ->setHeaderIfNone('Pragma', 'public')
                    ->setHeaderIfNone('Content-Length', $filesize);

            parent::send();
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getContents() {
        if ($this->sendFilename === FALSE) {
            $contents = (string) $this->body;
            return $contents;
        }
        return NULL;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilename() {
        return $this->filename;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFilenameExtension() {
        return $this->filenameExtension;
    }
    
    /**
     * {@inheritdoc}
     */
    public function withContents($contents) {
        $clone = $this->cloneThis();
        if ($clone->sendFilename) {
            $clone->filenameExtension = '';
        }
        $clone->sendFilename = FALSE;
        $clone->body = $this->readableWritableStreamFactory->make();
        $clone->body->write($contents);
        $clone->filename = NULL;
        $clone->body->rewind();
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withFilename($filename) {
        $this->throwExceptionIfFilenameDoesNotExist($filename);
        
        $clone = $this->cloneThis();
        $clone->sendFilename = TRUE;
        $clone->filename = $filename;
        $clone->body = $this->readableStreamFactory->makeFromFilename($clone->filename);
        $clone->filenameExtension = pathinfo($clone->filename, PATHINFO_EXTENSION);

        return $clone;
    }
    
    /**
     * {@inheritdoc}
     */
    public function withFilenameExtension($filenameExtension) {
        $clone = $this->cloneThis();
        $clone->filenameExtension = ltrim($filenameExtension, '.');
        
        return $clone;
    }
    
}
