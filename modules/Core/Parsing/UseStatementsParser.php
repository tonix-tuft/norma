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

namespace Norma\Core\Parsing;

use Norma\IPC\FlockSync;
use Norma\Core\Utils\FrameworkUtils;

/**
 * An implementation of a {@link UseStatementsParserInterface}.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class UseStatementsParser implements UseStatementsParserInterface {
    
    const PARSING_CACHE_DIR = __DIR__ . '/cache/parsed';
    
    const PARSING_LOCK_DIR = __DIR__ . '/cache/lock';
    
    const CACHED_FILENAME_SEPARATOR = '_';
    
    /**
     * @var FrameworkUtils 
     */
    protected $flockSync;
    
    /**
     * @var FlockSync 
     */
    protected $utils;
    
    /**
     * @var array Array of {@link ParsedUseStatement}
     */
    protected $parsed;
    
    /**
     * Constructs a new parser.
     * 
     * @param FrameworkUtils $utils Framework utilities object.
     * @param FlockSync $flockSync Lock file code block synchronizer.
     */
    public function __construct(FrameworkUtils $utils, FlockSync $flockSync) {
        $this->flockSync  = $flockSync;
        $this->utils = $utils;
        $this->parsed = [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function parseUseStatements($filename, $namespace) {
        $namespaceTrim = ltrim($namespace, '\\');
        $fileLastModTime = filemtime($filename);
        $basenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
        $sha1 = sha1($filename . $namespaceTrim . $fileLastModTime);
        
        if (!isset($this->parsed[$namespaceTrim][$sha1])) {
            $this->parsed[$namespaceTrim][$sha1] = [];
        }
        else {
            return $this->parsed[$namespaceTrim][$sha1];
        }
        
        $filenameWithoutExtensionToCheck = $basenameWithoutExtension . self::CACHED_FILENAME_SEPARATOR . $sha1;
        $namespacePath = str_replace('\\', DIRECTORY_SEPARATOR, $namespaceTrim);
        $absoluteFilenameToCheck = self::PARSING_CACHE_DIR . DIRECTORY_SEPARATOR . $namespacePath . DIRECTORY_SEPARATOR . $filenameWithoutExtensionToCheck . '.php';
        if (!file_exists($absoluteFilenameToCheck)) {
            $useStatements = $this->parseUseStatementsFromSource(file_get_contents($filename), $namespaceTrim);
            
            $closureSync = \Closure::bind(function() use ($useStatements, $absoluteFilenameToCheck) {
                $export = var_export($useStatements, TRUE);
                $phpCode = <<<HERECODE
<?php

return {$export};

HERECODE;
                $tmp = $absoluteFilenameToCheck . '.tmp';
                $ret = $this->utils->filePutContentsMissingDirectories($tmp, $phpCode) && rename($tmp, $absoluteFilenameToCheck);
                if (!$ret) {
                    throw new ParsingCacheException(
                        sprintf('Could not create parsing cache file "%1$s".', $absoluteFilenameToCheck)
                    );
                }

                $useStatements = require $absoluteFilenameToCheck;
            }, $this, self::class);
            
            $lockFile = self::PARSING_LOCK_DIR . DIRECTORY_SEPARATOR . $filenameWithoutExtensionToCheck;
            $fileContents = getmypid() . ':' . $filename . ':' . $namespaceTrim;
            $this->flockSync->synchronize($lockFile, $closureSync, $fileContents, TRUE);
        }
        else {
            $useStatements = require $absoluteFilenameToCheck;
        }
        
        foreach ($useStatements as $useStatementAlias => $useStatementName) {
            $this->parsed[$namespaceTrim][$sha1][] = new ParsedUseStatement($useStatementAlias, $useStatementName);
        }
        return $this->parsed[$namespaceTrim][$sha1];
    }
    
    /**
     * Parses use statements from a PHP source code string.
     * 
     * @param string $source The source code to parse for use statements.
     * @param string $namespace The namespace defined within `$filename` where the use statements are.
     * @return array An array of the parsed use statements where the key is the alias and the value is the imported name.
     */
    protected function parseUseStatementsFromSource($source, $namespace) {
        $contextNamespace = '';
        $namespaceLowerCase = strtolower($namespace);
        $tokens = token_get_all($source);
        $count = count($tokens);
        $useStatements = [];
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            if (!$this->checkUsefulToken($token)) {
                continue;
            }
            else if ($this->isTokenArray($token, T_USE) && ($namespaceLowerCase === $contextNamespace)) {
                $baseName = '';
                $currentName = '';
                $currentAlias = '';
                $isOnAliasPart = false;
                for ($i += 1; $i < $count; $i++) {
                    $possibleToken = $tokens[$i];
                    
                    if (!$this->checkUsefulToken($possibleToken)) {
                        continue;
                    }
                    
                    $isNameToken = $this->isTokenArray($possibleToken, T_NS_SEPARATOR) || $this->isTokenArray($possibleToken, T_STRING);
                    if ($isNameToken && !$isOnAliasPart) {
                        $currentAlias = $possibleToken[1];
                        $currentName .= $possibleToken[1];
                        continue;
                    }
                    else if ($isNameToken && $isOnAliasPart) {
                        $currentAlias = $possibleToken[1];
                        continue;
                    }
                    else if ($this->isTokenArray($possibleToken, T_AS)) {
                        $isOnAliasPart = true;
                        continue;
                    }
                    else if ($possibleToken === ';' || $possibleToken === ',') {
                        $aliasLowerCase = strtolower($currentAlias);
                        $useStatements[$aliasLowerCase] = $baseName . $currentName;
                        $baseName = '';
                        $currentName = '';
                        $currentAlias = '';
                        $isOnAliasPart = false;
                        continue;
                    }
                    else if ($possibleToken === '{') {
                        $baseName = $currentName;
                        $currentName = '';
                        continue;
                    }
                    else if ($possibleToken === '}') {
                        continue;
                    }
                    else if ($this->isTokenArray($possibleToken, T_NAMESPACE) || $this->isTokenArray($possibleToken, T_USE)
                    ) {
                        $i--;
                        break;
                    }
                    else {
                        break;
                    }
                }
                continue;
            }
            else if ($this->isTokenArray($token, T_NAMESPACE)) {
                // Could be a namespace.
                $isNamespace = false;
                $possibleNamespace = '';
                for ($i += 1; $i < $count; $i++) {
                    $possibleNamespaceToken = $tokens[$i];
                    if (!$this->checkUsefulToken($possibleNamespaceToken)) {
                        continue;
                    }
                    else if ($this->isTokenArray($possibleNamespaceToken, T_NS_SEPARATOR)) {
                        if (empty($possibleNamespace)) {
                            break;
                        }
                        $possibleNamespace .= $possibleNamespaceToken[1];
                        continue;
                    }
                    else if ($this->isTokenArray($possibleNamespaceToken, T_STRING)) {
                        $possibleNamespace .= $possibleNamespaceToken[1];
                        continue;
                    }
                    else if ($possibleNamespaceToken === ';' || $possibleNamespaceToken === '{') {
                        $isNamespace = true;
                        break;
                    }
                    else {
                        break;
                    }
                }
                if ($isNamespace) {
                    $contextNamespace = ltrim(strtolower($possibleNamespace), '\\');
                }
                continue;
            }
            else {
                continue;
            }
        }
        return $useStatements;
    }
    
    /**
     * Checks whether a token is useful (whether it is not a whitespace, a comment or a doc comment).
     * 
     * @param array|string $token The token as returned by the PHP's function `token_get_all()`
     * @return bool True if the token is not a whitespace, a comment or a doc comment, false otherwise.
     */
    protected function checkUsefulToken($token) {
        if (is_array($token)) {
            return !in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT]);
        }
        return true;
    }
    
    /**
     * Tests whether the token is of a particular type `T_*`.
     * 
     * @param array|string $token The token to test as returned by the PHP's function `token_get_all()`.
     * @param int $tokenType The type of which the token should be in order to return true. Must be a valid parser token, i.e.
     *                                       the constant `T_*` ({@link http://php.net/manual/en/tokens.php}).
     * @return bool True if the token is of the given type, false otherwise.
     */
    protected function isTokenArray($token, $tokenType) {
        return is_array($token) && $token[0] === $tokenType;
    }

}