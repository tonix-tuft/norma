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

namespace Norma\AOP\Pointcut\Parsing;

use Norma\AOP\Pointcut\Parsing\PointcutExpressionParserInterface;
use Norma\AOP\Pointcut\Parsing\PointcutParsingException;
use Norma\AOP\Pointcut\Parsing\State\Lexer\TokenStartState;
use Norma\AOP\Pointcut\Parsing\State\Parser\NewTokenToParseState;
use Norma\AOP\Pointcut\Parsing\TokenTypeEnum;
use Norma\AOP\Pointcut\PointcutInterface;
use Norma\AOP\Pointcut\Factory\PointcutFactoryInterface;
use Norma\Algorithm\Parsing\ParsingInterface;
use Norma\Algorithm\Parsing\ASTInterface;
use StatusQuo\FSM\Factory\FiniteStateMachineFactoryInterface;
use StatusQuo\FSM\FiniteStateMachineInterface;

/**
 * The implementation of a pointcut parser.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
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
     * @var string
     */
    protected $currentPointcutName;
    
    /**
     * @var string
     */
    protected $aspectName;
    
    /**
     * @var ParsingInterface
     */
    protected $parsingAlgorithm;
    
    /**
     * @var PointcutFactoryInterface
     */
    protected $pointcutFactory;
    
    /**
     * Constructs a new parser.
     * 
     * @param FiniteStateMachineFactoryInterface $FSMFactory A finite-state machine factory.
     * @param ParsingInterface $parsingAlgorithm A parsing algorithm.
     * @param PointcutFactoryInterface $pointcutFactory A pointcut factory.
     */
    public function __construct(FiniteStateMachineFactoryInterface $FSMFactory, ParsingInterface $parsingAlgorithm, PointcutFactoryInterface $pointcutFactory) {
        $this->lexer = $FSMFactory->make(TokenStartState::class);
        $this->parser = $FSMFactory->make(NewTokenToParseState::class);
        $this->parsingAlgorithm = $parsingAlgorithm;
        $this->pointcutFactory = $pointcutFactory;
        
        $this->setUp();
    }
    
    /**
     * Set up the parser.
     * 
     * @return void
     */
    protected function setUp() {
        $lexer = $this->lexer;
        $parser = $this->parser;
        $that = $this;
        
        $lexer->onData('reiterate', function($data) use ($that, $parser) {
            $mergedData = array_merge($data, [
                'parser' => $parser
            ]);
            $that->processLexerData($mergedData);
        });
        
        $parser->onData('token', function($token) use ($lexer, $that) {
            if (!empty($token['type']) && $token['type'] !== TokenTypeEnum::TOKEN_WHITESPACE) {
                $lexer->setData('last_non_whitespace_token_before_current', $token);
                $that->processToken($token);
            }
        });
    }
    
    /**
     * Processes lexer's data.
     * 
     * @param array $data Data to pass to lexer.
     * @return void
     * @throws PointcutParsingException If the lexer fails to process the given data.
     */
    protected function processLexerData($data) {
        try {
            $this->lexer->process($data);
        }
        catch (PointcutParsingException $ex) {
            throw new PointcutParsingException(sprintf('Lexer error for pointcut "%s" of aspect "%s": %s', $this->currentPointcutName, $this->currentAspectName, $ex->getMessage()));
        }
    }
    
    /**
     * Processes a token.
     * 
     * @param array $token The token to pass to the parser.
     * @return void
     * @throws PointcutParsingException If the parser fails to process the given token.
     */
    protected function processToken($token) {
        $this->parsingAlgorithm->step($token['type'], $token['lexeme'], $token['pos']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function parse($aspectName, $explodedName, $originalName, $pointcutExpression): PointcutInterface {
        $pointcutName = $explodedName[1] ?? NULL;
        if (empty($pointcutName)) {
            throw new PointcutParsingException(sprintf('Pointcut name token for pointcut method "%s" is empty.', $originalName));
        }
        
        $this->currentPointcutName = $pointcutName;
        $this->currentAspectName = $aspectName;
        
        $length = strlen($pointcutExpression);
        for ($i = 0; $i < $length; $i++) {
            $char = $pointcutExpression[$i];
            $this->processLexerData([
                'char' => $char,
                'pos' => $i,
                'parser' => $this->parser,
                'is_last_char' => $i == $length - 1
            ]);
        }
        $this->lexer->setState(TokenStartState::class);
        $this->parser->setState(NewTokenToParseState::class);
        
        /* @var $AST ASTInterface */
        $AST = $this->parsingAlgorithm->generateAST();
        
        /* @var $pointcut PointcutInterface */
        $pointcut = $this->pointcutFactory->makePointcutFromAST($AST);
        
        return $pointcut;
    }

}
