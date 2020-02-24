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

namespace Norma\Core\Runtime;

use Norma\Core\Runtime\AbstractRuntime;
use Norma\DI\AbstractDependencyInjectionContainer;
use Norma\CLI\CLIEntryPointInterface;
use Norma\CLI\Middleware\MiddlewareLayerInterface;
use Norma\CLI\Middleware\MiddlewareLayerEnum;
use Norma\CLI\CLIInputInterface;
use Norma\CLI\CLIOutputInterface;
use Norma\CLI\CLIErrorOutputInterface;
use Norma\CLI\CommandCollectionInterface;

/**
 * The implementation of a CLI application runtime.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class CLIApplicationRuntime extends AbstractRuntime {
    
    /**
     * {@inheritdoc}
     */
    protected function executeApplication(AbstractDependencyInjectionContainer $container) {
        global $argv;
        
        $middlewareLayer = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', 'middleware', MiddlewareLayerInterface::class]));
        
        $input = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', CLIInputInterface::class]));
        $output = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', CLIOutputInterface::class]));
        $errOutput = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', CLIErrorOutputInterface::class]));
        $params = [$input, $output, $errOutput];
        
        /* @var $CLIEntryPoint Norma\CLI\CLIEntryPointInterface */
        $CLIEntryPoint = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', CLIEntryPointInterface::class]));
        
        $this->executeMiddlewareLayer($middlewareLayer, MiddlewareLayerEnum::BEFORE_COMMAND_EXECUTION, $container, $params);
        
        $arguments = $argv;
        unset($arguments[0]);
        $arguments = array_values($arguments);
        $CLIEntryPoint->run($arguments);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function onTermination(AbstractDependencyInjectionContainer $container) {
        $middlewareLayer = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', 'middleware', MiddlewareLayerInterface::class]));
        
        $input = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', CLIInputInterface::class]));
        $output = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', CLIOutputInterface::class]));
        $errOutput = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', CLIErrorOutputInterface::class]));
        
        $params = [$input, $output, $errOutput];
        
        $this->executeMiddlewareLayer($middlewareLayer, MiddlewareLayerEnum::AFTER_COMMAND_EXECUTION, $container, $params);
    }
    
    /**
     * Configures the CLI application's commands.
     * 
     * @param AbstractDependencyInjectionContainer $container
     * @return void
     */
    protected function configureCommands(AbstractDependencyInjectionContainer $container) {
        /* @var $commandCollection CommandCollectionInterface */
        $commandCollection = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', CommandCollectionInterface::class]));
        $commandsConfigCallable = (require_once $this->normaDir . '/app/config/runtime-' . $this->runtime . '/commands.php') ?? function() {};
        $commandsConfig = $container->call($commandsConfigCallable);
        $commandCollection->addCommands($commandsConfig ?? []);
    }

    /**
     * {@inheritdoc}
     */
    protected function onContainerConfigured(AbstractDependencyInjectionContainer $container) {
        // Commands configuration.
        $this->configureCommands($container);
        
        $middlewareLayer = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', 'middleware', MiddlewareLayerInterface::class]));
        $this->registerMiddlewares(
            $container,
            $middlewareLayer,
            $this->normaDir . '/app/config/runtime-' . $this->runtime . '/middlewares.php'
        );
        $this->registerMiddlewares(
            $container,
            $middlewareLayer,
            $this->normaDir . '/app/config/runtime-' . $this->runtime . '/' . $this->environment . '/middlewares.php',
            TRUE
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function onThrowable(\Throwable $e, AbstractDependencyInjectionContainer $container) {
        $middlewareLayer = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', 'middleware', MiddlewareLayerInterface::class]));
        
        $input = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', CLIInputInterface::class]));
        $output = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', CLIOutputInterface::class]));
        $errOutput = $container->get(AbstractDependencyInjectionContainer::buildQualifiedComponentKey(['norma', 'framework', 'cli', CLIErrorOutputInterface::class]));
        
        $this->executeMiddlewareLayerWithContext($middlewareLayer, MiddlewareLayerEnum::ON_THROWABLE, $e, $container, [$input, $output, $errOutput, $e]);
    }

}
