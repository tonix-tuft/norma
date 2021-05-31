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

namespace Norma\AOP\Aspect;

use Norma\AOP\Aspect\AspectInterface;

/**
 * Builtin Law of Demeter checker aspect.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class LawOfDemeterCheckerAspect implements AspectInterface {
    
    /**
     * The Law of Demeter states that an object can only send messages to:
     * 
     *      - Itself (call its own methods or access its own properties);
     *      - Its arguments (call the method of an argument or access an argument's property);
     *      - Its instance variables (call the method of a member field (property) or access its property);
     *      - A locally constructed object (call its method or access its property);
     *      - A returned object from a message sent to itself (call a method or access a property of an object returned from a method of itself);
     * 
     * In this context,  join points are method invocations and there's the concept of an enclosing (parent) join point.
     * The enclosing join point is the parent in the control flow.
     */
    
}
