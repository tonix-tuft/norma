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

use Psr\Http\Message\UploadedFileInterface;

/**
 * The interface of an uploaded file factory.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface UploadedFileFactoryInterface {
    
    /**
     * Makes an uploaded file from the given parameters.
     * 
     * @param string $tmp_name The temporary filename of the file in which the uploaded file was stored on the server.
     * @param int $error The error code associated with this file upload.
     * @param int $size The size, in bytes, of the uploaded file.
     * @param string $name The original name of the file on the client machine.
     * @param string $type The MIME type of the file, if the client provided this information.
     * @return UploadedFileInterface The uploaded file.
     */
    public function makeUploadedFile($tmp_name, $error, $size, $name, $type): UploadedFileInterface;
    
}
