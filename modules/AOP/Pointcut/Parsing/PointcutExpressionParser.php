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

namespace Norma\AOP\Pointcut\Parsing;

use Norma\AOP\Pointcut\Parsing\PointcutExpressionParserInterface;
use Norma\AOP\Pointcut\PointcutInterface;
use Norma\AOP\Pointcut\Parsing\PointcutParsingException;
use Norma\State\FSM\FiniteStateMachineFactoryInterface;
use Norma\State\FSM\FiniteStateMachineInterface;
use Norma\State\FSM\StateInterface;
use Norma\State\FSM\DistributedTransitionLogicFiniteStateMachineInterface;

/**
 * The implementation of a pointcut parser.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class PointcutExpressionParser implements PointcutExpressionParserInterface {
    
    /**
     * @var FiniteStateMachineInterface
     */
    protected $lexer;
    
    /**
     * @var FiniteStateMachineInterface
     */
    protected $parser;
    
    /**
     * Constructs a new parser.
     * 
     * @param FiniteStateMachineFactoryInterface $FSMFactory A finite-state machine factory.
     */
    public function __construct(FiniteStateMachineFactoryInterface $FSMFactory) {
        $this->lexer = $FSMFactory->make();
        $this->parser = $FSMFactory->make();
    }
    
    /**
     * {@inheritdoc}
     */
    public function parse($explodedName, $originalName, $pointcutExpression): PointcutInterface {
        $pointcutName = $explodedName[1] ?? NULL;
        if (empty($pointcutName)) {
            throw new PointcutParsingException(sprintf('Pointcut name token for pointcut method "%s" is empty.', $originalName));
        }
        
        $length = strlen($pointcutExpression);
        for ($i = 0; $i < $length; $i++) {
            $char = $pointcutExpression[$i];
            $this->FSM->process($char);
        }
    }

}
