<?php

/*
 * Copyright (c) 2021 Anton Bagdatyev (Tonix)
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

namespace Norma\Heuristic;

use Norma\IO\FileDoesNotExistException;

/**
 * An interface which which infers whether a given filename is a binary file or a text file.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface BinaryOrTextFileInfererInterface {
    
    /**
     * Tests whether the given filename is binary or not (text file).
     * 
     * @param string $filename An existent filename.
     * @return bool TRUE if the file is binary, FALSE otherwise (text file).
     * @throws FileDoesNotExistException If the file does not exist.
     * @throws \Exception If the inferer is not able to determine whether the given filename is a binary file or not (text file) for some reason.
     */
    public function isBinary($filename);
    
}
