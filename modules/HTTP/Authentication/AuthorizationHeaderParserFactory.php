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

namespace Norma\HTTP\Authentication;

use Norma\HTTP\Authentication\AuthorizationHeaderParserFactoryInterface;
use Norma\HTTP\Authentication\UnknownAuthenticationTypeException;
use Norma\HTTP\Authentication\Digest\DigestAuthorizationHeaderCredentialsParser;
use Norma\HTTP\Authentication\Basic\BasicAuthorizationHeaderCredentialsParser;

/**
 * An implementation of an authorization header parser factory.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class AuthorizationHeaderParserFactory implements AuthorizationHeaderParserFactoryInterface {
    
    /**
     * {@inheritdoc}
     */
    public function makeAuthorizationHeaderCredentialsParser($authorizationHeaderValue): AuthorizationHeaderCredentialsParserInterface {
        /*
         * @see https://regex101.com/r/hyj0bc/3 Regex for `Authorization` header type and credentials.
         */
        $matches = [];
        preg_match('/^(?P<type>[^\s]*)\s*(?P<credentials>.*)$/s', $authorizationHeaderValue, $matches);
        if (empty($matches['type'])) {
            throw new \InvalidArgumentException(sprintf('Missing authentication type for authorization header value "%s".', $authorizationHeaderValue));
        }
        else if (empty($matches['credentials'])) {
            throw new \InvalidArgumentException(sprintf('Missing authentication credentials for authorization header value "%s".', $authorizationHeaderValue));
        }
        else {
            $type = $matches['type'];
            $typeLower = strtolower($type);
            $credentials = $matches['credentials'];
            
            $authorizationHeaderCredentialsParser = NULL;
            switch ($typeLower) {
                case 'basic':
                    $authorizationHeaderCredentialsParser = new BasicAuthorizationHeaderCredentialsParser($credentials);
                    break;
                case 'digest':
                    $authorizationHeaderCredentialsParser = new DigestAuthorizationHeaderCredentialsParser($credentials);
                    break;
                default:
                    throw new UnknownAuthenticationTypeException(sprintf('Unknown authentication type "%s".', $type));
            }
            
            return $authorizationHeaderCredentialsParser;
        }
    }

}
