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

namespace Norma\Core\Runtime;

use Norma\Core\Env\Norma;
use Norma\Core\Utils\FrameworkUtils;
use Norma\Core\Parsing\UseStatementsParser;
use Norma\DI\{AbstractDependencyInjectionContainer, DependencyInjectionContainer};
use Norma\IPC\FlockSync;
use Norma\Core\Env\EnvInterface;
use Norma\Middleware\MiddlewareLayerInterface;
use Norma\Middleware\MiddlewareLayerEnum;
use Norma\Middleware\MiddlewareLayerExecutorTrait;
use Norma\Core\Oops\ErrorCapturerInterface;
use Composer\Autoload\ClassLoader;
use Norma\AOP\Autoloading\AOPAutoloaderWrapperFactoryInterface;

/**
 * The abstract base class for Norma application's runtimes.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
abstract class AbstractRuntime implements RuntimeInterface {
    
    use MiddlewareLayerExecutorTrait;
    
    /**
     * @var EnvInterface
     */
    protected $env;
    
    /**
     * @var string
     */
    protected $normaDir;
    
    /**
     * @var string
     */
    protected $environment;
    
    /**
     * @var string
     */
    protected $runtime;
    
    /**
     * @var array
     */
    protected $containerFrameworkConfigCallables;

    /**
     * @var array
     */
    protected $containerApplicationConfigCallables;
    
    /**
     * @var ErrorCapturerInterface
     */
    protected $errorHandler;
    
    /**
     * @var bool
     */
    protected $errorOccurred = false;
    
    /**
     * Constructs a new runtime.
     * 
     * @param EnvInterface $env The environment.
     * @param ErrorCapturerInterface $errorHandler An error capturer.
     */
    public function __construct(EnvInterface $env, ErrorCapturerInterface $errorHandler) {
        $this->env = $env;
        $this->errorHandler = $errorHandler;
        
        $this->normaDir = $this->env->get('NORMA_APP_DIR') . '/norma';
        $this->environment = $this->env->get('NORMA_ENV');
        $this->runtime = $this->env->get('NORMA_RUNTIME');
        
        $this->containerFrameworkConfigCallables = [];
        $this->containerApplicationConfigCallables = [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function execute() {
        $container = $this->bootstrapApplication();
        
        $middlewareLayer = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'middleware', MiddlewareLayerInterface::class]));
        $this->registerErrorHandler($container, $middlewareLayer);
        
        $this->registerAfterExecutionMiddlewares($container, $middlewareLayer);
        
        // Runtime related configuration.
        $this->onContainerConfigured($container);
        
        $this->executeMiddlewareLayer($middlewareLayer, MiddlewareLayerEnum::BEFORE_EXECUTION, $container);
        $this->executeApplication($container);
    }
    
    /**
     * Registers the after execution middlewares.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @param MiddlewareLayerInterface $middlewareLayer The middleware layer.
     * @return void
     */
    protected function registerAfterExecutionMiddlewares(AbstractDependencyInjectionContainer $container, MiddlewareLayerInterface $middlewareLayer) {
        $self = $this;
        register_shutdown_function(function() use ($self, $container, $middlewareLayer) {
            $self->executeMiddlewareLayer($middlewareLayer, MiddlewareLayerEnum::AFTER_EXECUTION, $container);
            $self->onTermination($container);
        });
    }
    
    /**
     * Registers the error handler.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @param MiddlewareLayerInterface $middlewareLayer The middleware layer.
     * @return void
     */
    protected function registerErrorHandler(AbstractDependencyInjectionContainer $container, MiddlewareLayerInterface $middlewareLayer) {
        $self = $this;
        $this->errorHandler->onThrowable(function(\Throwable $e) use ($self, $container, $middlewareLayer) {
            $self->errorOccurred = true;
            $self->handleThrowable($e, $container, $middlewareLayer);
        });
    }
    
    /**
     * A hook called whenever an error (throwable exception or error) occurs.
     * 
     * @param \Throwable $e The throwable exception or error.
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @param MiddlewareLayerInterface $middlewareLayer The middleware layer.
     * @return void
     */
    protected function handleThrowable(\Throwable $e, AbstractDependencyInjectionContainer $container, MiddlewareLayerInterface $middlewareLayer) {
        $this->executeMiddlewareLayerWithContext($middlewareLayer, MiddlewareLayerEnum::ON_THROWABLE, $e, $container, [$e]);
        $this->onThrowable($e, $container);
    }
    
    protected function bootstrapApplication() {
        /*
         * Application's bootstrap process.
         */

        /*** 1. Framework and application's composition root bootstrap process ***/
        $container = $this->configureContainer();
        /*** /1. ***/
        
        /*** 2. AOP layer ***/
        /*
         * As soon as there's the container, the AOP layer is configured.
         */
        $this->configureAOPLayer($container);
        /*** /2. ***/
        
        /*** 3. Middlewares bootstrap process ***/
        $this->configureMiddlewares($container);
        /*** /3. ***/
        
        return $container;
    }
    
    /**
     * Configures the DI container and returns it.
     * 
     * @return AbstractDependencyInjectionContainer The DI container.
     */
    protected function configureContainer() {
        $container = $this->instantiateContainer();
        $this->configureContainerWithFrameworkConfig($container);

        /*
         * Application's DI configuration.
         */
        $this->configureContainerWithApplicationConfig($container);

        /*
         * Get the true container to use because application's code may have overridden the container with a custom one.
         */
        /* @var $trueContainer Norma\DI\AbstractDependencyInjectionContainer */
        $trueContainer = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'di', 'container', AbstractDependencyInjectionContainer::class]));
        if ($trueContainer !== $container) {
            // In the case of a custom container, the configuration process is repeated with the new container.
            $currentContainerConfig = $container->getConfig();
            $trueContainer->addConfig($currentContainerConfig);
            $this->configureContainerWithFrameworkConfig($trueContainer);
            $this->configureContainerWithApplicationConfig($trueContainer);
            $container = $trueContainer;
        }
        else {
            unset($trueContainer);
        }
        
        return $container;
    }
    
    /**
     * Instantiates the DI container.
     * 
     * @return AbstractDependencyInjectionContainer The DI container.
     */
    protected function instantiateContainer(): AbstractDependencyInjectionContainer {
        $flock = new FlockSync();
        $utils = new FrameworkUtils();
        $useStatementsParser = new UseStatementsParser($utils, $flock);
        $container = new DependencyInjectionContainer($utils, $flock, $useStatementsParser);
        
        return $container;
    }
    
    /**
     * Configures the DI container with the framework's components.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @return void
     */
    protected function configureContainerWithFrameworkConfig(AbstractDependencyInjectionContainer $container) {
        if (!empty($this->containerFrameworkConfigCallables)) {
            foreach ($this->containerFrameworkConfigCallables as $frameworkConfigCallable) {
                $frameworkConfig = $frameworkConfigCallable($container, $this->env);
                $container->addConfig($frameworkConfig ?? []);
            }
        }
        else {
            $frameworkConfigCallable = (require_once $this->normaDir . '/config/di.php') ?? function() {};
            $this->containerFrameworkConfigCallables[] = $frameworkConfigCallable;
            $frameworkConfig = $frameworkConfigCallable($container, $this->env);
            $container->addConfig($frameworkConfig ?? []);

            $runtimeConfig = $this->normaDir . '/config/runtime-' . $this->runtime . '/di.php';
            if (file_exists($runtimeConfig)) {
                $frameworkConfigCallable = (require_once $runtimeConfig) ?? function() {};
                $this->containerFrameworkConfigCallables[] = $frameworkConfigCallable;
                $frameworkConfig = $frameworkConfigCallable($container, $this->env);
                $container->addConfig($frameworkConfig ?? []);
            }
        }
    }
    
    /**
     * Configures the DI container with the application's components.
     * 
     * @param AbstractDependencyInjectionContainer $container
     * @return void
     */
    protected function configureContainerWithApplicationConfig(AbstractDependencyInjectionContainer $container) {
        if (!empty($this->containerApplicationConfigCallables)) {
            foreach ($this->containerApplicationConfigCallables as $appConfigCallable) {
                $appConfig = $container->call($appConfigCallable);
                $container->addConfig($appConfig ?? []);
            }
        }
        else {
            $appConfigCallable = (require_once $this->normaDir . '/app/config/di.php') ?? function() {};
            $this->containerApplicationConfigCallables[] = $appConfigCallable;
            $appConfig = $container->call($appConfigCallable);
            $container->addConfig($appConfig ?? []);

            $furtherConfigs = [
                $this->normaDir . '/app/config/' . $this->environment . '/di.php',
                $this->normaDir . '/app/config/runtime-' . $this->runtime . '/di.php',
                $this->normaDir . '/app/config/runtime-' . $this->runtime . '/' . $this->environment . '/di.php',
            ];
            foreach ($furtherConfigs as $furtherConfig) {
                if (file_exists($furtherConfig)) {
                    $appConfigCallable = (require_once $furtherConfig) ?? function() {};
                    $this->containerApplicationConfigCallables[] = $appConfigCallable;
                    $appConfig = $container->call($appConfigCallable);
                    $container->addConfig($appConfig ?? []);
                }
            }   
        }
    }
    
    /**
     * Registers the middlewares for the given middleware layer.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @param MiddlewareLayerInterface $middlewareLayer The middleware layer instance where to register the middlewares.
     * @param string $configFile The configuration file of the middlewares.
     * @param bool $skipIfConfigFileDoesNotExist A boolean indicating whether to skip or not to skip if the given configuration file does not exist (the default value is not to skip).
     * @param bool $override Whether to override the same middleware layer with the given middlewares of not.
     *                                      If FALSE, the given middlewares MUST be appended to the current queue of middlewares.
     *                                      Otherwise, the given middlewares MUST override the current middlewares of the same middleware layer.
     * @return void
     */
    protected function registerMiddlewares(AbstractDependencyInjectionContainer $container, MiddlewareLayerInterface $middlewareLayer, $configFile, $skipIfConfigFileDoesNotExist = FALSE, $override = FALSE) {
        if (!$skipIfConfigFileDoesNotExist || file_exists($configFile)) {
            /* @var $middlewareLayer MiddlewareLayerInterface */
            $middlewaresConfigCallable = (require_once $configFile) ?? function() {};
            $middlewaresConfig = $container->call($middlewaresConfigCallable);
            foreach ($middlewaresConfig as $middlewareLayerCode => $middlewares) {
                $middlewareLayer->register($middlewareLayerCode, $middlewares, $override);
            }
        }
    }
    
    /**
     * Configures the AOP layer of the application.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @return void
     */
    protected function configureAOPLayer(AbstractDependencyInjectionContainer $container) {
        /*
         * Steps of AOP layer configuration:
         * 
         * 1) Registering the autoloader wrapper;
         * 
         * 2) Registering the aspect weaver;
         * 
         * 3) Registering all the aspects;
         */
        $this->registerAspects();
        $this->registerAOPAutoloaderWrapper($container);
        $this->registerAspectWeaver();
    }
    
    /**
     * Registers the AOP autoloader wrapper.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @return void
     * @throws 
     */
    protected function registerAOPAutoloaderWrapper(AbstractDependencyInjectionContainer $container) {
        // TODO: review
        $atLeastOneClassLoaderWasWrapped = FALSE;
        $registeredAutoloaders = spl_autoload_functions() ?? [];
        $autoloadersToReregister = [];
        foreach ($registeredAutoloaders as $registeredAutoloader) {
            $registeredAutoloaderToUnregister = $registeredAutoloader;
            if (is_array($registeredAutoloader) && ($registeredAutoloader[0] instanceof ClassLoader)) {
                $classLoader = $registeredAutoloader[0];
                $factory = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'aop', AOPAutoloaderWrapperFactoryInterface::class]));
                $autoloader = $factory->makeAOPAutoloaderWrapper($classLoader);
                $registeredAutoloader[0] = $autoloader;
                $atLeastOneClassLoaderWasWrapped = TRUE;
            }
            $autoloadersToReregister[] = $registeredAutoloader;
            
            spl_autoload_unregister($registeredAutoloaderToUnregister);
        }
        
        foreach ($autoloadersToReregister as $autoloaderToReregister) {
            spl_autoload_register($autoloaderToReregister);
        }
        
        if (!$atLeastOneClassLoaderWasWrapped) {
            throw new \RuntimeException(sprintf('No class loader of type "%s" was found when registering the AOP autoloader wrapper.', ClassLoader::class));
        }
    }
    
    /**
     * Registers the aspect weaver.
     * 
     * @return void
     */
    protected function registerAspectWeaver() {
        // TODO: implement
        
    }
    
    /**
     * Registers the aspects of the AOP layer.
     * 
     * @return void
     */
    protected function registerAspects() {
        // TODO: implement
        
    }
    
    /**
     * Configures the generic middlewares.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @return void
     */
    protected function configureMiddlewares(AbstractDependencyInjectionContainer $container) {
        $middlewareLayer = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'middleware', MiddlewareLayerInterface::class]));
        $this->registerMiddlewares(
            $container,
            $middlewareLayer,
            $this->normaDir . '/app/config/middlewares.php'
        );
        
        $this->registerMiddlewares(
            $container,
            $middlewareLayer,
            $this->normaDir . '/app/config/' . $this->environment . '/middlewares.php',
            TRUE
        );
    }
    
    /**
     * A method called once the DI container has been configured.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @return void
     */
    protected abstract function onContainerConfigured(AbstractDependencyInjectionContainer $container);
    
    /**
     * Executes the application.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @return void
     */
    protected abstract function executeApplication(AbstractDependencyInjectionContainer $container);
    
    /**
     * A hook called whenever an error (throwable exception or error) occurs.
     * 
     * @param \Throwable $e The throwable exception or error.
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @return void
     */
    abstract protected function onThrowable(\Throwable $e, AbstractDependencyInjectionContainer $container);
    
    /**
     * A hook called whenever the application is completing its execution.
     * This is a good hook for executing after middlewares.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @return void
     */
    abstract protected function onTermination(AbstractDependencyInjectionContainer $container);
    
}
