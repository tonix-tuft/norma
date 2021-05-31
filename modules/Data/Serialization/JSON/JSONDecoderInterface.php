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

namespace Norma\Data\Serialization\JSON;

/**
 * The interface of a JSON decoder.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
interface JSONDecoderInterface {
    
    /**
     * Decodes a JSON payload and returns the respective PHP data structure.
     * Returns FALSE if the JSON cannot be decoded.
     * 
     * @source http://php.net/manual/en/function.json-decode.php
     * 
     * @param string $payload The JSON payload string to decode.
     * @param bool $assoc A boolean which indicates whether the returned deserialized data structure should be an associative array or an object.
     * @param int $depth The maximum depth of the data structure.
     * @param int $options Optional options.
     * @return mixed Returns the value encoded in JSON in appropriate PHP type.
     *                         Values true, false and null are returned as TRUE, FALSE and NULL respectively.
     * @throws JSONException If a JSON payload cannot be decoded for some reason.
     */
    public function decode($payload, $assoc = FALSE, $depth = 512, $options = 0);
    
    /**
     * Gets the last error message returned during a decoding procedure.
     *
     * @param array $customMessages An array which maps a PHP JSON error code to custom error messages to use.
     *                                                     It MUST be possible to specify the array only once within a script because the component or this method MUST
     *                                                     use a caching mechanism internally.
     *                                                     A new array of `$customMessages` MUST override the previous cached static one.
     *                                                     The special string key `'JSON_UNKNOWN_ERROR'` MAY be used to specify a custom message
     *                                                     in case `json_last_error()` returns an unknown error code.
     * @return string The error message.
     */
    public function getLastErrorMessage($customMessages = []);
    
}
