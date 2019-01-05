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

namespace Norma\HTTP\Response\Download;

use Norma\HTTP\Response\StreamedResponseInterface;

/**
 * The interface of an HTTP file download response which conforms with the PRS-7 specification.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface FileDownloadResponseInterface extends StreamedResponseInterface {
    
    /**
     * Get the filename associated with this response.
     * 
     * @return string|null The filename. NULL MUST be returned in case there isn't a filename associated with this response
     *                                (i.e. the response is associated with string representing the contents of the file or neither the filename nor a contents string
     *                                has been set yet).
     */
    public function getFilename();
    
    /**
     * Get the contents of the filename associated with this response as a contents string.
     * 
     * @return string|null The contents associated with this response as a string.
     *                                NULL MUST be returned in case there isn't a string associated with this response
     *                                (i.e. the response is associated with a filename or neither the filename nor a string
     *                                has been set yet).
     */
    public function getContents();
    
    /**
     * Get the filename extension of the filename associated with this response.
     * 
     * The leading "." character MUST be stripped by implementors.
     * 
     * An empty string MUST be returned if there isn't a filename extension.
     * 
     * @return string The filename extension. If the response is bound to a filename, its filename extension MUST be returned,
     *                         unless {@link FileDownloadResponseInterface::withFilenameExtension()} has been called after a file has been bound
     *                         to the response through  {@link FileDownloadResponseInterface::withFilename()}, in which case the file extension given
     *                         to {@link FileDownloadResponseInterface::withFilenameExtension()} MUST be used.
     */
    public function getFilenameExtension();
    
    /**
     * Return an instance with the specified filename.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated filename.
     * 
     * @param string $filename The filename.
     * @return static
     * @throws \Norma\IO\FileDoesNotExistException If the given file does not exist.
     * @throws \RuntimeException If the filename could not be associated with this response.
     */
    public function withFilename($filename);
    
    /**
     * Return an instance with the specified contents.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated contents.
     * 
     * @param string $contents The contents of the file to use for the file download response.
     * @return static
     * @throws \RuntimeException If the string could not be associated with this response.
     */
    public function withContents($contents);
    
    /**
     * Return an instance with the specified file extension.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated file extension.
     * 
     * @param string $filenameExtension The filename extension of the filename.
     *                                                         The leading "." character MAY appear in the string and MUST be stripped by implementors.
     * @return static
     */
    public function withFilenameExtension($filenameExtension);
    
}
