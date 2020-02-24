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

namespace Norma\HTTP\Client;

use Norma\Core\Utils\GetterSetterTrait;
use Norma\HTTP\Request\RequestInterface;
use Norma\HTTP\Response\ResponseInterface;

/**
 * An implementation of a simple cURL client.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class CURLClient implements ClientInterface {
    
    use GetterSetterTrait;
    
    /**
     * @var null|resource
     */
    protected $CURLHandle = NULL;
    
    /**
     * @var null|string
     */
    protected $HTTPMethod = NULL;
    
    /**
     * @var null|string
     */
    protected $lastURL = NULL;
    
    /**
     * @var array
     */
    protected $GET = [];
    
    /**
     * @var bool
     */
    protected $isPOSTRaw = TRUE;
    
    /**
     * Destroys this instance.
     */
    public function __destruct() {
        curl_close($this->getCURLHandle());
    }

    /**
     * Sets an option on the internal cURL resource.
     * 
     * @param int $opt The CURLOPT_XXX option to set.
     * @param mixed $value The option's value.
     * @return bool TRUE on success or FALSE on failure.
     */
    public function setOpt(int $opt, $value) {
        $handle = $this->getCURLHandle();
        return curl_setopt($handle, $opt, $value);
    }
    
    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request): ResponseInterface { 
        $handle = $this->getCURLHandle();

        // Building the final request URI.
        $URI = (string) ($request->getUri());
        $finalURI = $URI;
        
        $finalQueryString = "";
        $parsedURL = parse_url($URI) ?? [];
        $queryString = isset($parsedURL['query']) ? $parsedURL['query'] : NULL;
        $params = []; 
        parse_str($queryString, $params);
        $params = array_merge($params, $this->GET);
        
        if (!empty($params)) {
            $finalQueryString .= http_build_query($params);
        }
        unset($parsedURL['query']);
        if (!empty($finalQueryString)) {
             $parsedURL['query'] = $finalQueryString;
        }
        $finalURI = $this->unparseURL($parsedURL);
        
        curl_setopt($handle, CURLOPT_URL, $finalURI);
        
        $body = (string) ($request->getBody());
        $requestMethod = $request->getMethod();
        if (!empty($body)) {
            if ($requestMethod === RequestMethodEnum::POST) {
                curl_setopt($handle, CURLOPT_POST, TRUE);
            }
            curl_setopt($handle, CURLOPT_POSTFIELDS, $body);
        }
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handle, CURLOPT_HEADER, TRUE);
        
        $headers = $request->getHeaders();
        if (!empty($headers)) {
            $headersToSet = [];
            
            /*
             * Duplicate header names are considered the same as comma-separated ones.
             * 
             * @source https://stackoverflow.com/questions/4371328/are-duplicate-http-response-headers-acceptable#answer-4371395
             */
            foreach ($headers as $header => $values) {
                foreach ($values as $value) {
                    $headersToSet[] = sprintf('%s: %s', $header, $value);
                }
            }
            curl_setopt($handle, CURLOPT_HTTPHEADER, $headersToSet);
        }
        
        if (!empty($requestMethod)) {
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $requestMethod);
        }
        else if (!empty($this->HTTPMethod)) {
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $this->HTTPMethod);
        }
        else {
            curl_setopt($handle, CURLOPT_CUSTOMREQUEST, RequestMethodEnum::GET);
        }
        
        $this->lastURL = $finalURI;
        $return = curl_exec($handle);
        if ($return === FALSE) {
            throw new CURLHTTPException('cURL error: ' . curl_error($handle) . ' - ' . curl_errno($handle));
        }
        $response = $this->parseResponse($return);
        return $response;
    }
    
    /**
     * Parses a raw response returned by cURL.
     * 
     * @param string $response The raw response to parse.
     * @return ResponseInterface The response.
     */
    protected function parseResponse($response) {
        // TODO: implement
        
    }
    
    /**
     * Return the underlying cURL handle.
     * 
     * @return resource|false The cURL handle, FALSE on failure.
     */
    public function getCURLHandle() {
        if (is_null($this->CURLHandle)) {
            $this->CURLHandle = curl_init();
        }
        return $this->CURLHandle;
    }
    
    /**
     * Unparses a URL previously parsed with the PHP `parse_url` function.
     * 
     * @param array $parsedURL The parsed URL.
     * @return string The unparsed URL.
     */
    protected function unparseURL(array $parsedURL) {
        $scheme = isset($parsedURL['scheme']) ? $parsedURL['scheme'] . '://' : '';
        $host = isset($parsedURL['host']) ? $parsedURL['host'] : '';
        $port = isset($parsedURL['port']) ? ':' . $parsedURL['port'] : '';
        $user = isset($parsedURL['user']) ? $parsedURL['user'] : '';
        $pass = isset($parsedURL['pass']) ? ':' . $parsedURL['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsedURL['path']) ? $parsedURL['path'] : '';
        $query = isset($parsedURL['query']) ? '?' . $parsedURL['query'] : '';
        $fragment = isset($parsedURL['fragment']) ? '#' . $parsedURL['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
    
}
