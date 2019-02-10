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
use Norma\IPC\FlockSync;
use Norma\Core\Parsing\UseStatementsParserInterface;

/**
 * The implementation of the Norma's DI container.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class DependencyInjectionContainer extends AbstractDependencyInjectionContainer {
    
    /**
     * Namespace of dynamic proxy classes.
     */
    const DYN_PROXY_CACHE_NAMESPACE_PREFIX = __NAMESPACE__ . '\\Cache\\DynamicProxy';
    
    /**
     * Directory of the cache.
     */
    const CACHE_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'cache';
    
    /**
     * Base directory of the cache of the dynamic proxy classes.
     */
    const CACHE_DYN_PROXY_DIR = self::CACHE_DIR . DIRECTORY_SEPARATOR . 'dyn_proxy';
    
    /**
     * Directory of the cache of the dynamic proxy classes from which to create the dynamically generated code.
     */
    const CACHE_DYN_PROXY_CODE_DIR = self::CACHE_DYN_PROXY_DIR . DIRECTORY_SEPARATOR . 'code';
    
    /**
     * Directory of the cache of the dynamic proxy classes where to store lock files.
     */
    const CACHE_DYN_PROXY_LOCK_DIR = self::CACHE_DYN_PROXY_DIR . DIRECTORY_SEPARATOR . 'lock';
    
    /**
     * The separator to use between dynamic proxy classes and the SHA1 hash.
     */
    const DYN_PROXY_CLASS_FILE_SHA1_SEPARATOR = '_';
    
    /**
     * The proxy lock file.
     */
    const DYN_PROXY_FILE_OPERATION_LOCK_FILE_NAME_PREFIX = 'dyn_proxy_class_file_lock_operation_';
    
    /**
     * @var array PHP's builtin magic methods.
     */
    protected static $builtinMagicMethodsToOverride = [
        '__call',
        '__callStatic',
        '__clone',
        '__debugInfo',
        '__destruct',
        '__get',
        '__invoke',
        '__isset',
        '__set',
        '__set_state',
        '__sleep',
        '__toString',
        '__unset',
        '__wakeup'
    ];

    /**
     * @var FrameworkUtils
     */
    protected $utils;
    
    /**
     * @var FlockSync
     */
    protected $flock;
    
    /**
     * @var UseStatementsParserInterface
     */
    protected $useStatementsParser;
    
    /**
     * Construct a new DI container.
     * 
     * @param FrameworkUtils $utils A utility object used by this DI container.
     * @param FlockSync $flock A FlockSync object used to synchronize internal dynamic proxy classes' code caching procedures.
     * @param UseStatementsParserInterface $useStatementsParser A use statements parser.
     */
    public function __construct(FrameworkUtils $utils, FlockSync $flock, UseStatementsParserInterface $useStatementsParser) {
        $this->utils = $utils;
        $this->flock = $flock;
        $this->useStatementsParser = $useStatementsParser;
        
        $storeTree = [
            'norma' => [
                'framework' => [
                    'di' => [
                        'container' => [
                            AbstractDependencyInjectionContainer::class => $this
                        ]
                    ],
                    'core' => [
                        'utils' => [
                            FrameworkUtils::class => $this->utils
                        ],
                        'parsing' => [
                            UseStatementsParserInterface::class => $this->useStatementsParser
                        ],
                        'ipc' => [
                            FlockSync::class => $this->flock
                        ]
                    ]
                ]
            ]
        ];
        $this->initConfigAndStoreFromStoreTree($storeTree);
    }
    
    /**
     * Initializes the configuration and the internal store of this container's implementation using a store tree.
     * 
     * @param array $storeTree The store tree.
     * @return void
     */
    protected function initConfigAndStoreFromStoreTree($storeTree) {
        $this->config = [];
        
        $loosenStore = $this->utils->loosenMultiDimensionalArrayPathForEachVal($storeTree);
        $config = [];
        foreach ($loosenStore as $loosenStoreEntry) {
            list($component, $componentPath) = $loosenStoreEntry;
            
            if (is_object($component)) {
                $configLeafComponent = get_class($component);
            }
            else {
                $configLeafComponent = $component;
            }
            
            $countPathKeyCount = count($componentPath);
            $leafComponentKey = $componentPath[$countPathKeyCount - 1];
            $normalizedLeafComponentKey = $this->normalizeComponent($leafComponentKey);
            $componentPath[$countPathKeyCount - 1] = $normalizedLeafComponentKey;
            $storeKey = implode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, $componentPath);
            $store[$storeKey] = $component;
            
            $keys = array_merge($componentPath, [$configLeafComponent]);
            $this->utils->arrayMultiDim($config, ...$keys);
        }
        $this->addConfig($config);
        $this->store = $store;
    }

    /**
     * {@inheritdoc}
     */
    public function createLazyComponent($componentToResolve, $normalizedComponent) {
        $reflectionClass = new \ReflectionClass($componentToResolve);
        $filename = $reflectionClass->getFileName();
        $namespace = self::DYN_PROXY_CACHE_NAMESPACE_PREFIX . '\\' . $reflectionClass->getNamespaceName();
        
        if ($reflectionClass->isFinal()) {
            throw new DependencyInjectionException(
                sprintf('Could not instantiate component "%1$s" lazily as the class is declared as final.', $componentToResolve)
            );
        }

        $lastModTs = filemtime($filename);

        $basenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
        $sha1 = sha1($filename . $lastModTs);

        $dynProxyBasenameWithoutExtension = $basenameWithoutExtension . self::DYN_PROXY_CLASS_FILE_SHA1_SEPARATOR . $sha1;

        // 1. If the class definition is already in memory. It will be used for the current request.
        $FQDynProxyClassName = $namespace . '\\' . $dynProxyBasenameWithoutExtension;
        $containerReflection = new \ReflectionClass($this);
        if (class_exists($FQDynProxyClassName, FALSE)) {
            return $this->createDynProxyInjectionComponentInstance($FQDynProxyClassName, $containerReflection);
        }

        // 2. If the cache file doesn't exists, or it was changed because an update was made to the original class.        
        $namespaceParts = explode('\\', $namespace);
        $pathToCacheSubDir = self::CACHE_DYN_PROXY_CODE_DIR . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $namespaceParts);
        $cacheFilename = $pathToCacheSubDir . DIRECTORY_SEPARATOR . $dynProxyBasenameWithoutExtension . '.php';
        if (file_exists($cacheFilename)) {
            require_once $cacheFilename;
        }
        else {
            $closureSync = \Closure::bind(function() use (
                    $cacheFilename, $namespace,
                    $componentToResolve, $reflectionClass, $basenameWithoutExtension,
                    $pathToCacheSubDir, $dynProxyBasenameWithoutExtension, $normalizedComponent, $containerReflection
            ) {
                // 2.1. Generate the dynamic proxy code.
                $dynProxyClassCode = $this->generateDynProxyClassCode($namespace, $dynProxyBasenameWithoutExtension, $componentToResolve, $reflectionClass, $normalizedComponent, $containerReflection);

                // 2.2. Create the new file and require it.
                //        Must be sure that before clearing there isn't another process which uses one of the files.
                $tmpCacheFilename = $cacheFilename . '.tmp';
                $ret = $this->utils->filePutContentsMissingDirectories($tmpCacheFilename, $dynProxyClassCode) && rename($tmpCacheFilename, $cacheFilename);
                if (!$ret) {
                    throw new DependencyInjectionException(
                        sprintf('Could not create dynamic proxy class file "%1$s" required for lazy component "%2$s".', $cacheFilename, $componentToResolve)
                    );
                }

                // 2.3. Clear existing files of the same class which now won't be used anymore.
                //        Must be sure that before clearing there isn't another process which uses one of the files, therefore this code is
                //        within a synchronized closure's block.
                $this->clearOldCachedDynProxyClasses($pathToCacheSubDir, $basenameWithoutExtension, $cacheFilename);

                require_once $cacheFilename;
            }, $this, self::class);
            $closureNonBlocking = \Closure::bind(function() use ($namespace, $dynProxyBasenameWithoutExtension, $componentToResolve, $reflectionClass, $normalizedComponent, $containerReflection) {
                // 2.4. If the lock is already acquired by another process, don't wait, just eval the dynamically generated code.
                //        We don't want to let clients wait for any reason. The next time maybe the file will probably already be written
                //        therefore the cached dynamic proxy class will be obtained from it.
                $dynProxyClassCode = $this->generateDynProxyClassCode($namespace, $dynProxyBasenameWithoutExtension, $componentToResolve, $reflectionClass, $normalizedComponent, $containerReflection);
                eval($dynProxyClassCode);
            }, $this, self::class);
            
            $fileContents = getmypid() . ':' . $FQDynProxyClassName;
            $lockFile = self::CACHE_DYN_PROXY_LOCK_DIR . DIRECTORY_SEPARATOR . self::DYN_PROXY_FILE_OPERATION_LOCK_FILE_NAME_PREFIX . sha1($FQDynProxyClassName);
            $this->flock->synchronize($lockFile, $closureSync, $fileContents, TRUE, $closureNonBlocking);
        }

        // 2.5. Create the dynamic proxy instance.
        return $this->createDynProxyInjectionComponentInstance($FQDynProxyClassName, $containerReflection);
    }
    
    /**
     * Generate the dynamic proxy class code.
     * 
     * @param string $namespace The namespace name of the class.
     * @param string $dynProxyBasenameWithoutExtension The name of the dynamic proxy class.
     * @param string $componentToResolve The name of the component to resolve.
     * @param \ReflectionClass $reflectionClass The reflection class instance of the component to resolve.
     * @param string $normalizedComponent The normalized component with whom the component to resolve is associated with within the container.
     * @param \ReflectionClass $containerReflection The container's reflection class.
     * @return string The dynamically generated code.
     */
    protected function generateDynProxyClassCode($namespace, $dynProxyBasenameWithoutExtension, $componentToResolve, \ReflectionClass $reflectionClass, $normalizedComponent, \ReflectionClass $containerReflection) {
        $normalizedComponentParsable = var_export($normalizedComponent, TRUE);
        $componentToResolveParsable = var_export($componentToResolve, TRUE);
        
        $lazyInstanceName = 'lazyInstance'.sha1($dynProxyBasenameWithoutExtension.time());
        
        $classShortName = $reflectionClass->getShortName();
        
        /*
         * Inspired by Phockito.
         * 
         * @source https://github.com/hafriedlander/phockito/blob/master/Phockito.php#L170
         */
        $containerReflectionName = $containerReflection->getName();
        $codeArray[] = <<<HERECODE
<?php

namespace $namespace;

use {$containerReflectionName};
HERECODE;

        $reflectionClassName = $reflectionClass->getName();
        if ($reflectionClassName !== $containerReflectionName) {
            $codeArray[] = <<<HERECODE
use {$reflectionClassName};
HERECODE;
        }
        $codeArray[] = <<<HERECODE
class $dynProxyBasenameWithoutExtension extends {$classShortName} {

    protected \${$lazyInstanceName}Container;
    protected \${$lazyInstanceName}ContainerReflection;
    protected \${$lazyInstanceName}Obj;
    protected \${$lazyInstanceName}Serialization;

    public function __construct({$containerReflection->getShortName()} \$container, \ReflectionClass \$containerReflection) {
        \$this->{$lazyInstanceName}Container = \$container;
        \$this->{$lazyInstanceName}ContainerReflection = \$containerReflection;
        \$this->{$lazyInstanceName}Init();
    }

    protected function {$lazyInstanceName}Init() {
        \$this->{$lazyInstanceName}Obj = NULL;
    }
    
    protected function {$lazyInstanceName}Method() {
        if (!empty(\$this->{$lazyInstanceName}Serialization)) {
            \$this->{$lazyInstanceName}Obj = unserialize(\$this->{$lazyInstanceName}Serialization);
            \$this->{$lazyInstanceName}UpdateContainer();
        }
        else if (is_null(\$this->{$lazyInstanceName}Obj)) {
            \$resolveMethod = \$this->{$lazyInstanceName}ContainerReflection->getMethod('resolveClass');
            \$resolveMethod->setAccessible(TRUE);
            
            // Adding the context.
            \$componentFromResolvableMethod = \$this->{$lazyInstanceName}ContainerReflection->getMethod('getComponentFromResolvable');
            \$componentFromResolvableMethod->setAccessible(TRUE);
            \$resolvableComponent = \$componentFromResolvableMethod->invoke(\$this->{$lazyInstanceName}Container, $componentToResolveParsable);

            \$contextualBindingsPropertyReflection = \$this->{$lazyInstanceName}ContainerReflection->getProperty('contextualBindings');
            \$contextualBindingsPropertyReflection->setAccessible(TRUE);
            \$containerContextualBindings = \$contextualBindingsPropertyReflection->getValue(\$this->{$lazyInstanceName}Container);
            
            \$contextualBindingsStacksPropertyReflection = \$this->{$lazyInstanceName}ContainerReflection->getProperty('contextualBindingsStacks');
            \$contextualBindingsStacksPropertyReflection->setAccessible(TRUE);
            \$contextualBindingsStacks = \$contextualBindingsStacksPropertyReflection->getValue(\$this->{$lazyInstanceName}Container);
            
            \$pendingClientGetIdentifierPropertyReflection = \$this->{$lazyInstanceName}ContainerReflection->getProperty('pendingClientGetIdentifier');
            \$pendingClientGetIdentifierPropertyReflection->setAccessible(TRUE);
            \$pendingClientGetIdentifier = \$pendingClientGetIdentifierPropertyReflection->getValue(\$this->{$lazyInstanceName}Container);
            
            \$pendingClientGetIdentifier++;
            \$pendingClientGetIdentifierPropertyReflection->setValue(\$this->{$lazyInstanceName}Container, \$pendingClientGetIdentifier);
            
            \$contextualBindingsStacks[\$pendingClientGetIdentifier] = [];
            \$contextualBindingsStacks[\$pendingClientGetIdentifier] = new \SplStack();
            \$contextualBindingsStacks[\$pendingClientGetIdentifier]->push(NULL); // First context is NULL, as when the lazy component is resolved method is called from the client's code.
            
            if (
                (is_string(\$resolvableComponent) || is_int(\$resolvableComponent)) && array_key_exists(\$resolvableComponent, \$containerContextualBindings)
            ) {
                // More specific binding.
                \$contextualBindingsStacks[\$pendingClientGetIdentifier]->push(\$resolvableComponent);
            }
            else if (array_key_exists($normalizedComponentParsable, \$containerContextualBindings)) {
                // Contextual binding configuration.
                \$contextualBindingsStacks[\$pendingClientGetIdentifier]->push($normalizedComponentParsable);
            }
            else {
                // Fallback to null.
                \$contextualBindingsStacks[\$pendingClientGetIdentifier]->push(NULL);
            }
            
            \$contextualBindingsStacksPropertyReflection->setValue(\$this->{$lazyInstanceName}Container, \$contextualBindingsStacks);
            
            \$obj = \$resolveMethod->invoke(\$this->{$lazyInstanceName}Container, $componentToResolveParsable);
            \$this->{$lazyInstanceName}Obj = \$obj;
            \$this->{$lazyInstanceName}UpdateContainer();
            
            \$pendingClientGetIdentifier = \$pendingClientGetIdentifierPropertyReflection->getValue(\$this->{$lazyInstanceName}Container);
            \$pendingClientGetIdentifier--;
            \$pendingClientGetIdentifierPropertyReflection->setValue(\$this->{$lazyInstanceName}Container, \$pendingClientGetIdentifier);
            
            unset(\$resolveMethod, \$componentFromResolvableMethod, \$contextualBindingsPropertyReflection, \$contextualBindingsStacksPropertyReflection, \$pendingClientGetIdentifierPropertyReflection);
        }
    }
    
    protected function {$lazyInstanceName}UpdateContainer() {
        \$propertyReflection = \$this->{$lazyInstanceName}ContainerReflection->getProperty('store');
        \$propertyReflection->setAccessible(TRUE);
        \$store = \$propertyReflection->getValue(\$this->{$lazyInstanceName}Container);
        \$store[$normalizedComponentParsable] = \$this->{$lazyInstanceName}Obj;
        \$propertyReflection->setValue(\$this->{$lazyInstanceName}Container, \$store);
        unset(\$propertyReflection);
    }
HERECODE;
        
        $builtinMagicMethods = self::$builtinMagicMethodsToOverride;
        $missingMagicMethods = array_flip($builtinMagicMethods);
        $reflectionMethods = $reflectionClass->getMethods();
        foreach ($reflectionMethods as $reflectionMethod) {
            $methodCodeArray = [];
            if ($reflectionMethod->isPrivate()) {
                // We are not interested in private methods, they can't be called.
                continue;
            }
            
            $methodName = $reflectionMethod->getName();
            if ($methodName == '__construct') {
                // The constructor has already been defined.
                continue;
            }
            if ($reflectionMethod->isFinal()) {
                throw new DependencyInjectionException(
                    sprintf('Could not create dynamic proxy class required for lazy component "%1$s" because the component has a final method named "%2$s".', $componentToResolve, $methodName)
                );
            }
            
            $modifiers = implode(' ', \Reflection::getModifierNames($reflectionMethod->getModifiers() & ~(\ReflectionMethod::IS_ABSTRACT)));
            $returnByRef = $reflectionMethod->returnsReference() ? '&' : '';
            
            $methodParamsDef = [];
            $methodCallParams = [];
            $reflectionParameters = $reflectionMethod->getParameters();
            foreach ($reflectionParameters as $reflectionParameter) {
                /* @var $reflectionParameter \ReflectionParameter */
                $reflectionParameterName = $reflectionParameter->getName();
                
                $isVariadic = $reflectionParameter->isVariadic();
                
                $methodCallParams[] = ($isVariadic ? '...' : '') . '$' . $reflectionParameterName;
                
                if ($reflectionParameter->isArray()) {
                    $type = 'array ';
                }
                else if ($reflectionParameterClass = $reflectionParameter->getClass()) {
                    $type = '\\' . $reflectionParameterClass->getName() . ' ';
                }
                else if ($reflectionParameterType = $reflectionParameter->getType()) {
                    $type = $reflectionParameterType . ' ';
                }
                else {
                    $type = '';
                }
                
                try {
                    $defaultValue = $reflectionParameter->getDefaultValue();
                }
                catch (\ReflectionException $e) {
                    $defaultValue = null;
                }
                
                $methodParamsDef[] = $type .
                        ($reflectionParameter->isPassedByReference() ? '&' : '') .
                        ($isVariadic ? '...' : '') .
                        '$' . $reflectionParameterName .
                        ($reflectionParameter->isOptional() && !$isVariadic ? '=' . var_export($defaultValue, TRUE) : '');
            }
            
            $methodParamsDefCodeStr = implode(', ', $methodParamsDef);
            $methodCallParamsCodeStr = implode(', ', $methodCallParams);
            
            $returnType = '';
            $reflectionType = $reflectionMethod->getReturnType();
            if (!is_null($reflectionType)) {
                if ($reflectionType->isBuiltin()) {
                    $returnType = ': ' . $reflectionType;
                }
                else {
                    $returnType = ': ' . '\\' . $reflectionType;
                }
            }
            
            if (isset($missingMagicMethods[$methodName])) {
                $methodCodeArray[] = $this->generateDynProxyClassMagicMethodCode($methodName, $lazyInstanceName, $classShortName,
                        $modifiers, $returnByRef, $methodParamsDefCodeStr, $methodCallParamsCodeStr, $returnType);
                unset($missingMagicMethods[$methodName]);
            }
            else {
                $methodCodeArray[] = <<<HERECODE
    $modifiers function $returnByRef$methodName($methodParamsDefCodeStr){$returnType} {
HERECODE;
                    if ($reflectionMethod->isStatic()) {
                        // Static method.
                        $methodCodeArray[] = <<<HERECODE
        return {$classShortName}::$methodName($methodCallParamsCodeStr);
HERECODE;
                    }
                    else {
                        // Instance method.
                        $methodCodeArray[] = <<<HERECODE
        \$this->{$lazyInstanceName}Method();
        return \$this->{$lazyInstanceName}Obj->$methodName($methodCallParamsCodeStr);
HERECODE;
                    }
            }

            $methodCodeArray[] = <<<HERECODE
    }
HERECODE;
            $codeArray[] = implode("\n", $methodCodeArray);
        }
        
        $reallyMissingMagicMethods = array_keys($missingMagicMethods);
        foreach ($reallyMissingMagicMethods as $missingMagicMethod) {
            $methodCodeArray = [];
            $methodCodeArray[] = $this->generateDynProxyClassMagicMethodCode($missingMagicMethod, $lazyInstanceName, $classShortName);
            $methodCodeArray[] = <<<HERECODE
    }
HERECODE;
            $codeArray[] = implode("\n", $methodCodeArray);
        }
        
        $codeArray[] = '}';
        return implode("\n\n", $codeArray);
    }
    
    /**
     * Generate the code for a magic method of a dynamic proxy class.
     * 
     * @param string $methodName The name of the magic method.
     * @param string $lazyInstanceName The name of the lazy instance method.
     * @param string $classShortName The short name of the class of the component which the dynamic proxy classes extends.
     * @param string|null $modifiers The modifiers of the method or `null` for default modifiers depending on the magic method.
     * @param string $returnByRef A string identifying whether the method should return by reference or not.
     * @param string $methodParamsDefCodeStr A parsable comma-separated list of the name of the method's arguments.
     * @param string $methodCallParamsCodeStr A parsable comma-separated list of the method arguments to use within the method body if needed.
     * @return string $returnType The return type of the method.
     * @throws DependencyInjectionException If this method is called with a `$methodName` which is not a PHP magic method.
     *                                                                 {@link http://php.net/manual/en/language.oop5.magic.php}
     */
    protected function generateDynProxyClassMagicMethodCode($methodName, $lazyInstanceName, $classShortName,
            $modifiers = NULL, $returnByRef = '', $methodParamsDefCodeStr = '', $methodCallParamsCodeStr = '', $returnType = '') {
        $methodCode = "";
        switch ($methodName) {
            case '__call':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName(\$name, \$arguments)$returnType {
        \$this->{$lazyInstanceName}Method();
        return \$this->{$lazyInstanceName}Obj->{\$name}(...\$arguments);
HERECODE;
                break;
            case '__callStatic': // static
                $modifiers = is_null($modifiers) ? 'public static' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName(\$name, \$arguments)$returnType {
        return {$classShortName}::{\$name}(...\$arguments);
HERECODE;
                break;
            case '__clone':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName()$returnType {
        \$this->{$lazyInstanceName}Method();
        \$this->{$lazyInstanceName}Obj = clone \$this->{$lazyInstanceName}Obj;
HERECODE;
                break;
            case '__debugInfo':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodName = 'THE MAGIC METHOD __debugInfo IS INTENTIONALLY LEFT UNIMPLEMENTED_'.$lazyInstanceName;
                $methodCode = <<<HERECODE
    /**
     * __debugInfo is not implemented because there is a known issue with Xdebug.
     * 
     * "Using __debugInfo is a problem for all XDebug users because it causes many problems and
     *  unwanted side effects (like the database lastInsertId bug).
     *  I hope the CakePHP team will soon remove the __debugInfo() method everywhere in the code base." - Cit. odan
     * 
     * See:
     * 
     *      https://github.com/cakephp/chronos/issues/164#issuecomment-376905110
     * 
     */
    $modifiers function $returnByRef $methodName()$returnType {
        \$this->{$lazyInstanceName}Method();
        if (method_exists(\$this->{$lazyInstanceName}Obj, '__debugInfo')) {
            var_dump(\$this->{$lazyInstanceName}Obj);
            return \$this->{$lazyInstanceName}Obj->__debugInfo();
        }
        else {
            \$reflectionClass = new \ReflectionClass(\$this->{$lazyInstanceName}Obj);
            \$properties = \$reflectionClass->getProperties();
            \$array = [];
            foreach (\$properties as \$property) {
                \$propertyName = \$property->getName();
                \$property->setAccessible(TRUE);
                \$propertyValue = \$property->getValue(\$this->{$lazyInstanceName}Obj);
                \$array[\$propertyName] = \$propertyValue;
            }
            return \$array;
        }
HERECODE;
                break;
            case '__destruct':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName()$returnType {
        if (!is_null(\$this->{$lazyInstanceName}Obj)) {
            \$this->{$lazyInstanceName}Obj = NULL;
        }
HERECODE;
                break;
            case '__get':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName(\$name)$returnType {
        \$this->{$lazyInstanceName}Method();
        return \$this->{$lazyInstanceName}Obj->\$name;
HERECODE;
                break;
            case '__invoke':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName($methodParamsDefCodeStr)$returnType {
        \$this->{$lazyInstanceName}Method();
        \$obj = \$this->{$lazyInstanceName}Obj;
        return \$obj($methodCallParamsCodeStr);
HERECODE;
                break;
            case '__isset':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName(\$name)$returnType {
        \$this->{$lazyInstanceName}Method();
        return isset(\$this->{$lazyInstanceName}Obj->{\$name});
HERECODE;
                break;
            case '__set':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName(\$name, \$value)$returnType {
        \$this->{$lazyInstanceName}Method();
        \$this->{$lazyInstanceName}Obj->{\$name} = \$value;
HERECODE;
                break;
            case '__set_state': // static
                $modifiers = is_null($modifiers) ? 'public static' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName(\$properties)$returnType {
        return {$classShortName}::{$methodName}(\$properties);
HERECODE;
                break;
            case '__sleep':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName($methodParamsDefCodeStr)$returnType {
        \$this->{$lazyInstanceName}Method();
        \$this->{$lazyInstanceName}Serialization = serialize(\$this->{$lazyInstanceName}Obj);
        return \array_merge(parent::__sleep(),
            [
                '{$lazyInstanceName}Serialization',
                '{$lazyInstanceName}Container',
                '{$lazyInstanceName}ContainerReflection',
            ]
        );
HERECODE;
                break;
            case '__toString':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName($methodParamsDefCodeStr)$returnType {
        \$this->{$lazyInstanceName}Method();
        return (string)\$this->{$lazyInstanceName}Obj;
HERECODE;
                break;
            case '__unset':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName(\$name)$returnType {
        \$this->{$lazyInstanceName}Method();
        unset(\$this->{$lazyInstanceName}Obj->{\$name});
HERECODE;
                break;
            case '__wakeup':
                $modifiers = is_null($modifiers) ? 'public' : $modifiers;
                $methodCode = <<<HERECODE
    $modifiers function $returnByRef $methodName($methodParamsDefCodeStr)$returnType {
        \$this->{$lazyInstanceName}Init();
        \$this->{$lazyInstanceName}Method();
HERECODE;
                break;
            default:
                throw new DependencyInjectionException(
                    sprintf('Anomaly. Missing dynamic proxy method implementation for magic method "%1$s" within the container of class "%2$s". The method might be new and is not supported by the framework.', $methodName, self::class)
                );
        }
        return $methodCode;
    }
    
    /**
     * Clear the old cached dynamic proxy classes created for a component.
     * 
     * @param string $pathToCacheSubDir The path where the classes have been created.
     * @param string $basenameWithoutExtension The common basename of the classes.
     * @param string $cacheFilename The new dynamic proxy file in the same directory.
     * @return void
     */
    protected function clearOldCachedDynProxyClasses($pathToCacheSubDir, $basenameWithoutExtension, $cacheFilename) {
        $this->utils->deleteFilesStartingWith($basenameWithoutExtension . self::DYN_PROXY_CLASS_FILE_SHA1_SEPARATOR, $pathToCacheSubDir, [
            $cacheFilename
        ]);
    }
    
    /**
     * Creates an instance of the dynamic proxy class.
     * 
     * @param string $FQDynProxyClassName The fully qualified name of the dynamic proxy class.
     * @param ReflectionClass $containerReflection The container's reflection class.
     * @return object The dynamic proxy instance.
     */
    protected function createDynProxyInjectionComponentInstance($FQDynProxyClassName, \ReflectionClass $containerReflection) {
        $obj = new $FQDynProxyClassName($this, $containerReflection);
        return $obj;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrameworkUtils(): FrameworkUtils {
        return $this->utils;
    }

    /**
     * {@inheritdoc}
     */
    public function parseUseStatements($filename, $namespace) {
        return $this->useStatementsParser->parseUseStatements($filename, $namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig() {
        $config = [];
        foreach ($this->config as $key => $leaf) {
            $qualifiedPathToLeafKeys = explode(self::QUALIFIED_COMPONENT_KEY_SEPARATOR, $key);
            $merge = array_merge($qualifiedPathToLeafKeys, [$leaf]);
            $this->utils->arrayMultiDim($config, ...$merge);
        }
        return $config;
    }

}
