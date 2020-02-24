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
use Norma\AOP\Pointcut\Parsing\State\Lexer\LexerEndState;
use Norma\AOP\Pointcut\Parsing\PointcutParsingException;

/**
 * Pointcut parser lexer's state waiting for next token to start.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class TokenStartState extends AbstractLexerState {
    
    /**
     * {@inheritdoc}
     */
    public function processChar($char, $pos, DistributedTransitionLogicFiniteStateMachineInterface $lexerFSM, DistributedTransitionLogicFiniteStateMachineInterface $parserFSM, $isLastChar) {
        $lexerFSM->setData('token', [
            'type' => NULL,
            'lexeme' => '',
            'pos' => $pos,
        ]);
        
        // Unambiguous single character tokens.
        foreach (static::UNAMBIGUOUS_SINGLE_CHAR_TOKENS_MAP as $tokenType => $tokenRegex) {
            if (preg_match($tokenRegex, $char)) {
                $lexerFSM->setData('token', function($oldData) use ($tokenType, $char) {
                    return [
                        'type' => $tokenType,
                        'lexeme' => $char,
                        'pos' => $oldData['pos']
                    ];
                });
                $token = $lexerFSM->getData('token');
                $parserFSM->setData('token', $token);
                if ($isLastChar) {
                    $lexerFSM->setState(LexerEndState::class);
                }
                return;
            }
        }
        
        // Unambiguous double character tokens.
        foreach (static::UNAMBIGUOUS_DOUBLE_CHAR_TOKENS_MAP as $tokenType => $mappedVal) {
            $regex = $mappedVal['regex'];
            $doubleChar = $char . $mappedVal['second_char'];
            if (preg_match($regex, $doubleChar)) {
                $state = $mappedVal['state'];
                $lexerFSM->setData('token', function($oldData) use ($char, $tokenType) {
                    return [
                        'type' => $tokenType,
                        'lexeme' => $oldData['lexeme'] . $char,
                        'pos' => $oldData['pos']
                    ];
                });
                if ($isLastChar) {
                    if (strlen($doubleChar) > 1) {
                        $tokenLabel = static::tokenLabel($tokenType);
                        throw new PointcutParsingException(sprintf('Incomplete %s token at position %s.', $tokenLabel, $pos));
                    }
                    $token = $lexerFSM->getData('token');
                    $parserFSM->setData('token', $token);
                    $lexerFSM->setState(LexerEndState::class);
                }
                else {
                    $lexerFSM->setState($state);
                }
                return;
            }
        }
        
        // Remaining ambiguous characters.
        $lexerFSM->setState(ScanAmbiguousTokenStartState::class);
        if ($isLastChar) {
            $lexerFSM->setData('reiterate', [
                'char' => $char,
                'pos' => $pos,
                'is_last_char' => $isLastChar
            ]);
        }
        else {
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
