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

namespace Norma\Core\Utils;

use Norma\IO\FileWritingException;

/**
 * A trait containing useful I/O methods.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
trait FrameworkIOUtilsTrait {
    
    /**
     * Create file with unique file name.
     * 
     * @param $prefix string The prefix of the generated temporary filename.
     * @return string Returns the new temporary filename (with path), or FALSE on failure.
     * @throws FileWritingException If the file with unique filename could not be created.
     */
    private function tempFile() {
        $prefix = substr(__CLASS__, strrpos(__CLASS__, '\\') + 1);
        $tempFile = tempnam(sys_get_temp_dir(), $prefix);
        if ($tempFile === FALSE) {
            throw new FileWritingException('Could not create file with unique filename.');
        }
        return $tempFile;
    }
    
    /**
     * Tests whether the given filename exists.
     * 
     * @source https://stackoverflow.com/questions/14857080/file-exists-expects-parameter-1-to-be-a-valid-path-string-given/22957831#22957831 For the removal of the NULL byte.
     * 
     * @param string $filename The filename
     * @return bool TRUE if the file exists, FALSE otherwise.
     */
    private function fileExists($filename) {
        $cleanedFilename = strval(str_replace("\0", "", $filename));
        return file_exists($cleanedFilename);
    }
    
}
