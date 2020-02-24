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

namespace Norma\HTTP;

use Norma\Core\Utils\EnumToKeyValTrait;

/**
 * HTTP protocol version.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
abstract class HTTPProtocolVersionEnum {
    
    use EnumToKeyValTrait;
    
    const HTTP_PROTOCOL_VERSION_PREFIX = 'HTTP/';
    
    const V1_0_CODE = '1.0';
    
    const V1_1_CODE = '1.1';
    
    const V2_CODE = '2';
    
    const V1_0 = self::HTTP_PROTOCOL_VERSION_PREFIX . self::V1_0_CODE;
    
    const V1_1 = self::HTTP_PROTOCOL_VERSION_PREFIX . self::V1_1_CODE;
    
    const V2 = self::HTTP_PROTOCOL_VERSION_PREFIX . self::V2_CODE;
    
}
