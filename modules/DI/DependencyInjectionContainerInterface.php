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

namespace Norma\DI;

/**
 * The interface of a DI container used within the Norma framework.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface DependencyInjectionContainerInterface {
    
    /**
     * Adds a config to the container so that it can construct the dependency graph.
     * Subsequent calls to this method should override previous configurations for the same component.
     * 
     * @param array $config An Array containing the configuration to add to the container.
     * @return static
     */
    public function addConfig(array $config);
    
    /**
     * Gets the current container's configuration as it would have been added through {@link DependencyInjectionContainerInterface::addConfig}.
     * 
     * @return array The current container's configuration.
     */
    public function getConfig();
    
    /**
     * Gets a component from the container.
     * 
     * @param string $component The component to get from within the container.
     * @return mixed The asked component.
     */
    public function get($component);

    /**
     * Calls a method or function passing from the container, therefore letting the container to resolve the
     * dependencies of the method or the function being called.
     * 
     * @param callable|string $whatToCall The method or function to call.
     * @param array $paramsMapInjection An optional map of parameters where the key is the name of the parameter and the value is the parameter
     *                                                          itself to use when calling the method or function.
     *                                                          The container should use this parameters instead of resolving the dependencies for parameters having the same
     *                                                          name.
     * @return mixed The return value of the called method or function.
     */
    public function call($whatToCall, $paramsMapInjection = []);
    
    /**
     * Bind a component needed by another component contextually.
     * That is, if a component requires a component which implements an interface or extends a common base type
     * and a different implementation is needed from the one specified in the container's configuration, this method
     * should tweak the default behaviour and make the container use another specific component instead of the one
     * defined in the configuration.
     * 
     * @param string $componentWhichNeedsAnotherComponent The component which needs the other component.
     * @param string $neededComponent The other component which the first component given as parameter might need.
     * @param mixed $componentToGive The component to give when the first component given as parameter needs it.
     * @return static
     */
    public function bindContextually($componentWhichNeedsAnotherComponent, $neededComponent, $componentToGive);
    
}
