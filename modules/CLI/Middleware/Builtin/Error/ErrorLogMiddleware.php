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

namespace Norma\CLI\Middleware\Builtin\Error;

use Norma\Core\Env\EnvInterface;
use Norma\CLI\CLIInputInterface;
use Norma\CLI\CLIOutputInterface;
use Norma\CLI\CLIErrorOutputInterface;

/**
 * An error log middleware for Norma CLI applications.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class ErrorLogMiddleware {
    
    /**
     * @var EnvInterface
     */
    protected $env;
    
    /**
     * Constructs a new middleware.
     * 
     * @param EnvInterface $env The environment instance.
     */
    public function __construct(EnvInterface $env) {
        $this->env = $env;
    }
    
    /**
     * Execute the error log middleware.
     * 
     * @param CLIInputInterface $input The CLI input (stdin abstraction).
     * @param CLIOutputInterface $output The CLI output (stdout abstraction).
     * @param CLIErrorOutputInterface $errOutput The CLI error output (stderr abstraction).
     * @param \Throwable $e The throwable exception or error.
     * @return void
     */
    public function execute(CLIInputInterface $input, CLIOutputInterface $output, CLIErrorOutputInterface $errOutput, \Throwable $e) {
        $throwableHash = $this->generateThrowableHash($e);
        $throwableSHA1OpeningMarker = $this->env->get('NORMA_ERR_SHA1_OPENING_PREFIX', '') . $throwableHash;
        $throwableSHA1ClosingMarker = $this->env->get('NORMA_ERR_SHA1_CLOSING_PREFIX', '') . $throwableHash;
        
        $environment = $this->env->get('NORMA_ENV');
        $runtime = $this->env->get('NORMA_RUNTIME');
        
        error_log(sprintf("%s\n%s\n%s\n%s\n%s\n", $throwableSHA1OpeningMarker, $e, 'NORMA_ENV=' . $environment, 'NORMA_RUNTIME=' . $runtime, $throwableSHA1ClosingMarker));
    }
    
    /**
     * A function to generate a SHA1 hash for the given throwable.
     * 
     * @param \Throwable $throwable A throwable to hash.
     * @return string The SHA1 hash of the throwable.
     */
    protected function generateThrowableHash(\Throwable $throwable) {
        return sha1(microtime() . ((string) $throwable) . random_bytes(16));
    }
    
}
