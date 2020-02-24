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

namespace Norma\AOP\Pointcut\Parsing\State\Lexer;

use Norma\State\FSM\DistributedTransitionLogicFiniteStateMachineInterface;
use Norma\AOP\Pointcut\Parsing\State\Lexer\AbstractLexerState;
use Norma\AOP\Pointcut\Parsing\State\Lexer\Regex\LexerTokenRegex;
use Norma\AOP\Pointcut\Parsing\State\Lexer\LexerEndState;
use Norma\AOP\Pointcut\Parsing\State\Lexer\TokenStartState;
use Norma\AOP\Pointcut\Parsing\TokenTypeEnum;
use Norma\AOP\Pointcut\Parsing\PointcutParsingException;

/**
 * Ambiguous token scanning state.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class ScanAmbiguousTokenStartState extends AbstractLexerState {
        
    /**
     * @var array|null
     */
    protected static $unambiguousTokensMap = NULL;
    
    /**
     * Constructs a new state.
     */
    public function __construct() {
        static::$unambiguousTokensMap = $this->buildUnambiguousTokensMap();
    }
    
    /**
     * Builds a map of unambiguous tokens which maps a token type to its corresponding regex.
     * 
     * @return array A map represented by an array of token types as keys and either one of the following values:
     *                         - A `string` representing the regex for that token if it is a single char;
     *                         - An associative `array` with keys `regex`, `second_char`, `state` if the token is made of two characters;
     */
    protected function buildUnambiguousTokensMap() {
        if (static::$unambiguousTokensMap === NULL) {
            static::$unambiguousTokensMap = array_merge(
                static::UNAMBIGUOUS_SINGLE_CHAR_TOKENS_MAP,
                static::UNAMBIGUOUS_DOUBLE_CHAR_TOKENS_MAP
            );
        }
        return static::$unambiguousTokensMap;
    }
    
    /**
     * {@inheritdoc}
     */
    public function processChar($char, $pos, DistributedTransitionLogicFiniteStateMachineInterface $lexerFSM, DistributedTransitionLogicFiniteStateMachineInterface $parserFSM, $isLastChar) {
        $lastNonWhitespaceTokenBeforeCurrent = $lexerFSM->getData('last_non_whitespace_token_before_current');
        $currentLexerToken = $lexerFSM->getData('token');
        
        $thisCharIsUnambiguous = FALSE;
        foreach (static::$unambiguousTokensMap as $tokenMetadata) {
            $matchAgainst = $char;
            if (is_array($tokenMetadata)) {
                $regex = $tokenMetadata['regex'];
                $matchAgainst .= $tokenMetadata['second_char'];
            }
            else {
                $regex = $tokenMetadata;
            }
            if (preg_match($regex, $matchAgainst)) {
                $thisCharIsUnambiguous = TRUE;
                break;
            }
        }
        
        if ($thisCharIsUnambiguous) {
            // Token boundary
            $this->processAmbiguousToken($currentLexerToken, $lastNonWhitespaceTokenBeforeCurrent, $parserFSM);
            $lexerFSM->setState(TokenStartState::class);
            $lexerFSM->setData('reiterate', [
                'char' => $char,
                'pos' => $pos,
                'is_last_char' => $isLastChar
            ]);
        }
        else {
            if ($isLastChar) {
                // Last ambiguous char. What the lexer has so far with this character is enough.
                $currentLexerToken['lexeme'] = $currentLexerToken['lexeme'] . $char;
                $this->processAmbiguousToken($currentLexerToken, $lastNonWhitespaceTokenBeforeCurrent, $parserFSM);
                $lexerFSM->setState(LexerEndState::class);
            }
            else {
                // Character is still ambiguous, and is not the last. Remaining in the same state.
                // Lexer asks to be fed with more characters.
                $lexerFSM->setData('token', function($oldData) use ($char) {
                    return [
                        'type' => NULL, // Token type is unknown yet.
                        'lexeme' => $oldData['lexeme'] . $char,
                        'pos' => $oldData['pos']
                    ];
                });
            }
        }
        
    }
    
    /**
     * Processes ambiguous token.
     * 
     * @param string $currentLexerToken The current token to process.
     * @param array|NULL $lastNonWhitespaceTokenBeforeCurrent Last parsed non-whitespace token before current one or NULL if no previous token exists.
     * @param DistributedTransitionLogicFiniteStateMachineInterface $parserFSM Parser's finite-state machine.
     * @return void
     */
    protected function processAmbiguousToken($currentLexerToken, $lastNonWhitespaceTokenBeforeCurrent, DistributedTransitionLogicFiniteStateMachineInterface $parserFSM) {
        $tokenType = NULL;
        $lexeme = $currentLexerToken['lexeme'];
        
        $lastNonWhitespaceTokenBeforeCurrentType = $lastNonWhitespaceTokenBeforeCurrent !== NULL ? $lastNonWhitespaceTokenBeforeCurrent['type'] : NULL;
        
        // Reserved tokens.
        if (preg_match(LexerTokenRegex::TOKEN_PUBLIC_MEMBER_ACCESS_MODIFIER_REGEX, $lexeme)) {
            $tokenType = TokenTypeEnum::TOKEN_PUBLIC_MEMBER_ACCESS_MODIFIER;
        }
        else if (preg_match(LexerTokenRegex::TOKEN_PROTECTED_MEMBER_ACCESS_MODIFIER_REGEX, $lexeme)) {
            $tokenType = TokenTypeEnum::TOKEN_PROTECTED_MEMBER_ACCESS_MODIFIER;
        }
        else if (preg_match(LexerTokenRegex::TOKEN_PRIVATE_MEMBER_ACCESS_MODIFIER_REGEX, $lexeme)) {
            $tokenType = TokenTypeEnum::TOKEN_PRIVATE_MEMBER_ACCESS_MODIFIER;
        }
        else if (preg_match(LexerTokenRegex::TOKEN_STATIC_KEYWORD_REGEX, $lexeme)) {
            $tokenType = TokenTypeEnum::TOKEN_STATIC_KEYWORD;
        }
        else if (preg_match(LexerTokenRegex::TOKEN_NEW_KEYWORD_REGEX, $lexeme)) {
            $tokenType = TokenTypeEnum::TOKEN_NEW_KEYWORD;
        }
        else if (preg_match(LexerTokenRegex::TOKEN_WILDCARD_REGEX, $lexeme)) {
             // Can be a wildcard in the context of an access operation, access modifier, namespace pattern or name pattern.
            $tokenType = TokenTypeEnum::TOKEN_WILDCARD;
        }
        // Tokens depending on previous non-whitespace token.
        else if (
            preg_match(LexerTokenRegex::TOKEN_READ_ACCESS_OPERATION_REGEX, $lexeme)
            &&
            (
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_POINTCUT_OPEN_DELIMITER
                ||
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_PROPERTY_KEYWORD
            )
        ) {
            // `read` access operation token.
            $tokenType = TokenTypeEnum::TOKEN_READ_ACCESS_OPERATION;
        }
        else if (
            preg_match(LexerTokenRegex::TOKEN_WRITE_ACCESS_OPERATION_REGEX, $lexeme)
            &&
            (
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_POINTCUT_OPEN_DELIMITER
                ||
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_PROPERTY_KEYWORD
            )
        ) {
            // `write` access operation token.
            $tokenType = TokenTypeEnum::TOKEN_WRITE_ACCESS_OPERATION;
        }
        else if (
            preg_match(LexerTokenRegex::TOKEN_METHOD_KEYWORD_REGEX, $lexeme)
            &&
            $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_POINTCUT_OPEN_DELIMITER
        ) {
            $tokenType = TokenTypeEnum::TOKEN_METHOD_KEYWORD;
        }
        else if (
            preg_match(LexerTokenRegex::TOKEN_PROPERTY_KEYWORD_REGEX, $lexeme)
            &&
            $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_POINTCUT_OPEN_DELIMITER
        ) {
            $tokenType = TokenTypeEnum::TOKEN_PROPERTY_KEYWORD;
        }
        else if (
            preg_match(LexerTokenRegex::TOKEN_NAME_PATTERN_REGEX, $lexeme)
            &&
            (
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_INSTANCE_MEMBER_ACCESS_OPERATOR
                ||
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_STATIC_MEMBER_ACCESS_OPERATOR
            )
        ) {
            $tokenType = TokenTypeEnum::TOKEN_NAME_PATTERN;
        }
        else if (
            preg_match(LexerTokenRegex::TOKEN_NAMESPACE_PATTERN_REGEX, $lexeme)
            &&
            (
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_PUBLIC_MEMBER_ACCESS_MODIFIER
                ||
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_PROTECTED_MEMBER_ACCESS_MODIFIER
                ||
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_PRIVATE_MEMBER_ACCESS_MODIFIER
                ||
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_WILDCARD
                ||
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_NEW_KEYWORD
                ||
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_STATIC_KEYWORD
                ||
                $lastNonWhitespaceTokenBeforeCurrentType === TokenTypeEnum::TOKEN_ANNOTATION_START
            )
        ) {
            $tokenType = TokenTypeEnum::TOKEN_NAMESPACE_PATTERN;
        }
        else if (
            preg_match(LexerTokenRegex::TOKEN_POINTCUT_IDENTIFIER_REGEX, $lexeme)
        ) {
            $tokenType = TokenTypeEnum::TOKEN_POINTCUT_IDENTIFIER;
        }
        
        if (is_null($tokenType)) {
            $tokenStartPos = $currentLexerToken['pos'];
            $intruder = $this->findTheIntruder($lexeme, $tokenStartPos);
            if ($intruder !== NULL) {
                list($invalidChar, $invalidCharPos) = $intruder;
                throw new PointcutParsingException(sprintf('Invalid character "%s" for ambiguous token lexeme "%s" at position %s.', $invalidChar, $lexeme, $invalidCharPos));
            }
            else {
                throw new PointcutParsingException(sprintf('Ambiguous token lexeme "%s" starting at position %s contains an invalid character.', $lexeme, $tokenStartPos));
            }
        }
        
        $currentLexerToken['type'] = $tokenType;
        $parserFSM->setData('token', $currentLexerToken);
    }
    
    /**
     * Finds an intruder character as well as its position within the pointcut expression.
     * 
     * @param string $lexeme The token's lexeme.
     * @param int $tokenStartPos The token initial position.
     * @return array|null If the intruder character is found, an array of two elements, intruder character at index 0
     *                                 and its position within the pointcut expression at index 1 is returned.
     *                                 Otherwise, if the intruder is not found, NULL is returned.
     */
    protected function findTheIntruder($lexeme, $tokenStartPos) {
        $len = strlen($lexeme);
        for ($i = 0; $i < $len; $i++) {
            $lexemeChar = $lexeme[$i];
            $match = FALSE;
            $onMatch = function() use (&$match) {
                $match = TRUE;
            };
            
            preg_replace_callback_array([
                LexerTokenRegex::TOKEN_WILDCARD_REGEX => $onMatch,
                LexerTokenRegex::TOKEN_NAMESPACE_PATTERN_REGEX => $onMatch,
                LexerTokenRegex::TOKEN_NAME_PATTERN_REGEX => $onMatch,
                LexerTokenRegex::TOKEN_POINTCUT_IDENTIFIER_REGEX => $onMatch,
            ], $lexemeChar);
            
            if (!$match) {
                return [$lexemeChar, $tokenStartPos + $i];
            }
        }
        return NULL;
    }
    
}
