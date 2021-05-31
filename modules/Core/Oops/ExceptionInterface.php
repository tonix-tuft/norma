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

namespace Norma\Core\Oops;

/**
 * Interface of an exception within the Norma framework.
 * 
 * @source http://php.net/manual/en/language.exceptions.php#91159
 * @author <ask at nilpo dot com> (http://php.net/manual/pl/language.exceptions.php#91159)
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface ExceptionInterface {

    /**
     * Gets the exception message.
     * 
     * @return string The exception message.
     */
    public function getMessage();

    /**
     * Gets the exception code.
     * 
     * @return int The exception code.
     */
    public function getCode();

    /**
     * Gets the source filename where the exception was thrown.
     * 
     * @return string The filename.
     */
    public function getFile();

    /**
     * Gets the source line where the exception was thrown.
     * 
     * @return int The line number.
     */
    public function getLine();

    /**
     * Gets the trace of the exception as an array.
     * 
     * @return array The trace of the exception.
     */
    public function getTrace();

    /**
     * Gets the trace of the exception as string.
     * 
     * @return string The trace of the exception.
     */
    public function getTraceAsString();
    
    /**
     * Generates a string representation of the exception.
     * 
     * @return string The string representation of the exception.
     */
    public function __toString();

}
