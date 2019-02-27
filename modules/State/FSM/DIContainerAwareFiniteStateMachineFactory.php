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

namespace Norma\State\FSM;

use Norma\State\FSM\FiniteStateMachineFactoryInterface;
use Norma\State\FSM\StateInterface;
use Norma\State\FSM\FiniteStateMachineInterface;
use Norma\DI\AbstractDependencyInjectionContainer;

/**
 * The implementation of a finite-state machine factory.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class DIContainerAwareFiniteStateMachineFactory implements FiniteStateMachineFactoryInterface {
    
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
    public function make($initialState): FiniteStateMachineInterface {
        return new class($initialState, $this->container, $this->cacheStates) implements FiniteStateMachineInterface {
            
            /**
             * @var array
             */
            protected $cachedStatesMap;
            
            /**
             * @var AbstractDependencyInjectionContainer
             */
            protected $container;
            
            /**
             * @var bool
             */
            protected $cacheStates;
            
            /**
             * @var StateInterface
             */
            protected $state;
            
            /**
             * @var array
             */
            protected $data;
            
            /**
             * @var array
             */
            protected $onDataCallables;
            
            /**
             * Constructs a new finite-state machine.
             * 
             * @param string|StateInterface $initialState The initial state of the state machine.
             * @param AbstractDependencyInjectionContainer $container The DI container.
             * @param $cacheStates Whether or not to cache states within the DI container.
             */
            public function __construct($initialState, AbstractDependencyInjectionContainer $container, $cacheStates = TRUE) {
                $this->container = $container;
                $this->cacheStates = $cacheStates;
                $this->cachedStatesMap = [];
                $this->data = [];
                $this->onDataCallables = [];
                
                $state = $this->getStateInstance($initialState);
                $this->state = $state;
            }
            
            /**
             * Obtains a state instance of the state machine.
             * 
             * @param string|StateInterface $state The state instance of the state machine to obtain.
             * @return StateInterface The state instance.
             */
            protected function getStateInstance($state): StateInterface {
                if ($state instanceof StateInterface) {
                    return $state;
                }
                else {
                    $stateInstance = $this->container->get($state);
                    if (
                        $this->cacheStates
                        &&
                        !isset($this->cachedStatesMap[$state])
                        &&
                        !$this->container->has($state)
                    ) {
                        $this->cachedStatesMap[$state] = TRUE;
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
            
            /**
             * Triggers the callables bound to a data key after they are changed.
             * 
             * @param string $key The key of the data.
             * @param mixed $oldData The old data of the corresponding key before they changed.
             * @return void
             */
            protected function triggerOnDataCallables($key, $oldData) {
                if (isset($this->onDataCallables[$key])) {
                    $callables = $this->onDataCallables[$key];
                    $currentData = $this->data[$key] ?? NULL;
                    foreach ($callables as $callable) {
                        $callable($currentData, $oldData);
                    }
                }
            }
            
            /**
             * {@inheritdoc}
             */
            public function process($input = NULL) {
                $this->state->process($this, $input);
            }

            /**
             * {@inheritdoc}
             */
            public function setState($state) {
                /* @var $nextState StateInterface */
                $nextState = $this->getStateInstance($state);
                $this->state = $nextState;
            }

            /**
             * {@inheritdoc}
             */
            public function setData($key, $data) {
                $oldData = $oldData = $this->data[$key] ?? NULL;
                if (is_callable($data)) {
                    $dataToSet = $data($oldData);
                    $this->data[$key] = $dataToSet;
                }
                else {
                    $this->data[$key] = $data;
                }
                $this->triggerOnDataCallables($key, $oldData);
            }
            
            /**
             * {@inheritdoc}
             */
            public function getData($key) {
                if (array_key_exists($key, $this->data)) {
                    return $this->data[$key];
                }
                return NULL;
            }
            
            /**
             * {@inheritdoc}
             */
            public function onData($key, callable $callable) {
                if (!isset($this->onDataCallables[$key])) {
                    $this->onDataCallables[$key] = [];
                }
                $this->onDataCallables[$key][] = $callable;
            }

        };
    }

}
