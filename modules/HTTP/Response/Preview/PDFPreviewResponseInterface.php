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

namespace Norma\HTTP\Response\Preview;

use Norma\HTTP\Response\StreamedResponseInterface;

/**
 * The interface of a response to send to a client able to preview the contents of a PDF file
 * which conforms with the PSR-7 specification.
 * 
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface PDFPreviewResponseInterface extends StreamedResponseInterface {
    
    /**
     * Get the PDF filename associated with this response.
     * 
     * @return string|null The PDF filename. NULL MUST be returned in case there isn't a PDF filename associated with this response
     *                                (i.e. the response is associated with a binary string or neither the PDF filename nor a binary string
     *                                has been set yet).
     */
    public function getPDFFilename();
    
    /**
     * Get the binary contents of the PDF associated with this response as a binary string.
     * 
     * @return string|null The PDF binary contents of the PDF associated with this response as a string.
     *                                NULL MUST be returned in case there isn't a binary string associated with this response
     *                                (i.e. the response is associated with a PDF filename or neither the PDF filename nor a binary string
     *                                has been set yet).
     */
    public function getBinary();
    
    /**
     * Return an instance with the specified PDF filename.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated PDF filename.
     * 
     * @param string $PDFFilename The filename of a PDF file.
     * @return static
     * @throws \Norma\IO\FileDoesNotExistException If the given file does not exist.
     * @throws \InvalidArgumentException If the given filename is not a valid PDF file.
     * @throws \RuntimeException If the PDF filename could not be associated with this response.
     */
    public function withPDFFilename($PDFFilename);
    
    /**
     * Return an instance with the specified binary contents.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated PDF binary contents.
     * 
     * @param string $binary The binary contents of the PDF to use for the response.
     * @return static
     * @throws \RuntimeException If the binary string could not be associated with this response.
     */
    public function withBinary($binary);
    
}
