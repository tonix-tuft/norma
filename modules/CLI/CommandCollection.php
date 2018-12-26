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

namespace Norma\CLI;

use Norma\CLI\CommandCollectionInterface;
use Norma\CLI\CommandAlreadyExistsException;
use Norma\CLI\CommandDoesNotExistsException;

/**
 * The implementation of a command collection.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class CommandCollection implements CommandCollectionInterface {
    
    /**
     * @var array
     */
    protected $commands;
    
    public function __construct() {
        $this->commands = [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function addCommand($commandName, $commandToExecute) {
        if (isset($this->commands[$commandName])) {
            throw new CommandAlreadyExistsException(sprintf('The CLI command "%s" already exists.', $commandName));
        }
        $this->commands[$commandName] = $commandToExecute;
    }

    /**
     * {@inheritdoc}
     */
    public function addCommands(array $commands) {
        foreach ($commands as $commandName => $commandToExecute) {
            $this->addCommand($commandName, $commandToExecute);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand($commandName) {
        if (!isset($this->commands[$commandName])) {
            throw new CommandDoesNotExistsException(sprintf('The CLI command "%s" does not exist.', $commandName));
        }
        return $this->commands[$commandName];
    }

}
