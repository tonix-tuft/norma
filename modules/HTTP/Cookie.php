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

namespace Norma\HTTP;

use Norma\HTTP\CookieInterface;

/**
 * The implementation of the interface of a cookie.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class Cookie implements CookieInterface {
    
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;
    
    /**
     * @var int
     */
    protected $expire;
    
    /**
     * @var string
     */
    protected $path;
    
    /**
     * @var string
     */
    protected $domain;
    
    /**
     * @var bool
     */
    protected $secure;
    
    /**
     * @var bool
     */
    protected $HTTPOnly;
    
    /**
     * @var null|array
     */
    protected $options = NULL;
    
    /**
     * Constructs a new cookie. Starting with PHP 7.3.0, the PHP's `setcookie` function has two signatures:
     * 
     *      bool setcookie ( string $name [, string $value = "" [, int $expires = 0 [, string $path = "" [, string $domain = "" [, bool $secure = FALSE [, bool $httponly = FALSE ]]]]]] )
     *      bool setcookie ( string $name [, string $value = "" [, array $options = [] ]] )
     * 
     * This method guarantees compliance with these two signatures, though it does not check the effective PHP version.
     * 
     * The `$options` parameter is an associative array which may have any of the keys expires, path, domain, secure, httponly and samesite.
     * The values have the same meaning as described for the parameters with the same name. The value of the samesite element should be either Lax or Strict.
     * If any of the allowed options are not given, their default values are the same as the default values of the explicit parameters.
     * If the samesite element is omitted, no SameSite cookie attribute is set.
     * 
     * @source http://php.net/manual/en/function.setcookie.php
     * 
     * @param string $name The name of the cookie.
     * @param string $value The value of the cookie. This value is stored on the clients computer; do not store sensitive information.
     *                                    Assuming the name is 'cookiename', this value is retrieved through $_COOKIE['cookiename'].
     * @param int $expire The time the cookie expires. This is a Unix timestamp so is in number of seconds since the epoch.
     *                                 In other words, you'll most likely set this with the time() function plus the number of seconds before you want it to expire.
     *                                 Or you might use mktime(). time()+60*60*24*30 will set the cookie to expire in 30 days.
     *                                 If set to 0, or omitted, the cookie will expire at the end of the session (when the browser closes).
     * @param string $path The path on the server in which the cookie will be available on. If set to '/', the cookie will be available within the entire domain.
     *                                   If set to '/foo/', the cookie will only be available within the /foo/ directory and all sub-directories such as /foo/bar/ of domain.
     *                                   The default value is the current directory that the cookie is being set in.
     * @param string $domain The (sub)domain that the cookie is available to. Setting this to a subdomain (such as 'www.example.com')
     *                                        will make the cookie available to that subdomain and all other sub-domains of it (i.e. w2.www.example.com).
     *                                        To make the cookie available to the whole domain (including all subdomains of it), simply set the value to the domain name ('example.com', in this case).
     *                                        Older browsers still implementing the deprecated » RFC 2109 may require a leading . to match all subdomains.
     * @param bool $secure Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client. When set to TRUE,
     *                                    the cookie will only be set if a secure connection exists. On the server-side, it's on the programmer to send this kind of cookie only on secure connection
     *                                    (e.g. with respect to $_SERVER["HTTPS"]).
     * @param bool $httponly When TRUE the cookie will be made accessible only through the HTTP protocol. This means that the cookie won't be accessible by scripting languages,
     *                                       such as JavaScript. It has been suggested that this setting can effectively help to reduce identity theft through XSS attacks (although it is not supported by all browsers),
     *                                       but that claim is often disputed. Added in PHP 5.2.0. TRUE or FALSE.
     */
    public function __construct(string $name, string $value = "", $expire = 0, string $path = "", string $domain = "", bool $secure = false, bool $httponly = false) {
        $this->name = $name;
        $this->value = $value;
        $this->expire = $expire;
        if (is_array($this->expire)) {
            $this->options = $this->expire;
            $this->valorizeFieldsFromOptions();
        }
        else {
            $this->path = $path;
            $this->domain = $domain;
            $this->secure = $secure;
            $this->HTTPOnly = $httponly;
        }
    }
    
    /**
     * Valorizes the fields (attributes) of the cookie through the options array (PHP 7.3.0).
     * The `samesite` attribute is intentionally omitted, because clients should
     * rely on the return value of {@link CookieInterface::getOptions()} and use the corresponding
     * signature of the `setcookie()` PHP function.
     * 
     * @return void
     */
    protected function valorizeFieldsFromOptions() {
        $this->expire = $this->options['expire'] ?? 0;
        $this->path = $this->options['path'] ?? '';
        $this->domain = $this->options['domain'] ?? '';
        $this->secure = $this->options['secure'] ?? false;
        $this->HTTPOnly = $this->options['httponly'] ?? false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getValue() {
        return $this->value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getExpire() {
        return $this->expire;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPath() {
        return $this->path;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDomain() {
        return $this->domain;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isSecure() {
        return $this->secure;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isHTTPOnly() {
        return $this->HTTPOnly;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOptions() {
        return $this->options;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setValue(string $value) {
        $this->value = $value;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setExpire(int $expire) {
        $this->expire = $expire;
        if (is_array($this->options)) {
            $this->options['expire'] = $expire;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setPath(string $path) {
        $this->path = $path;
        if (is_array($this->options)) {
            $this->options['path'] = $path;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDomain(string $domain) {
        $this->domain = $domain;
        if (is_array($this->options)) {
            $this->options['domain'] = $domain;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setSecure(bool $secure) {
        $this->secure = $secure;
        if (is_array($this->options)) {
            $this->options['secure'] = $secure;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setHTTPOnly(bool $HTTPOnly) {
        $this->HTTPOnly = $HTTPOnly;
        if (is_array($this->options)) {
            $this->options['httponly'] = $HTTPOnly;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options) {
        $this->options = $options;
        $this->valorizeFieldsFromOptions();
    }
    
}
