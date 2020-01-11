<?php

/*
 * Copyright (c) 2020 Anton Bagdatyev (Tonix-Tuft)
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

namespace Norma\CLI;

use Norma\CLI\CLIEntryPointInterface;
use Norma\DI\AbstractDependencyInjectionContainer;
use Norma\CLI\CommandCollectionInterface;
use Norma\CLI\CLIInputInterface;
use Norma\CLI\CLIOutputInterface;
use Norma\CLI\CLIErrorOutputInterface;

/**
 * The implementation of a Norma CLI application's entry point.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class CLIEntryPoint implements CLIEntryPointInterface {
    
    /**
     * @var AbstractDependencyInjectionContainer
     */
    protected $container;
    
    /**
     * @var CommandCollectionInterface
     */
    protected $commandCollection;
    
    /**
     * @var CLIInputInterface
     */
    protected $input;
    
    /**
     * @var CLIOutputInterface
     */
    protected $output;
    
    /**
     * @var CLIErrorOutputInterface
     */
    protected $errOutput;
    
    /**
     * Constructs a new entry point.
     * 
     * @param AbstractDependencyInjectionContainer $container The DI container.
     * @param CommandCollectionInterface $commandCollection A command collection.
     * @param CLIInputInterface $input The CLI input (stdin abstraction).
     * @param CLIOutputInterface $output The CLI output (stdout abstraction).
     * @param CLIErrorOutputInterface $errOutput The CLI error output (stderr abstraction).
     */
    public function __construct(AbstractDependencyInjectionContainer $container, CommandCollectionInterface $commandCollection, CLIInputInterface $input, CLIOutputInterface $output, CLIErrorOutputInterface $errOutput) {
        $this->container = $container;
        $this->commandCollection = $commandCollection;
        $this->input = $input;
        $this->output = $output;
        $this->errOutput = $errOutput;
    }
    
    /**
     * {@inheritdoc}
     */
    public function run(array $args) {
        $commandName = $args[0];
        unset($args[0]);
        
        $params = [];
        foreach ($args as $argument) {
            $explode = explode('=', $argument, 2);
            $key = $explode[0];
            if (isset($explode[1])) {
                $params[$key] = $explode[1];
            }
            else {
                $params[$key] = TRUE;
            }
        }
        
        $commandToExecute = $this->commandCollection->getCommand($commandName);
        $command = $this->container->get($commandToExecute[0]);
        $commandMethod = $commandToExecute[1];
        $statusCode = $this->container->call([$command, $commandMethod], $params) ?? 0;
        register_shutdown_function(function() use (&$statusCode) {
            exit($statusCode);
        });
    }
    
}
