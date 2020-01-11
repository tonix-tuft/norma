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

use Norma\Core\Oops\ExceptionInterface;
use Norma\Core\Oops\AbstractExceptionTrait;

/**
 * Abstract representation of an exception of the Norma's framework.
 * 
 * @source http://php.net/manual/en/language.exceptions.php#91159
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 * @author <ask at nilpo dot com> (http://php.net/manual/pl/language.exceptions.php#91159)
 * @author Ladislav Prskavec <ladislav@prskavec.net>
 */
abstract class AbstractException extends \Exception implements ExceptionInterface {

    use AbstractExceptionTrait;
    
    /**
     * Get the trace of a throwable as a string.
     * 
     * @source https://gist.github.com/abtris/1437966
     * 
     * @param \Throwable $e
     * @return string The string representation of the stack trace.
     */
    public static function traceAsStr(\Throwable $e) {
        $rtn = "";
        $count = 0;
        foreach ($e->getTrace() as $frame) {
            $args = "";
            if (isset($frame['args'])) {
                $args = [];
                foreach ($frame['args'] as $arg) {
                    if (is_string($arg)) {
                        $args[] = "'" . $arg . "'";
                    }
                    else if (is_array($arg)) {
                        $args[] = "Array";
                    }
                    else if (is_null($arg)) {
                        $args[] = 'NULL';
                    }
                    else if (is_bool($arg)) {
                        $args[] = ($arg) ? "true" : "false";
                    }
                    else if (is_object($arg)) {
                        $args[] = get_class($arg);
                    }
                    else if (is_resource($arg)) {
                        $args[] = get_resource_type($arg);
                    }
                    else {
                        $args[] = $arg;
                    }
                }
                $args = join(", ", $args);
            }
            if (!isset($frame['file'])) {
                $rtn .= sprintf( "#%s %s(%s): %s(%s)\n",
                            $count,
                            '[internal function]',
                            isset($frame['line']) ? $frame['line'] : 'unknown line',
                            (isset($frame['class']))  ? $frame['class'] . $frame['type'] . $frame['function'] : $frame['function'], 
                            $args
                );
            }
            else {
                $rtn .= sprintf( "#%s %s(%s): %s(%s)\n",
                            $count,
                            $frame['file'],
                            isset($frame['line']) ? $frame['line'] : 'unknown line',
                            (isset($frame['class']))  ? $frame['class'] . $frame['type'] . $frame['function'] : $frame['function'], 
                            $args
                );   
            }
            $count++;
        }
        $rtn .= "#$count {main}";
        return $rtn;
    }

}
