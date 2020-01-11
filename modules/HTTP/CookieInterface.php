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

namespace Norma\HTTP;

/**
 * The interface of a cookie.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface CookieInterface {
    
    /**
     * Gets the name of the cookie.
     * 
     * @return string The name of the cookie.
     */
    public function getName();
    
    /**
     * Get the value of the cookie.
     * 
     * @return string The value of the cookie.
     */
    public function getValue();
    
    /**
     * Get the time the cookie expires. This is a Unix timestamp so is in number of seconds since the epoch.
     * 
     * @return int The expire Unix timestamp of the cookie.
     */
    public function getExpire();
    
    /**
     * Get the path on the server in which the cookie will be available on.
     * 
     * @return string The path.
     */
    public function getPath();
    
    /**
     * Get the (sub)domain that the cookie is available to.
     * 
     * @return string The domain.
     */
    public function getDomain();
    
    /**
     * Checks whether the cookie is secure (should only be transmitted over a secure HTTPS connection from the client) or not.
     * 
     * @return bool TRUE if the cookie will only be set if a secure connection exists, FALSE otherwise.
     */
    public function isSecure();
    
    /**
     * Checks whether the cookie will be made accessible only through the HTTP protocol or not.
     * 
     * @return bool TRUE if cookie will be made accessible only through the HTTP protocol, FALSE otherwise.
     */
    public function isHTTPOnly();
    
    /**
     * Gets the options associated with this cookie. This method SHOULD be used on PHP versions 7.3.0 and higher.
     * 
     * Implementors are NOT REQUIRED to check for the right PHP version when using this method and when setting the cookie.
     * The client's code is responsible of identifying the correct PHP version and use this method when setting a cookie if they need
     * to use the new signature of the `setcookie()` PHP function.
     * 
     * NULL MUST be returned in case the cookie attributes were not set through an options associative array.
     * 
     * @return array|null An associative array which may have any of the keys expires, path, domain, secure, httponly and samesite.
     */
    public function getOptions();
    
    /**
     * Sets the value of the cookie.
     * 
     * @param string $value The value of the cookie.
     * @return static
     */
    public function setValue(string $value);
    
    /**
     * Sets the expire time of the cookie, as a Unix timestamp.
     * 
     * If the options of this cookie were specified using an associative options array (PHP 7.3.0 `setcookie()` syntax),
     * then this method MUST change the corresponding `expire` key of the options array.
     * 
     * @param int $expire The time the cookie expires.
     * @return static
     */
    public function setExpire(int $expire);
    
    /**
     * Sets the path on the server in which the cookie will be available on.
     * 
     * If the options of this cookie were specified using an associative options array (PHP 7.3.0 `setcookie()` syntax),
     * then this method MUST change the corresponding `path` key of the options array.
     * 
     * @param string $path The path on the server in which the cookie will be available on.
     * @return static
     */
    public function setPath(string $path);
    
    /**
     * Sets the (sub)domain that the cookie is available to.
     * If the cookie should be restricted to a single host, the domain parameter has to be an empty string.
     * 
     * If the options of this cookie were specified using an associative options array (PHP 7.3.0 `setcookie()` syntax),
     * then this method MUST change the corresponding `domain` key of the options array.
     * 
     * @param string $domain The domain.
     * @return static
     */
    public function setDomain(string $domain);
    
    /**
     * Sets whether the cookie is secure (should only be transmitted over a secure HTTPS connection from the client) or not.
     * 
     * If the options of this cookie were specified using an associative options array (PHP 7.3.0 `setcookie()` syntax),
     * then this method MUST change the corresponding `secure` key of the options array.
     * 
     * @param bool $secure TRUE if secure, FALSE otherwise.
     * @return static
     */
    public function setSecure(bool $secure);
    
    /**
     * Sets whether the cookie will be made accessible only through the HTTP protocol.
     * 
     * If the options of this cookie were specified using an associative options array (PHP 7.3.0 `setcookie()` syntax),
     * then this method MUST change the corresponding `httponly` key of the options array.
     * 
     * @param bool $HTTPOnly TRUE if the cookie will be made accessible only through the HTTP protocol, FALSE otherwise.
     * @return static
     */
    public function setHTTPOnly(bool $HTTPOnly);
    
    /**
     * Sets the options of this cookie.
     * The `$options` parameter is an associative array which may have any of the keys expires, path, domain, secure, httponly and samesite.
     * Implementors SHOULD NOT check whether the PHP version of the client's code is suitable for the usage of this method or not,
     * but as soon as this method is called, then the setter methods of the expires, path, domain, secure and httponly attributes
     * MUST modify the underlying options array and {@link CookieInterface::getOptions()} MUST return an array with the modified values
     * for the corresponding keys.
     * 
     * @param array $options An associative array which may have any of the keys expires, path, domain, secure, httponly and samesite.
     *                                      The values of expires, path, domain, secure and httponly have the same type as their corresponding setter methods.
     *                                      The value of the samesite key should be either Lax or Strict.
     * @return static
     */
    public function setOptions(array $options);
    
}
