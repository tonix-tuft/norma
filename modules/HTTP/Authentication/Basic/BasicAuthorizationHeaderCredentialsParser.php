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

namespace Norma\HTTP\Authentication\Basic;

use Norma\HTTP\Authentication\AbstractAuthorizationHeaderCredentialsParser;

/**
 * An authorization header credentials parser for an `Authorization` header with the `Basic` authentication type.
 * 
 * An example of a `Basic` authentication type `Authorization` header:
 * 
 *      Authorization: Basic YWxhZGRpbjpvcGVuc2VzYW1l
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class BasicAuthorizationHeaderCredentialsParser extends AbstractAuthorizationHeaderCredentialsParser {
    
    /**
     * {@inheritdoc}
     */
    public function parseUserAndPassword() {
        $password = null;
        $userInfo = base64_decode($this->credentials);
        $explode = explode(':', $userInfo, 2);
        $user = $explode[0];
        if (isset($explode[1])) {
            $password = $explode[1];
        }
        return [$user, $password];
    }

}
