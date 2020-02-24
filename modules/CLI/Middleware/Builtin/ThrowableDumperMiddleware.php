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

namespace Norma\CLI\Middleware\Builtin;

use Norma\CLI\CLIInputInterface;
use Norma\CLI\CLIOutputInterface;
use Norma\CLI\CLIErrorOutputInterface;

/**
 * A middleware which dumps a throwable exception or error.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class ThrowableDumperMiddleware {
    
    /**
     * Execute the middleware.
     * 
     * @param CLIInputInterface $input The CLI input (stdin abstraction).
     * @param CLIOutputInterface $output The CLI output (stdout abstraction).
     * @param CLIErrorOutputInterface $errOutput The CLI error output (stderr abstraction).
     * @param \Throwable $e The throwable exception or error.
     * @return void
     */
    public function execute(CLIInputInterface $input, CLIOutputInterface $output, CLIErrorOutputInterface $errOutput, \Throwable $e) {
        $errOutput->writeln((string) $e);
    }
    
}
