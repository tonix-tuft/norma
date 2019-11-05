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

namespace Norma\Algorithm\Parsing;

use Norma\Algorithm\Parsing\ParsingException;
use Norma\Algorithm\Parsing\ASTInterface;
use Norma\Algorithm\Parsing\ParseTreeInterface;

/**
 * The interface of a parsing algorithm.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface ParsingInterface {
    
    /**
     * Step through the parsing algorithm with the given lookahead terminal token information.
     * 
     * @param int $lookaheadTerminalTokenIdentifier The identifier of the lookahead terminal token.
     * @param string $lookaheadTerminalTokenLexeme The lookahead terminal token lexeme.
     * @param int $lookaheadTerminalTokenPos The position of the lookahead terminal token.
     * @return void
     * @throws ParsingException If a parsing error occurs.
     */
    public function step(int $lookaheadTerminalTokenIdentifier, string $lookaheadTerminalTokenLexeme, int $lookaheadTerminalTokenPos);
    
    /**
     * Generate the AST (Abstract Syntax Tree) of the so far parsed input.
     * 
     * @return ASTInterface The AST.
     * @throws ParsingException If the AST cannot be generated for some reason.
     */
    public function generateAST(): ASTInterface;
    
    /**
     * Generate the parse tree of the so far parsed input.
     * 
     * @return ParseTreeInterface The parse tree.
     * @throws ParsingException If the parse tree cannot be generated for some reason.
     */
    public function generateParseTree(): ParseTreeInterface;
    
}
