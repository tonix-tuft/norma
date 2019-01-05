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

namespace Norma\HTTP\Upload;

use Norma\HTTP\HTTPException;

/**
 * A file upload exception.
 * 
 * @source http://php.net/manual/en/features.file-upload.errors.php#89374
 *
 * @author <danbrown AT php DOT net> (http://php.net/manual/en/features.file-upload.errors.php#89374)
 * @author Thalent, Michiel Thalen (http://php.net/manual/en/features.file-upload.errors.php#89374)
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class FileUploadException extends HTTPException {

    /**
     * Constructs a new exception.
     * 
     * @param int $code The code of the file upload `UPLOAD_ERR_*` PHP error.
     */
    public function __construct($code) {
        $message = $this->codeToMessage($code);
        parent::__construct($message, $code);
    }

    /**
     * Translates a file upload `UPLOAD_ERR_*` PHP error code into a message string.
     * 
     * @param int $code The code of the file upload `UPLOAD_ERR_*` PHP error.
     * @return string The message string.
     */
    private function codeToMessage($code) {
        $message = NULL;
        if (
            isset(FileUploadErrorMessageMap::$lookup[$code])
        ) {
            $message = FileUploadErrorMessageMap::$lookup[$code];
        }
        else {
            $message = 'Unknown upload error.';
        }
        return $message;
    }

}
