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

use StatusQuo\FSM\DistributedTransitionLogicFiniteStateMachineInterface;
use Norma\AOP\Pointcut\Parsing\State\Lexer\AbstractLexerState;
use Norma\AOP\Pointcut\Parsing\State\Lexer\Regex\LexerTokenRegex;
use Norma\AOP\Pointcut\Parsing\TokenTypeEnum;
use Norma\AOP\Pointcut\Parsing\State\Lexer\TokenStartState;
use Norma\AOP\Pointcut\Parsing\State\Lexer\LexerEndState;

/**
 * Parenthesis start scanning state.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class ScanParenthesisStartState extends AbstractLexerState {
    
    /**
     * {@inheritdoc}
     */
    public function processChar($char, $pos, DistributedTransitionLogicFiniteStateMachineInterface $lexerFSM, DistributedTransitionLogicFiniteStateMachineInterface $parserFSM, $isLastChar) {
        $token = $lexerFSM->getData('token');
        if (preg_match(LexerTokenRegex::TOKEN_PARENTHESIS_CLOSE_REGEX, $char)) {
            $token['type'] = TokenTypeEnum::TOKEN_METHOD_PARENTHESES;
            $token['lexeme'] .= $char;
            $parserFSM->setData('token', $token);
            $lexerFSM->setState($isLastChar ? LexerEndState::class : TokenStartState::class);
        }
        else {
            $token['type'] = TokenTypeEnum::TOKEN_PARENTHESIS_OPEN;
            $parserFSM->setData('token', $token);
            $lexerFSM->setState(TokenStartState::class);
            $lexerFSM->setData('reiterate', [
                'char' => $char,
                'pos' => $pos,
                'is_last_char' => $isLastChar
            ]);
        }
    }
    
}
