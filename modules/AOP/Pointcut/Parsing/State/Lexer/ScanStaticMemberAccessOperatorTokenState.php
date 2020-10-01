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

use Norma\AOP\Pointcut\Parsing\State\Lexer\AbstractLexerState;
use StatusQuo\FSM\DistributedTransitionLogicFiniteStateMachineInterface;
use Norma\AOP\Pointcut\Parsing\State\Lexer\Regex\LexerTokenRegex;
use Norma\AOP\Pointcut\Parsing\TokenTypeEnum;
use Norma\AOP\Pointcut\Parsing\State\Lexer\TokenStartState;
use Norma\AOP\Pointcut\Parsing\State\Lexer\LexerEndState;
use Norma\AOP\Pointcut\Parsing\PointcutParsingException;

/**
 * Static member access operator scanning state.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class ScanStaticMemberAccessOperatorTokenState extends AbstractLexerState {
    
    /**
     * {@inheritdoc}
     */
    public function processChar($char, $pos, DistributedTransitionLogicFiniteStateMachineInterface $lexerFSM, DistributedTransitionLogicFiniteStateMachineInterface $parserFSM, $isLastChar) {
        $token = $lexerFSM->getData('token');
        $lexeme = $token['lexeme'] . $char;
        if (!preg_match(LexerTokenRegex::TOKEN_STATIC_MEMBER_ACCESS_OPERATOR_REGEX, $lexeme)) {
            $tokenLabel = static::tokenLabel(TokenTypeEnum::TOKEN_STATIC_MEMBER_ACCESS_OPERATOR);
            throw new PointcutParsingException(
                sprintf('Unexpected character "%s" while parsing %s token at position %s.', $char, $tokenLabel, $pos)
            );
        }
        $token['lexeme'] = $lexeme;
        $parserFSM->setData('token', $token);
        $lexerFSM->setState($isLastChar ? LexerEndState::class : TokenStartState::class);
    }

}
