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

namespace Norma\HTTP\Upload;

use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\StreamInterface;
use Norma\HTTP\Stream\Stream;
use Norma\HTTP\Upload\FileUploadException;

/**
 * The implementation of a PSR-7 uploaded file.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class UploadedFile implements UploadedFileInterface {
    
    /**
     * @var string|resource|StreamInterface
     */
    protected $filenameOrStreamOrStreamResource;
    
    /**
     * @var string|null
     */
    protected $filename = NULL;
    
    /**
     * @var int|null
     */
    protected $size;
    
    /**
     * @var int
     */
    protected $error;
    
    /**
     * @var string|null
     */
    protected $clientFilename;
    
    /**
     * @var string|null
     */    
    protected $clientMediaType;
    
    /**
     * @var bool
     */
    protected $moveToMethodHasBeenCalledPreviously = FALSE;
    
    /**
     * Constructs a new uploaded file.
     * 
     * @param string|resource|StreamInterface $filenameOrStreamOrStreamResource The uploaded file name or the stream resource representing
     *                                                                                                                             the uploaded file or the stream instance representing the uploaded file.
     * @param int $error The error code of the file upload. MUST be one of the `UPLOAD_ERR_XXX` constants.
     * @param int|null $size The size of the uploaded file in bytes or null if unknown.
     * @param string|null $clientFilename The filename sent by the client or null if none was provided.
     * @param string|null $clientMediaType The media type sent by the client or null if none was provided.
     * @throws \InvalidArgumentException For invalid error code.
     */
    public function __construct($filenameOrStreamOrStreamResource, $error, $size = NULL, $clientFilename = NULL, $clientMediaType = NULL) {
        $this->throwExceptionIfInvalidErrorCode($error);
        
        $this->error = $error;
        $this->size = $size;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
        if (is_string($filenameOrStreamOrStreamResource)) {
            $this->filename = $filenameOrStreamOrStreamResource;
        }
        $this->filenameOrStreamOrStreamResource = $filenameOrStreamOrStreamResource;
    }
    
    /**
     * Throws an exception if the given file upload error code is invalid.
     * 
     * @param int $error The error code of the file upload. MUST be one of the `UPLOAD_ERR_XXX` constants in order not to throw an exception.
     * @return void
     * @throws \InvalidArgumentException If the given file upload error code is invalid.
     */
    protected function throwExceptionIfInvalidErrorCode($error) {
        if (!isset(FileUploadErrorMessageMap::$lookup[$error])) {
            throw new \InvalidArgumentException(sprintf('Invalid file upload error code "%s".', $error));
        }
    }
    
    /**
     * Throws an exception if the file upload is not OK, meaning that its {@link static::getError()} method
     * doesn't evaluate to `UPLOAD_ERR_OK`.
     * 
     * @return void
     * @throws \RuntimeException If the file upload is not OK.
     */
    protected function throwExceptionIfFileUploadIsNotOK() {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new FileUploadException($this->error);
        }
    }
    
    /**
     * Throws an exception telling that the given filename is not an uploaded file.
     * 
     * @param string $filename The filename.
     * @return void
     * @throws \RuntimeException
     */
    protected function throwNotUploadedFileException($filename) {
        throw new \RuntimeException(sprintf('The associated filename "%s" of the uploaded file is not an uploaded file.', $filename));
    }
    
    /**
     * Writes the contents of the specified stream to the specified target path.
     * 
     * @param StreamInterface $stream The stream.
     * @param string $targetPath The target path.
     * @param int $bufferSize The size of a single buffer chunk to write in bytes. Defaults to 8 KiB.
     * @return void
     * @throws \RuntimeException If the writing operation fails.
     */
    protected function writeStreamToTargetPath(StreamInterface $stream, $targetPath, $bufferSize = 8192) {
        $resourceHandle = fopen($targetPath, 'w+b');
        if ($resourceHandle === FALSE) {
            throw new \RuntimeException(sprintf('Could not write to target path %1$s.', $targetPath));
        }
        
        $stream->rewind();
        while (!$stream->eof()) {
            if (fwrite($resourceHandle, $stream->read($bufferSize)) === FALSE) {
                throw new \RuntimeException(sprintf('Could not write %2$s bytes to target path "%1$s".', $targetPath, $bufferSize));
            }
        }

        if (fclose($resourceHandle) === FALSE) {
            throw new \RuntimeException(sprintf('Could not close resource handle opened for target path "%1$s".', $targetPath));   
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getStream() {
        if ($this->moveToMethodHasBeenCalledPreviously) {
            throw new \RuntimeException('Could not get stream. Uploaded file has been moved.');
        }
        
        $this->throwExceptionIfFileUploadIsNotOK();
        
        $cannotGetStream = FALSE;
        if ($this->filenameOrStreamOrStreamResource instanceof StreamInterface) {
            return $this->filenameOrStreamOrStreamResource;
        }
        else if (is_string($this->filenameOrStreamOrStreamResource) || is_resource($this->filenameOrStreamOrStreamResource)) {
            try {
                $this->filenameOrStreamOrStreamResource = new Stream($this->filenameOrStreamOrStreamResource, 'r+b');
                return $this->filenameOrStreamOrStreamResource;
            }
            catch (\RuntimeException $e) {
                throw $e;
            }
            catch (\Exception $e) {
                $cannotGetStream = TRUE;
            }
        }
        
        if ($cannotGetStream) {
            throw new \RuntimeException('Could not get stream. The stream is not available or cannot be created from the given stream used during the construction of the file upload instance.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function moveTo($targetPath) {
        if ($this->moveToMethodHasBeenCalledPreviously) {
            throw new \RuntimeException('Uploaded file has already been moved.');
        }
        
        $this->throwExceptionIfFileUploadIsNotOK();
        
        if (empty($targetPath)) {
            throw new \InvalidArgumentException('Target path is emtpy.');
        }
        
        $targetDirectory = dirname($targetPath);
        if (!is_dir($targetDirectory)) {
            throw new \RuntimeException('Target path directory does not exist.');
        }
        else if (!is_writable($targetDirectory)) {
            throw new \RuntimeException('Target path directory is not writable.');
        }
        
        $SAPI = php_sapi_name();
        $mvResult = FALSE;
        if ($SAPI !== 'cli' && is_string($this->filename)) {
            // Non-CLI environment and filename present.
            if (is_uploaded_file($this->filename)) {
                $mvResult = @move_uploaded_file($this->filename, $targetPath);
            }
            else {
                $this->throwNotUploadedFileException($this->filename);
            }
        }
        else {
            // CLI environment or filename not present.
            if (is_string($this->filename)) {
                // Filename present. CLI environment.
                $mvResult = @rename($this->filename, $targetPath);
            }
            else {
                // Filename not present. Environment?
                $stream = $this->getStream();
                $sourceFilename = $stream->getMetadata('uri');
                if (!empty($sourceFilename)) {
                    // Filename from stream metadata.
                    $filenameRealpath = realpath($sourceFilename);
                    if (file_exists($filenameRealpath)) {
                        if ($SAPI !== 'cli') {
                            // Non-CLI environment.
                            if (is_uploaded_file($filenameRealpath)) {
                                $mvResult = @move_uploaded_file($filenameRealpath, $targetPath);
                            }
                            else {
                                $this->throwNotUploadedFileException($filenameRealpath);
                            }
                        }
                        else {
                            // CLI environment.
                            $mvResult = @rename($filenameRealpath, $targetPath);
                        }
                    }
                    else {
                        // Stream metadata URI does not identify an existent file.
                        $this->writeStreamToTargetPath($stream, $targetPath);
                        $mvResult = TRUE;
                    }
                }
                else {
                    // No filename from stream metadata.
                    $this->writeStreamToTargetPath($stream, $targetPath);
                    $mvResult = TRUE;
                }
            }
        }
        
        if (!$mvResult) {
            throw new \RuntimeException(sprintf('Could not move uploaded file to "%s".', $targetPath));
        }
        
        $resourceCloseResult = NULL;
        if ($this->filenameOrStreamOrStreamResource instanceof StreamInterface) {
            $detachedResourceStream = $this->filenameOrStreamOrStreamResource->detach();
            $resourceCloseResult = fclose($detachedResourceStream);
            $this->filenameOrStreamOrStreamResource->close();
        }
        else if (is_resource($this->filenameOrStreamOrStreamResource)) {
            $resourceCloseResult = fclose($this->filenameOrStreamOrStreamResource);
        }
        
        if (!$resourceCloseResult === FALSE) {
            throw new \RuntimeException('Could not close resource handle of uploaded file.');
        }
        
        $this->moveToMethodHasBeenCalledPreviously = TRUE;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSize() {
        return $this->size;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getError() {
        return $this->error;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getClientFilename() {
        return $this->clientFilename;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getClientMediaType() {
        return $this->clientMediaType;
    }
    
}
