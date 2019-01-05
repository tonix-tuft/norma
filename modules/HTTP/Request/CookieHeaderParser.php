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

namespace Norma\HTTP\Request;

/**
 * The implementation of a cookie header parser.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class CookieHeaderParser implements CookieHeaderParserInterface {
    
    /**
     * {@inheritdoc}
     */
    public function parseCookieFromCookieHeader($cookieHeaderValue) {
        /*
         * The cookie header has the following structure defined by RFC 6265:
         * 
         *      cookie-header = "Cookie:" OWS cookie-string OWS
         * 
         *      cookie-string = cookie-pair *( ";" SP cookie-pair )
         * 
         *      cookie-pair = cookie-name "=" cookie-value
         * 
         *      cookie-name = token
         * 
         *      token = <token, defined in [RFC2616], Section 2.2> = 1*<any CHAR except CTLs or separators>
         * 
         *      CHAR = %x01-7F
         * 
         *      CTL = %x00-1F / %x7F
         * 
         *      separators = "(" | ")" | "<" | ">" | "@" | "," | ";" | ":" | "\" | <"> | "/" | "[" | "]" | "?" | "=" | "{" | "}" | SP | HT 
         *                           ; in ABNF, the pipe character (`|`) is sometimes used instead of the slash character (`/`) to represent alternatives
         * 
         *      cookie-value = *cookie-octet / ( DQUOTE *cookie-octet DQUOTE )
         *                               ; `*cookie-octet` is used for zero or more element of type `cookie-octet`
         * 
         *      cookie-octet = %x21 / %x23-2B / %x2D-3A / %x3C-5B / %x5D-7E
         *                               ; US-ASCII characters excluding CTLs,
         *                               ; whitespace DQUOTE, comma, semicolon,
         *                               ; and backslash
         * 
         *      OWS = *( [ obs-fold ] WSP )
         *                  ; "optional" whitespace
         * 
         *      obs-fold = CRLF
         * 
         *      WSP = SP / HTAB
         * 
         *      HTAB = %x09
         *                   ; horizontal tab
         * 
         *      SP = %x20
         *              ; whitespace
         * 
         *      CRLF = CR LF 
         *                  ; \r\n
         * 
         *      CR = %x0D
         *              ; \r
         * 
         *      LF = %x0A
         *              ; \n
         * 
         * @source https://tools.ietf.org/html/rfc6265#section-4.2.1
         * @source https://tools.ietf.org/html/rfc2616#section-2.2
         * @source https://en.wikipedia.org/wiki/Augmented_Backus%E2%80%93Naur_form#Core_rules
         * @source https://regex101.com/r/sNRqS1/4 Regex for cookie request header name value parsing.
         */
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
                        ;[ ]
                    )
                )
                (?P<cookie_name>
                    (?:
                        (?!(?:[\x00-\x1F\x7F()<>@,;:\"/[\]?={} ]|\t)) # Characters excluded from cookie names allowed characters.
                        [\x01-\x7F]
                    )+?
                )
                =
                (?P<cookie_value>
                    (?P<DQUOTE>"?)
                    (?P<cookie_octet>
                        [\x21\x23-\x2B\x2D-\x3A\x3C-\x5B\x5D-\x7E]*
                    )
                    (?P=DQUOTE)
                )
                (?= # An assertion is used to not move the regex engine.
                    (?:
                        (?P>OWS)
                        $
                    )
                    |
                    (?:;[ ])?
                )
                ~xm', $cookieHeaderValue, $matches, PREG_SET_ORDER);
        
        $cookies = [];
        foreach ($matches as $match) {
            $cookies[$match['cookie_name']] = urldecode($match['cookie_octet']);
        }
        
        return $cookies;
    }

}
