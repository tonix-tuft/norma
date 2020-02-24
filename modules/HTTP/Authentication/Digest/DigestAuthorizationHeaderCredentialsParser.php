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

namespace Norma\HTTP\Authentication\Digest;

use Norma\HTTP\Authentication\AbstractAuthorizationHeaderCredentialsParser;

/**
 * An authorization header credentials parser for an `Authorization` header with the `Digest` authentication type.
 * 
 * An example of a `Digest` authentication type `Authorization` header:
 *  
 *      Authorization: Digest username="bob", realm="members only", qop="auth", algorithm="MD5", uri="/digest_auth/test.html", nonce="5UImQA==3d76b2ab859e1770ec60ed285ec68a3e63028461", nc=00000001, cnonce="1672b410efa182c061c2f0a58acaa17d", response="3d9ebe6b9534a7135a3fde59a5a72668"
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class DigestAuthorizationHeaderCredentialsParser extends AbstractAuthorizationHeaderCredentialsParser {
    
    /**
     * {@inheritdoc}
     */
    public function parseUserAndPassword() {
        $user = '';
        
        // There's no way to determine the password when `Digest` authentication is being used, as the password sent by the client is hashed.
        $password = null;
        
        $parsedCredentials = $this->parseDigestCredentials($this->credentials);
        if (!empty($parsedCredentials['username'])) {
            $user = $parsedCredentials['username'];
        }
        
        return [$user, $password];
    }
    
    /**
     * Parses the credentials of the `Digest` `Authorization` header.
     * 
     * @see https://regex101.com/r/5ca1eR/1 Regex to match name value pairs of the `Digest` credentials.
     * @source https://evertpot.com/223/ Provides some insights on Basic and Digest authentication.
     * @source https://tools.ietf.org/html/rfc2617 RFC for Basic and Digest authentication.
     * 
     * @param string $credentials The digest credentials string.
     * @return array An associative array with the following keys:
     * 
     *                          - nonce -> A unique code originally sent by the server and used within the hash which needs to be sent back by the client. NULL if missing.
     *                          - nc -> Nonce-count. This is a hexadecimal serial number for the request. The client should increase this number by one for every request. NULL if missing.
     *                          - cnonce -> A unique id generated by the client. NULL if missing.
     *                          - qop -> Can be auth and auth-int and has influence on how the hash is created.
     *                                          `qop` is quality of protection.
     *                                          This serves as an integrity code for the request.
     *                                          A hacker could steal all your HTTP Digest headers and simply change the body to make it do something else.
     *                                          If `qop` is set to 'auth', only the requested `uri` will be taken into consideration.
     *                                          If `qop` is 'auth-int' the body of the request will also be used in the hash. For example:
     * 
     *                                                  (A2 = md5(request-method:uri:md5(request-body))).
     *                                        
     *                                          NULL if missing.
     *                          - username -> The supplied username. NULL if missing.
     *                          - uri -> The authentication uri. NULL if missing.
     *                          - response -> The validation hash. NULL if missing.
     *                          - realm -> A string identifying the realm (protection space) of the credentials. NULL if missing.
     *                          - algorithm -> The digest algorithm chosen by the client from the available server's options. NULL if missing.
     *
     *                      Any key which doesn't have a corresponding value within the `$credentials` string will remain NULL.
     */
    public function parseDigestCredentials($credentials) {
        $defaultParts = [
            'nonce' => NULL,
            'nc' => NULL,
            'cnonce' => NULL,
            'qop' => NULL,
            'username' => NULL,
            'uri' => NULL,
            'response' => NULL,
            'realm' => NULL,
            'algorithm' => NULL,
        ];
        $data = [];

        $matches = [];
        preg_match_all('~
                (?:
                    (?:^
                        (?P<OWS>
                            (?:(?:\r\n)?(?:[ ]|\t))*
                        )
                    )
                    |
                    (?:
                        ,[ ]?
                    )
                )
                (?P<name>
                    .+?
                )
                =
                (?:
                    (?P<QUOTE>(?:"|\')?)
                    (?P<value>
                        .+?
                    )
                    (?P=QUOTE)(?=(?:(?:,[ ]?)|$))
                )
                (?= # An assertion is used to not move the regex engine.
                    (?:
                        (?P>OWS)
                        $
                    )
                    |
                    (?:,[ ]?)?
                )
                ~xm', $credentials, $matches, PREG_SET_ORDER);
        
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $data[$match['name']] = $match['value'];
            }
            $data = array_merge($defaultParts, $data);
        }
        
        return $data;
    }

}
