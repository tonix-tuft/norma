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

use Norma\CLI\CommandAlreadyExistsException;
use Norma\CLI\CommandDoesNotExistException;

/**
 * The interface of a Norma CLI application's command collection.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface CommandCollectionInterface {
    
    /**
     * Adds a command to the collection.
     * 
     * @param string $commandName The command name.
     * @param array $commandToExecute The command to execute.
     * @return void
     * @throws CommandAlreadyExistsException If a command with the same name already exists within the collection.
     */
    public function addCommand($commandName, $commandToExecute);
    
    /**
     * Adds the given commands to the collection.
     * 
     * @param array $commands An array of commands. Command name as key, command to execute as value.
     * @return void
     * @throws CommandAlreadyExistsException If a command with the same name already exists within the collection.
     */
    public function addCommands(array $commands);
    
    /**
     * Retrieves a command from the collection.
     * 
     * @param string $commandName The command name.
     * @return array|null An array representing the command within the collection matching the command name
     *                              or NULL if there isn't a command with the given command name.
     * @throws CommandDoesNotExistException If there isn't a command with the given name within the collection.
     */
    public function getCommand($commandName);
    
}
