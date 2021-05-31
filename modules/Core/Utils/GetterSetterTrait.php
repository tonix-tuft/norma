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

namespace Norma\Core\Utils;

/**
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
trait GetterSetterTrait {
    
    static $getterPrefix = 'get';
    static $setterPrefix = 'set';
    
    /**
     * Get or set a property.
     * 
     * @param string $name The name of the getter/setter method.
     * @param array $arguments Array of arguments (empty if getter method, otherwise a single element representing the value to set).
     * @return mixed Returns the property value if the getter is called, otherwise `void` for the setter.
     */
    public function __call($name, $arguments) {
        $getterPrefix = static::$getterPrefix;
        $posGet = strpos($name, $getterPrefix);
        if ($posGet === 0) {
            // Getter.
            $property = lcfirst(substr($name, strlen(static::$getterPrefix)));
            return $this->{$property};
        }
        $setterPrefix = static::$setterPrefix;
        $posSet = strpos($name, $setterPrefix);
        if ($posSet === 0) {
            // Setter.
            $property = lcfirst(substr($name, strlen(static::$setterPrefix)));
            $this->{$property} = $arguments[0];
        }
    }
    
}
