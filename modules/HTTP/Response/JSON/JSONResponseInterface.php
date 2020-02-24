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

namespace Norma\HTTP\Response\JSON;

use Norma\HTTP\Response\ResponseInterface;

/**
 * The interface of a JSON response which conforms with the PSR-7 specification.
 * 
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface JSONResponseInterface extends ResponseInterface {
    
    /**
     * Get the data associated with this JSON response.
     * 
     * @return mixed The data of this response.
     */
    public function getData();
   
    /**
     * Get the JSON encoding options to use for the JSON serialization of the data associated with this response.
     * 
     * @source http://php.net/manual/en/json.constants.php
     * 
     * @return int The encoding options.
     */
    public function getOptions();
    
    /**
     * Return an instance with the provided data to encode into a JSON payload.
     * This method SHOULD NOT update the underlying body stream of the response.
     * If data has already been written to the underlying body stream of the response then the
     * data added through this method SHOULD NOT be used for the response.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated JSON data.
     * 
     * @param mixed $data The data to convert to JSON.
     * @return static
     */
    public function withData($data);
    
    /**
     * Return an instance with the provided JSON encoding options to use for the JSON serialization of the response.
     * This method SHOULD NOT update the underlying body stream of the response by writing
     * the underlying JSON-encoded data using the new provided options.
     * If data has already been written to the underlying body stream of the response then the
     * data added with {@link JSONResponseInterface::withData()} SHOULD NOT be used for the response,
     * as well as the provided options.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated JSON encoding options.
     * 
     * @source http://php.net/manual/en/json.constants.php
     * 
     * @param int $options The JSON encoding options.
     * @return static
     */
    public function withOptions($options);
    
}
