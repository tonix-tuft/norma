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

namespace Norma\AOP\Aspect;

use Norma\AOP\Aspect\AspectInterface;

/**
 * Builtin benchmark aspect.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class BenchmarkAspect implements AspectInterface {
    
    /**
     * Pointcut.
     */
    public function pointcut pointcutName1() {
        
        /*
         * Types of pointcuts in Norma:
         * 
         * 1) Pointcut1: Method execution (comprises constructor if the string `__construct` matches the pattern and there's a `__construct` method defined):
         * 
         *      MemberModifiers NamespacePattern[::|->]NamePattern()
         * 
         *          - MemberModifiers = [public|protected|private|*]
         *          - NamespaceIdentifierPattern = [GlobalNamespaceClassName|\GlobalNamespaceClassName|NamespaceName\ClassName|*|**]
         *          - NamespacePattern = [NamespaceIdentifierPattern|NamespaceIdentifierPattern+]
         *          - NamePattern = [MethodNamePattern|PropertyNamePattern|*]
         * 
         * 2) Pointcut2: Property access (read/write):
         *      
         *      AccessOperation MemberModifiers NamespacePattern[::|->]NamePattern
         *          
         *          - AccessOperation = [read|write|]
         *  
         * 3) Pointcut3: Annotated method or property or both:
         *      
         *      ([Method|AccessOperation Property] @NamespaceIdentifierPattern)
         * 
         *          - Method = method
         *          - Property = property
         * 
         * 4) Pointcut4: Initialization (constructor execution):
         *      
         *      new NamespacePattern
         * 
         * 5) Pointcut5: Static initialization (class loaded into memory by PHP for the first time):
         *      
         *      static NamespacePattern
         * 
         * Pointcuts can be grouped with logical operators:
         * 
         *      !Pointcut
         *      Pointcut && Pointcut
         *      Pointcut || Pointcut
         *      (Pointcut && (Pointcut || Pointcut)) || (Pointcut && Pointcut) || Pointcut
         * 
         *          - Pointcut = [!Pointcut1|!Pointcut2|!Pointcut3|!Pointcut4|!Pointcut5]
         *
         */
        
        return '(public TestClass->*())'; // 1) Instance public method execution
        return '(public TestClass::*())'; // 1.1) Static public method execution
        return '(* Some*\NamespaceName\**TestClass+->*MethodName*())'; // 1.2) ...
        return '(* Some*\NamespaceName\**\TestClass+->*MethodName*())'; // 1.3) ...
        
        return '(public TestClass->property)'; // 2) Property access (read/write)
        return '(read public TestClass->property)'; // 2.1) Property access (read)
        return '(read public TestClass::$property)'; // 2.2) Static property access (read)
        return '(write TestClass->property)'; // 2.3) Property access (write)
        return '(write TestClass::property)'; // 2.4) Static property access (write)
        
        return '(@Annotation)'; // 3) Execution of annotated method or access of annotated property (read/write).
        return '(method @Annotation)'; // 3.1) Execution of annotated method.
        return '(property @NormaAOPCacheable)'; // 3.2) Access of annotated property (read/write).
        return '(read property @NormaAOPCacheable)'; // 3.2) Access of annotated property (read).
        return '(write property @NormaAOPCacheable)'; // 3.2) Access of annotated property (write).
        
        return '(new Some*\NamespaceName\**Test+)'; // 4) Initialization (constructor execution, even if the class does not define a `__construct` method).
        
        return '(static Some*\NamespaceName\**Test+)'; // 5) Static initialization (class loaded into memory by PHP for the first time).   
        
    }
    
    /**
     * Pointcut.
     */
    public function pointcut pointcutName3() {
        return '';
    }
    
    /**
     * Pointcut made of other pointcuts.
     */
    public function pointcut complexPointcut() {
        return '(pointcutName1 && pointcutName2) || pointcutName3';
    }
    
    /**
     * Before advice.
     */
    public function before pointcutName1 beforeAdviceName(MethodExecutionJoinPointInterface $jointPoint) {
        
    }

    /**
     * After advice.
     */    
    public function after pointcutName2 afterAdviceName(JoinPointInterface $jointPoint) {
        
    }
    
    /**
     * Around advice.
     */
    public function around complexPointcut aroundAdviceName(JoinPointInterface $jointPoint) {
        
    }
    
}