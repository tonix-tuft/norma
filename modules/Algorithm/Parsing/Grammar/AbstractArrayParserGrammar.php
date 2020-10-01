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

namespace Norma\Algorithm\Parsing\Grammar;

use Norma\Algorithm\Parsing\Grammar\ParserGrammarInterface;
use Norma\Algorithm\Parsing\Grammar\InvalidParserGrammarException;
use Norma\Data\Structure\Tree\Trie\TrieFactoryInterface;
use Norma\Data\Structure\Tree\Trie\TrieNodeInterface;

/**
 * An abstract base class which implements the interface of a parser's grammar
 * and lets implementors to describe the grammar of a language using a suitably
 * structured array.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
abstract class AbstractArrayParserGrammar implements ParserGrammarInterface {
    
    /**
     * @var TrieFactoryInterface
     */
    protected $trieFactory;
    
    /**
     * Constructs a new grammar.
     * 
     * @param TrieFactoryInterface $trieFactory A trie factory.
     */
    public function __construct(TrieFactoryInterface $trieFactory) {
        $this->trieFactory = $trieFactory;
    }
    
    /**
     * Generates an array representing the rules of the grammar.
     * 
     * The returned array representing the rules of the grammar MUST have the following structure:
     *     - The first dimension of the array MUST define the name of grammar rules using string keys.
     *        Each key defines a grammar rule and can be reused in other rules;

     *     - Each value of the first dimension MUST be an array defining that grammar rule;

     *     - Within the array defining a grammar rule, elements on a same level with an odd dimension
     *        are considered to be ANDed.
     *        Whereas, elements on a same level with an even dimension are considered to be ORed;

     *     - Within the array defining a grammar rule, at any level with any dimension (odd or even),
     *        an element which is an array containing a single array as its element which in turn contains
     *        a single string or integer element either representing a grammar rule or a terminal token identifier,
     *        defines an optional grammar rule or token identifier in that position;
     * 
     * An example is worth a thousand words.
     * 
     * Given the following grammar:
     * 
     * E → T
     * E → ( E + E )
     * T → n
     * 
     * Where `E`, `T` are nonterminal symbols (i.e. grammar rules) and `(`, `+`, `)` and `n` are terminal ones,
     * the grammar can be represented returning the following array when implementing this method:
     * 
     * return [
     *        'E' => [
     *               [
     *                      'T',
     *                      // OR
     *                      [
     *                             1,
     *                             // AND (odd dimension)
     *                             'E',
     *                             // AND (odd dimension)
     *                             2,
     *                             'E',
     *                             // AND (odd dimension)
     *                             3
     *                      ]
     *               ]
     *        ],
     *        'T' => [
     *               4
     *        ]
     * ];
     * 
     * Here 1, 2, 3 and 4 represent the token identifiers `(`, `+`, `)` and `n`, respectively
     * (which MUST be integers in order to distinguish them from grammar rules, which are strings).
     * 
     * @return array The array representing the rules of the grammar.
     */
    abstract protected function generateRulesArray(): array;
    
    /**
     * {@inheritdoc}
     */
    public function getRules(): array {
        $grammarArray = $this->generateRulesArray();
        
        $this->throwExceptionIfEmptyGrammar($grammarArray);
        
        $rules = [];
        foreach ($grammarArray as $nonterminal => $ruleDefinition) {
            if (!isset($rules[$nonterminal])) {
                $this->throwExceptionIfInvalidNonterminal($nonterminal);
                $rules[$nonterminal] = [];
            }
            
            $trie = $this->trieFactory->make();
            $trieNodes = [$trie];
            if (!is_array($ruleDefinition)) {
                $this->throwExceptionIfInvalidSymbol($ruleDefinition);
                $this->setSymbolOnTrieNodes($ruleDefinition, $trieNodes);
            }
            else {
                // Initially the rule is in an AND context.
                $dimension = 1;
                foreach ($ruleDefinition as $rulePart) {
                    // Odd dimensions are ANDed, even dimensions are ORed.
                    $ANDContext = $dimension % 2 == 1;

                    if (!is_array($rulePart)) {
                        // This rule part identifies a symbol.
                        $this->throwExceptionIfInvalidSymbol($rulePart);
                        
                        if ($dimension == 1) {
                            // Symbols at dimension 1 are ANDed and therefore are always appended to each node.
                            $trieNodes = $this->setSymbolOnTrieNodes($rulePart, $trieNodes);
                            
                            // TODO
                        }
                        else {
                            // TODO
                            
                            // Symbol at dimension bigger than 1.
                        }
                    }
                    else {
                        if ($ANDContext && $this->isOptionalRulePart($rulePart)) {
                            // Optional symbol
                            
                            // TODO
                            
                        }
                    }
                }
            }
            
            // TODO
        }
        
        // TODO
        var_dump($rules);
        // exit; // TODO: exit
        
        return [];
    }
    
    /**
     * Tests whether a rule part array is optional.
     * 
     * @param array $rulePart A rule part array.
     * @return bool TRUE if the rule part is optional, FALSE otherwise.
     */
    protected function isOptionalRulePart($rulePart) {
        return count($rulePart) == 1 && is_array(reset($rulePart));
    }
    
    /**
     * Sets the given symbol on the given trie nodes.
     * 
     * @param int|string $symbol A symbol. Either an int identifying a terminal symbol (terminal token identifier)
     *                                             or a string identifying a nonterminal symbol.
     * @param array<TrieNodeInterface> An array of trie nodes.
     * @return array<TrieNodeInterface> An array of trie nodes leaves formed from the given trie nodes.
     */
    protected function setSymbolOnTrieNodes($symbol, $trieNodes) {
        $trieNodesLeaves = [];
        foreach ($trieNodes as $trieNode) {
            /* @var $trieNode TrieNodeInterface */
            
            // Not interested in values. Only the branch/path of the rule is needed.
            $trieNode[[$symbol]] = NULL;
            $trieNodesLeaves[] = $trieNode[[$symbol]];
        }
        return $trieNodesLeaves;
    }
    
    /**
     * Throws an exception if the grammar array is empty, i.e. the grammar has no rules and is therefore pointless.
     * 
     * @param array $grammarArray The grammar array.
     * @return void
     * @throws InvalidParserGrammarException If the given grammar array is empty.
     */
    protected function throwExceptionIfEmptyGrammar(array $grammarArray) {
        if (empty($grammarArray)) {
            throw new InvalidParserGrammarException(
                sprintf(
                    'The rules array of the grammar of type "%s" is empty. The grammar is pointless.',
                    get_class($this)
                )
            );
        }
    }
    
    /**
     * Throws an exception if the given nonterminal symbol is invalid.
     * 
     * The nonterminal symbol is valid if and only if it is a non-empty string.
     * 
     * @param mixed $nonterminal Nonterminal symbol.
     * @return void
     * @throws InvalidParserGrammarException If the given nonterminal symbol is invalid.
     */
    protected function throwExceptionIfInvalidNonterminal($nonterminal) {
        if (!is_string($nonterminal) || empty($nonterminal)) {
            throw new InvalidParserGrammarException(
                sprintf(
                    'The grammar of type "%s" contains an invalid nonterminal symbol. Each nonterminal symbol (grammar rule) must be a non-empty string.',
                    get_class($this)
                )
            );
        }
    }
    
    /**
     * Throws an exception if the given terminal symbol is invalid.
     * 
     * The terminal symbol is valid if and only if it is an integer greater than 0.
     * 
     * @param mixed $terminalSymbol Terminal symbol.
     * @return void
     * @throws InvalidParserGrammarException If the given terminal symbol is invalid.
     */
    protected function throwExceptionIfInvalidTerminal($terminalSymbol) {
        if (!is_int($terminalSymbol) || $terminalSymbol <= 0) {
            throw new InvalidParserGrammarException(
                sprintf(
                    'The grammar of type "%s" contains an invalid terminal symbol. Each terminal symbol (terminal token identifier) must be an integer greater than 0.',
                    get_class($this)
                )
            );
        }
    }
    
    /**
     * Throws an exception if the given symbol is invalid (i.e. it is not a valid nonterminal (string) or terminal token (int)).
     * 
     * @param mixed $symbol A symbol.
     * @return void
     * @throws InvalidParserGrammarException If the given symbol is invalid.
     */
    protected function throwExceptionIfInvalidSymbol($symbol) {
        try {
            $this->throwExceptionIfInvalidNonterminal($symbol);
            return;
        }
        catch (\Exception $ex1) {
            try {
                $this->throwExceptionIfInvalidTerminal($symbol);
                return;
            }
            catch (\Exception $ex2) {
                throw new InvalidParserGrammarException(
                    sprintf(
                        'The grammar of type "%s" contains an invalid symbol. Each symbol must be either a valid terminal or a valid nonterminal symbol.',
                        get_class($this)
                    )
                );
            }
        }
    }

}
