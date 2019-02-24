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

use Norma\Core\Utils\FrameworkUtils;
use Norma\Regex\CodeRegex;

/**
 * An abstract class which implements the interface of the Norma's DI container and
 * defines the base logic of a container in a Norma application.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
abstract class AbstractDependencyInjectionContainer implements DependencyInjectionContainerInterface  {

    /**
     * Norma's annotation used for dependency injections.
     */
    const NORMA_INJECT_ANNOTATION = '@NormaInject';
    
    /**
     * Norma's annotation used to identify lazy class components.
     */
    const NORMA_LAZY_ANNOTATION = '@NormaLazy';
    
    /**
     * Norma's annotation used to identify the scope of a class component.
     */
    const NORMA_SCOPE_ANNOTATION = '@NormaScope';
    
    /**
     * Var annotations commonly used in class properties doc comments.
     */
    const VAR_ANNOTATION = '@var';
    
    /**
     * Parameters annotations commonly used in function and method parameters doc comments.
     */
    const PARAM_ANNOTATION = '@param';
    
    /**
     * Regular expression to match a valid PHP variable name.
     */
    const VALID_PHP_VAR_NAME_REGEX = CodeRegex::VALID_PHP_NAME_REGEX;
    
    /**
     * A separator character used internally to identify circular dependencies.
     */
    const CIRCULAR_DEPENDENCY_IDENTIFICATION_SEPARATOR = '~';
    
    /**
     * A character used as a separator for qualified components names.
     */
    const QUALIFIED_COMPONENT_KEY_SEPARATOR = '|';

    /**
     * @var array
     */
    protected $config = [];
    
    /**
     * @var array
     */
    protected $store = [];

    /**
     * @var array
     */
    protected $circularDependencyIdentification = [];
    
    /**
     * @var array
     */
    protected $contextualBindings = [];
    
    /**
     * @var array
     */
    protected $contextualBindingsStore = [];
    
    /**
     * @var array<\SplStack>
     */
    protected $contextualBindingsStacks = [];
    
    /**
     * @var mixed
     */
    protected $storedComponent;

    /**
     * @var int
     */
    protected $pendingClientGetIdentifier = -1;
    
    /**
     * @var array
     */
    protected $reversedPathKeysTrie = [];
    
    /**
     * @var array
     */
    protected $annotatedComponents = [];
    
    /**
     * Static helper to build a qualified component key given an array of its path keys.
     * 
     * @param array $pathKeys The path keys of the component.
     * @return string The qualified component key.
     */
    public static function buildQualifiedComponentKey(array $pathKeys) {
        return implode(AbstractDependencyInjectionContainer::QUALIFIED_COMPONENT_KEY_SEPARATOR, $pathKeys);
    }
    
    /**
     * Creates a lazy component which should create the instance only when effectively needed by the application.
     * 
     * @param string $componentToResolve The component to resolve lazily.
     * @param string $normalizedComponent The normalized component with whom the component to resolve is associated with within the container.
     * @return object The lazy instance of the class created.
     */
    abstract function createLazyComponent($componentToResolve, $normalizedComponent);
    
    /**
     * Get a framework utility object.
     * 
     * @return FrameworkUtils The framework utility object.
     */
    abstract function getFrameworkUtils(): FrameworkUtils;
    
    /**
     * Parses the use statements defined in a file under a namespace in order to determine the scope of the components
     * to build within this DI container (used for annotation-based components).
     * 
     * @param string $filename The filename.
     * @param string $namespace The namespace defined within `$filename` where the use statements are.
     * @return array An array of {@link Norma\Core\Parsing\ParsedUseStatementInterface}.
     */
    abstract function parseUseStatements($filename, $namespace);

    /**
     * {@inheritdoc}
     */
    public function addConfig(array $config) {
        $this->mergeConfigs($config);
        return $this;
    }
    
    /**
     * Merges the given config with the current container's configuration.
     * 
     * @param array $config The config to merge.
     * @return void
     * @throws DependencyInjectionException If a key of the path keys of a component contains a qualified component key separator.
     *                                                                 This is because otherwise the container wouldn't be able to resolve the component later on
     *                                                                 because it uses the separator to identify ambiguous paths.
     */
    protected function mergeConfigs(array $config) {
        $loosenLeafsAndPaths = $this->getFrameworkUtils()->loosenMultiDimensionalArrayPathForEachVal($config);
        
        foreach ($loosenLeafsAndPaths as $loosenLeafAndPath) {
            list($leafVal, $pathToLeafKeys) = $loosenLeafAndPath;
            
            foreach ($pathToLeafKeys as $pathKey) {
                if (strpos($pathKey, self::QUALIFIED_COMPONENT_KEY_SEPARATOR) !== false) {
                    throw new DependencyInjectionException(
                        sprintf('Unexpected qualified component key separator character "%3$s" within key "%1$s" with key path "%2$s". This character is forbidden.',
                                $pathKey,
                                implode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, $pathToLeafKeys),
                                self::QUALIFIED_COMPONENT_KEY_SEPARATOR
                        )
                    );
                } 
            }
            
            $qualifiedComponent = $this->normalizeComponent(implode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, $pathToLeafKeys));
            $qualifiedPathToLeafKeys = explode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, $qualifiedComponent);
            $this->indexPathKeysTrie($qualifiedPathToLeafKeys);
            
            $this->config[$qualifiedComponent] = $leafVal;
            unset($this->store[$qualifiedComponent]);
        }
    }
    
    /**
     * Indexes the path keys' trie of the container with the given path keys.
     * 
     * @param array $pathToLeafKeys The path keys.
     * @return void
     */
    protected function indexPathKeysTrie($pathToLeafKeys) {
        $pathKeysTrie = &$this->reversedPathKeysTrie;
        $reverse = array_reverse($pathToLeafKeys);
        foreach ($reverse as $pathKey) {
            // Indexing path keys' trie.
            if (!array_key_exists($pathKey, $pathKeysTrie)) {
                $pathKeysTrie[$pathKey] = [];
            }
            $pathKeysTrie = &$pathKeysTrie[$pathKey];
        }
    }
    
    /**
     * Helper method used to normalize the name of the component, qualifying it.
     * 
     * @param string $component The component name to normalize.
     * @return string The normalized component.
     */
    protected function normalizeComponent($component) {
        $explode = explode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, $component);
        $lastIndex = count($explode) - 1;
        $leafComponentKey = $explode[$lastIndex];
        $leafComponentNormalizedKey = ltrim($leafComponentKey, '\\');
        
        $explode[$lastIndex] = $leafComponentNormalizedKey;
        
        $qualifiedExplode = $this->qualifyComponent($explode);
        
        $implode = implode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, $qualifiedExplode);
        return $implode;
    }
    
    /**
     * Qualifies the name of a component by returning its full name determined by the container's configuration.
     * 
     * @param array $explodedComponentName The component name to qualify exploded using the key path separator.
     * @return array The qualified exploded name of the component.
     * @throws DependencyInjectionException If the component is not enough qualified and there's ambiguity.
     */
    protected function qualifyComponent($explodedComponentName) {
        $originalReversedPathKeys = array_reverse($explodedComponentName);
        
        $pathKeysTrie = $this->reversedPathKeysTrie;
        while (!empty($explodedComponentName)) {
            $pathKey = array_pop($explodedComponentName);
            if (!array_key_exists($pathKey, $pathKeysTrie)) {
                $pathKeys = $originalReversedPathKeys;
                break;
            }
            else {
                $pathKeys[] = $pathKey;
                if (count($pathKeysTrie[$pathKey]) > 1 && empty($explodedComponentName)) {
                    $inner = $pathKeysTrie[$pathKey];
                    $loosenPathKeys = $this->getFrameworkUtils()->loosenMultiDimensionalArrayPathForEachVal($inner);
                    $choices = [];
                    $pathKeysReversed = array_reverse($pathKeys);
                    foreach ($loosenPathKeys as $valPathKeys) {
                        $keys = $valPathKeys[1];
                        $keysReversed = array_reverse($keys);
                        $choices[] = implode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, array_merge($keysReversed, $pathKeysReversed));
                    }
                    throw new DependencyInjectionException(
                        sprintf('Ambiguous component name "%1$s" while qualifying it with key "%3$s". Possible components are: %2$s.',
                                implode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, array_reverse($originalReversedPathKeys)),
                                implode(', ', array_map(function ($el) { return '"' . $el . '"'; }, $choices)),
                                $pathKey
                        )
                    );
                }
                else {
                    if (empty($pathKeysTrie[$pathKey])) {
                        // Reached the end of the trie.
                        $pathKeysTrie = $pathKeysTrie[$pathKey];
                        continue;
                    }
                    $firstAndOnlyKey = $this->getFrameworkUtils()->arrayFirstKey($pathKeysTrie[$pathKey]);
                    $pathKeysTrie = $pathKeysTrie[$pathKey];
                    if (empty($explodedComponentName)) {
                        $explodedComponentName[] = $firstAndOnlyKey;
                    }
                }
            }
        }
        $reversed = array_reverse($pathKeys);
        return $reversed;
    }
    
    /**
     * Performs initialization operations when the client's code accesses the components of the container
     * or interacts with it.
     * 
     * @return void
     */
    protected function initClientAccess() {
        $this->pendingClientGetIdentifier++;
        $this->circularDependencyIdentification[$this->pendingClientGetIdentifier] = [];
        $this->contextualBindingsStacks[$this->pendingClientGetIdentifier] = new \SplStack();
        
        // First context is always NULL, as this method is called within a method which is called from the client's code.
        $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->push(NULL);
    }
    
    /**
     * Performs cleanup operations when the client's code has finished accessing the components of the container
     * or has finished interacting with it.
     * 
     * @return void
     */
    protected function cleanClientAccess() {
        $this->pendingClientGetIdentifier--;
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($component) {
        $this->initClientAccess();
        
        /*
         * `$component` can be:
         *      - A key of the container's config (class, abstract class, interface or key, e.g.: AnInterface::class, 'key.name');
         * 
         * If `$component` is not a key of the container's config, then `$component` will be returned as is.
         * However:
         *      - If it is an instance of {@link DependencyInjectionContainerComponentInterface}, it will be resolved and its component will be resolved;
         *      - If it is an existent class which can be instantiated (not abstract), it will be resolved even if it is not within the container's configuration;
         *      - If it is an anonymous function, that function will be called with resolved dependencies and its
         *         return value will be used;
         */
        // Outermost call.
        $componentToReturn = $this->getInternally($component);
        
        $this->cleanClientAccess();
        
        return $componentToReturn;
    }
    
    /**
     * Get the current binding context.
     * 
     * @return string The current binding context.
     */
    protected function getCurrentBindingContext() {
        if (isset($this->contextualBindingsStacks[$this->pendingClientGetIdentifier])) {
            return $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->offsetGet(0);
        }
        return null;
    }
    
    /**
     * Gets a component resolving it within the container.
     * 
     * @param DependencyInjectionContainerComponentInterface|string|mixed $component The component to get.
     * @param string|null $componentOfComponent Optional. Specifies the component of the current resolvable `$component` which is resolved directly through contextual
     *                                                                           binding. This parameter is set when the a component is retrieved internally through this method from `$this->contextualBindings`.
     * @return mixed The component.
     * @throws DependencyInjectionException If a circular dependency is found or the component cannot be obtained for some reason.
     */
    protected function getInternally($component, $componentOfComponent = NULL) {
        /*
         * `$component` can be:
         *      - A key of container's config (class, abstract class, interface or key, e.g.: AnInterface::class, 'key.name');
         *      - An instance of a class which implements the {@link DependencyInjectionContainerComponentInterface} interface;
         *      - An existent non-abstract class (not within container's config);
         *      - An anonymous function which return value would be the component to use (dependecies will be injected into its parameters);
         *      - A primitive value or another PHP type used as-is;
         */
        $componentToResolveDirectly = NULL;
        if (!is_null($componentOfComponent)) {
            $componentToResolveDirectly = $component;
            
            // Nothing to get from config at this point.
            $componentToEventuallyGet = $componentOfComponent;
            
            if (!$this->getFrameworkUtils()->strStartsWith($componentOfComponent, '$')) {
                // We can arrive here from contextual bindings of variables while resolving the parameters of functions/methods.
                $normalizedComponent = $this->normalizeComponent($componentOfComponent);
            }
            else {
                $normalizedComponent = $componentOfComponent;
            }
        }
        else {
            if ($component instanceof DependencyInjectionContainerComponentInterface) {
                // This could happen only internally when resolving annotated components. In this case `$containerComponent->getComponent()` returns a string.
                $containerComponent = $component;
                $component = $containerComponent->getComponent();
                $normalizedComponent = $this->normalizeComponent($component);
                $containerComponent->setComponent($normalizedComponent);
                $componentToEventuallyGet = $containerComponent;
            }
            else {
                $normalizedComponent = $this->normalizeComponent($component);
                $componentToEventuallyGet = $normalizedComponent;
            }

            if ($this->componentFoundWithinStores($normalizedComponent)) {
                $componentToReturn = $this->storedComponent;
                $this->storedComponent = NULL;
                return $componentToReturn;
            }

            /*
             * We identify circular dependency only for those components which cannot be resolved rightaway,
             * because otherwise if `$component` is a component resolvable through {@link AbstractDependencyInjectionContainer::resolve()},
             * if we even had a component which causes circular dependency, we would identify while resolving it and not at the point when `$component` is:
             * 
             *      - An instance of a class which implements the {@link DependencyInjectionContainerComponentInterface} interface;
             * Or:
             *      - An existent non-abstract class (not within container's config);
             * Or:
             *      - An anonymous function which return value would be the component to use (dependecies will be injected into its parameters);
             * Or:
             *      - A primitive value or another PHP type used as-is;
             */
            $this->identifyCircularDependency($normalizedComponent);
        }

        return $this->getFromConfig($componentToEventuallyGet, $componentToResolveDirectly);
    }
    
    /**
     * Identifies circular dependency and throws an exception in case there's one.
     * 
     * @param string $normalizedComponent The searched component.
     * @return void
     * @throws DependencyInjectionException If a circular dependency is found.
     */
    protected function identifyCircularDependency($normalizedComponent) {
        $circularDependencyIdentificationComponent = $normalizedComponent;
        
        $currentBindingContext = $this->getCurrentBindingContext();
        if (!is_null($currentBindingContext)
            &&
            $this->getFrameworkUtils()->arrayKeysExist($this->contextualBindings, $currentBindingContext, $circularDependencyIdentificationComponent)
        ) {
            $circularDependencyIdentificationComponent = $this->getComponentFromResolvable($this->contextualBindings[$currentBindingContext][$circularDependencyIdentificationComponent]);
        }
        else if (array_key_exists($circularDependencyIdentificationComponent, $this->config)) {
            $circularDependencyIdentificationComponent = $this->config[$circularDependencyIdentificationComponent];
            if ($circularDependencyIdentificationComponent instanceof DependencyInjectionContainerComponentInterface) {
                $circularDependencyIdentificationComponent = $this->getComponentFromResolvable($circularDependencyIdentificationComponent);
            }
        }
        
        $componentWhichMayCauseCircularDependency = $normalizedComponent;
        if (is_string($circularDependencyIdentificationComponent) && class_exists($circularDependencyIdentificationComponent)) {
            // If the leaf component is an existent class, that specific class together with its component prepended 
            // will be used as the key to identify circular dependency. This lets the container to handle potential circular dependency cases
            // which can be resolved with different implementations thanks to contextual bindings
            // when the same interface or abstract class is needed more than once.
            $circularDependencyIdentificationComponent = $normalizedComponent . self::CIRCULAR_DEPENDENCY_IDENTIFICATION_SEPARATOR . $circularDependencyIdentificationComponent;
        }
        else {
            $circularDependencyIdentificationComponent = $normalizedComponent;
        }
        
        if (empty($this->circularDependencyIdentification[$this->pendingClientGetIdentifier][$circularDependencyIdentificationComponent])) {
            $this->circularDependencyIdentification[$this->pendingClientGetIdentifier][$circularDependencyIdentificationComponent] = 0;
        }
        $this->circularDependencyIdentification[$this->pendingClientGetIdentifier][$circularDependencyIdentificationComponent]++;
        
        if ($this->circularDependencyIdentification[$this->pendingClientGetIdentifier][$circularDependencyIdentificationComponent] > 1) {
            throw new DependencyInjectionException(sprintf('Circular dependency for component "%1$s".', $componentWhichMayCauseCircularDependency));
        }
    }
    
    /**
     * Tests whether a component can be found within the stores of the DI container.
     * If a component is found, `$this->storedComponent` will be overridden and will contain the component.
     * If it is not found, `$this->storedComponent` will be overridden to NULL.
     * 
     * @param string $normalizedComponent The component which could be found within the stores of the container.
     * @return bool True if the component can be found (`$this->storedComponent` will be overridden and will contain the component).
     *                      False otherwise (`$this->storedComponent` will be overridden to NULL).
     */
    protected function componentFoundWithinStores($normalizedComponent) {
        $this->storedComponent = NULL;
        
        // 1. Check contextual bindings' store.
        $currentBindingContext = $this->getCurrentBindingContext();
        if (!is_null($currentBindingContext)
            &&
            $this->getFrameworkUtils()->arrayKeysExist($this->contextualBindingsStore, $currentBindingContext, $normalizedComponent)
        ) {
            $componentToResolve = $this->getComponentFromResolvable($this->contextualBindings[$currentBindingContext][$normalizedComponent]);
            
            if ((is_string($componentToResolve) || is_int($componentToResolve)) && array_key_exists($componentToResolve, $this->config)) {
                $componentToResolve = $this->config[$componentToResolve];
            }
            
            if ($this->canGetFromStore($componentToResolve)) {
                $this->storedComponent = $this->getFromContextualBindingStore($currentBindingContext, $normalizedComponent);
                return TRUE;
            }
        }
        
        // 2. Check container's config store.
        if (array_key_exists($normalizedComponent, $this->store)) {
            $configComponent = $this->config[$normalizedComponent];

            if ($this->canGetFromStore($configComponent)) {
                $this->storedComponent = $this->getFromStore($normalizedComponent);
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * Tests whether a component to resolve which is already in a store can be used.
     * 
     * @param DependencyInjectionContainerComponentInterface|string $componentToResolve The component to resolve.
     * @return bool True if the component within the store can be used.
     */
    protected function canGetFromStore($componentToResolve) {
        // Components with instance scope must be recreated.
        return !($componentToResolve instanceof DependencyInjectionContainerComponentInterface)
                  ||
                  $componentToResolve->getScope() != DependencyInjectionContainerComponentScopeEnum::INSTANCE;
    }
    
    /**
     * Retrieve a component from within the container's internal store.
     * 
     * @param string $normalizedComponent The component to retrieve from the store.
     * @return mixed The component.
     */
    protected function getFromStore($normalizedComponent) {
        return $this->store[$normalizedComponent];
    }
    
    /**
     * Retrieves a component contextually bound and needed by another component.
     * 
     * @param string $componentWhichNeedsAnotherComponent The component which needs the other component.
     * @param string $neededComponent The needed component.
     * @return mixed The contextually bound component.
     */
    protected function getFromContextualBindingStore($componentWhichNeedsAnotherComponent, $neededComponent) {
        return $this->contextualBindingsStore[$componentWhichNeedsAnotherComponent][$neededComponent];
    }
    
    /**
     * Retrieve a component from within the container given its current configuration.
     * If a component is not within the container's configuration, the container
     * will try to create an instance the component and if an error doesn't occur it will
     * save it within its internal store.
     * 
     * @param string|DependencyInjectionContainerComponentInterface $normalizedComponent The component to get.
     *                                                                                                          `DependencyInjectionContainerComponentInterface` objects are used for internal wiring.
     * @param mixed $componentToResolveDirectly A component to resolve directly.
     * @return mixed The component.
     */
    protected function getFromConfig($normalizedComponent, $componentToResolveDirectly = NULL) {
        $componentToResolve = NULL;
        
        if (!is_null($componentToResolveDirectly)) {
            // For contextual binding, resolving a component directly.
            $resolvableComponent = $this->getComponentFromResolvable($componentToResolveDirectly);
            if (
                (is_string($resolvableComponent) || is_int($resolvableComponent)) && array_key_exists($resolvableComponent, $this->contextualBindings)
            ) {
                // More specific binding.
                $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->push($resolvableComponent);
            }
            else if (array_key_exists($normalizedComponent, $this->contextualBindings)) {
                // Contextual binding configuration.
                $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->push($normalizedComponent);
            }
            else {
                // Fallback to null.
                $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->push(NULL);
            }
            $resolvedComponent = $this->resolve($componentToResolveDirectly, $normalizedComponent);
            $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->pop();
            return $resolvedComponent;
        }
        
        if ($normalizedComponent instanceof DependencyInjectionContainerComponentInterface) {
            // This could happen only internally when resolving annotated components.
            $containerComponent = $normalizedComponent;
            $normalizedComponent = $containerComponent->getComponent();
            $componentToResolve = $containerComponent;
        }
        
        // Current binding context. Can either be NULL or can be a key of the first dimension of the `$this->contextualBindings` array.
        $currentBindingContext = $this->getCurrentBindingContext();
        if (!is_null($currentBindingContext)
            &&
            $this->getFrameworkUtils()->arrayKeysExist($this->contextualBindings, $currentBindingContext, $normalizedComponent)
        ) {
            $componentToResolve = $this->contextualBindings[$currentBindingContext][$normalizedComponent]; 

            /*
             * Resolve the component given the contextual binding configuration.
             * 
             * `$componentToResolve` can be:
             *      - A key of the container's config (class, abstract class, interface or key, e.g.: AnInterface::class, 'key.name');
             *      - An instance of {@link DependencyInjectionContainerComponentInterface}, its component will be resolved;
             *      - An existent class which can be instantiated (not abstract), it will be resolved even if it is not within the container's configuration;
             *      - An anonymous function, that function will be called with resolved dependencies and its return value will be used;
             */
            $this->contextualBindingsStore[$currentBindingContext][$normalizedComponent] = $this->getInternally($componentToResolve, $normalizedComponent);
            
            return $this->contextualBindingsStore[$currentBindingContext][$normalizedComponent];
        }
        
        $pushedToContextualBindingStack = FALSE;
        if (array_key_exists($normalizedComponent, $this->contextualBindings)) {
            $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->push($normalizedComponent);
            $pushedToContextualBindingStack = TRUE;
        }
        
        $existsWithinConfig = TRUE;
        if (!array_key_exists($normalizedComponent, $this->config)) {
            /*
             * If a component is not defined within this container, try at least to resolve it anyway.
             * 
             * If it is a {@link DependencyInjectionContainerComponentInterface} its resolvable component
             * will be resolved.
             * 
             * If it's a specific class, an instance will be created and at least injection will be applied
             * to its constructor's dependencies and properties.
             * 
             * If it is an anonymous function, it will be called by the container and the returned component will be used.
             * 
             * However, if the component is a scalar value, an exception will be thrown, as the key does not
             * exist within the container's configuration.
             */
            $existsWithinConfig = FALSE;
            if (!$pushedToContextualBindingStack) {
                // Just fill the stack on top if there wasn't a context pushed to the stack already
                // as the context should always change whenever another component is resolved.
                $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->push(NULL);
                $pushedToContextualBindingStack = TRUE;
            }
            $this->store[$normalizedComponent] = $this->resolve($normalizedComponent, $normalizedComponent, TRUE);
        }
        else {
            if ($componentToResolve instanceof DependencyInjectionContainerComponentInterface) {
                // Again, internally, the `$componentToResolve` is still the instance of `DependencyInjectionContainerComponentInterface`.
                // But its component now becomes the truly component defined by the configuration (which could in turn be a `DependencyInjectionContainerComponentInterface`.
                $containerComponent->setComponent($this->config[$normalizedComponent]);
            }
            else {
                $componentToResolve = $this->config[$normalizedComponent];
            }

            $component = $this->getComponentFromResolvable($componentToResolve);
            if (
                (is_string($component) || is_int($component)) && array_key_exists($component, $this->contextualBindings)
            ) {
                if ($pushedToContextualBindingStack) {
                    // Pop the previous context as there's one which is more specific and which therefore should be used.
                    $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->pop();
                }
                $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->push($component);
                $pushedToContextualBindingStack = TRUE;
            }
            else {
                if (!$pushedToContextualBindingStack) {
                    // Just fill the stack on top if there wasn't a context pushed to the stack already
                    // as the context should always change whenever another component is resolved.
                    $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->push(NULL);
                    $pushedToContextualBindingStack = TRUE;
                }
            }
            
            // Resolve the component given the config information.
            $this->store[$normalizedComponent] = $this->resolve($componentToResolve, $normalizedComponent);
        }
        
        if ($pushedToContextualBindingStack == TRUE) {
            // Push the context, moving to the previous one.
            $this->contextualBindingsStacks[$this->pendingClientGetIdentifier]->pop();
        }
        
        $componentToReturn = $this->store[$normalizedComponent];
        
        // If the component is not defined within the container's configuration, it is always
        // removed from the store because we always create a new instance for components
        // which are not defined within the container's configuration.
        // However, if we have an annotation for that component, we keep it, because annotations
        // are considered part of the container's configuration.
        if (!$existsWithinConfig && empty($this->annotatedComponents[$normalizedComponent])) {
            unset($this->store[$normalizedComponent]);
        }
        return $componentToReturn;
    }
    
    /**
     * Get the component from a resolvable component of the container. A resolvable component is a component which
     * can be resolved through the {@link AbstractDependencyInjectionContainer::resolve()} method.
     * If the component is an instance of {@link DependencyInjectionContainerComponentInterface},
     * the internal component it refers to will be returned.
     * 
     * @param string|DependencyInjectionContainerComponentInterface $resolvableComponent The resolvable component.
     * @return mixed The component.
     */
    protected function getComponentFromResolvable($resolvableComponent) {
        while ($resolvableComponent instanceof DependencyInjectionContainerComponentInterface) {
            $resolvableComponent = $resolvableComponent->getComponent();
        }   
        return $resolvableComponent;
    }
    
    /**
     * Resolve a class component.
     * 
     * @param string $componentToResolve The class component to resolve.
     * @return object The resolved instance of the class.
     * @throws DependencyInjectionException If the component could not be created and therefore resolved.
     */
    protected function resolveClass($componentToResolve) {
        $reflectionClass = new \ReflectionClass($componentToResolve);
        $constructor = $reflectionClass->getConstructor();
        $params = [];
        if (!is_null($constructor)) {
            $params = $this->resolveMethodParams($reflectionClass, $constructor);
        }
        $instance = new $componentToResolve(...$params);
        $this->injectPropertyDependencies($instance, $reflectionClass);
        return $instance;
    }
    
    /**
     * Resolve a class component lazily.
     * 
     * @param string $componentToResolve The class component to resolve lazily.
     * @param string $normalizedComponent The normalized component with whom the component to resolve is associated with within the container.
     * @return object The resolved lazy instance of the class.
     */
    protected function resolveLazyClass($componentToResolve, $normalizedComponent) {
        $component = $this->createLazyComponent($componentToResolve, $normalizedComponent);
        return $component;
    }
    
    /**
     * Resolves the parameters of a function.
     * 
     * @param \ReflectionFunction $reflectionFunction The function which parameters to resolve.
     * @param array $paramsMapInjection An optional map of parameters where the key is the name of the parameter and the value is the parameter
     *                                                          itself to use when calling the function.
     *                                                          The container should use this parameters instead of resolving the dependencies for parameters having the same
     *                                                          name.
     * @return array An array of resolved parameters.
     * @throws DependencyInjectionException If there's a parameter which is missing and is not optional.
     */
    protected function resolveFunctionParams(\ReflectionFunction $reflectionFunction, $paramsMapInjection = []) {
        $params = $this->resolveParams($reflectionFunction, $paramsMapInjection);
        if (!is_array($params)) {
            $missingParameter = $params;
            throw new DependencyInjectionException(
                    sprintf('Missing parameter "%1$s" needed by the function "%2$s". The parameter is not optional, not type hinted and does not have a corresponding value.',
                        $missingParameter,
                        $reflectionFunction->getName()
                    )
            );
        }
        return $params;
    }
    
    /**
     * Resolves the parameters of a method.
     * 
     * @param \ReflectionClass $reflectionClass The class of the method.
     * @param \ReflectionMethod $reflectionMethod The method.
     * @param array $paramsMapInjection An optional map of parameters where the key is the name of the parameter and the value is the parameter
     *                                                          itself to use when calling the method.
     *                                                          The container should use this parameters instead of resolving the dependencies for parameters having the same
     *                                                          name.
     * @return array An array of resolved parameters.
     * @throws DependencyInjectionException If there's a parameter which is missing and is not optional.
     */
    protected function resolveMethodParams(\ReflectionClass $reflectionClass, \ReflectionMethod $reflectionMethod, $paramsMapInjection = []) {
        $params = $this->resolveParams($reflectionMethod, $paramsMapInjection);
        if (!is_array($params)) {
            $missingParameter = $params;
            throw new DependencyInjectionException(
                    sprintf('Missing parameter "%1$s" needed by the method "%3$s" of class "%2$s". The parameter is not optional, not type hinted and does not have a corresponding value.',
                        $missingParameter,
                        $reflectionClass->getName(),
                        $reflectionMethod->getName()
                    )
            );
        }
        return $params;
    }
    
    /**
     * Splits a string using a split delimiter and returing an array of the split result including the trailing delimiter for each substring.
     * 
     * @param string $split The split delimiter string.
     * @param string $string The string to split
     * @return array|false An array of the splitted strings or false on failure.
     */
    protected function splitAnnotations($split, $string) {
        return preg_split('~([\s\S]*?'.preg_quote($split, '~').'[\s\S]*?(?=@|(?:\*(?=/))))~', $string, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
    }
    
    /**
     * Parses components defined through annotations on a method or function.
     * 
     * @param \ReflectionFunctionAbstract $reflectionFuncOrMethod A reflection instance of a method or function.
     * @return array An array containing the parsed data indexed by the name of the parameter variable defined in the annotation
     *                       which starts with '$'.
     */
    protected function parseComponentsFromReflectionFunctionAbstractAnnotations(\ReflectionFunctionAbstract $reflectionFuncOrMethod) {
        // Sample Regexes:
        // 
        //     https://regex101.com/r/Nut9AP/2 -> For splitting params.
        //     https://regex101.com/r/Nut9AP/3 -> For splitting inject annotations.
        //     https://regex101.com/r/fiKGSc/3 -> For a single inject annotation parse through param annotation. Gives variable name.
        //     https://regex101.com/r/93u9l5/1 -> For a single inject annotation parse through inject annotation. Gives variable name.
        //
        $parse = [];
        $docComment = $reflectionFuncOrMethod->getDocComment();

        // 1. We are looking for param annotations:
        $paramSplit = $this->splitAnnotations(self::PARAM_ANNOTATION, $docComment);
        if (
            $paramSplit !== FALSE
            &&
            (($count = count($paramSplit)) > 1)
        ) {
            // At least one param annotation is present.
            $injectToSearchAtTheEnd = $paramSplit[$count - 1];
            unset($paramSplit[$count - 1]);
            
            // 1.1. We search for the inject and optional annotations.
            foreach ($paramSplit as $paramSubstring) {
                $paramParse = [];
                $regex = '~^
                                    (?=.*'.preg_quote(self::NORMA_INJECT_ANNOTATION, '~').'
                                      (?:
                                          (?:
                                              (?:[^\S\n]+)?
                                              (?P<inject_component>(?<=[\s])[\S]+)
                                              (?:[^\S\n]+?(?P<inject_param_name>(?<=[\s])\$'.self::VALID_PHP_VAR_NAME_REGEX.'))?
                                              (?:[^\n]*$)?
                                          )
                                          |
                                          [\s]
                                      )
                                    )
                                    (?:(?=.*(?P<lazy>' . preg_quote(self::NORMA_LAZY_ANNOTATION, '~') . ')
                                      (?:$|[\s])
                                    ))?
                                    (?:(?=.*'.preg_quote(self::NORMA_SCOPE_ANNOTATION, '~').'
                                        (?:
                                            (?:
                                                (?:[^\S\n]+)?
                                                (?P<scope>(?<=[\s])[\S]+)
                                                (?:[^\n]*$)?
                                            )
                                            |
                                            [\s]
                                        )
                                    ))?
                                    (?:(?=.*'.preg_quote(self::PARAM_ANNOTATION, '~').'
                                      (?:
                                       (?:[^\S\n]+)?
                                       (?P<param_component>(?<=[\s])[\S]+)
                                       (?:[^\S\n]+?(?P<param_name>(?<=[\s])\$'.self::VALID_PHP_VAR_NAME_REGEX.'))?
                                       (?:[^\n]*$)?
                                      )?
                                    )).*
                                $~msx';
                $matches = [];
                preg_match($regex, $paramSubstring, $matches);
                if (
                    (isset($matches['inject_component']))
                ) {
                    $component = NULL;
                    $paramName = NULL;
                    
                    // Variable name.
                    if (!empty($matches['inject_param_name'])) {
                        $paramName = $matches['inject_param_name'];
                    }
                    else if (!empty($matches['param_name'])) {
                        $paramName = $matches['param_name'];
                    }
                    
                    if (!empty($paramName)) {
                        // Component.
                        if (!empty($matches['inject_component'])) {
                            $component = $matches['inject_component'];
                        }
                        else if (!empty($matches['param_component'])) {
                            $component = $matches['param_component'];
                        }
                        $paramParse['component'] = $component;

                        // Check for laziness.
                        if (!empty($matches['lazy'])) {
                            $paramParse['lazy'] = TRUE;
                        }
                                                
                        // Scope.
                        if (!empty($matches['scope'])) {
                            $paramParse['scope'] = $this->parseScopeAnnotation($matches['scope']);
                        }
                        
                        $parse[$paramName] = $paramParse;
                    }
                }
            }
        }
        else {
            $injectToSearchAtTheEnd = $docComment;
        }
        
        // 2. At the end we search for the inject annotation against the string after the last param annotation (if there was at least one)
        //     or we search against the whole doc string.
        //     At this point, the other optional annotations (scope, laziness and so on) must preceed the nth inject annotation found
        //     because there aren't any param annotations which specify the context at this point.
        $injectSplit = $this->splitAnnotations(self::NORMA_INJECT_ANNOTATION, $injectToSearchAtTheEnd);
        if (
            $injectSplit !== FALSE
            &&
            (($countInject = count($injectSplit)) > 1)
        ) {
            // At least one inject annotations should be present.
            unset($injectSplit[$countInject - 1]);
            
            // 2.1. We override the current `$parse` with these inject annotations.
            foreach ($injectSplit as $injectSubstring) {
                $paramParse = [];
                $regex = '~^
                                    (?:(?=.*(?P<lazy>' . preg_quote(self::NORMA_LAZY_ANNOTATION, '~') . ')
                                      (?:$|[\s])
                                    ))?
                                    (?:(?=.*'.preg_quote(self::NORMA_SCOPE_ANNOTATION, '~').'
                                        (?:
                                            (?:
                                                (?:[^\S\n]+)?
                                                (?P<scope>(?<=[\s])[\S]+)
                                                (?:[^\n]*$)?
                                            )
                                            |
                                            [\s]
                                        )
                                    ))?
                                    (?=.*'. preg_quote(self::NORMA_INJECT_ANNOTATION, '~').'
                                      (?:
                                          (?:
                                              (?:[^\S\n]+)?
                                              (?P<inject_component>(?<=[\s])[\S]+)
                                              (?:[^\S\n]+?(?P<inject_param_name>(?<=[\s])\$'.self::VALID_PHP_VAR_NAME_REGEX.'))?
                                              (?:[^\n]*$)?
                                          )
                                          |
                                          [\s]
                                      )
                                    )
                                $~msx';
                $matches = [];
                preg_match($regex, $injectSubstring, $matches);
                if (
                    (isset($matches['inject_component']))
                ) {
                    $component = NULL;
                    $paramName = NULL;
                    
                    // Variable name.
                    if (!empty($matches['inject_param_name'])) {
                        $paramName = $matches['inject_param_name'];
                    }
                    
                    if (!empty($paramName)) {
                        // Component.
                        if (!empty($matches['inject_component'])) {
                            $component = $matches['inject_component'];
                        }
                        $paramParse['component'] = $component;

                        // Check for laziness.
                        if (!empty($matches['lazy'])) {
                            $paramParse['lazy'] = TRUE;
                        }
                        
                        // Scope.
                        if (!empty($matches['scope'])) {
                            $paramParse['scope'] = $this->parseScopeAnnotation($matches['scope']);
                        }
                        
                        $parse[$paramName] = $paramParse;
                    }
                }
            }
        }
        return $parse;
    }
    
    /**
     * Parses the scope string of a component defined by an annotation.
     * 
     * @param string $scopeStr
     * @return int The type of the parsed scope of a component (a constant of the enum-like class {@link DependencyInjectionContainerComponentScopeEnum}).
     */
    protected function parseScopeAnnotation($scopeStr) {
        switch ($scopeStr) {
            case 'instance':
                return DependencyInjectionContainerComponentScopeEnum::INSTANCE;
            default:
                return static::defaultComponentScope();
        }
    }
    
    /**
     * Returns the default scope of this DI container.
     * 
     * @return int The default scope. A constant of the {@link DependencyInjectionContainerComponentScopeEnum} enum-like class.
     */
    public static function defaultComponentScope() {
        return DependencyInjectionContainerComponentScopeEnum::SINGLETON;
    }
    
    /**
     * Build a component for the DI container given an array which describes the component's definition.
     * 
     * @param array $componentDefinition An array describing the component's definition.
     * @return DependencyInjectionContainerComponentInterface The container component.
     */
    public function buildComponent($componentDefinition) {
        $isLazy = !empty($componentDefinition['lazy']);
        $scope = !empty($componentDefinition['scope']) ? $componentDefinition['scope'] : static::defaultComponentScope();
        $containerComponent = (new DependencyInjectionContainerComponent($componentDefinition['component']))
                ->setLazy($isLazy)
                ->setScope($scope);
        return $containerComponent;
    }
    
    /**
     * Resolve the parameters of a method or function.
     * 
     * @param \ReflectionFunctionAbstract $reflectionFuncOrMethod The method or function.
     * @param array $paramsMapInjection An optional map of parameters where the key is the name of the parameter and the value is the parameter
     *                                                          itself to use when calling the method or function.
     *                                                          The container should use this parameters instead of resolving the dependencies for parameters having the same
     *                                                          name.
     * @return array|string An array of resolved parameters or a string with the name of the parameter which could not be resolved.
     */
    protected function resolveParams(\ReflectionFunctionAbstract $reflectionFuncOrMethod, $paramsMapInjection = []) {
        $params = [];
        $reflectionParams = $reflectionFuncOrMethod->getParameters();
        
        // Annotation parsing is deferred until actually needed.
        $funcOrMethodAnnotationsParse = function() use ($reflectionFuncOrMethod) {
            static $parseResult = NULL;
            if (is_null($parseResult)) {
                $parseResult = $this->parseComponentsFromReflectionFunctionAbstractAnnotations($reflectionFuncOrMethod);
            }
            return $parseResult;
        };
        
        if (is_a($reflectionFuncOrMethod, \ReflectionMethod::class)) {
            /* @var $reflectionClass \ReflectionClass */
            $reflectionClass = $reflectionFuncOrMethod->getDeclaringClass();
            $filename = $reflectionClass->getFileName();
            $namespace = $reflectionClass->getNamespaceName();
        }
        else {
            $filename = $reflectionFuncOrMethod->getFileName();
            $namespace = $reflectionFuncOrMethod->getNamespaceName();
        }
        
        foreach ($reflectionParams as $reflectionParam) {
            try {
                $parameterName = $reflectionParam->getName();

                // 1. If the parameter exists within the array, use it.
                if (array_key_exists($parameterName, $paramsMapInjection)) {
                    $params[] = $paramsMapInjection[$parameterName];
                    continue;
                }

                // 2. Search for a class type hint. If there's one, it always wins.
                $paramReflectionClass = $reflectionParam->getClass();
                if (!is_null($paramReflectionClass)) {
                    /* @var $paramReflectionClass \ReflectionClass */
                    $paramType = $paramReflectionClass->getName();
                    
                    /*
                     * `$paramType` here is a type represented by a string.
                     */
                    $params[] = $this->getInternally($paramType);
                    continue;
                }

                // 3. If the parameter is not type hinted, search for a value using the parse of the method/function annotations.
                $paramName = '$'.$parameterName;
                $funcOrMethodAnnotationsParseResult = $funcOrMethodAnnotationsParse();
                if (!empty($funcOrMethodAnnotationsParseResult[$paramName])) {
                    // 3.1. There's a param.
                    $componentDefinition = $funcOrMethodAnnotationsParseResult[$paramName];
                    if (!empty($componentDefinition['component'])) {
                        // Param annotation injection.
                        $componentParameter = $this->buildAnnotatedComponentInternally($componentDefinition, $filename, $namespace);
                        $params[] = $componentParameter;
                        continue;
                    }
                }

                // 4. Contextual binding of `$paramName`.
                $currentBindingContext = $this->getCurrentBindingContext();
                if (!is_null($currentBindingContext)
                    &&
                    $this->getFrameworkUtils()->arrayKeysExist($this->contextualBindings, $currentBindingContext, $paramName)
                ) {
                    $componentToGet = $this->contextualBindings[$currentBindingContext][$paramName];
                    
                    /*
                     * `$componentToGet` can be:
                     * 
                     *      - A key of container's config (class, abstract class, interface or key, e.g.: AnInterface::class, 'key.name');
                     *      - An instance of a class which implements the {@link DependencyInjectionContainerComponentInterface} interface;
                     *      - An existent non-abstract class (not within container's config);
                     *      - An anonymous function which return value would be the component to use (dependecies will be injected into its parameters);
                     *      - A primitive value or another PHP type used as-is;
                     */
                    $params[] = $this->getInternally($componentToGet, $paramName);
                    continue;
                }

                // 5. Optional parameters. At this point, let it have its default value.
                if ($reflectionParam->isOptional()) {
                    $params[] = $reflectionParam->getDefaultValue();
                    continue;
                }
                else {
                    // 6. Error, return missing parameter.
                    return $paramName;
                }   
            }
            catch (DependencyInjectionException $ex) {
                if ($reflectionParam->isOptional()) {
                    $params[] = $reflectionParam->getDefaultValue();
                    continue;
                }
                else {
                    throw $ex;
                }
            }
        }
        // Return the resolved parameters.
        return $params;
    }
    
    /**
     * Gets the leaf key of a component which may be qualified or not.
     * 
     * @param string $component The component's name.
     * @return string The leaf key of the component.
     */
    protected function getComponentLeafKey($component) {
        $explode = explode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, $component);
        $lastIndex = count($explode) - 1;
        return $explode[$lastIndex];
    }
    
    /**
     * Gets the component's qualified name before the leaf component's key.
     * 
     * @param string $component The component's name
     * @return string The qualified name before the leaf key.
     */
    protected function getComponentQualifiedNameBeforeLeafKey($component) {
        $explode = explode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, $component);
        $lastIndex = count($explode) - 1;
        unset($explode[$lastIndex]);
        return implode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, $explode);
    }
    
    /**
     * Tests whether a component name has a fully qualified leaf starting with a namespace separator or not.
     * 
     * @param string $component The component's name
     * @return bool True if the component's name starts with a namespaca separator, false otherwise.
     */
    protected function isFullyQualifiedLeaf($component) {
        $leafComponent = $this->getComponentLeafKey($component);
        return strpos($leafComponent, '\\') === 0;
    }
    
    /**
     * Builds a component internally within this DI container. This method makes the container to introspect the code which
     * uses the component being built with was defined through an annotation.
     * 
     * @param array $componentDefinition An array of a component's definition, which is being built internally.
     * @param string $filename The filename where the component is used (i.e. the filename of the code using that component).
     * @param string $namespace The namespace where the component is used (i.e. the namespace of the code using the component).
     * @return DependencyInjectionContainerComponentInterface The container component.
     */
    protected function buildAnnotatedComponentInternally($componentDefinition, $filename, $namespace) {
        $component = $componentDefinition['component'];
        
        // `$component` here can only be a string here, as it comes internally from an annotation.
        // Therefore it could be an existent interface or class (it could be an interface because it is being built internally)
        // which points to a component which is resolvable through {@link AbstracDependencyInjectionContainer::resolve()}.
        if (!$this->isFullyQualifiedLeaf($component)) {
            // Component doesn't start with namespace separator. Therefore, `$component` could either be a PHP name or a custom string.
            // 1. We normalize.
            $componentToInject = $this->normalizeComponent($component);
            
            if (!array_key_exists($componentToInject, $this->config)) {
                // 2. If the component does not exist within the container's configuration,
                //     we look for use statements defined within the caller's code file in order to find a possible interface or class.
                $componentQualifiedNameBeforeLeafKey = $this->getComponentQualifiedNameBeforeLeafKey($componentToInject);
                $componentToInject = $this->getComponentLeafKey($componentToInject); $componentAlias = $componentToInject;
                $firstNamespaceSeparator = strpos($componentToInject, '\\');
                $componentAppend = '';
                if ($firstNamespaceSeparator !== FALSE) {
                    $componentAlias = substr($componentToInject, 0, $firstNamespaceSeparator);
                    $componentAppend = substr($componentToInject, $firstNamespaceSeparator);
                }
                $componentAliasLowerCase = strtolower($componentAlias);
                
                // Use statements parsing.
                $useStatements = $this->parseUseStatements($filename, $namespace);
                foreach ($useStatements as $parsedUseStatement) {
                    /* @var $parsedUseStatement \Norma\Core\Parsing\ParsedUseStatementInterface */
                    $alias = $parsedUseStatement->getAlias();
                    if (strtolower($alias) == $componentAliasLowerCase) {
                        // Name found.
                        $name = $parsedUseStatement->getName();
                        $componentToInject = $name . $componentAppend;
                        $qualifiedNameToImplode = [
                            $componentToInject
                        ];
                        if (!empty($componentQualifiedNameBeforeLeafKey)) {
                            array_unshift($qualifiedNameToImplode, $componentQualifiedNameBeforeLeafKey);
                        }
                        $qualifiedComponentName = implode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, $qualifiedNameToImplode);
                        $componentToInject = $this->normalizeComponent($qualifiedComponentName);
                        break;
                    }
                }
            }
        }
        else {
            // The component starts with a backslash, chances are it is a PHP fully qualified name.
            // It could be a custom string as well (they are normalized too within the container).
            // As a consequence, we just normalize here as we would do for any other component added to the
            // container through {@link AbstracDependencyInjectionContainer::addConfig()}.
            $componentToInject = $this->normalizeComponent($component);
        }
        $componentDefinition['component'] = $componentToInject;
        $builtComponent = $this->buildComponent($componentDefinition);
        
        // Lastly, we get the component internally, resolving it.
        
        $componentOfBuildComponent = $builtComponent->getComponent();
        $this->annotatedComponents[$componentOfBuildComponent] = TRUE;
        
        /*
         * `$builtComponent` is an instance of {@link DependencyInjectionContainerComponentInterface} and its component is a string representing the component to resolve.
         */
        return $this->getInternally($builtComponent);
    }
    
    /**
     * Resolve a closure component.
     * 
     * @param \Closure $componentToResolve The closure.
     * @return mixed The resolved component.
     */
    protected function resolveClosure(\Closure $componentToResolve) {
        $closureReturn = $this->callFunction($componentToResolve);
        return $closureReturn;
    }
    
    /**
     * Resolve a component. A component can be:
     * 
     *      - An instance of a class which implements the {@link DependencyInjectionContainerComponentInterface} interface;
     *      - An existent class;
     *      - An anonymous function which return value would be the component to use;
     *      - A primitive value or another PHP type used as-is.
     * 
     * @param mixed $componentToResolve The component to resolve. `DependencyInjectionContainerComponentInterface` is passed either internally 
     *                                                              for annotated components to resolve, or externally if the developer defines a component while configuring the container.
     *                                                              Other common components may be: strings, integers, anonymous functions (which will be called by the container's {@link AbstractDependencyInjectionContainer::callFunction()}
     *                                                              method).
     * @param string $normalizedComponent The normalized component with whom the component to resolve is associated with within the container.
     * @param bool $throwExceptionIfGoingToReturnAsIs A boolean which instructs the container to throw a {@link DependencyInjectionException} instead of returning the component as is.
     *                                                                                  This parameter is used internally to throw an exception when a component doesn't exist within the container's configuration.
     * @return mixed The resolved component.
     * @throws DependencyInjectionException If the component could not be resolved.
     */
    protected function resolve($componentToResolve, $normalizedComponent, $throwExceptionIfGoingToReturnAsIs = false) {
        $lazy = FALSE;
        
        // 1. Instance of {@link DependencyInjectionContainerComponentInterface}.
        if ($componentToResolve instanceof DependencyInjectionContainerComponentInterface) {
            // Could be either internally (while this container is resolving the dependencies needed by a component)
            // or not (the code of the client configured a component using an implementation of {@link DependencyInjectionContainerComponentInterface}).
            $containerComponent = $componentToResolve;
            $lazy = $containerComponent->isLazy();
            while (($trulyContainerComponent = $containerComponent->getComponent()) instanceof DependencyInjectionContainerComponentInterface) {
                if (empty($lazy)) {
                    $lazy = $trulyContainerComponent->isLazy();
                }
                $containerComponent = $trulyContainerComponent;
            }
            $componentToResolve = $trulyContainerComponent;
        }
        // 2. Existent non-abstract class.
        if (is_string($componentToResolve)
            &&
            (class_exists($componentToResolve) && !$this->getFrameworkUtils()->isAbstractClassOrInterface($componentToResolve))
        ) {
            if ($lazy) {
                $lazyInstance = $this->resolveLazyClass($componentToResolve, $normalizedComponent);
                return $lazyInstance;
            }
            else {
                return $this->resolveClass($componentToResolve);
            }
        }
        // 3. Anonymous function.
        else if ($this->getFrameworkUtils()->isAnonymousFunction($componentToResolve)) {
            return $this->resolveClosure($componentToResolve);
        }
        // 4. Anything else returned as is.
        else {
            if ($throwExceptionIfGoingToReturnAsIs) {
                throw new DependencyInjectionException(
                    sprintf('The component "%1$s" is not defined within the container.',
                        $normalizedComponent
                    )
                );
            }
            return $componentToResolve;
        }
    }
    
    /**
     * Autowire the properties of a newly created instance.
     * 
     * @param object $instance An instance of an object.
     * @param \ReflectionClass $reflectionClass Its corresponding `ReflectionClass` instance.
     * @return void
     */
    protected function injectPropertyDependencies($instance, \ReflectionClass $reflectionClass) {
        $properties = $reflectionClass->getProperties();
        $namespace = $reflectionClass->getNamespaceName();
        $defaultProperties = $reflectionClass->getDefaultProperties();
        foreach ($properties as $property) {
            /* @var $property \ReflectionProperty */
            $propertyName = $property->getName();
            $isOptional = FALSE;
            if (array_key_exists($propertyName, $defaultProperties) && $defaultProperties[$propertyName] !== NULL) {
                $isOptional = TRUE;
            }
            $docComment = $property->getDocComment();
            if (!empty($docComment)) {
                try {
                    $componentDefinition = $this->parseComponentFromPropertyAnnotations($property, $docComment, $reflectionClass);
                    if (!empty($componentDefinition['component'])) {
                        $declaringClass = $property->getDeclaringClass();
                        $filename = $declaringClass->getFileName();
                        // Property annotation injection.
                        $dependency = $this->buildAnnotatedComponentInternally($componentDefinition, $filename, $namespace);
                        $property->setAccessible(TRUE);
                        $property->setValue($instance, $dependency);
                        unset($property);
                    }
                }
                catch (DependencyInjectionException $ex) {
                    if ($isOptional) {
                        continue;
                    }
                    else {
                        throw $ex;
                    }
                }
            }
        }
    }
    
    /**
     * Parse the annotations of a property in order to identify components.
     * 
     * @param \ReflectionProperty $property The property.
     * @param string $docComment The doc comments.
     * @param \ReflectionClass $reflectionClass The class of the property.
     * @return array An array of components which can be used with the method {@link AbstracDependencyInjectionContainer::buildComponent()}
     * @throws DependencyInjectionException
     */
    protected function parseComponentFromPropertyAnnotations(\ReflectionProperty $property, $docComment, \ReflectionClass $reflectionClass) {
        // Regex sample:
        // 
        //      https://regex101.com/r/vRlCMw/6
        //
        $parse = [];
        $regex = '~^
                        (?=.*'.preg_quote(self::NORMA_INJECT_ANNOTATION, '~').'
                          (?:
                              (?:
                                  (?:[^\S\n]+)?
                                  (?P<inject_component>(?<=[\s])[\S]+)
                                  (?:[^\n]*$)?
                              )
                              |
                              [\s]
                          )
                        )
                        (?:(?=.*(?P<lazy>' . preg_quote(self::NORMA_LAZY_ANNOTATION, '~') . ')
                          (?:$|[\s])
                        ))?
                        (?:(?=.*'.preg_quote(self::NORMA_SCOPE_ANNOTATION, '~').'
                            (?:
                                (?:
                                    (?:[^\S\n]+)?
                                    (?P<scope>(?<=[\s])[\S]+)
                                    (?:[^\n]*$)?
                                )
                                |
                                [\s]
                            )
                        ))?
                        (?:(?=.*'.preg_quote(self::VAR_ANNOTATION, '~').'
                          (?:
                           (?:[^\S\n]+)?
                           (?P<var_component>(?<=[\s])[\S]+)
                           (?:[^\n]*$)?
                          )?
                        ))?.*
                        $~msx';
        $matches = [];
        preg_match($regex, $docComment, $matches);
        if (
            (isset($matches['inject_component']))
        ) {
            $component = NULL;
            if (!empty($matches['inject_component'])) {
                $component = $matches['inject_component'];
            }
            else if (!empty($matches['var_component'])) {
                $component = $matches['var_component'];
            }
            if (empty($component)) {
                throw new DependencyInjectionException(
                        sprintf('The property "%1$s" of object of class "%3$s" is annotated with "%2$s", but the annotation does not define a component.',
                            $property->getName(),
                            self::NORMA_INJECT_ANNOTATION,
                            $reflectionClass->getName()
                        )
                );
            }
            $parse['component'] = $component;
            
            if (!empty($matches['lazy'])) {
                $parse['lazy'] = TRUE;
            }
            
            if (isset($matches['scope'])) {
                $parse['scope'] = $this->parseScopeAnnotation($matches['scope']);
            }
        }
        return $parse;
    }

    /**
     * {@inheritdoc}
     */
    public function call($whatToCall, $paramsMapInjection = []) {
        $return = null;
        
        $this->initClientAccess();
        
        if (
            (is_string($whatToCall) && function_exists($whatToCall))
            ||
            $this->getFrameworkUtils()->isAnonymousFunction($whatToCall)
        ) {
            // Function or closure
            $return = $this->callFunction($whatToCall, $paramsMapInjection);
        }
        else {
            // Method
            if (is_string($whatToCall) && strpos($whatToCall, '::') !== false) {
                $whatToCall = explode('::', $whatToCall);
            }
            
            if (is_array($whatToCall)) {
                $objectOrClass = $whatToCall[0];
                if (!empty($whatToCall[1])) {
                    $method = $whatToCall[1];
                }
                else {
                    $whatToCall = $objectOrClass;
                    $method = '__invoke';
                }
            }
            else {
                $objectOrClass = $whatToCall;
                $method = '__invoke';
            }
            $reflectionClass = new \ReflectionClass($objectOrClass);
            $method = $reflectionClass->getMethod($method);
            $params = $this->resolveMethodParams($reflectionClass, $method, $paramsMapInjection);
            $return = $whatToCall(...$params);
        }
        
        $this->cleanClientAccess();
        
        return $return;
    }
    
    /**
     * Calls a function.
     * 
     * @param string|\Closure $function The function's name or closure.
     * @param array $paramsMapInjection An optional map of parameters where the key is the name of the parameter and the value is the parameter
     *                                                          itself to use when calling the function.
     *                                                          The container should use this parameters instead of resolving the dependencies for parameters having the same
     *                                                          name.
     * @return mixed The return value of the called function.
     */
    protected function callFunction($function, $paramsMapInjection = []) {
        $reflectionFunction = new \ReflectionFunction($function);
        $params = $this->resolveFunctionParams($reflectionFunction, $paramsMapInjection);
        return $function(...$params);
    }
    
    /**
     * {@inheritdoc}
     */
    public function bindContextually($componentWhichNeedsAnotherComponent, $neededComponent, $componentToGive) {
        /*
         * `$componentWhichNeedsAnotherComponent` can be:
         *      - A key of container's config (class, abstract class, interface or key, e.g.: AnInterface::class, 'key.name');
         * 
         * `$neededComponent` can be:
         *      - A key of container's config (class, abstract class, interface or key, e.g.: AnInterface::class, 'key.name');
         *      - String which represents the name of a '$variableName';
         *
         * `$componentToGive` can be:
         *      - An instance of a class which implements the {@link DependencyInjectionContainerComponentInterface} interface;
         *      - An existent class;
         *      - An anonymous function which return value would be the component to use (dependecies will be injected into the anonymoys function parameters);
         *      - A primitive value or another PHP type to use as-is.
         */
        
        //`$normalizedComponent` is a string which would represent a container's contextual binding config (class, abstract class, interface or key),
        // just like for the normal keys added through {@link AbstractDependencyInjectionContainer::addConfig()}
        $normalizedComponent = $this->normalizeComponent($componentWhichNeedsAnotherComponent);
        $normalizedNeededComponent = $neededComponent;
        if (!$this->getFrameworkUtils()->strStartsWith($neededComponent, '$')) {
            $normalizedNeededComponent = $this->normalizeComponent($neededComponent);
        }
        $this->contextualBindings[$normalizedComponent][$normalizedNeededComponent] = $componentToGive;
        
        return $this;
    }

}
