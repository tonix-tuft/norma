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

namespace Norma\HTTP;

use Norma\Core\Utils\FrameworkStringUtilsTrait;

/**
 * A trait used to group useful methods concerned with HTTP.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
trait HTTPTrait {
    
    use FrameworkStringUtilsTrait;
    
    /**
     * Escapes the basename of the given filename and wraps it within double quotes.
     * It returns a double quoted and escaped basename of the filename so that it can be used within
     * HTTP headers (e.g. within the `Content-Disposition` header of a response message).
     * 
     * @param string $filename The filename.
     * @return string The escaped basename of the given filename wrapped within double quotes.
     */
    private function doubleQuoteEscapeBasename($filename) {
        $quoted = sprintf('"%s"', addcslashes(basename($filename), '"\\'));
        return $quoted;
    }
    
    /**
     * Generates a string which can be used as a boundary for multipart responses.
     * 
     * @return string The boundary string.
     */
    private function generateMultipartBoundary() {
        return substr($this->generateBoundaryPrefix(), 0, 15) .
                $this->randomlyPickCharacters($this->generateRandomString(20) . sha1($this->generateRandomString(10) . time()), 40) .
                substr($this->generateBoundarySuffix(), 0, 10);
    }
    
    /**
     * Generates a boundary prefix to use for a boundary string.
     * 
     * @return string
     */
    private function generateBoundaryPrefix() {
        return '===boundary_';
    }
    
    /**
     * Generates a boundary suffix to use for a boundary string.
     * 
     * @return string
     */
    private function generateBoundarySuffix() {
        return '===';
    }
    
}
