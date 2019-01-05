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

namespace Norma\Core\Oops;

use Norma\Core\Oops\UnexistentSeverityCodeException;

/**
 * The implementation of an error exception factory.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class ErrorExceptionFactory implements ErrorExceptionFactoryInterface {
    
    /**
     * {@inheritdoc}
     */
    public function makeErrorException($errno, $errstr, $errfile, $errline) {
        if (!isset(SeverityCodeMap::$exceptions[$errno])) {
            throw new UnexistentSeverityCodeException(sprintf('The severity code "%1$s" does not exist for error "%2$s" occurred in file "%3$s" on line %4$s.'),
                    $errno, $errstr, $errfile, $errline
            );
        }
        $exceptionClass = SeverityCodeMap::$exceptions[$errno];
        $exception = new $exceptionClass($errstr, 0, $errfile, $errline);
        return $exception;
    }

}
