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

use Norma\Core\Oops\ExceptionInterface;

/**
 * An abstract error exception of the Norma's framework.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class AbstractErrorException extends \ErrorException implements ExceptionInterface {
    
    /**
     * {@inheritdoc}
     */
    public function __construct($message = "", $code = 0, $severity = 1, $filename = __FILE__, $lineno = __LINE__, \Exception $previous = null) {
        if (empty($message)) {
            throw new $this('Unknown empty message for error exception ' . get_class($this));
        }
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() {
        $errorType = SeverityCodeMap::$types[$this->severity];
        $traceAsStr = AbstractException::traceAsStr($this);
        return "Error Exception " . $errorType . " " . get_class($this) . " \"{$this->message}\" in {$this->file}({$this->line}).\nStack trace:\n"
                . "{$traceAsStr}";
    }
    
}
