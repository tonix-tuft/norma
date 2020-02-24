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

namespace Norma\Core\Env;

use Norma\Core\Env\EnvInterface;

/**
 * The implementation of an environment interface.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class Env implements EnvInterface {
    
    /**
     * @var array
     */
    protected $env;
    
    /**
     * Constructs a new environment instance.
     * 
     * @param array $env A key value pair of the environment variables to set for this environment.
     */
    public function __construct($env) {
        $this->env = $env;
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($envVariableKey, $envVariableDefaultValue = NULL) {
        if (array_key_exists($envVariableKey, $this->env)) {
            return $this->env[$envVariableKey];
        }
        return $envVariableDefaultValue;
    }

}
