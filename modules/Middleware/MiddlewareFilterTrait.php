<?php

/*
 * Copyright (c) 2019 Anton Bagdatyev (Tonix-Tuft)
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

namespace Norma\Middleware;

/**
 * A trait containing methods to filter middlewares.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
trait MiddlewareFilterTrait {
    
    /**
     * Filter the given middlewares taking an object into account so that only the relevant ones are executed.
     * 
     * @param array $middlewares An array of middlewares.
     * @param object $object An object to use as the "context".
     * @return array An array of middleware components to execute.
     */
    protected function filterMiddlewaresWithContext($middlewares, $object) {
        $objectClass = get_class($object);
        $middlewaresToExecute = [];
        foreach ($middlewares as $key => $middlewareComponent) {
            if (is_int($key)) {
                // Middlewares without a context are always executed.
                $middlewaresToExecute[] = $middlewareComponent;
            }
            else {
                $trimmedKey = trim($key, '\\');
                $explodedTrimmedKey = explode('\\', $trimmedKey);
                $objectFQN = trim($objectClass, '\\');
                
                $count = count($explodedTrimmedKey);
                $objectFQNPieces = array_slice(explode('\\', $objectFQN), 0, $count);
                if (
                    count($objectFQNPieces) === $count
                    &&
                    strtolower(implode('\\', $objectFQNPieces)) === strtolower($trimmedKey)
                ) {
                    if (is_array($middlewareComponent)) {
                        $middlewaresToExecute = array_merge($middlewaresToExecute, $middlewareComponent);
                    }
                    else {
                        $middlewaresToExecute[] = $middlewareComponent;
                    }
                }
            }
        }
        return $middlewaresToExecute;
    }
    
}
