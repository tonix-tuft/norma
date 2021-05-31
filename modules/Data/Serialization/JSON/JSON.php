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

use Norma\Data\Serialization\JSON\JSONException;

/**
 * A utility class which aim is to serialize and deserialize (encode/decode) JSON strings and data.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class JSON implements JSONEncoderDecoderInterface {
    
    /**
     * {@inheritdoc}
     */
    public function encode($value, $options = 0, $depth = 512) {
        if (is_resource($value)) {
            throw new JSONException('Resources cannot be serialized to a JSON payload string.');
        }
        
        // Clear an eventual previous error returned by `json_last_error()`
        json_encode(null);
        
        $payload = json_encode($value, $options, $depth);
        $JSONLastError = json_last_error();
        if (JSON_ERROR_NONE !== $JSONLastError) {
            // Cannot encode to JSON payload.
            throw new JSONException($this->getLastErrorMessage(), $JSONLastError);
        }
        return $payload;
    }
    
    /**
     * {@inheritdoc}
     */
    public function decode($payload, $assoc = FALSE, $depth = 512, $options = 0) {
        // Clear an eventual previous error returned by `json_last_error()`
        json_decode('null');
        
        $dataStructure = json_decode($payload, $assoc, $depth, $options);
        $JSONLastError = json_last_error();
        if ($JSONLastError !== JSON_ERROR_NONE) {
            // JSON payload cannot be decoded.
            throw new JSONException($this->getLastErrorMessage(), json_last_error());
        }
        return $dataStructure;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLastErrorMessage($customMessages = []) {
        static $customMessagesCache = [];
        if (!empty($customMessages)) {
            $customMessagesCache = $customMessages;
        }
        
        $defaultMessage = NULL;
        if (function_exists('json_last_error_msg')) {
            $defaultMessage = json_last_error_msg();
        }
        
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                if (!isset($customMessagesCache[JSON_ERROR_NONE])) {
                    return is_null($defaultMessage) ? 'No error.' : $defaultMessage;
                }
                return $customMessagesCache[JSON_ERROR_NONE];
            case JSON_ERROR_DEPTH:
                if (!isset($customMessagesCache[JSON_ERROR_DEPTH])) {
                    return is_null($defaultMessage) ? 'Maximum stack depth exceeded.' : $defaultMessage;
                }
                return $customMessagesCache[JSON_ERROR_DEPTH];
            case JSON_ERROR_STATE_MISMATCH:
                if (!isset($customMessagesCache[JSON_ERROR_STATE_MISMATCH])) {
                    return is_null($defaultMessage) ? 'State mismatch (invalid or malformed JSON).' : $defaultMessage;
                }
                return $customMessagesCache[JSON_ERROR_STATE_MISMATCH];
            case JSON_ERROR_CTRL_CHAR:
                if (!isset($customMessagesCache[JSON_ERROR_CTRL_CHAR])) {
                    return is_null($defaultMessage) ? 'Control character error, possibly incorrectly encoded.' : $defaultMessage;
                }
                return $customMessagesCache[JSON_ERROR_CTRL_CHAR];
            case JSON_ERROR_SYNTAX:
                if (!isset($customMessagesCache[JSON_ERROR_SYNTAX])) {
                    return is_null($defaultMessage) ? 'Syntax error.' : $defaultMessage;
                }
                return $customMessages[JSON_ERROR_SYNTAX];
            case JSON_ERROR_UTF8:
                if (!isset($customMessagesCache[JSON_ERROR_UTF8])) {
                    return is_null($defaultMessage) ? 'Malformed UTF-8 characters, possibly incorrectly encoded.' : $defaultMessage;
                }
                return $customMessagesCache[JSON_ERROR_UTF8];
            default:
                if (isset($customMessagesCache['JSON_UNKNOWN_ERROR'])) {
                    return $customMessagesCache['JSON_UNKNOWN_ERROR'];
                }
                return 'JSON unknown error.';
        }
    }
    
}
