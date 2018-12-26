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

use Norma\HTTP\Stream\Stream;
use Norma\Heuristic\BinaryOrTextFileInfererInterface;
use Norma\Core\Utils\FrameworkIOUtilsTrait;

/**
 * A stream which uses heuristic to infer if a file is a binary (non-text) file or a text file
 * and sets/corrects the file mode of the stream before opening it provided that the
 * given stream resource is an existent file.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class BinaryOrTextInferenceStream extends Stream {
    
    use FrameworkIOUtilsTrait;
    
    /**
     * @var BinaryOrTextFileInfererInterface
     */
    protected $inferer;
   
    /**
     * Constructs a new stream instance.
     * 
     * @param BinaryOrTextFileInfererInterface $inferer An inferer able to tell whether a file is a binary file or not (text file).
     * @param resource|string $streamResource The stream resource. Either a resource handle or a string specifying a resource to open.
     *                                                                    Defaults to PHP's `php://temp` stream.
     * @param string $mode The mode of the stream. Defaults to 'w+b' (therefore a readable and writable stream with binary mode enabled).
     *                                     However, if the given `$streamResource` is an existent file, then this stream implementation will attempt to set or
     *                                     unset the 'b' flag on the mode of the stream if the file is respectively a binary file or a text file.
     *                                     If a resource handle for `$streamResource` is given then this parameter will be ignored.
     * @throws \InvalidArgumentException If the provided `$streamResource` is not a string and is not a resource handle identifying the stream.
     * @throws \RuntimeException If `$streamResource` is a string identifying the stream resource handle to open and the stream resource cannot be opened for some reason.
     * @throws \Exception If `$streamResource` is an existent file and the inferer is not able to determine whether the file is a binary file or not (text file) for some reason.
     */
    public function __construct(BinaryOrTextFileInfererInterface $inferer, $streamResource = 'php://temp', $mode = 'w+b') {
        $this->inferer = $inferer;
        $correctedMode = $this->correctMode($streamResource, $mode);
        parent::__construct($streamResource, $correctedMode);
    }
    
    /**
     * Corrects the mode of the stream if the stream is binary.
     * 
     * @param resource|string $streamResource The stream resource. Either a resource handle or a string specifying a resource to open.
     * @param string $mode The mode of the stream. If the given `$streamResource` is an existent file, then this stream implementation will attempt to set or
     *                                     unset the 'b' flag on the mode of the stream if the file is respectively a binary file or a text file.
     *                                     If a resource handle for `$streamResource` is given then this parameter will be ignored.
     * @return string The corrected mode.
     */
    protected function correctMode($streamResource, $mode) {
        if (is_string($streamResource) && $this->fileExists($streamResource)) {
            if ($this->inferer->isBinary($streamResource)) {
                if (strpos($mode, 'b') === FALSE) {
                    $mode .= 'b';
                }
            }
            else {
                if (strpos($mode, 'b') !== FALSE) {
                    $mode = str_replace('b', '', $mode);
                }
            }
        }
        return $mode;
    }
    
}
