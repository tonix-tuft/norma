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

namespace Norma\AOP\FS;

use Norma\AOP\FS\AOPEligiblePathDeterminerInterface;

/**
 * The implementation of an AOP eligible path determiner.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class AOPEligiblePathDeterminer implements AOPEligiblePathDeterminerInterface {
    
    /**
     * @var array<string>
     */
    protected $weaveableDirs;

    /**
     * @var array<string>
     */
    protected $weaveablePathEligibleFilenamePatterns;
    
    /**
     * @var array<string>
     */
    protected $weaveablePathNotEligibleFilenamePatterns;
    
    /**
     * Constructs a new AOP eligible path determiner.
     * 
     * @param array<string> $weaveableDirs List of weaveable directories.
     * @param array<string> $weaveablePathEligibleFilenamePatterns List of eligible patterns.
     * @param array<string> $weaveablePathNotEligibleFilenamePatterns List of not eligible patterns.
     */
    public function __construct($weaveableDirs, $weaveablePathEligibleFilenamePatterns, $weaveablePathNotEligibleFilenamePatterns) {
        $this->weaveableDirs = $this->normalizeDirs($weaveableDirs);
        $this->weaveablePathEligibleFilenamePatterns = $weaveablePathEligibleFilenamePatterns;
        $this->weaveablePathNotEligibleFilenamePatterns = $weaveablePathNotEligibleFilenamePatterns;
    }
    
    /**
     * Normalizes the given directories.
     * 
     * @param array<string> $dirs The directories to normalize.
     * @return array<string> The same directories, normalized.
     */
    protected function normalizeDIrs($dirs) {
        $ret = [];
        foreach ($dirs as $dir) {
            $normalizedDir = realpath($dir) . '/';
            $ret[] = $normalizedDir;
        }
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function isPathEligible($path) {
        $isEligible = FALSE;
        $realPath = realpath($path);
        foreach ($this->weaveableDirs as $weaveableDir) {
            if (strpos($realPath, $weaveableDir) === 0) {
                $isEligible = TRUE;
                break;
            }
        }
        if (!$isEligible) {
            return FALSE;
        }
        
        $isEligiblePattern = FALSE;
        foreach ($this->weaveablePathEligibleFilenamePatterns as $pattern) {
            if (preg_match($pattern, $realPath)) {
                $isEligiblePattern = TRUE;
                break;
            }
        }
        if (!$isEligiblePattern && !empty($this->weaveablePathEligibleFilenamePatterns)) {
            return FALSE;
        }
        
        foreach ($this->weaveablePathNotEligibleFilenamePatterns as $pattern) {
            if (preg_match($pattern, $realPath)) {
                return FALSE;
            }
        }
        
        return TRUE;
    }

}
