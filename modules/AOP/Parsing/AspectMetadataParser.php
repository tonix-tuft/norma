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

namespace Norma\AOP\Parsing;

use Norma\AOP\Parsing\AspectMetadataParserInterface;
use Norma\AOP\Registration\AspectMetadataInterface;
use Norma\AOP\Registration\AspectMetadataFactoryInterface;
use Norma\AOP\Management\AspectManagerInterface;
use Norma\AOP\Parsing\AspectMetadataParsingException;
use Norma\AOP\Parsing\AspectParserTokenEnum;
use Norma\AOP\Pointcut\Parsing\PointcutExpressionParserInterface;
use Norma\AOP\Advice\Parsing\AdviceParserInterface;
use Norma\AOP\Pointcut\PointcutInterface;

/**
 * Aspect metadata parser implementation.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class AspectMetadataParser implements AspectMetadataParserInterface {
    
    /**
     * @var array
     */
    protected $parsedPointcuts;
    
    /**
     * @var array
     */
    protected $parsedAdvices;
    
    /**
     * @var array
     */
    protected $pointcutAdvicesMap;
    
    /**
     * @var AspectMetadataFactoryInterface
     */
    protected $metadataFactory;
    
    /**
     * @var AspectManagerInterface
     */
    protected $aspectManager;
    
    /**
     * @var PointcutExpressionParserInterface 
     */
    protected $pointcutExpressionParser;
    
    /**
     * @var AdviceParserInterface
     */
    protected $adviceParser;
    
    /**
     * Constructs a new parser.
     * 
     * @param AspectMetadataFactoryInterface $metadataFactory An aspect metadata factory.
     * @param AspectManagerInterface $aspectManager An aspect manager.
     * @param PointcutExpressionParserInterface $pointcutExpressionParser A pointcut parser.
     * @param AdviceParserInterface $adviceParser An advice parser.
     */
    public function __construct(AspectMetadataFactoryInterface $metadataFactory, AspectManagerInterface $aspectManager,
            PointcutExpressionParserInterface $pointcutExpressionParser, AdviceParserInterface $adviceParser
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->aspectManager = $aspectManager;
        $this->pointcutExpressionParser = $pointcutExpressionParser;
        $this->adviceParser = $adviceParser;
    }
   
    /**
     * {@inheritdoc}
     */
    public function parse($aspect): AspectMetadataInterface {
        $reflectionAspectClass = new \ReflectionClass($aspect);
        
        $aspectComponent = $reflectionAspectClass->getName();
        $methods = $reflectionAspectClass->getMethods();
        
        $this->parseMethods($aspectComponent, $methods);
        
        return $this->metadataFactory->make($aspect, $this->parsedPointcuts, $this->pointcutAdvicesMap);
    }
    
    /**
     * Parses the methods of an aspect extrapolating pointcuts and advices
     * and populating the internal aspects metadata structures.
     * 
     * @param string The aspect component.
     * @param array<\ReflectionMethod> An array of all of the methods of the reflected aspect.
     * @return void
     */
    protected function parseMethods($aspectComponent, $methods) {
        $this->parsedPointcuts = [];
        $this->parsedAdvices = [];
        $this->pointcutAdvicesMap = [];
        foreach ($methods as $method) {
            /* @var $method \ReflectionMethod */
            $name = $method->getName();
            $explodedName = explode(AspectParserTokenEnum::TOKEN_METHOD_NAME_DELIMITER, $name);
            if (count($explodedName) > 1) {
                $this->parseInterestingMethod($aspectComponent, $method, $explodedName);
            }
        }
        
        // TODO
        
        if (empty($this->parsedPointcuts) || empty($this->pointcutAdvicesMap)) {
            throw new AspectMetadataParsingException(sprintf('Pointless aspect of type "%s". The aspect either doesn\'t define any pointcut or doesn\'t define any advice.', $aspectComponent));
        }
    }
    
    /**
     * Parses an interesting method which can potentially refer to a pointcut or an advice
     * populating the respective internal aspects metadata structure.
     * 
     * @param string $aspectComponent The aspect component.
     * @param \ReflectionMethod $method The reflected method.
     * @param array $explodedName An array of exploded parts which form the name of the interesting method.
     * @return void
     * @throws AspectMetadataParsingException If the parsing process fails due to invalid syntax or for some other reason.
     */
    protected function parseInterestingMethod($aspectComponent, \ReflectionMethod $method, $explodedName) {
        $firstMethodNamePart = $explodedName[0];
        $explodedPartsCount = count($explodedName);
        if ($firstMethodNamePart === AspectParserTokenEnum::TOKEN_POINTCUT && $explodedPartsCount == 2) {
            $this->parsedPointcuts[] = $this->parsePointcut($aspectComponent, $method, $explodedName);
        }
        else if ($explodedPartsCount == 3) {
            if ($firstMethodNamePart === AspectParserTokenEnum::TOKEN_ADVICE_BEFORE) {
                $this->parsedAdvices[] = $this->adviceParser->parseBeforeAdvice($aspectComponent, $method, $explodedName);
            }
            else if ($firstMethodNamePart === AspectParserTokenEnum::TOKEN_ADVICE_AFTER) {
                $this->parsedAdvices[] = $this->adviceParser->parseAfterAdvice($aspectComponent, $method, $explodedName);
            }
            else if ($firstMethodNamePart === AspectParserTokenEnum::TOKEN_ADVICE_AROUND) {
                $this->parsedAdvices[] = $this->adviceParser->parseAroundAdvice($aspectComponent, $method, $explodedName);
            }   
        }
    }
    
    /**
     * Parses a pointcut.
     * 
     * @param string $aspectComponent The aspect component.
     * @param \ReflectionMethod $method The reflected pointcut method.
     * @param array $explodedName An array of exploded parts which form the name of the pointcut method.
     * @return PointcutInterface The parsed pointcut.
     * @throws AspectMetadataParsingException If the parsing process fails due to invalid syntax or for some other reason.
     */
    protected function parsePointcut($aspectComponent, \ReflectionMethod $method, $explodedName): PointcutInterface {
        $aspect = $this->aspectManager->getAspect($aspectComponent);
        $pointcutMethodName = $method->getName();
        $pointcutExpression = $this->aspectManager->callAspectMethod($aspect, $pointcutMethodName);
        return $this->pointcutExpressionParser->parse($explodedName, $pointcutMethodName, $pointcutExpression);
    }

}
