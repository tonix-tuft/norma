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

/**
 * The implementation of a server request headers parser interface.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class ServerRequestHeadersParser implements ServerRequestHeadersParserInterface {
    
    /**
     * {@inheritdoc}
     */
    public function parseHeadersFromServerParams($serverParams) {
        $parsedHeaders = [];
        $redirectCountMap = [];
        foreach ($serverParams as $key => $serverParam) {
            if (strpos($key, 'REDIRECT_') === 0) {
                $i = 0;
                while (strpos($key, 'REDIRECT_') === 0) {
                    $i++;
                    $key = substr($key, 9);
                }
                
                if (array_key_exists($key, $serverParams)) {
                    continue;
                }
                else if (
                    !array_key_exists($key, $redirectCountMap)
                    ||
                    ($i > $redirectCountMap[$key])
                ) {
                    $redirectCountMap[$key] = $i;
                }
                else {
                    continue;
                }
            }
            
            if (strpos($key, 'HTTP_') === 0) {
                $explode = array_map('ucfirst', explode('_', strtolower(substr($key, 5))));
                $name = implode('-', $explode);
                $parsedHeaders[$name] = $serverParam;
            }
            else if (strpos($key, 'CONTENT_') === 0) {
                $name = 'Content-' . ucfirst(strtolower(substr($key, 8)));
                $parsedHeaders[$name] = $serverParam;
            }
        }
        
        return $parsedHeaders;
    }

}
