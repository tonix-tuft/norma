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

namespace Norma\Data\Structure\Map;

/**
 * The interface of a map which maps objects to data.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface ObjectMapInterface extends \Countable, \ArrayAccess, \Iterator {
    
    /**
     * Map an object to data.
     * 
     * @param object $object The object key.
     * @param mixed $data The data.
     * @return void
     */
    public function set($object, $data);
    
    /**
     * Retrieves the data mapped by an object from the map.
     * 
     * @param object $object The object key.
     * @return mixed The data or NULL if the key with the given object key does not exist.
     */
    public function get($object);
    
    /**
     * Tests if the map has the given object as key.
     * 
     * @param object $object The object key to test.
     * @return bool TRUE if the object key exists in the map, FALSE otherwise.
     */
    public function has($object);
    
    /**
     * Removes an entry from the map given an object key.
     * 
     * @param object $object The object.
     * @return void
     */
    public function remove($object);
    
}
