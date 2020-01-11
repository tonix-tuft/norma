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

namespace Norma\Core\Oops;

use Norma\Core\Oops\AbstractException;

/**
 * A trait containing common exception methods.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
trait AbstractExceptionTrait {

    /**
     * Constructs the exception.
     * 
     * @param string $message The exception message.
     * @param int $code The exception code.
     * @param Throwable $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0, \Throwable $previous = null) {
        if (empty($message)) {
            throw new $this('Unknown empty message for exception ' . get_class($this), $code, $previous);
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * Generates a string representation of the exception.
     * 
     * @return string The string representation of the exception.
     */
    public function __toString() {
        $traceAsStr = AbstractException::traceAsStr($this);
        return "Exception " . get_class($this) . " \"{$this->message}\" in {$this->file}({$this->line}).\nStack trace:\n"
                . "{$traceAsStr}";
    }
    
}
