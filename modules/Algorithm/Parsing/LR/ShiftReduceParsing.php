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

namespace Norma\Algorithm\Parsing\LR;

use Norma\Algorithm\Parsing\Grammar\ParserGrammarInterface;
use Norma\Algorithm\Parsing\ASTInterface;
use Norma\Algorithm\Parsing\ParsingInterface;
use Norma\Algorithm\Parsing\ParseTreeInterface;

/**
 * Shift-reduce parsing algorithm implementation for implementing LR parsers.
 * 
 * @see https://en.wikipedia.org/wiki/Shift-reduce_parser
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class ShiftReduceParsing implements ParsingInterface {
    
    /**
     * @var \SplStack
     */
    protected $stack;
    
    /**
     * @var ParserGrammarInterface
     */
    protected $grammar;
    
    /**
     * @var array
     */
    protected $grammarRules;
    
    /**
     * Constructs a new shift-reduce parsing algorithm.
     * 
     * @param ParserGrammarInterface $grammar The parser grammar to use for the parsing.
     */
    public function __construct(ParserGrammarInterface $grammar) {
        $this->stack = new \SplStack();
        $this->grammar = $grammar;
        $this->grammarRules = $this->grammar->getRules();
    }
    
    /**
     * {@inheritdoc}
     */
    public function step(int $lookaheadTerminalTokenIdentifier, string $lookaheadTerminalTokenLexeme, int $lookaheadTerminalTokenPos) {
        // TODO
        $args = func_get_args();
        $args[] = $this->grammar->terminalTokenIdentifierToString($lookaheadTerminalTokenIdentifier);
        var_dump($args);
        
        
    }

    /**
     * {@inheritdoc}
     */
    public function generateAST(): ASTInterface {
        // TODO
    }
    
    /**
     * {@inheritdoc}
     */
    public function generateParseTree(): ParseTreeInterface {
        // TODO
    }
    
}
