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

namespace Norma\AOP\Pointcut\Parsing;

use Norma\Core\Utils\EnumToKeyValTrait;

/**
 * Pointcut token enum-like class.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
abstract class TokenTypeEnum {
    
    use EnumToKeyValTrait;
    
    /**
     * Whitespace token.
     */
    const TOKEN_WHITESPACE = 1;
    
    /**
     * Simple pointcut open delimiter (`{`).
     */
    const TOKEN_POINTCUT_OPEN_DELIMITER = 2;
    
    /**
     * Simple pointcut closing delimiter (`}`).
     */
    const TOKEN_POINTCUT_CLOSE_DELIMITER = 3;
    
    /**
     * Namespace pattern token.
     */
    const TOKEN_NAMESPACE_PATTERN = 4;
    
    /**
     * Name pattern token.
     */
    const TOKEN_NAME_PATTERN = 5;
    
    /**
     * Access modifier token (`public`).
     */
    const TOKEN_PUBLIC_MEMBER_ACCESS_MODIFIER = 6;
    
    /**
     * Access modifier token (`protected`).
     */
    const TOKEN_PROTECTED_MEMBER_ACCESS_MODIFIER = 7;
    
    /**
     * Access modifier token (`private`).
     */
    const TOKEN_PRIVATE_MEMBER_ACCESS_MODIFIER = 8;
    
    /**
     * Static access operator token (`::`).
     */
    const TOKEN_STATIC_MEMBER_ACCESS_OPERATOR  = 9;
    
    /**
     * Instance access operator token (`->`).
     */
    const TOKEN_INSTANCE_MEMBER_ACCESS_OPERATOR = 10;
    
    /**
     * Method parentheses token (`()`).
     */
    const TOKEN_METHOD_PARENTHESES = 11;
    
    /**
     * Property access operation token (`read`).
     */
    const TOKEN_READ_ACCESS_OPERATION = 12;

    /**
     * Property access operation token (`write`).
     */
    const TOKEN_WRITE_ACCESS_OPERATION = 13;
    
    /**
     * Namespace identifier plus operator token (`+`).
     */
    const TOKEN_NAMESPACE_IDENTIFIER_PLUS_OPERATOR = 14;
    
    /**
     * Single wildcard token (`*`).
     */
    const TOKEN_WILDCARD = 15;
    
    /**
     * Annotation start token (`@`).
     */
    const TOKEN_ANNOTATION_START = 16;
    
    /**
     * Method keyword token (`method`).
     */
    const TOKEN_METHOD_KEYWORD = 17;

    /**
     * Property keyword token (`property`).
     */
    const TOKEN_PROPERTY_KEYWORD = 18;
    
    /**
     * Static keyword token (`static`).
     */
    const TOKEN_STATIC_KEYWORD = 19;
    
    /**
     * New keyword token (`new`).
     */
    const TOKEN_NEW_KEYWORD = 20;
    
    /**
     * NOT operator token (`!`).
     */
    const TOKEN_NOT_OPERATOR = 21;

    /**
     * AND operator token (`&&`).
     */
    const TOKEN_AND_OPERATOR = 22;

    /**
     * OR operator token (`||`).
     */
    const TOKEN_OR_OPERATOR = 23;
    
    /**
     * Opening parenthesis token (`(`).
     */
    const TOKEN_PARENTHESIS_OPEN = 24;

    /**
     * Closing parenthesis token (`)`).
     */
    const TOKEN_PARENTHESIS_CLOSE = 25;
    
    /**
     * Pointcut identifier token.
     */
    const TOKEN_POINTCUT_IDENTIFIER = 26;
    
}
