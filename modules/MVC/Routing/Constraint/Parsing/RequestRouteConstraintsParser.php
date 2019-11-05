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

namespace Norma\MVC\Routing\Constraint\Parsing;

use Norma\MVC\Routing\Constraint\RequestRouteConstraintFactoryInterface;
use Norma\MVC\Routing\Constraint\RequestRouteConstraintsFactoryInterface;
use Norma\MVC\Routing\Constraint\RequestRouteConstraintsInterface;

/**
 * The implementation of a request route constraints parser.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class RequestRouteConstraintsParser implements RequestRouteConstraintsParserInterface {

    /**
     * @var RequestRouteConstraintFactoryInterface
     */
    protected $requestRouteConstraintFactory;
    
    /**
     * @var RequestRouteConstraintsFactoryInterface
     */
    protected $requestRouteConstraintsFactory;
    
    /**
     * Constructs a new request route constraints parser.
     * 
     * @param RequestRouteConstraintFactoryInterface $requestRouteConstraintFactory A request route constraint factory to use to create a new constraint.
     * @param RequestRouteConstraintsFactoryInterface $requestRouteConstraintsFactory A request route constraints factory to use to construct the request route constraints.
     */
    public function __construct(RequestRouteConstraintFactoryInterface $requestRouteConstraintFactory, RequestRouteConstraintsFactoryInterface $requestRouteConstraintsFactory) {
        $this->requestRouteConstraintFactory = $requestRouteConstraintFactory;
        $this->requestRouteConstraintsFactory = $requestRouteConstraintsFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function parse($constraints): RequestRouteConstraintsInterface {
        $requestRouteConstraints = $this->requestRouteConstraintsFactory->make();
        if (!is_null($constraints)) {
            foreach ($constraints as $constraintName => $constraintData) {
                $constraint = $this->requestRouteConstraintFactory->makeConstraint($constraintName, $constraintData);
                $requestRouteConstraints->add($constraint);
            }
        }
        return $requestRouteConstraints;
    }

}
