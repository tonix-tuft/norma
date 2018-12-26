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

namespace Norma\HTTP\Request\Server;

use Norma\Core\Utils\FrameworkArrayUtilsTrait;
use Norma\Core\Utils\FrameworkStringUtilsTrait;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use Norma\HTTP\Request\Request;
use Norma\HTTP\Request\Server\ServerRequestInterface;

/**
 * The implementation of a request as seen on the server (server-side).
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class ServerRequest extends Request implements ServerRequestInterface {
    
    use FrameworkArrayUtilsTrait;
    use FrameworkStringUtilsTrait;
    
    /**
     * A slash character in its URL-encoded form.
     */
    const URL_ENCODED_SLASH = '%2F';
    
    /**
     * @var array
     */
    protected $serverParams;
    
    /**
     * @var array
     */
    protected $cookieParams;
    
    /**
     * @var array
     */
    protected $queryParams;
    
    /**
     * @var array<\Psr\Http\Message\UploadedFileInterface>
     */
    protected $uploadedFiles;
    
    /**
     * @var null|array|object
     */
    protected $parsedBody;
    
    /**
     * @var array
     */
    protected $attributes;
    
    /**
     * Constructs a new server request.
     * 
     * @param array $serverParams Server parameters. Typically `$_SERVER`. An empty array if there are no server parameters.
     * @param UriInterface|null $URI The server request URI or NULL if unknown.
     * @param string|null $method The method. If NULL, the server request instance will try to set the request method using the provided server params
     *                                               and throw an exception if the HTTP method is invalid.
     * @param array $headers The request headers.
     * @throws \InvalidArgumentException If one of the arguments are invalid (e.g. if the given HTTP method is invalid).
     */
    public function __construct($serverParams = [], UriInterface $URI = NULL, $method = NULL, $headers = []) {
        parent::__construct($URI, $method, $headers);
        $this->serverParams = $serverParams;
        $this->attributes = [];
        $this->valorizeMethodFromServerParamIfNull($method);
    }
    
    /**
     * Sets the method of the request from the server param if the given method is NULL.
     * 
     * @param null|string $method The method of the request or NULL.
     * @return void
     * @throws \InvalidArgumentException If the given HTTP method is invalid.
     */
    protected function valorizeMethodFromServerParamIfNull($method = NULL) {
        if ($method === NULL) {
            $method = $this->getServerParam('REQUEST_METHOD');
            if (!empty($method)) {
                $this->throwExceptionIfInvalidMethod($method);
                $this->method = $method;
            }
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAppRequestURIPath() {
        $appRequestURI = $this->getEncodedAppRequestURI();
        $arr = explode('?', $appRequestURI, 2);
        $first = $arr[0];
        $appRequestURIPath = $this->URLDecodeWithoutDecodingEncodedSlashes($first);
        return $appRequestURIPath;
    }
    
    /**
     * Returns the URL-encoded app request URI (that is, without the application base path prefix determined by the framework).
     * 
     * @return string The URL-encoded app request URI.
     */
    protected function getEncodedAppRequestURI() {
        $requestURI = $this->getServerParam('REQUEST_URI');
        $normaApplicationBasePath = $this->getServerParam('NORMA_APP_URI_BASE_PATH') ?? '';
        $appRequestURI = substr($requestURI, strlen($normaApplicationBasePath));
        return $appRequestURI;
    }
    
    /**
     * Decodes a URL without decoding the encoded slashes which remain untouched.
     * 
     * @param string $URL the URL.
     * @return string The decoded URL.
     */
    protected function URLDecodeWithoutDecodingEncodedSlashes($URL) {
        $replaceStr = '%2-' . $this->generateRandomMD5ChunkNotWithinString($URL) . 'F';
        $replaced = str_replace(self::URL_ENCODED_SLASH, $replaceStr, $URL);
        $decoded = urldecode($replaced);
        $replacedAgain = str_replace($replaceStr, self::URL_ENCODED_SLASH, $decoded);
        return $replacedAgain;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getContentType() {
        return $this->getServerParam('CONTENT_TYPE');
    }
    
    /**
     * {@inheritdoc}
     */
    public function getServerParam($param) {
        if (array_key_exists($param, $this->serverParams)) {
            return $this->serverParams[$param];
        }
        return null;
    }
    
    /**
     * Throws an {@link \InvalidArgumentException} if an invalid uploaded files structure is given as parameter.
     * 
     * @param array $uploadedFiles The uploaded files structure.
     * @return void
     * @throws \InvalidArgumentException If an invalid uploaded files structure is provided.
     */
    protected function throwExceptionIfInvalidUploadedFilesStructure($uploadedFiles) {
        $flatted = $this->privateLoosenInternalMultiDimensionalArrayPathForEachVal($uploadedFiles);
        foreach ($flatted as $flat) {
            $uploadedFile = $flat[0];
            if (!($uploadedFile instanceof UploadedFileInterface)) {
                $path = $flat[1];
                $keys = implode('', array_map(function($pathKey) {
                    return '[' . $pathKey . ']';
                }, $path));
                throw new \InvalidArgumentException(sprintf("Invalid uploaded file at key %s.", $keys));
            }
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getServerParams() {
        return $this->serverParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams() {
        return $this->cookieParams;
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies) {
        $clone = $this->cloneThis();
        $clone->cookieParams = $cookies;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams() {
        return $this->queryParams;
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query) {
        $clone = $this->cloneThis();
        $clone->queryParams = $query;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles() {
        return $this->uploadedFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles) {
        $this->throwExceptionIfInvalidUploadedFilesStructure($uploadedFiles);
        $clone = $this->cloneThis();
        $clone->uploadedFiles = $uploadedFiles;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody() {
        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data) {
        if (!is_object($data) && !is_array($data) && !is_null($data)) {
            throw new \InvalidArgumentException('Parsed body must be an array, an object or null.');
        }
        $clone = $this->cloneThis();
        $clone->parsedBody = $data;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null) {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($name, $value) {
        $clone = $this->cloneThis();
        $clone->attributes[$name] = $value;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($name) {
        $clone = $this->cloneThis();
        unset($clone->attributes[$name]);
        return $clone;
    }

}
