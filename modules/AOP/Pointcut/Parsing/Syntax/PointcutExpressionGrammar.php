<?php

/*
 * Copyright (c) 2021 Anton Bagdatyev (Tonix)
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

namespace Norma\AOP\Pointcut\Parsing\Syntax;

use Norma\Algorithm\Parsing\Grammar\AbstractArrayParserGrammar;
use Norma\AOP\Pointcut\Parsing\TokenTypeEnum;
use Norma\AOP\Pointcut\Parsing\State\Lexer\AbstractLexerState;

/**
 * The grammar of a pointcut expression.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class PointcutExpressionGrammar extends AbstractArrayParserGrammar {
    
    /**
     * {@inheritdoc}
     */
    public function terminalTokenIdentifierToString($terminalTokenIdentifier): string {
        return AbstractLexerState::tokenLabel($terminalTokenIdentifier);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function generateRulesArray(): array {
        return [
            'RULE' => 1,
            'POINTCUT_EXPRESSION' => [
                // Dimension 1 (odd). Odd dimensions of grammar rule are ANDed.
                [
                    // Dimension 2 (even). Even dimensions of grammar rule are ORed.
                    'POINTCUT',
                    // OR
                    [
                        TokenTypeEnum::TOKEN_PARENTHESIS_OPEN,
                        // AND (means "followed by")
                        'POINTCUT',
                        // AND (means "followed by")
                        TokenTypeEnum::TOKEN_PARENTHESIS_CLOSE
                    ]
                ]
            ],

            'POINTCUT' => [
                // Dimension 1 (odd).
                [
                    // Dimension 2 (even).
                    'METHOD_EXECUTION_POINTCUT',  // Nonterminal
                    // OR
                    'PROPERTY_ACCESS_POINTCUT',  // Nonterminal
                    // OR
                    'ANNOTATED_POINTCUT',  // Nonterminal
                    // OR
                    'INITIALIZATION_POINTCUT',  // Nonterminal
                    // OR
                    'STATIC_INITIALIZATION_POINTCUT',  // Nonterminal
                    // OR
                    'POINTCUT_IDENTIFIER', // Nonterminal
                    // OR
                    'COMPLEX_POINTCUT',  // Nonterminal
                ]
            ],

            'METHOD_EXECUTION_POINTCUT' => [
                // Dimension 1 (odd).
                TokenTypeEnum::TOKEN_POINTCUT_OPEN_DELIMITER,
                // AND
                'MEMBER_ACCESS_MODIFIER',
                [TokenTypeEnum::TOKEN_NAMESPACE_PATTERN, TokenTypeEnum::TOKEN_WILDCARD],
                // AND
                // Double array with only one terminal or nonterminal means that this rule's symbol is optional
                // (only in the context of an AND, therefore when the dimension of the containing array is odd).
                [[TokenTypeEnum::TOKEN_NAMESPACE_IDENTIFIER_PLUS_OPERATOR]], // Optional rule's symbol
                // AND
                'MEMBER_ACCESS_OPERATOR',
                // AND
                [TokenTypeEnum::TOKEN_NAME_PATTERN, TokenTypeEnum::TOKEN_WILDCARD],
                // AND
                TokenTypeEnum::TOKEN_METHOD_PARENTHESES,
                // AND
                TokenTypeEnum::TOKEN_POINTCUT_CLOSE_DELIMITER,
            ],

            'PROPERTY_ACCESS_POINTCUT' => [
                TokenTypeEnum::TOKEN_POINTCUT_OPEN_DELIMITER,
                // AND
                'ACCESS_OPERATION',
                // AND
                'MEMBER_ACCESS_MODIFIER',
                [
                    TokenTypeEnum::TOKEN_NAMESPACE_PATTERN,
                    // OR
                    TokenTypeEnum::TOKEN_WILDCARD
                ],
                // AND
                [[TokenTypeEnum::TOKEN_NAMESPACE_IDENTIFIER_PLUS_OPERATOR]], // Optional rule's symbol
                // AND
                'MEMBER_ACCESS_OPERATOR',
                // AND
                [TokenTypeEnum::TOKEN_NAME_PATTERN, TokenTypeEnum::TOKEN_WILDCARD],
                // AND
                TokenTypeEnum::TOKEN_POINTCUT_CLOSE_DELIMITER,
            ],

            'ANNOTATED_POINTCUT' => [
                // Dimension 1 (odd).
                TokenTypeEnum::TOKEN_POINTCUT_OPEN_DELIMITER,
                // AND
                [
                    // Dimension 2 (even).
                    TokenTypeEnum::TOKEN_METHOD_KEYWORD,
                    // OR
                    [
                        // Dimension 3 (odd).
                        TokenTypeEnum::TOKEN_PROPERTY_KEYWORD,
                        // AND
                        //'ACCESS_OPERATION', // TODO: Uncomment
                        /* TODO: Remove */
                        [
                            TokenTypeEnum::TOKEN_READ_ACCESS_OPERATION,
                            // OR
                            TokenTypeEnum::TOKEN_WRITE_ACCESS_OPERATION,
                            // OR
                            TokenTypeEnum::TOKEN_WILDCARD,
                            // OR
                            [
                                'A',
                                // AND
                                'B'
                            ]
                        ]
                        /* /TODO: Remove */
                    ]
                ],
                // AND
                TokenTypeEnum::TOKEN_ANNOTATION_START,
                // AND
                [
                    TokenTypeEnum::TOKEN_NAMESPACE_PATTERN,
                    // OR
                    TokenTypeEnum::TOKEN_WILDCARD
                ],
                // AND
                TokenTypeEnum::TOKEN_POINTCUT_CLOSE_DELIMITER,
            ],

            'INITIALIZATION_POINTCUT' => [
                TokenTypeEnum::TOKEN_POINTCUT_OPEN_DELIMITER,
                // AND
                TokenTypeEnum::TOKEN_NEW_KEYWORD,
                // AND
                [TokenTypeEnum::TOKEN_NAMESPACE_PATTERN, TokenTypeEnum::TOKEN_WILDCARD],
                // AND
                [[TokenTypeEnum::TOKEN_NAMESPACE_IDENTIFIER_PLUS_OPERATOR]], // Optional rule's symbol
                // AND
                TokenTypeEnum::TOKEN_POINTCUT_CLOSE_DELIMITER,
            ],

            'STATIC_INITIALIZATION_POINTCUT' => [
                TokenTypeEnum::TOKEN_POINTCUT_OPEN_DELIMITER,
                // AND
                TokenTypeEnum::TOKEN_STATIC_KEYWORD,
                // AND
                [TokenTypeEnum::TOKEN_NAMESPACE_PATTERN, TokenTypeEnum::TOKEN_WILDCARD],
                // AND
                [[TokenTypeEnum::TOKEN_NAMESPACE_IDENTIFIER_PLUS_OPERATOR]], // Optional rule's symbol
                // AND
                TokenTypeEnum::TOKEN_POINTCUT_CLOSE_DELIMITER,
            ],

            'MEMBER_ACCESS_MODIFIER' => [
                [
                    TokenTypeEnum::TOKEN_PUBLIC_MEMBER_ACCESS_MODIFIER,
                    // OR
                    TokenTypeEnum::TOKEN_PROTECTED_MEMBER_ACCESS_MODIFIER,
                    // OR
                    TokenTypeEnum::TOKEN_PRIVATE_MEMBER_ACCESS_MODIFIER,
                    // OR
                    TokenTypeEnum::TOKEN_WILDCARD,
                ]
            ],

            'ACCESS_OPERATION' => [
                [
                    TokenTypeEnum::TOKEN_READ_ACCESS_OPERATION,
                    // OR
                    TokenTypeEnum::TOKEN_WRITE_ACCESS_OPERATION,
                    // OR
                    TokenTypeEnum::TOKEN_WILDCARD,
                ]
            ],

            'MEMBER_ACCESS_OPERATOR' => [
                [
                    TokenTypeEnum::TOKEN_INSTANCE_MEMBER_ACCESS_OPERATOR,
                    // OR
                    TokenTypeEnum::TOKEN_STATIC_MEMBER_ACCESS_OPERATOR,
                ]
            ],

            'POINTCUT_IDENTIFIER' => [
                [
                    TokenTypeEnum::TOKEN_POINTCUT_IDENTIFIER, // Terminal
                    // OR
                    [
                        TokenTypeEnum::TOKEN_POINTCUT_OPEN_DELIMITER, // Terminal
                        // AND
                        TokenTypeEnum::TOKEN_POINTCUT_IDENTIFIER, // Terminal
                        // AND
                        TokenTypeEnum::TOKEN_POINTCUT_CLOSE_DELIMITER, // Terminal
                    ]
                ]
            ],

            'COMPLEX_POINTCUT' => [
                [
                    'POINTCUT_EXPRESSION',
                    // OR
                    'NEGATION_POINTCUT', // NOT
                    // OR
                    'CONJUNCTION_POINTCUT', // AND
                    // OR
                    'DISJUNCTION_POINTCUT', // OR
                ]
            ],

            'NEGATION_POINTCUT' => [
                TokenTypeEnum::TOKEN_NOT_OPERATOR,
                // AND
                'POINTCUT_EXPRESSION'
            ],

            'CONJUNCTION_POINTCUT' => [
                'POINTCUT_EXPRESSION',
                // AND
                TokenTypeEnum::TOKEN_AND_OPERATOR,
                // AND
                'POINTCUT_EXPRESSION',
            ],

            'DISJUNCTION_POINTCUT' => [
                'POINTCUT_EXPRESSION',
                // AND
                TokenTypeEnum::TOKEN_OR_OPERATOR,
                // AND
                'POINTCUT_EXPRESSION'
            ]

        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRootRule(): string {
        return 'POINTCUT_EXPRESSION';
    }
    
}
