<?php

/*
 * Copyright (c) 2020 Anton Bagdatyev (Tonix-Tuft)
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

namespace Norma\Algorithm\Parsing\Grammar;

/**
 * The interface of a parser's grammar.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface ParserGrammarInterface {
    
    /**
     * Obtains the textual representation of the given terminal token identifier.
     * 
     * @param int $terminalTokenIdentifier The terminal token identifier of the grammar.
     * @return string The string representing the given terminal token identifier.
     */
    public function terminalTokenIdentifierToString($terminalTokenIdentifier): string;
    
    /**
     * Get the rules of the grammar.
     * 
     * The returned array MUST obey to the following structure:
     * 
     *     - Each key of the array MUST be of type `string` and represent the name of a rule of the grammar (nonterminal symbol);
     * 
     *     - Each value of the array MUST be in turn an array where each element is in turn another array containing the rule's symbols,
     *        which MUST be of type `string` for nonterminal ones and of type `int` for terminal tokens (terminal token identifier);
     * 
     * E.g., for the following grammar:
     * 
     * E → T
     * E → ( E + E )
     * T → n
     * 
     * This method MUST return a rules array having the following shape:
     * 
     * return [
     *        'E' => [
     *               ['T'],
     *               [1, 'E', '2', 'E', 3] // 1 -> `(`, 2 -> `+`, 3 -> `)` (terminal token identifiers)
     *        ],
     *        'T' => [
     *               [4] // 4 -> `n` (terminal token identifier)
     *        ]
     * ];
     * 
     * Here 1, 2, 3 and 4 represent the token identifiers `(`, `+`, `)` and `n`, respectively
     * (which MUST be integers in order to distinguish them from grammar rules (nonterminal symbols), which are of type `string`).
     * 
     * @return array The rules of the grammar.
     */
    public function getRules(): array;
    
    /**
     * Returns the root rule, i.e. the nonterminal symbol to use as the root symbol to derive the augmented grammar
     * given the grammar returned by {@link Norma\Algorithm\Parsing\Grammar\ParserGrammarInterface::getRules()}.
     * 
     * This method MUST return the root rule which is nothing more than one of the string keys of the rules array returned by
     * {@link Norma\Algorithm\Parsing\Grammar\ParserGrammarInterface::getRules()}.
     * This root rule is the topmost nonterminal symbol to use to derive the augmented grammar.
     * 
     * An example is worth a thousand words.
     * 
     * For the following grammar (the same grammar as in the example of {@link Norma\Algorithm\Parsing\Grammar\ParserGrammarInterface::getRules()}):
     * 
     * E → T
     * E → ( E + E )
     * T → n
     * 
     * This method MUST return 'E', as 'E' is the root nonterminal rule to use as the base to derive the whole augmented grammar
     * which can then be used by a parser.
     * 
     * @return string The root nonterminal symbol (grammar rule).
     */
    public function getRootRule(): string;
    
}
