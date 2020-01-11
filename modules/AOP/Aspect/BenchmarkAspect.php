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

namespace Norma\AOP\Aspect;

use Norma\AOP\Aspect\AspectInterface;

/**
 * Builtin benchmark aspect.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class BenchmarkAspect implements AspectInterface {
    
    /*
     * Types of pointcuts in Norma:
     * 
     * 1) Pointcut1: Method execution (comprises constructor if the string `__construct` matches the pattern and there's a `__construct` method defined):
     * 
     *      {MemberAccessModifiers NamespacePattern[`::`|`->`]NamePattern()}
     * 
     *          - MemberAccessModifiers = [`public`|`protected`|`private`|`*`]
     *          - NamespacePattern = [`NamespaceIdentifierPattern`|`NamespaceIdentifierPattern+`] (for `+`, see: https://stackoverflow.com/questions/29397872/difference-between-and-when-matching-a-class-in-aspectj)
     *          - NamespaceIdentifierPattern = [`GlobalNamespaceClassName`|`\GlobalNamespaceClassName`|`NamespaceName\ClassName`|`*`|`**`]
     *          - NamePattern = [`^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$`|`*`]
     * 
     * 2) Pointcut2: Property access (read/write):
     *      
     *      {AccessOperation MemberAccessModifiers NamespacePattern[`::`|`->`]NamePattern}
     *          
     *          - AccessOperation = [`read`|`write`|`*`]
     *  
     * 3) Pointcut3: Annotated method execution or property access:
     *      
     *      {[`Method`|`Property AccessOperation`] @NamespaceIdentifierPattern}
     * 
     *          - Method = method
     *          - Property = property
     * 
     * 4) Pointcut4: Initialization (constructor execution, even if the class does not define a `__construct` constructor method):
     *      
     *      {new NamespacePattern}
     * 
     * 5) Pointcut5: Static initialization (class loaded into memory by PHP for the first time):
     *      
     *      {static NamespacePattern}
     * 
     * Pointcuts can be grouped with logical operators:
     * 
     *      !Pointcut
     *      Pointcut && Pointcut
     *      Pointcut || Pointcut
     *      (Pointcut && (Pointcut || Pointcut)) || (Pointcut && Pointcut) || Pointcut
     * 
     *          - Pointcut = [`Pointcut1`|`Pointcut2`|`Pointcut3`|`Pointcut4`|`Pointcut5`]
     *
     * A simple pointcut definition (`Pointcut1`, `Pointcut2`, `Pointcut3`, `Pointcut4`, `Pointcut5`) MUST be encapsulated into curly braces (`{}`),
     * otherwise the AOP pointcut parser component will throw an exception. This is done to force using curly braces with simple poincut definitions
     * to simplify readability.
     * The only exception are named pointcuts: a pointcut already defined which is referenced by its name is not required to be encapsulated into
     * curly braces.
     * 
     * Examples:
     * 
     * return '{public TestClass->*()}'; // 1) Instance public method execution
     * return '{public TestClass::*()}'; // 1.1) Static public method execution
     * return '{* Some*\NamespaceName\**TestClass+->*MethodName*()}'; // 1.2) ...
     * return '{* Some*\NamespaceName\**\TestClass+->method*Name*()}'; // 1.3) ...
     * 
     * return '{* public TestClass->property}'; // 2) Property access (read/write)
     * return '{read public TestClass+->property}'; // 2.1) Property access (read)
     * return '{read public TestClass::property}'; // 2.2) Static property access (read)
     * return '{write * TestClass->property}'; // 2.3) Property access (write)
     * return '{write * TestClass::property}'; // 2.4) Static property access (write)
     * 
     * return '{method @Annotation}'; // 3) Execution of method annotated with `@Annotation`.
     * return '{property * @Annotation}'; // 3.1) Access of property annotated with `@Annotation` (read/write).
     * return '{property read @Annotation}'; // 3.2) Access of property annotated with `@Annotation` (read).
     * return '{property write @Annotation}'; // 3.3) Access of property annotated with `@Annotation` (write).
     * 
     * return '{new Some*\NamespaceName\**Test+}'; // 4) Initialization (constructor execution, even if the class does not define a `__construct` method).
     * 
     * return '{static Some*\NamespaceName\**Test+}'; // 5) Static initialization (class loaded into memory by PHP for the first time, similar to the static block construct of Java).
     */
    public function pointcut pointcutName1() {
        return '{* *+->*s()}';
    }
    
    /**
     * Pointcut.
     */
    public function pointcut pointcutName2() {
        return '{* public TestClass->property}';
    }
    
    /**
     * Pointcut.
     */
    public function pointcut pointcutName3() {
        return '{method @Annotation}';
    }
    
    /**
     * Pointcut made of other pointcuts.
     */
    public function pointcut complexPointcut() {
        // Curly braces are not required for named pointcuts.
        return '({pointcutName1} && pointcutName2) || (pointcutName3 && {pointcutName1}) || pointcutName2';
    }
    
    /**
     * Before advice.
     */
    public function before pointcutName1 advice(MethodExecutionJoinPointInterface $jointPoint) {
        // ...
    }

    /**
     * After advice.
     */
    public function after pointcutName2 advice(PropertyAccessJoinPointInterface $jointPoint) {
        // ...
    }
    
    /**
     * Around advice.
     */
    public function around complexPointcut advice(JoinPointInterface $jointPoint) {
        // ...
    }
    
}