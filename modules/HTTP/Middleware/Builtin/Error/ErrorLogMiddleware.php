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

namespace Norma\HTTP\Middleware\Builtin\Error;

use Norma\MVC\Routing\RoutingException;
use Norma\Core\Env\EnvInterface;
use Norma\HTTP\Request\Server\ServerRequestInterface;
use Norma\HTTP\Response\ResponseInterface;

/**
 * A middleware which logs a throwable exception or error.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class ErrorLogMiddleware {
    
    /**
     * @var EnvInterface
     */
    protected $env;
    
    /**
     * @var array
     */
    static protected $throwablesToIgnore = [
        RoutingException::class
    ];
    
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
     * @param ServerRequestInterface $request The request.
     * @param ResponseInterface $response The current response to use.
     * @param \Throwable $e The throwable exception or error.
     * @return void
     */
    public function execute(ServerRequestInterface $request, ResponseInterface $response, \Throwable $e) {
        if (!in_array(get_class($e), static::$throwablesToIgnore)) {
            $throwableHash = $this->generateThrowableHash($e, $request);
            $throwableSHA1OpeningMarker = $this->env->get('NORMA_ERR_SHA1_OPENING_PREFIX', '') . $throwableHash;
            $throwableSHA1ClosingMarker = $this->env->get('NORMA_ERR_SHA1_CLOSING_PREFIX', '') . $throwableHash;
            
            $environment = $this->env->get('NORMA_ENV');
            $runtime = $this->env->get('NORMA_RUNTIME');
            
            error_log(sprintf("%s\n%s\n%s\n%s\n%s\n", $throwableSHA1OpeningMarker, $e, 'NORMA_ENV=' . $environment, 'NORMA_RUNTIME=' . $runtime, $throwableSHA1ClosingMarker));
        }
    }
    
    /**
     * A function to generate a SHA1 hash for the given throwable.
     * 
     * @param \Throwable $throwable A throwable to hash.
     * @param ServerRequestInterface $request the request.
     * @return string The SHA1 hash of the throwable.
     */
    protected function generateThrowableHash(\Throwable $throwable, ServerRequestInterface $request) {
        $IPAddress = $request->getAttribute('REMOTE_ADDR', '');
        if ($request->hasHeader('X-Forwarded-For')) {
            $IPAddress = $request->getHeaderLine('X-Forwarded-For');
        }
        return sha1(microtime() . ((string) $throwable) . ($IPAddress) . random_bytes(16));
    }
    
}
