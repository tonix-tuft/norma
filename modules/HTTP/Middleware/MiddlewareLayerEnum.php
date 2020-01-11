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

namespace Norma\HTTP\Middleware;

use Norma\Core\Utils\EnumToKeyValTrait;

/**
 * An enum-like class defining the middleware layers of the Norma's framework.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
abstract class MiddlewareLayerEnum {
    
    use EnumToKeyValTrait;
    
    /**
     * 1. On throwable middleware layer.
     */
    const ON_THROWABLE = 1;
    
    /**
     * 2. Before route matching process middleware layer.
     */
    const BEFORE_ROUTE_MATCHING_PROCESS = 2;
    
    /**
     * 3. After route matching process middleware layer.
     */
    const AFTER_ROUTE_MATCHING_PROCESS = 3;
    
    /**
     * 4. Before controller execution middleware layer.
     */
    const BEFORE_CONTROLLER_EXECUTION = 4;
    
    /**
     * 5. After controller execution middleware layer.
     */
    const AFTER_CONTROLLER_EXECUTION = 5;
    
    /**
     * 6. When partially unmatched route middleware layer.
     */
    const WHEN_PARTIALLY_UNMATCHED_ROUTE = 6;
    
    /**
     * 7. When no matching route middleware layer.
     */
    const WHEN_NO_MATCHING_ROUTE = 7;
    
    /**
     * 8. Before sending response middleware layer.
     */
    const BEFORE_SENDING_RESPONSE = 8;
    
    /**
     * 9. After sending response middleware layer.
     */
    const AFTER_SENDING_RESPONSE = 9;
    
}
