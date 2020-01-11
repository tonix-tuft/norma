<?php

/*
 * Copyright (c) 2020 Anton Bagdatyev (Tonix-Tuft)
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

use Norma\Core\Utils\EnumToKeyValTrait;

/**
 * Request method.
 * 
 * @source https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class RequestMethodEnum {
    
    use EnumToKeyValTrait;
    
    /**
     * The GET method requests a representation of the specified resource. Requests using GET should only retrieve data.
     */
    const GET = 'GET';
    
    /**
     * The HEAD method asks for a response identical to that of a GET request, but without the response body.
     */
    const HEAD = 'HEAD';
    
    /**
     * The POST method is used to submit an entity to the specified resource, often causing a change in state or side effects on the server.
     */
    const POST = 'POST';
    
    /**
     * The PUT method replaces all current representations of the target resource with the request payload.
     */
    const PUT = 'PUT';
    
    /**
     * The DELETE method deletes the specified resource.
     */
    const DELETE = 'DELETE';
    
    /**
     * The CONNECT method establishes a tunnel to the server identified by the target resource.
     */
    const CONNECT = 'CONNECT';

    /**
     * The OPTIONS method is used to describe the communication options for the target resource.
     */
    const OPTIONS = 'OPTIONS';
    
    /**
     * The TRACE method performs a message loop-back test along the path to the target resource.
     */
    const TRACE = 'TRACE';
    
    /**
     * The PATCH method is used to apply partial modifications to a resource.
     */
    const PATCH = 'PATCH';

    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const ACL = 'ACL';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const BASELINE_CONTROL = 'BASELINE-CONTROL';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const BIND = 'BIND';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const CHECKIN = 'CHECKIN';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const CHECKOUT = 'CHECKOUT';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const COPY = 'COPY';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const LABEL = 'LABEL';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const LINK = 'LINK';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const LOCK = 'LOCK';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const MERGE = 'MERGE';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const MKACTIVITY = 'MKACTIVITY';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const MKCALENDAR = 'MKCALENDAR';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const MKCOL = 'MKCOL';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const MKREDIRECTREF = 'MKREDIRECTREF';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const MKWORKSPACE = 'MKWORKSPACE';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const MOVE = 'MOVE';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const ORDERPATCH = 'ORDERPATCH';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const PRI = 'PRI';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const PROPFIND = 'PROPFIND';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const PROPPATCH = 'PROPPATCH';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const REBIND = 'REBIND';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const REPORT = 'REPORT';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const SEARCH = 'SEARCH';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const UNBIND = 'UNBIND';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const UNCHECKOUT = 'UNCHECKOUT';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const UNLINK = 'UNLINK';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const UNLOCK = 'UNLOCK';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const UPDATE = 'UPDATE';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const UPDATEREDIRECTREF = 'UPDATEREDIRECTREF';
    
    /**
     * https://stackoverflow.com/questions/41411152/how-many-http-verbs-are-there#answer-41411515
     */
    const VERSION_CONTROL = 'VERSION-CONTROL';

}
