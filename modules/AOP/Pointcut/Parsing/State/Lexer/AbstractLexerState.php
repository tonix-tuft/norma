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

use Norma\State\FSM\StateInterface;
use Norma\State\FSM\DistributedTransitionLogicFiniteStateMachineInterface;
use Norma\AOP\Pointcut\Parsing\PointcutParsingException;
use Norma\Core\Utils\FrameworkArrayUtilsTrait;

/**
 * An abstract pointcut parser lexer's state.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
abstract class AbstractLexerState implements StateInterface {
    
    use FrameworkArrayUtilsTrait;
    
    const REQUIRED_INPUT_KEYS = ['char', 'pos', 'parser_fsm'];
    
    /**
     * {@inheritdoc}
     */
    public function process(DistributedTransitionLogicFiniteStateMachineInterface $FSM, $input = NULL) {
        if (
            !is_array($input)
        ) {
            throw new PointcutParsingException('The lexer received no input.');
        }
        
        $emptyKey = $this->atLeastOneArrayKeyIsEmpty($input, static::INPUT_REQUIRED_KEYS);
        if (
            $emptyKey !== FALSE
        ) {
            throw new PointcutParsingException(sprintf('Required lexer input "%s" key is missing.', $emptyKey));
        }
        
        $char = $input['char'];
        $pos = $input['pos'];
        $parserFSM = $input['parser_fsm'];
        $this->processChar($FSM, $char, $pos, $parserFSM);
    }
    
    /**
     * Processes a single char.
     * 
     * @param DistributedTransitionLogicFiniteStateMachineInterface $FSM The lexer's state machine.
     * @param string $char A single char to process.
     * @param int $pos The position of the char within the original input source.
     * @param DistributedTransitionLogicFiniteStateMachineInterface $parserFSM The parser's state machine.
     */
    abstract public function processChar(DistributedTransitionLogicFiniteStateMachineInterface $FSM, $char, $pos, DistributedTransitionLogicFiniteStateMachineInterface $parserFSM);
    
}
