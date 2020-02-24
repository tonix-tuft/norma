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

namespace Norma\HTTP\Request\Server;

use Norma\HTTP\Request\Server\ServerRequestProtocolVersionParserInterface;
use Norma\HTTP\HTTPProtocolVersionEnum;
use Norma\Core\Oops\ParseException;

/**
 * The implementation of a server request protocol version parser.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class ServerRequestProtocolVersionParser implements ServerRequestProtocolVersionParserInterface {
    
    /**
     * {@inheritdoc}
     */
    public function parseProtocolVersionFromServerParams($serverParams) {
        if (array_key_exists('SERVER_PROTOCOL', $serverParams)) {
            $serverProtocol = $serverParams['SERVER_PROTOCOL'];
            if (preg_match('%^(HTTP/)?(?P<protocol_version_code>(?:(?:[1](?:\.[0-1])?))|(?:[2](?:\.[0])?))$%', $serverProtocol, $matches)) {
                return $matches['protocol_version_code'];
            }
            else {
                throw new \InvalidArgumentException(sprintf(
                    'Unknown protocol version "%s".',
                    $serverProtocol
                ));
            }
        }
        else {
            return HTTPProtocolVersionEnum::V1_1;
        }
    }

}
