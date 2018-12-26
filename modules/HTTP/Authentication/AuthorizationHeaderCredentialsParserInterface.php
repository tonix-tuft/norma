<?php

/*
 * Copyright (c) 2018 Anton Bagdatyev
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

/**
 * The interface of an authorization header parser.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
interface AuthorizationHeaderCredentialsParserInterface {
    
    /**
     * Parses the user name and password using the credentials stored within this authorization header credentials parser
     * during setup.
     * 
     * @return array An array with the user name at index 0 and the password at index 1. If the authorization header credentials
     *                       of this parser is missing a user name implementors MUST return an empty string at index 0.
     *                       If the authorization header credentials of this parser are missing a password,
     *                       implementors MUST return NULL.
     */
    public function parseUserAndPassword();
    
}
