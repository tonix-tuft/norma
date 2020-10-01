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

namespace Norma\State\FSM;

use StatusQuo\FSM\State\StateInterface;
use Norma\DI\AbstractDependencyInjectionContainer;
use StatusQuo\FSM\State\Factory\StateFactoryInterface;

/**
 * The implementation of a finite-state machine's state factory in the context of the Norma framework.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class DIContainerAwareStateFactory implements StateFactoryInterface {
    /**
     * @var array
     */
    protected $stateIsCachedMap;
    
    /**
     * @var AbstractDependencyInjectionContainer 
     */
    protected $container;
    
    /**
     * @var bool
     */
    protected $cacheStates;
    
    /**
     * Constructs a new factory.
     * 
     * @param AbstractDependencyInjectionContainer $container DI container.
     * @param $cacheStates Whether or not to cache states within the DI container.
     */
    public function __construct(AbstractDependencyInjectionContainer $container, $cacheStates = TRUE) {
        $this->container = $container;
        $this->cacheStates = $cacheStates;
    }
    
    /**
     * {@inheritdoc}
     */
    public function make($state): StateInterface {
        $stateInstance = $this->container->get($state);
        if (
            $this->cacheStates
            &&
            !isset($this->stateIsCachedMap[$state])
            &&
            !$this->container->has($state)
        ) {
            $this->stateIsCachedMap[$state] = TRUE;
            $this->container->addConfig([
                'norma' => [
                    'framework' => [
                        'fsm' => [
                            'states' => [
                                $state => $stateInstance
                            ]
                        ]
                    ]
                ]
            ]);
        }
        return $stateInstance;
    }

}
