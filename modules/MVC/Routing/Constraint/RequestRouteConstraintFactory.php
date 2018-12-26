<?php

/*
 * Copyright (c) 2018 Anton Bagdatyev
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

namespace Norma\MVC\Routing\Constraint;

use Norma\MVC\Routing\RoutingException;

/**
 * The implementation of a request route constraint factory.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class RequestRouteConstraintFactory implements RequestRouteConstraintFactoryInterface {

    /**
     * {@inheritdoc}
     */
    public function makeConstraint($constraintName, $constraintData) {
        switch ($constraintName) {
            case 'method':
                return new RequestRouteRequestMethodConstraint($constraintData);
            case 'content_type':
                return new RequestRouteContentTypeConstraint($constraintData);
            default:
                throw new RoutingException(sprintf('The request routing constraint with name %1$s is undefined.', $constraintName));
        }
    }

}
