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

namespace Norma\AOP\Management;

use Norma\AOP\Management\AspectManagerInterface;
use Norma\DI\AbstractDependencyInjectionContainer;

/**
 * An implementation of an aspect manager.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class AspectManager implements AspectManagerInterface {
    
    /**
     * @var AbstractDependencyInjectionContainer
     */
    protected $container;
    
    /**
     * @var array
     */
    protected $aspects;
    
    /**
     * Constructs a new aspect manager.
     * 
     * @param AbstractDependencyInjectionContainer $container A DI container.
     */
    public function __construct(AbstractDependencyInjectionContainer $container) {
        $this->aspects = [];
        $this->container = $container;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAspect($aspect) {
        $aspectInstance = $this->container->get($aspect);
        if (!isset($this->aspects[$aspect]) && !$this->container->has($aspect)) {
            $this->aspects[$aspect] = TRUE;
            $this->container->addConfig([
                'norma' => [
                    'framework' => [
                        'aop' => [
                            'aspects' => [
                                $aspect => $aspectInstance
                            ]
                        ]
                    ]
                ]
            ]);
        }
        return $aspectInstance;
    }
    
    /**
     * {@inheritdoc}
     */
    public function callAspectMethod($aspect, $method, $paramsMapInjection = []) {
        return $this->container->call([$aspect, $method], $paramsMapInjection);
    }

}
