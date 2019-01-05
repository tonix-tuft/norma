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

namespace Norma\Middleware;

use Norma\Middleware\UnknownMiddlewareLayerException;

/**
 * The interface of a middleware layer.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface MiddlewareLayerInterface {
    
    /**
     * Registers the given middlewares to the specified middlewares layer in the order by which they are given.
     * 
     * @param int $middlewareLayer The integer code of the middleware layer to register.
     * @param array $middlewares An array of middlewares to register.
     * @param bool $override Whether to override the same middleware layer with the given middlewares of not.
     *                                      If FALSE, the given middlewares MUST be appended to the current queue of middlewares.
     *                                      Otherwise, the given middlewares MUST override the current middlewares of the same middleware layer.
     * @return static
     * @throws UnknownMiddlewareLayerException If the given middleware layer is unknown.
     */
    public function register($middlewareLayer, $middlewares, $override = FALSE);
    
    /**
     * Gets the middlewares registered for the given middleware layer.
     * 
     * @param int $middlewareLayer The integer code of the middleware layer.
     * @return array This method MUST return an array containing the registered structure of middlewares for the given middleware layer, or an empty array if none middlewares are
     *                       registered for the given middleware layer.
     * @throws UnknownMiddlewareLayerException If the given middleware layer is unknown.
     */
    public function get($middlewareLayer);
    
}
