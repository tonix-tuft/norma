<?php

/*
 * Copyright (c) 2021 Anton Bagdatyev (Tonix)
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

namespace Norma\Heuristic;

use Norma\Heuristic\BinaryOrTextFileInfererInterface;
use Norma\IO\FileDoesNotExistException;
use Norma\IO\FileWritingException;
use Norma\IO\FileReadingException;
use Norma\IO\FileDeletionException;
use Norma\CLI\CommandFailedException;
use Norma\CLI\Command\Diff\DiffCommandPathInterface;
use Norma\CLI\Command\EchoCommand\EchoCommandPathInterface;
use Norma\CLI\Command\Grep\GrepCommandPathInterface;
use Norma\CLI\Exec\CommandExecutorInterface;
use Norma\CLI\Command\Export\ExportCommandPathInterface;
use Norma\Core\Utils\FrameworkIOUtilsTrait;

/**
 * A binary or text file inferer implementation which uses the `diff` command internally.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class DiffBinaryOrTextFileInferer implements BinaryOrTextFileInfererInterface {
    
    use FrameworkIOUtilsTrait;
    
    /**
     * @var CommandExecutorInterface 
     */
    protected $commandExecutor;
    
    /**
     * @var ExportCommandPathInterface
     */
    protected $export;
    
    /**
     * @var DiffCommandPathInterface
     */
    protected $diff;

    /**
     * @var GrepCommandPathInterface
     */
    protected $grep;
    
    /**
     * @var EchoCommandPathInterface
     */
    protected $echo;
    
    /**
     * Constructs a new inferer.
     * 
     * @param CommandExecutorInterface $commandExecutor A command executor.
     * @param ExportCommandPathInterface $export An `export` command path.
     * @param DiffCommandPathInterface $diff A `diff` command path.
     * @param GrepCommandPathInterface $grep A `grep` command path.
     * @param EchoCommandPathInterface $echo An `echo` command path.
     */
    public function __construct(CommandExecutorInterface $commandExecutor,
            ExportCommandPathInterface $export, DiffCommandPathInterface $diff, GrepCommandPathInterface $grep, EchoCommandPathInterface $echo
    ) {
        $this->commandExecutor = $commandExecutor;
        $this->export = $export;
        $this->diff = $diff;
        $this->grep = $grep;
        $this->echo = $echo;
    }
    
    /**
     * {@inheritdoc}
     * @throws FileWritingException If a writing operation fails on the test file used by the inferer to determine whether the given filename is a binary file or not (text file).
     * @throws FileReadingException If a reading operation fails on the file.
     * @throws FileDeletionException If the deletion of the test file used by the inferer fails.
     * @throws CommandFailedException If the underlying command which uses the `diff` CLI command fails.
     */
    public function isBinary($filename) {
        if (!$this->fileExists($filename)) {
            throw new FileDoesNotExistException(sprintf('The file with filename "%s" does not exist.', $filename));
        }
        
        $testAgainstFilename = $this->tempFile();
        if (file_put_contents($testAgainstFilename, sha1(time())) === FALSE) {
            throw new FileWritingException(sprintf('Could not write to temporary file against which to test with unique filename "%s".', $testAgainstFilename));
        }
        
        $filenameToTest = $this->tempFile();
        
        // The command should read the first bytes of the file to boost performance in case the file being tested is very large.
        $readRes = file_get_contents($filename, FALSE, NULL, 0, $this->getMaximumNumberOfBytesToRead());
        if ($readRes === FALSE) {
            throw new FileReadingException(sprintf('Could not read file "%s".', $filename));
        }
        $writeRes = file_put_contents($filenameToTest, $readRes);
        if ($writeRes === FALSE) {
            throw new FileWritingException(sprintf('Could not write to temporary file to test with unique filename "%s".', $filenameToTest));
        }
        
        $command = sprintf(
            '%s LC_ALL=C; %s %s %s | %s -E -q \'^Binary files\'; %s $?',
            (string) $this->export,
            (string) $this->diff,
            escapeshellarg($filenameToTest),
            escapeshellarg($testAgainstFilename),
            (string) $this->grep,
            (string) $this->echo
        );
        
        $res = $this->commandExecutor->execute($command);
        if (unlink($testAgainstFilename) === FALSE) {
            throw new FileDeletionException(sprintf('Could not unlink temporary file against which to test with unique filename "%s".', $testAgainstFilename));
        }
        if (unlink($filenameToTest) === FALSE) {
            throw new FileDeletionException(sprintf('Could not unlink temporary file to test with unique filename "%s".', $filenameToTest));
        }
        
        $stderr = $this->commandExecutor->getLastStderr();
        if (!empty($stderr)) {
            throw new CommandFailedException(sprintf('The command `%s` returned the error %s.', $command, var_export($stderr, TRUE)));
        }
        
        if ($res === NULL || !ctype_digit($res) || !in_array($res, ['0', '1'], TRUE)) {
            throw new CommandFailedException(sprintf('The command `%s` returned the unexpected code %s.', $command, var_export($res, TRUE)));
        }
        
        if ($res === '0') {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * Get the maximum number of bytes to read from the file when when determining if the file is binary or not.
     * 
     * @return int The maximum number of bytes to read.
     */
    protected function getMaximumNumberOfBytesToRead() {
        return 1024;
    }

}
