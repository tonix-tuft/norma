<?php

/*
 * Copyright (c) 2018 Anton Bagdatyev
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

/**
 * A map which describes metadata associated with PHP's internal severity codes.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
abstract class SeverityCodeMap {
    
    /**
     * @var array
     */
    public static $types = [
        E_ERROR => 'E_ERROR',
        E_WARNING => 'E_WARNING',
        E_PARSE => 'E_PARSE',
        E_NOTICE => 'E_NOTICE',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_USER_ERROR => 'E_USER_ERROR',
        E_USER_WARNING => 'E_USER_WARNING',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_STRICT => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED => 'E_DEPRECATED',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED'
    ];
    
    /**
     * @var array
     */
    public static $exceptions = [
        E_ERROR => ErrorException::class,
        E_WARNING => WarningException::class,
        E_PARSE => ParseException::class,
        E_NOTICE => NoticeException::class,
        E_CORE_ERROR => CoreErrorException::class,
        E_CORE_WARNING => CoreWarningException::class,
        E_COMPILE_ERROR => CompileErrorException::class,
        E_COMPILE_WARNING => CompileWarningException::class,
        E_USER_ERROR => UserErrorException::class,
        E_USER_WARNING => UserWarningException::class,
        E_USER_NOTICE => UserNoticeException::class,
        E_STRICT => StrictException::class,
        E_RECOVERABLE_ERROR => RecoverableErrorException::class,
        E_DEPRECATED => DeprecatedException::class,
        E_USER_DEPRECATED => UserDeprecatedException::class
    ];
    
}
