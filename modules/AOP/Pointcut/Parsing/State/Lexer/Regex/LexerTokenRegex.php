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

namespace Norma\AOP\Pointcut\Parsing\State\Lexer\Regex;

use Norma\Regex\CodeRegex;

/**
 * A class containing regular expressions to match against pointcut expressions to identify its tokens.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
final class LexerTokenRegex {
    
    /**
     * Private constructor to prevent the creation of objects of this class.
     */
    private function __construct() {}
    
    /**
     * Regular expression to match whitespaces within a pointcut expression.
     */
    const TOKEN_WHITESPACE_REGEX = '#^[\s]+$#';
    
    /**
     * Regular expression to match a simple pointcut opening delimiter.
     */
    const TOKEN_POINTCUT_OPEN_DELIMITER_REGEX = '#^{$#';
    
    /**
     * Regular expression to match a simple pointcut closing delimiter.
     */
    const TOKEN_POINTCUT_CLOSE_DELIMITER_REGEX = '#^}$#';
    
    /**
     * Regular expression to match a namespace pattern.
     * 
     * @see https://regex101.com/r/lzuWh8/12
     */
    const TOKEN_NAMESPACE_PATTERN_REGEX = '~
        (?(DEFINE)
           
           # OK start char.
           (?<OK_START_CHAR>
               [a-zA-Z_\x7f-\xff]
               |
               (?&WILDCARD)
               |
               (?&NS_SEPARATOR)
           )
    
           # OK middle or ending char.
           (?<OK_MIDDLE_CHAR>
               [a-zA-Z0-9_\x7f-\xff]
           )
    
           # NS separator.
           # `[\\\\]` becomes `[\\]` after the interpreter interprets the string and passes it to `preg_*` functions.
           (?<NS_SEPARATOR>
               [\\\\]
           )
    
           # `*` wildcard char.
           (?<WILDCARD>
               [*]
           )
    
           # ORed OK chars.
           (?<OK_CHAR>(?:
               (?&OK_START_CHAR)
               |
               (?&OK_MIDDLE_CHAR)
               |
               (?&NS_SEPARATOR)
               |
               (?&WILDCARD)
           ))
           
           # Digit char.
           (?<DIGIT_CHAR>[0-9])
           
           # Reserved tokens
           (?<RESERVED_TOKEN>
                public
                |
                protected
                |
                private
                |
                static
                |
                new
           )
        )
        ^
        (?=(?&OK_CHAR)+$)
        (?=(?&OK_START_CHAR))
        (?!.*(?&WILDCARD){3,})
        (?!.*(?&NS_SEPARATOR){2,})
        (?!.*(?&NS_SEPARATOR)(?=(?&DIGIT_CHAR)))
        (?!(?&RESERVED_TOKEN)$)
        (?!(?&RESERVED_TOKEN)(?&NS_SEPARATOR))
        (?!.*(?&NS_SEPARATOR)(?&RESERVED_TOKEN)(?&NS_SEPARATOR))
        (?!(?&WILDCARD)$)
        ~x';
    
    /**
     * Regular expression to match a name pattern.
     * 
     * @see https://regex101.com/r/Kyts0e/7
     */
    const TOKEN_NAME_PATTERN_REGEX = '~
        (?(DEFINE)
           
           # OK start char.
           (?<OK_START_CHAR>
               [a-zA-Z_\x7f-\xff]
               |
               (?&WILDCARD)
           )
    
           # OK middle or ending char.
           (?<OK_MIDDLE_CHAR>
               [a-zA-Z0-9_\x7f-\xff]
           )
    
           # `*` wildcard char.
           (?<WILDCARD>
               [*]
           )
           
           # ORed OK chars.
           (?<OK_CHAR>(?:
               (?&OK_START_CHAR)
               |
               (?&OK_MIDDLE_CHAR)
               |
               (?&WILDCARD)
           ))
           
           # Reserved tokens
           (?<RESERVED_TOKEN>
                public
                |
                protected
                |
                private
                |
                static
                |
                new
                |
                \*
           )
           
        )
        ^
        (?=(?&OK_CHAR)+$)
        (?=(?&OK_START_CHAR))
        (?!.*(?&WILDCARD){2,})
        (?!(?&RESERVED_TOKEN)$)
        ~x';
    
    /**
     * Regular expression to match a `public` member access modifier keyword.
     */
    const TOKEN_PUBLIC_MEMBER_ACCESS_MODIFIER_REGEX = '#^public$#';

    /**
     * Regular expression to match a `protected` member access modifier keyword.
     */
    const TOKEN_PROTECTED_MEMBER_ACCESS_MODIFIER_REGEX = '#^protected#';
    
    /**
     * Regular expression to match a `private` member access modifier keyword.
     */
    const TOKEN_PRIVATE_MEMBER_ACCESS_MODIFIER_REGEX = '#^private$#';
    
    /**
     * Regular expression to match a static member access operator.
     */
    const TOKEN_STATIC_MEMBER_ACCESS_OPERATOR_REGEX = '#^::$#';
    
    /**
     * Regular expression to match an instance member access operator.
     */
    const TOKEN_INSTANCE_MEMBER_ACCESS_OPERATOR_REGEX = '#^->$#';
    
    /**
     * Regular expression to match method parentheses.
     */
    const TOKEN_METHOD_PARENTHESES_REGEX = '#^\(\)$#';
    
    /**
     * Regular expression to match a `read` access operation keyword.
     */
    const TOKEN_READ_ACCESS_OPERATION_REGEX = '#^read$#';
    
    /**
     * Regular expression to match a `write` access operation keyword.
     */
    const TOKEN_WRITE_ACCESS_OPERATION_REGEX = '#^write$#';
    
    /**
     * Regular expression to match a namespace identifier plus operator.
     */
    const TOKEN_NAMESPACE_IDENTIFIER_PLUS_OPERATOR_REGEX = '#^\+$#';
    
    /**
     * Regular expression to match a single wildcard.
     */
    const TOKEN_WILDCARD_REGEX = '#^\*$#';
    
    /**
     * Regular expression to match the `@` annotation starting char.
     */
    const TOKEN_ANNOTATION_START_REGEX = '#^@$#';

    /**
     * Regular expression to match a `method` keyword.
     */
    const TOKEN_METHOD_KEYWORD_REGEX = '#^method$#';

    /**
     * Regular expression to match a `property` keyword.
     */
    const TOKEN_PROPERTY_KEYWORD_REGEX = '#^property#';
    
    /**
     * Regular expression to match a `static` keyword.
     */
    const TOKEN_STATIC_KEYWORD_REGEX = '#^static$#';
    
    /**
     * Regular expression to match a `new` keyword.
     */
    const TOKEN_NEW_KEYWORD_REGEX = '#^new$#';
    
    /**
     * Regular expression to match a `!` NOT operator.
     */
    const TOKEN_NOT_OPERATOR_REGEX = '#^!$#';
    
    /**
     * Regular expression to match a double ampersand `&&` AND operator.
     */
    const TOKEN_AND_OPERATOR_REGEX = '#^&&$#';
    
    /**
     * Regular expression to match a double pipe `||` OR operator.
     */
    const TOKEN_OR_OPERATOR_REGEX = '#^\|\|$#';
    
    /**
     * Regular expression to match an opening parenthesis.
     */
    const TOKEN_PARENTHESIS_OPEN_REGEX = '#^\($#';
    
    /**
     * Regular expression to match a closing parenthesis.
     */
    const TOKEN_PARENTHESIS_CLOSE_REGEX = '#^\)$#';
    
    /**
     * Regular expression to match a pointcut identifier.
     */
    const TOKEN_POINTCUT_IDENTIFIER_REGEX = '#^' . CodeRegex::VALID_PHP_NAME_REGEX . '$#';
    
}
