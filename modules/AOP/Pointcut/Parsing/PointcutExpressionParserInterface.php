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

namespace Norma\AOP\Pointcut\Parsing;

use Norma\AOP\Pointcut\PointcutInterface;
use Norma\AOP\Parsing\AspectMetadataParsingException;

/**
 * The interface of a pointcut parser.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface PointcutExpressionParserInterface {
    
    /**
     * Parses a pointcut.
     * 
     * @param string $aspectName The name of the aspect.
     * @param array $explodedName An array of exploded parts which form the name of the pointcut method.
     * @param string $originalName The original name of the pointcut method.
     * @param string $pointcutExpression The pointcut expression to parse.
     * @return PointcutInterface The parsed pointcut.
     * @throws AspectMetadataParsingException If the parsing process fails due to invalid syntax or for some other reason.
     */
    public function parse($aspectName, $explodedName, $originalName, $pointcutExpression): PointcutInterface;
    
}
