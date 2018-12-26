<?php

/*
 * Copyright (c) 2018 Anton Bagdatyev
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
use Norma\Core\Autoloading\AutoloaderInterface;
use Norma\Core\Env\EnvInterface;
use Norma\Middleware\MiddlewareLayerInterface;
use Norma\Middleware\MiddlewareLayerEnum;
use Norma\Middleware\MiddlewareLayerExecutorTrait;
use Norma\Core\Oops\ErrorCapturerInterface;

/**
 * The abstract base class for Norma application's runtimes.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
abstract class AbstractRuntime implements RuntimeInterface {
    
    use MiddlewareLayerExecutorTrait;
    
    /**
     * @var EnvInterface
     */
    protected $env;
    
    /**
     * @var AutoloaderInterface 
     */
    protected $autoloader;
    
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
     * @param AutoloaderInterface $autoloader An autoloader.
     * @param ErrorCapturerInterface $errorHandler An error capturer.
     */
    public function __construct(EnvInterface $env, AutoloaderInterface $autoloader, ErrorCapturerInterface $errorHandler) {
        $this->env = $env;
        $this->autoloader = $autoloader;
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
        $container = $this->instantiateContainer();
        $this->configureContainerWithFrameworkConfig($container);
        
        /*
         * Only when we have at least the framework's container then we autoload the client's names.
         * This is because we want to catch every possible error which may arise from client's code and have a container
         * instance already created.
         */
        $appAutoloadFilename = $this->normaDir . '/app/autoload/autoload.php';
        $namespaces = require_once $appAutoloadFilename; // Application components' autoloading.
        $this->autoloader->addNamespaces(...$namespaces);

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
        /*** /1. ***/
        
        /*** 2. Middlewares bootstrap process ***/
        $this->configureMiddlewares($container);
        /*** /2. ***/
        
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
     * @param MiddlewareLayerInterface $middlewareLayer The middleware layer instance where to use to register the middlewares.
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
     * Configures the generic middlewares.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container
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
     * This is a good hook for after middlewares.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @return void
     */
    abstract protected function onTermination(AbstractDependencyInjectionContainer $container);
    
}
