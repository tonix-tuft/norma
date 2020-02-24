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

namespace Norma\Middleware;

use Norma\Core\Utils\EnumToKeyValTrait;

/**
 * An enum-like class defining the middleware layers of the Norma's framework.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
abstract class MiddlewareLayerEnum {
    
    use EnumToKeyValTrait;
    
    /**
     * 1. On throwable middleware layer.
     */
    const ON_THROWABLE = 1;
    
    /**
     * 2. Before execution middleware layer.
     */
    const BEFORE_EXECUTION = 2;
    
    /**
     * 3. After execution middleware layer.
     */
    const AFTER_EXECUTION = 3;
    
}