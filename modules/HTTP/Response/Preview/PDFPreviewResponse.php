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

namespace Norma\HTTP\Response\Preview;

use Norma\HTTP\HTTPTrait;
use Norma\HTTP\HTTPStatusCodeEnum;
use Norma\IO\FileDoesNotExistException;
use Norma\Data\Validation\PDFFileValidatorInterface;
use Norma\HTTP\Stream\ReadableStreamFactoryInterface;
use Norma\HTTP\Stream\ReadableWritableStreamFactoryInterface;
use Norma\Core\Utils\FrameworkIOUtilsTrait;
use Norma\HTTP\Response\Preview\PDFPreviewResponseInterface;
use Norma\HTTP\Response\Range\ByteRangeRequestAwareStreamedResponse;
use Norma\HTTP\Request\Server\ServerRequestInterface;

/**
 * A class representing a simple HTTP response message which conforms with the PSR-7 specification
 * to send to a client able to preview the contents of a PDF file.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class PDFPreviewResponse extends ByteRangeRequestAwareStreamedResponse implements PDFPreviewResponseInterface {
    
    use HTTPTrait;
    use FrameworkIOUtilsTrait;
    
    /**
     * @var PDFFileValidatorInterface 
     */
    protected $PDFFileValidator;
    
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
    protected $PDFFilename;
    
    /**
     * @var null|bool
     */
    protected $sendFilename = NULL;
    
    /**
     * Construct a new response.
     * 
     * @param PDFFileValidatorInterface A PDF file validator.
     * @param ReadableStreamFactoryInterface $readableStreamFactory A readable stream factory to use to create a body stream for the PDF filename.
     * @param ReadableWritableStreamFactoryInterface $readableWritableStreamFactory A readable and writable stream factory to use to create a body stream for the binary contents of a PDF.
     * @param string|null $PDFFilename The PDF filename to use for the response.
     * @param int $statusCode The status code.
     * @param array $headers An array of headers.
     * @param int $bufferSize The buffer size to read for each chunk when sending the body of the response, in bytes.
     * @param ServerRequestInterface|null $request A server request interface to use to extrapolate requested byte ranges from the `Range` HTTP header.
     * 
     * @throws \Norma\IO\FileDoesNotExistException If a PDF filename is given and it does not exist.
     * @throws \InvalidArgumentException If the given filename is not a valid PDF file or if the given status code is invalid
     *                                                            or if the stream of the body cannot be constructed because of invalid arguments supplied during its construction.
     * @throws \RuntimeException If the stream of the body cannot be created for some other reason.
     * @throws \Exception An exception may be thrown if a PDF filename is given and the PDF file validator fails to determine if the given filename is a valid PDF file.
     */
    public function __construct(PDFFileValidatorInterface $PDFFileValidator, ReadableStreamFactoryInterface $readableStreamFactory, ReadableWritableStreamFactoryInterface $readableWritableStreamFactory,
            $PDFFilename = NULL, $statusCode = HTTPStatusCodeEnum::OK, array $headers = [], $bufferSize = 8192, ServerRequestInterface $request = null
    ) {
        $this->PDFFileValidator = $PDFFileValidator;
        $this->readableStreamFactory = $readableStreamFactory;
        $this->readableWritableStreamFactory = $readableWritableStreamFactory;
        
        if (!is_null($PDFFilename)) {
            $this->throwExceptionIfFilenameDoesNotExistOrIsNotValidPDF($PDFFilename);
            $this->sendFilename = TRUE;
            $body = $this->readableStreamFactory->makeFromFilename($PDFFilename);
        }
        else {
            $body = $this->readableWritableStreamFactory->make();
        }
        $this->PDFFilename = $PDFFilename;
        
        parent::__construct($body, $statusCode, $headers, $bufferSize, $request);
    }
    
    /**
     * Throws an exception if the given filename does not exist or is not a valid PDF file.
     * 
     * @param string $filename The filename.
     * @throws \Norma\IO\FileDoesNotExistException If the given file does not exist.
     * @throws \InvalidArgumentException If the given filename is not a valid PDF file.
     * @throws \Exception An exception may be thrown if the PDF file validator fails to determine if the given filename is a valid PDF file.
     */
    protected function throwExceptionIfFilenameDoesNotExistOrIsNotValidPDF($filename) {
        if (!$this->fileExists($filename)) {
            throw new FileDoesNotExistException(sprintf('The PDF file "%1$s" does not exist.', $filename));
        }
        
        if (!$this->PDFFileValidator->isValid($filename)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The file "%1$s" does not seem to be a valid PDF.', 
                    $filename
                )
            );
        }
    }
    
    /**
     * Generates a random PDF filename.
     * 
     * @return string A random PDF filename.
     */
    protected function generateRandomPDFFilename() {
        return date('Y-m-d_') . $this->generateRandomString(15) . '.pdf';
    }
    
    /**
     * {@inheritdoc}
     */
    public function send() {
        if (is_null($this->sendFilename) && $this->isBodyEmptyOrSizeUnknown()) {
            throw new \RuntimeException('PDF contents not set for the response. Either a filename or the binary contents of a PDF must be set.');
        }
        else {
            $filesize = $this->body->getSize();
            if ($this->sendFilename) {
                $filename = $this->PDFFilename;
            }
            else {
                $filename = $this->generateRandomPDFFilename();
            }
            $this->setHeaderIfNone('Cache-Control', 'no-cache, must-revalidate')
                    ->setHeaderIfNone('Pragma', 'public')
                    ->setHeaderIfNone('Content-Disposition', 'inline; filename=' . $this->doubleQuoteEscapeBasename($filename))
                    ->setHeaderIfNone('Content-Length', $filesize)
                    ->setHeaderIfNone('Content-Type', 'application/pdf');

            parent::send();
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPDFFilename() {
        return $this->PDFFilename;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getBinary() {
        if ($this->sendFilename === FALSE) {
            $binary = (string) $this->body;
            return $binary;
        }
        return NULL;
    }
    
    /**
     * {@inheritdoc}
     */
    public function withPDFFilename($PDFFilename) {
        $this->throwExceptionIfFilenameDoesNotExistOrIsNotValidPDF($PDFFilename);
        
        $clone = $this->cloneThis();
        $clone->sendFilename = TRUE;
        $clone->PDFFilename = $PDFFilename;
        $clone->body = $this->readableStreamFactory->makeFromFilename($clone->PDFFilename);
        
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withBinary($binary) {
        $clone = $this->cloneThis();
        $clone->sendFilename = FALSE;
        $clone->body = $this->readableWritableStreamFactory->make();
        $clone->body->write($binary);
        $clone->PDFFilename = NULL;
        $clone->body->rewind();
        
        return $clone;
    }
    
}
