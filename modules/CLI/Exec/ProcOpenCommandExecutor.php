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

namespace Norma\CLI\Exec;

use Norma\CLI\Exec\CommandExecutorInterface;
use Norma\CLI\CommandFailedException;

/**
 * The implementation of a CLI command executor which uses `proc_open` internally.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class ProcOpenCommandExecutor implements CommandExecutorInterface {

    /**
     * @var string
     */
    protected $lastStdout;
    
    /**
     * @var string
     */
    protected $lastStderr = NULL;
    
    /**
     * @var bool
     */
    protected $noCommandExecuted;
    
    /**
     * Constructs a new command executor.
     */
    public function __construct() {
        $this->noCommandExecuted = TRUE;
    }
    
    /**
     * Throws an exception if no command has been executed yet by this command executor.
     * 
     * @return void
     * @throws \RuntimeException If no command has been executed yet.
     */
    protected function throwExceptionIfNoCommandExecuted() {
        if ($this->noCommandExecuted) {
            throw new \RuntimeException("No command was executed yet.");
        }
    }
    
    /**
     * Trims leading and trailing newline and carriage return characters from the given string.
     * 
     * @param string The string to trim.
     * @return string The trimmed string without leading and trailing newline and carriage return characters.
     */
    protected function trimLeadingAndTrailingNewlineAndCarriageReturnCharacters($str) {
        return trim(trim(trim($str, "\r\n"), "\r"), "\n");
    }
    
    /**
     * {@inheritdoc}
     */
    public function execute($command) {
        $this->noCommandExecuted = FALSE;
        
        $pipes = [];
        
        $process = proc_open(
            $command, [
                0 => ['pipe', 'r'], // stdin
                1 => ['pipe', 'w'], // stout
                2 => ['pipe', 'w'], // stderr
            ], $pipes
        );
        
        if ($process === FALSE) {
            throw new CommandFailedException(sprintf('Could not open a resource representing the process of the command `%s`.', $command));
        }
        else {
            $stdoutContents = stream_get_contents($pipes[1]);
            if ($stdoutContents === FALSE) {
                throw new CommandFailedException(sprintf('Could not read the stdout stream of the command `%s`.', $command));
            }
            $stdout = $this->trimLeadingAndTrailingNewlineAndCarriageReturnCharacters($stdoutContents);
            $this->lastStdout = $stdout;
            
            $stderrContents = stream_get_contents($pipes[2]);
            if ($stderrContents === FALSE) {
                throw new CommandFailedException(sprintf('Could not read the stderr stream of the command `%s`.', $command));
            }
            $stderr = $this->trimLeadingAndTrailingNewlineAndCarriageReturnCharacters($stderrContents);
            $this->lastStderr = $stderr;
            
            if (!fclose($pipes[1])) {
                throw new CommandFailedException(sprintf('Could not close the stdout stream of the command `%s`.', $command));
            }
            if (!fclose($pipes[2])) {
                throw new CommandFailedException(sprintf('Could not close the stderr stream of the command `%s`.', $command));
            }
            
            if (proc_close($process) === -1) {
                throw new CommandFailedException(sprintf('Could not terminate the process of the command `%s`.', $command));
            }
            
            return $this->lastStdout;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLastStderr() {
        $this->throwExceptionIfNoCommandExecuted();
        return $this->lastStderr;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastStdout() {
        $this->throwExceptionIfNoCommandExecuted();
        return $this->lastStdout;
    }

}
