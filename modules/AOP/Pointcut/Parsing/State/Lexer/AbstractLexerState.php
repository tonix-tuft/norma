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

namespace Norma\AOP\Pointcut\Parsing\State\Lexer;

use StatusQuo\FSM\State\StateInterface;
use StatusQuo\FSM\DistributedTransitionLogicFiniteStateMachineInterface;
use Norma\Core\Utils\FrameworkArrayUtilsTrait;
use Norma\AOP\Pointcut\Parsing\TokenTypeEnum;
use Norma\AOP\Pointcut\Parsing\PointcutParsingException;
use Norma\AOP\Pointcut\Parsing\State\Lexer\Regex\LexerTokenRegex;
use Norma\AOP\Pointcut\Parsing\State\Lexer\ScanANDOperatorTokenState;
use Norma\AOP\Pointcut\Parsing\State\Lexer\ScanOROperatorTokenState;
use Norma\AOP\Pointcut\Parsing\State\Lexer\ScanStaticMemberAccessOperatorTokenState;
use Norma\AOP\Pointcut\Parsing\State\Lexer\ScanInstanceMemberAccessOperatorTokenState;
use Norma\AOP\Pointcut\Parsing\State\Lexer\ScanWhitespaceTokenState;
use Norma\AOP\Pointcut\Parsing\State\Lexer\ScanParenthesisStartState;

/**
 * An abstract pointcut parser lexer's state.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
abstract class AbstractLexerState implements StateInterface {
    
    use FrameworkArrayUtilsTrait;
    
    /**
     * Required input keys.
     */
    const REQUIRED_INPUT_KEYS = ['char', 'pos', 'parser', 'is_last_char'];
    
    /**
     * A constant array which maps every unambiguous single character token type to its corresponding regex.
     */
    const UNAMBIGUOUS_SINGLE_CHAR_TOKENS_MAP = [
        TokenTypeEnum::TOKEN_POINTCUT_OPEN_DELIMITER => LexerTokenRegex::TOKEN_POINTCUT_OPEN_DELIMITER_REGEX,
        TokenTypeEnum::TOKEN_POINTCUT_CLOSE_DELIMITER => LexerTokenRegex::TOKEN_POINTCUT_CLOSE_DELIMITER_REGEX,
        TokenTypeEnum::TOKEN_NAMESPACE_IDENTIFIER_PLUS_OPERATOR => LexerTokenRegex::TOKEN_NAMESPACE_IDENTIFIER_PLUS_OPERATOR_REGEX,
        TokenTypeEnum::TOKEN_ANNOTATION_START => LexerTokenRegex::TOKEN_ANNOTATION_START_REGEX,
        TokenTypeEnum::TOKEN_NOT_OPERATOR => LexerTokenRegex::TOKEN_NOT_OPERATOR_REGEX,
        TokenTypeEnum::TOKEN_PARENTHESIS_CLOSE => LexerTokenRegex::TOKEN_PARENTHESIS_CLOSE_REGEX
    ];
    
    /**
     * A constant array which maps every unambiguous double character token type to its regex, second character and pertaining state.
     */
    const UNAMBIGUOUS_DOUBLE_CHAR_TOKENS_MAP = [
        TokenTypeEnum::TOKEN_STATIC_MEMBER_ACCESS_OPERATOR => [
            'regex' => LexerTokenRegex::TOKEN_STATIC_MEMBER_ACCESS_OPERATOR_REGEX,
            'second_char' => ':',
            'state' => ScanStaticMemberAccessOperatorTokenState::class
        ],
        TokenTypeEnum::TOKEN_AND_OPERATOR => [
            'regex' => LexerTokenRegex::TOKEN_AND_OPERATOR_REGEX,
            'second_char' => '&',
            'state' => ScanANDOperatorTokenState::class
        ],
        TokenTypeEnum::TOKEN_OR_OPERATOR => [
            'regex' => LexerTokenRegex::TOKEN_OR_OPERATOR_REGEX,
            'second_char' => '|',
            'state' => ScanOROperatorTokenState::class
        ],
        TokenTypeEnum::TOKEN_INSTANCE_MEMBER_ACCESS_OPERATOR => [
            'regex' => LexerTokenRegex::TOKEN_INSTANCE_MEMBER_ACCESS_OPERATOR_REGEX,
            'second_char' => '>',
            'state' => ScanInstanceMemberAccessOperatorTokenState::class
        ],
        TokenTypeEnum::TOKEN_WHITESPACE => [
            'regex' => LexerTokenRegex::TOKEN_WHITESPACE_REGEX,
            'second_char' => '',
            'state' => ScanWhitespaceTokenState::class
        ],
        TokenTypeEnum::TOKEN_PARENTHESIS_OPEN => [
            'regex' => LexerTokenRegex::TOKEN_PARENTHESIS_OPEN_REGEX,
            'second_char' => '',
            'state' => ScanParenthesisStartState::class
        ]
    ];
    
    /**
     * @var array|null
     */
    protected static $inversedTokenTypeEnumConsts = NULL;
    
    /**
     * {@inheritdoc}
     */
    public function process(DistributedTransitionLogicFiniteStateMachineInterface $FSM, $input = NULL) {
        if (
            !is_array($input)
        ) {
            throw new PointcutParsingException('The lexer received no input.');
        }
        
        $emptyKey = $this->atLeastOneArrayKeyDoesNotExist($input, static::REQUIRED_INPUT_KEYS);
        if (
            $emptyKey !== FALSE
        ) {
            throw new PointcutParsingException(sprintf('Required lexer input "%s" key is missing.', $emptyKey));
        }
        
        $char = $input['char'];
        $pos = $input['pos'];
        $parserFSM = $input['parser'];
        $isLastChar = $input['is_last_char'];
        $this->processChar($char, $pos, $FSM, $parserFSM, $isLastChar);
    }
    
    /**
     * Returns the pointcut token label given its type.
     * 
     * @param int $tokenType The type of token. A value of a constant of the {@link TokenTypeEnum} enum-like class.
     * @return string The label of the token.
     * @throws PointcutParsingException If a token type is unknown.
     */
    public static function tokenLabel($tokenType) {
        if (!static::$inversedTokenTypeEnumConsts) {
            $reflectionClass = new \ReflectionClass(TokenTypeEnum::class);
            $tokenConsts = $reflectionClass->getConstants();
            static::$inversedTokenTypeEnumConsts = array_flip($tokenConsts);
        }
        if (isset(static::$inversedTokenTypeEnumConsts[$tokenType])) {
            return static::$inversedTokenTypeEnumConsts[$tokenType];
        }
        else {
            throw new PointcutParsingException(sprintf('Could not lookup token label. Unknown token type "%s".', $tokenType));
        }
    }
    
    /**
     * Processes a single char.
     * 
     * @param string $char A single char to process.
     * @param int $pos The position of the char within the original input source.
     * @param DistributedTransitionLogicFiniteStateMachineInterface $lexerFSM The lexer's state machine.
     * @param DistributedTransitionLogicFiniteStateMachineInterface $parserFSM The parser's state machine.
     * @param bool $isLastChar A boolean denoting whether the given char is the last char (TRUE) or not (FALSE).
     * @return void
     */
    abstract public function processChar($char, $pos, DistributedTransitionLogicFiniteStateMachineInterface $lexerFSM, DistributedTransitionLogicFiniteStateMachineInterface $parserFSM, $isLastChar);
    
}
