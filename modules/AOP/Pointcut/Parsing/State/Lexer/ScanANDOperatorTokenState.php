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

namespace Norma\AOP\Pointcut\Parsing\State\Lexer;

use Norma\AOP\Pointcut\Parsing\State\Lexer\AbstractLexerState;
use Norma\State\FSM\DistributedTransitionLogicFiniteStateMachineInterface;
use Norma\AOP\Pointcut\Parsing\State\Lexer\Regex\LexerTokenRegex;
use Norma\AOP\Pointcut\Parsing\TokenTypeEnum;
use Norma\AOP\Pointcut\Parsing\State\Lexer\TokenStartState;

/**
 * AND operator token scanning state.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class ScanANDOperatorTokenState extends AbstractLexerState {
    
    /**
     * {@inheritdoc}
     */
    public function processChar($char, $pos, DistributedTransitionLogicFiniteStateMachineInterface $lexerFSM, DistributedTransitionLogicFiniteStateMachineInterface $parserFSM, $isLastChar) {
        $token = $lexerFSM->getData('token');
        $lexeme = $token['lexeme'] . $char;
        if (!preg_match(LexerTokenRegex::TOKEN_AND_OPERATOR_REGEX, $lexeme)) {
            $tokenLabel = $this->tokenLabel(TokenTypeEnum::TOKEN_AND_OPERATOR);
            throw new Norma\AOP\Pointcut\Parsing\PointcutParsingException(
                sprintf('Unexpected character "%s" while parsing %s token at position %s.', $char, $tokenLabel, $pos)
            );
        }
        $token['lexeme'] = $lexeme;
        $parserFSM->setData('token', $token);
        $lexerFSM->setState($isLastChar ? LexerEndState::class : TokenStartState::class);
    }

}
