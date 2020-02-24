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

namespace Norma\HTTP;

use Norma\Core\Utils\EnumToKeyValTrait;

/**
 * An HTTP status code.
 * 
 * @source https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 * @source https://httpstatuses.com/599
 * @source https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
 * 
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
abstract class HTTPStatusCodeEnum {
    
    use EnumToKeyValTrait;

    /**
     * The server has received the request headers and the client should proceed to send the request body
     * (in the case of a request for which a body needs to be sent; for example, a POST request).
     * Sending a large request body to a server after a request has been rejected for inappropriate headers would be inefficient.
     * To have a server check the request's headers, a client must send Expect: 100-continue as a header in its initial request and
     * receive a 100 Continue status code in response before sending the body.
     * If the client receives an error code such as 403 (Forbidden) or 405 (Method Not Allowed) then it shouldn't send the request's body.
     * The response 417 Expectation Failed indicates that the request should be repeated without the Expect header as it indicates
     * that the server doesn't support expectations (this is the case, for example, of HTTP/1.0 servers).
     */
    const HTTP_CONTINUE = 100; // Continue
    
    /**
     * The requester has asked the server to switch protocols and the server has agreed to do so.
     */
    const SWITCHING_PROTOCOLS = 101; // Switching Protocols
    
    /**
     * A WebDAV request may contain many sub-requests involving file operations, requiring a long time to complete the request.
     * This code indicates that the server has received and is processing the request, but no response is available yet.
     * This prevents the client from timing out and assuming the request was lost.
     */
    const PROCESSING = 102; // Processing
    
    /**
     * Used to return some response headers before final HTTP message.
     */
    const EARLY_HINTS = 103; // Early Hints
    
    /**
     * Standard response for successful HTTP requests.
     * The actual response will depend on the request method used.
     * In a GET request, the response will contain an entity corresponding to the requested resource.
     * In a POST request, the response will contain an entity describing or containing the result of the action.
     */
    const OK = 200; // OK
    
    /**
     * The request has been fulfilled, resulting in the creation of a new resource.
     */
    const CREATED = 201; // Created
    
    /**
     * The request has been accepted for processing, but the processing has not been completed.
     * The request might or might not be eventually acted upon, and may be disallowed when processing occurs.
     */
    const ACCEPTED = 202; // Accepted
    
    /**
     * The server is a transforming proxy (e.g. a Web accelerator) that received a 200 OK from its origin,
     * but is returning a modified version of the origin's response.
     */
    const NON_AUTHORITATIVE_INFORMATION = 203; // Non-Authoritative Information
    
    /**
     * The server successfully processed the request and is not returning any content.
     */
    const NO_CONTENT = 204; // No Content
    
    /**
     * The server successfully processed the request, but is not returning any content.
     * Unlike a 204 response, this response requires that the requester reset the document view.
     */
    const RESET_CONTENT = 205; // Reset Content
    
    /**
     * The server is delivering only part of the resource (byte serving) due to a range header sent by the client.
     * The range header is used by HTTP clients to enable resuming of interrupted downloads, or split a download
     * into multiple simultaneous streams.
     */
    const PARTIAL_CONTENT = 206; // Partial Content
    
    /**
     * The message body that follows is by default an XML message and can contain a number of separate response codes,
     * depending on how many sub-requests were made.
     */
    const MULTI_STATUS = 207; // Multi-Status
    
    /**
     * The members of a DAV binding have already been enumerated in a preceding part of the (multistatus) response, and are not being included again.
     */
    const ALREADY_REPORTED = 208; // Already Reported
    
    /**
     * The server has fulfilled a request for the resource, and the response is a representation of the result of one or more
     * instance-manipulations applied to the current instance.
     */
    const IM_USED = 226; // IM Used
    
    /**
     * Indicates multiple options for the resource from which the client may choose (via agent-driven content negotiation).
     * For example, this code could be used to present multiple video format options, to list files with different filename extensions,
     * or to suggest word-sense disambiguation.
     */
    const MULTIPLE_CHOICES = 300; // Multiple Choices
    
    /**
     * This and all future requests should be directed to the given URI.
     */
    const MOVED_PERMANENTLY = 301; // Moved Permanently
    
    /**
     * Tells the client to look at (browse to) another url. 302 has been superseded by 303 and 307.
     * This is an example of industry practice contradicting the standard.
     * The HTTP/1.0 specification (RFC 1945) required the client to perform a temporary redirect (the original describing phrase was "Moved Temporarily"),
     * but popular browsers implemented 302 with the functionality of a 303 See Other.
     * Therefore, HTTP/1.1 added status codes 303 and 307 to distinguish between the two behaviours.
     * However, some Web applications and frameworks use the 302 status code as if it were the 303.
     */
    const FOUND = 302; // Found
    
    /**
     * The response to the request can be found under another URI using the GET method.
     * When received in response to a POST (or PUT/DELETE), the client should presume
     * that the server has received the data and should issue a new GET request to the given URI.
     */
    const SEE_OTHER = 303; // See Other
    
    /**
     * Indicates that the resource has not been modified since the version specified by the request headers If-Modified-Since or If-None-Match.
     * In such case, there is no need to retransmit the resource since the client still has a previously-downloaded copy.
     */
    const NOT_MODIFIED = 304; // Not Modified
    
    /**
     * The requested resource is available only through a proxy, the address for which is provided in the response.
     * Many HTTP clients (such as Mozilla and Internet Explorer) do not correctly handle responses with this status code,
     * primarily for security reasons.
     */
    const USE_PROXY = 305; // Use Proxy
    
    /**
     * In this case, the request should be repeated with another URI; however, future requests should still use the original URI.
     * In contrast to how 302 was historically implemented, the request method is not allowed to be changed when reissuing the original request.
     * For example, a POST request should be repeated using another POST request.
     */
    const TEMPORARY_REDIRECT = 307; // Temporary Redirect
    
    /**
     * The request and all future requests should be repeated using another URI.
     * 307 and 308 parallel the behaviors of 302 and 301, but do not allow the HTTP method to change.
     * So, for example, submitting a form to a permanently redirected resource may continue smoothly.
     */
    const PERMANENT_REDIRECT = 308; // Permanent Redirect
    
    /**
     * The server cannot or will not process the request due to an apparent client error
     * (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing).
     */
    const BAD_REQUEST = 400; // Bad Request
    
    /**
     * Similar to 403 Forbidden, but specifically for use when authentication is required and has failed or has not yet been provided.
     * The response must include a WWW-Authenticate header field containing a challenge applicable to the requested resource.
     * See Basic access authentication and Digest access authentication.
     * 401 semantically means "unauthenticated", i.e. the user does not have the necessary credentials.
     * 
     * Note: Some sites issue HTTP 401 when an IP address is banned from the website (usually the website domain)
     *          and that specific address is refused permission to access a website.
     */
    const UNAUTHORIZED = 401; // Unauthorized
    
    /**
     * Reserved for future use.
     * The original intention was that this code might be used as part of some form of digital cash or micropayment scheme,
     * as proposed for example by GNU Taler, but that has not yet happened, and this code is not usually used.
     * Google Developers API uses this status if a particular developer has exceeded the daily limit on requests.
     */
    const PAYMENT_REQUIRED = 402; // Payment Required
    
    /**
     * The request was valid, but the server is refusing action.
     * The user might not have the necessary permissions for a resource, or may need an account of some sort.
     */
    const FORBIDDEN = 403; // Forbidden
    
    /**
     * The requested resource could not be found but may be available in the future. Subsequent requests by the client are permissible.
     */
    const NOT_FOUND = 404; // Not Found
    
    /**
     * A request method is not supported for the requested resource;
     * for example, a GET request on a form that requires data to be presented via POST, or a PUT request on a read-only resource.
     */
    const METHOD_NOT_ALLOWED = 405; // Method Not Allowed
    
    /**
     * The requested resource is capable of generating only content not acceptable according to the Accept headers sent in the request.
     * See Content negotiation.
     */
    const NOT_ACCEPTABLE = 406; // Not Acceptable
    
    /**
     * The client must first authenticate itself with the proxy.
     */
    const PROXY_AUTHENTICATION_REQUIRED = 407; // Proxy Authentication Required
    
    /**
     * The server timed out waiting for the request.
     * According to HTTP specifications: "The client did not produce a request within the time that the server was prepared to wait.
     * The client MAY repeat the request without modifications at any later time."
     */
    const REQUEST_TIMEOUT = 408; // Request Timeout
    
    /**
     * Indicates that the request could not be processed because of conflict in the request,
     * such as an edit conflict between multiple simultaneous updates.
     */
    const CONFLICT = 409; // Conflict
    
    /**
     * Indicates that the resource requested is no longer available and will not be available again.
     * This should be used when a resource has been intentionally removed and the resource should be purged.
     * Upon receiving a 410 status code, the client should not request the resource in the future.
     * Clients such as search engines should remove the resource from their indices.
     * Most use cases do not require clients and search engines to purge the resource, and a "404 Not Found" may be used instead.
     */
    const GONE = 410; // Gone
    
    /**
     * The request did not specify the length of its content, which is required by the requested resource.
     */
    const LENGTH_REQUIRED = 411; // Length Required
    
    /**
     * The server does not meet one of the preconditions that the requester put on the request.
     */
    const PRECONDITION_FAILED = 412; // Precondition Failed
    
    /**
     * The request is larger than the server is willing or able to process. Previously called "Request Entity Too Large".
     */
    const PAYLOAD_TOO_LARGE = 413; // Payload Too Large
    
    /**
     * The URI provided was too long for the server to process.
     * Often the result of too much data being encoded as a query-string of a GET request, in which case it should be converted to a POST request.
     * Called "Request-URI Too Long" previously.
     */
    const URI_TOO_LONG = 414; // URI Too Long
    
    /**
     * The request entity has a media type which the server or resource does not support.
     * For example, the client uploads an image as image/svg+xml, but the server requires that images use a different format.
     */
    const UNSUPPORTED_MEDIA_TYPE = 415; // Unsupported Media Type
    
    /**
     * The client has asked for a portion of the file (byte serving), but the server cannot supply that portion.
     * For example, if the client asked for a part of the file that lies beyond the end of the file.
     * Called "Requested Range Not Satisfiable" previously.
     */
    const RANGE_NOT_SATISFIABLE = 416; // Range Not Satisfiable
    
    /**
     * The server cannot meet the requirements of the Expect request-header field.
     */
    const EXPECTATION_FAILED = 417; // Expectation Failed
    
    /**
     * This code was defined in 1998 as one of the traditional IETF April Fools' jokes,
     * in RFC 2324, Hyper Text Coffee Pot Control Protocol, and is not expected to be implemented by actual HTTP servers.
     * The RFC specifies this code should be returned by teapots requested to brew coffee.
     * This HTTP status is used as an Easter egg in some websites, including Google.com.
     */
    const IM_A_TEAPOT = 418; // I'm a teapot
    
    /**
     * The request was directed at a server that is not able to produce a response (for example because of connection reuse).
     */
    const MISDIRECTED_REQUEST = 421; // Misdirected Request
    
    /**
     * The request was well-formed but was unable to be followed due to semantic errors.
     */
    const UNPROCESSABLE_ENTITY = 422; // Unprocessable Entity
    
    /**
     * The resource that is being accessed is locked.
     */
    const LOCKED = 423; // Locked
    
    /**
     * The request failed because it depended on another request and that request failed (e.g., a PROPPATCH).
     */
    const FAILED_DEPENDENCY = 424; // Failed Dependency
    
    /**
     * Indicates that the server is unwilling to risk processing a request that might be replayed.
     */
    const TOO_EARLY = 425; // Too Early
    
    /**
     * The client should switch to a different protocol such as TLS/1.0, given in the Upgrade header field.
     */
    const UPGRADE_REQUIRED = 426; // Upgrade Required
    
    /**
     * The origin server requires the request to be conditional.
     * Intended to prevent the 'lost update' problem, where a client GETs a resource's state, modifies it, and PUTs it back to the server,
     * when meanwhile a third party has modified the state on the server, leading to a conflict.
     */
    const PRECONDITION_REQUIRED = 428; // Precondition Required
    
    /**
     * The user has sent too many requests in a given amount of time.
     * Intended for use with rate-limiting schemes.
     */
    const TOO_MANY_REQUESTS = 429; // Too Many Requests
    
    /**
     * The server is unwilling to process the request because either an individual header field,
     * or all the header fields collectively, are too large.
     */
    const REQUEST_HEADER_FIELDS_TOO_LARGE = 431; // Request Header Fields Too Large
    
    /**
     * A server operator has received a legal demand to deny access to a resource or to a set of resources that includes the requested resource.
     * The code 451 was chosen as a reference to the novel Fahrenheit 451 (see the Acknowledgements in the RFC).
     */
    const UNAVAILABLE_FOR_LEGAL_REASONS = 451; // Unavailable For Legal Reasons
    
    /**
     * A generic error message, given when an unexpected condition was encountered and no more specific message is suitable.
     */
    const INTERNAL_SERVER_ERROR = 500; // Internal Server Error
    
    /**
     * The server either does not recognize the request method, or it lacks the ability to fulfil the request.
     * Usually this implies future availability (e.g., a new feature of a web-service API).
     */
    const NOT_IMPLEMENTED = 501; // Not Implemented
    
    /**
     * The server was acting as a gateway or proxy and received an invalid response from the upstream server.
     */
    const BAD_GATEWAY = 502; // Bad Gateway
    
    /**
     * The server is currently unavailable (because it is overloaded or down for maintenance).
     * Generally, this is a temporary state.
     */
    const SERVICE_UNAVAILABLE = 503; // Service Unavailable
    
    /**
     * The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.
     */
    const GATEWAY_TIMEOUT = 504; // Gateway Timeout
    
    /**
     * The server does not support the HTTP protocol version used in the request.
     */
    const HTTP_VERSION_NOT_SUPPORTED = 505; // HTTP Version Not Supported
    
    /**
     * Transparent content negotiation for the request results in a circular reference.
     */
    const VARIANT_ALSO_NEGOTIATES = 506; // Variant Also Negotiates
    
    /**
     * The server is unable to store the representation needed to complete the request.
     */
    const INSUFFICIENT_STORAGE = 507; // Insufficient Storage
    
    /**
     * The server detected an infinite loop while processing the request (sent in lieu of 208 Already Reported).
     */
    const LOOP_DETECTED = 508; // Loop Detected
    
    /**
     * Further extensions to the request are required for the server to fulfil it.
     */
    const NOT_EXTENDED = 510; // Not Extended
    
    /**
     * The client needs to authenticate to gain network access.
     * Intended for use by intercepting proxies used to control access to the network
     * (e.g., "captive portals" used to require agreement to Terms of Service before granting full Internet access via a Wi-Fi hotspot).
     */
    const NETWORK_AUTHENTICATION_REQUIRED = 511; // Network Authentication Required
    
    /**
     * This status code is not specified in any RFCs, but is used by some HTTP proxies to signal a network connect timeout
     * behind the proxy to a client in front of the proxy.
     */
    const NETWORK_CONNECTION_TIMEOUT_ERROR = 599; // Network Connect Timeout Error
    
    /* UNOFFICIAL CODES =================================================================================================== */
    /**
     * Used as a catch-all error condition for allowing response bodies to flow through Apache when ProxyErrorOverride is enabled.
     * When ProxyErrorOverride is enabled in Apache, response bodies that contain a status code of 4xx or 5xx are automatically discarded by Apache
     * in favor of a generic response or a custom response specified by the ErrorDocument directive.
     */
    const THIS_IS_FINE = 218; // This is fine
    
    /**
     * The Microsoft extension code indicated when Windows Parental Controls are turned on and are blocking access to the requested webpage.
     */
    const BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450; // Blocked by Windows Parental Controls
    
    /**
     * Returned by ArcGIS for Server. This code indicates an expired or otherwise invalid token.
     */
    const INVALID_TOKEN = 498; // Invalid Token
    
    /**
     * The server has exceeded the bandwidth specified by the server administrator;
     * this is often used by shared hosting providers to limit the bandwidth of customers.
     */
    const BANDWIDTH_LIMIT_EXCEEDED = 509; // Bandwidth Limit Exceeded
    
    /**
     * Used by some HTTP proxies to signal a network read timeout behind the proxy to a client in front of the proxy.
     */
    const NETWORK_READ_TIMEOUT_ERROR = 598; // Network read timeout error
    
    /* Internet Information Services */
    /**
     * The client's session has expired and must log in again.
     */
    const LOGIN_TIME_OUT = 440; // Login Time-out
    
    /**
     * The server cannot honour the request because the user has not provided the required information.
     */
    const RETRY_WITH = 449; // Retry With
    /* /Internet Information Services */
    
    /* nginx */
    /**
     * Used internally to instruct the server to return no information to the client and close the connection immediately.
     */
    const NO_RESPONSE = 444; // No Response
    
    /**
     * Client sent too large request or too long header line.
     */
    const REQUEST_HEADER_TOO_LARGE = 494; // Request header too large
    
    /**
     * An expansion of the 400 Bad Request response code, used when the client has provided an invalid client certificate.
     */
    const SSL_CERTIFICATE_ERROR = 495; // SSL Certificate Error
    
    /**
     * An expansion of the 400 Bad Request response code, used when a client certificate is required but not provided.
     */
    const SSL_CERTIFICATE_REQUIRED = 496; // SSL Certificate Required
    
    /**
     * An expansion of the 400 Bad Request response code, used when the client has made a HTTP request to a port listening for HTTPS requests.
     */
    const HTTP_REQUEST_SENT_TO_HTTPS_PORT = 497; // HTTP Request Sent to HTTPS Port
    
    /**
     * Used when the client has closed the request before the server could send a response.
     */
    const CLIENT_CLOSED_REQUEST = 499; // Client Closed Request
    /* /nginx */
    
    /* Cloudflare */
    /**
     * This error is used as a "catch-all response for when the origin server returns something unexpected",
     * listing connection resets, large headers, and empty or invalid responses as common triggers.
     */
    const UNKNOWN_ERROR = 520; // Unknown Error
    
    /**
     * The origin server has refused the connection from Cloudflare.
     */
    const WEB_SERVER_IS_DOWN = 521; // Web Server Is Down
    
    /**
     * Cloudflare could not negotiate a TCP handshake with the origin server.
     */
    const CONNECTION_TIMED_OUT = 522; // Connection Timed Out
    
    /**
     * Cloudflare could not reach the origin server; for example, if the DNS records for the origin server are incorrect.
     */
    const ORIGIN_IS_UNREACHABLE = 523; // Origin Is Unreachable
    
    /**
     * Cloudflare was able to complete a TCP connection to the origin server, but did not receive a timely HTTP response.
     */
    const A_TIMEOUT_OCCURRED = 524; // A Timeout Occurred
    
    /**
     * Cloudflare could not negotiate a SSL/TLS handshake with the origin server.
     */
    const SSL_HANDSHAKE_FAILED = 525; // SSL Handshake Failed
    
    /**
     * Cloudflare could not validate the SSL/TLS certificate that the origin server presented.
     */
    const INVALID_SSL_CERTIFICATE = 526; // Invalid SSL Certificate
    
    /**
     * Error 527 indicates that the request timed out or failed after the WAN connection had been established.
     */
    const RAILGUN_ERROR = 527; // Railgun Error
    
    /**
     * Error 530 indicates that the requested host name could not be resolved on the Cloudflare network to an origin server.
     */
    const ORIGIN_DNS_ERROR = 530; // Origin DNS Error
    /* /Cloudflare */
    /* /UNOFFICIAL CODES =================================================================================================== */
    
    /**
     * @var array Maps a status code to its textual representation.
     */
    public static $texts = [
        self::HTTP_CONTINUE => 'Continue',
        self::SWITCHING_PROTOCOLS => 'Switching Protocols',
        self::PROCESSING => 'Processing',
        self::EARLY_HINTS => 'Early Hints',
        self::OK => 'OK',
        self::CREATED => 'Created',
        self::ACCEPTED => 'Accepted',
        self::NON_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
        self::NO_CONTENT => 'No Content',
        self::RESET_CONTENT => 'Reset Content',
        self::PARTIAL_CONTENT => 'Partial Content',
        self::MULTI_STATUS => 'Multi-Status',
        self::ALREADY_REPORTED => 'Already Reported',
        self::IM_USED => 'IM Used',
        self::MULTIPLE_CHOICES => 'Multiple Choices',
        self::MOVED_PERMANENTLY => 'Moved Permanently',
        self::FOUND => 'Found',
        self::SEE_OTHER => 'See Other',
        self::NOT_MODIFIED => 'Not Modified',
        self::USE_PROXY => 'Use Proxy',
        self::TEMPORARY_REDIRECT => 'Temporary Redirect',
        self::PERMANENT_REDIRECT => 'Permanent Redirect',
        self::BAD_REQUEST => 'Bad Request',
        self::UNAUTHORIZED => 'Unauthorized',
        self::PAYMENT_REQUIRED => 'Payment Required',
        self::FORBIDDEN => 'Forbidden',
        self::NOT_FOUND => 'Not Found',
        self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::NOT_ACCEPTABLE => 'Not Acceptable',
        self::PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::REQUEST_TIMEOUT => 'Request Timeout',
        self::CONFLICT => 'Conflict',
        self::GONE => 'Gone',
        self::LENGTH_REQUIRED => 'Length Required',
        self::PRECONDITION_FAILED => 'Precondition Failed',
        self::PAYLOAD_TOO_LARGE => 'Payload Too Large',
        self::URI_TOO_LONG => 'URI Too Long',
        self::UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        self::RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable',
        self::EXPECTATION_FAILED => 'Expectation Failed',
        self::IM_A_TEAPOT => 'I\'m a teapot',
        self::MISDIRECTED_REQUEST => 'Misdirected Request',
        self::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
        self::LOCKED => 'Locked',
        self::FAILED_DEPENDENCY => 'Failed Dependency',
        self::TOO_EARLY => 'Too Early',
        self::UPGRADE_REQUIRED => 'Upgrade Required',
        self::PRECONDITION_REQUIRED => 'Precondition Required',
        self::TOO_MANY_REQUESTS => 'Too Many Requests',
        self::REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
        self::UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',
        self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::NOT_IMPLEMENTED => 'Not Implemented',
        self::BAD_GATEWAY => 'Bad Gateway',
        self::SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::GATEWAY_TIMEOUT => 'Gateway Timeout',
        self::HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
        self::VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
        self::INSUFFICIENT_STORAGE => 'Insufficient Storage',
        self::LOOP_DETECTED => 'Loop Detected',
        self::NOT_EXTENDED => 'Not Extended',
        self::NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required',
        self::THIS_IS_FINE => 'This is fine',
        self::BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS => 'Blocked by Windows Parental Controls',
        self::INVALID_TOKEN => 'Invalid Token',
        self::BANDWIDTH_LIMIT_EXCEEDED => 'Bandwidth Limit Exceeded',
        self::NETWORK_READ_TIMEOUT_ERROR => 'Network read timeout error',
        self::LOGIN_TIME_OUT => 'Login Time-out',
        self::RETRY_WITH => 'Retry With',
        self::NO_RESPONSE => 'No Response',
        self::REQUEST_HEADER_TOO_LARGE => 'Request header too large',
        self::SSL_CERTIFICATE_ERROR => 'SSL Certificate Error',
        self::SSL_CERTIFICATE_REQUIRED => 'SSL Certificate Required',
        self::HTTP_REQUEST_SENT_TO_HTTPS_PORT => 'HTTP Request Sent to HTTPS Port',
        self::CLIENT_CLOSED_REQUEST => 'Client Closed Request',
        self::UNKNOWN_ERROR => 'Unknown Error',
        self::WEB_SERVER_IS_DOWN => 'Web Server Is Down',
        self::CONNECTION_TIMED_OUT => 'Connection Timed Out',
        self::ORIGIN_IS_UNREACHABLE => 'Origin Is Unreachable',
        self::A_TIMEOUT_OCCURRED => 'A Timeout Occurred',
        self::SSL_HANDSHAKE_FAILED => 'SSL Handshake Failed',
        self::INVALID_SSL_CERTIFICATE => 'Invalid SSL Certificate',
        self::RAILGUN_ERROR => 'Railgun Error',
        self::ORIGIN_DNS_ERROR => 'Origin DNS Error',
        self::NETWORK_CONNECTION_TIMEOUT_ERROR => 'Network Connect Timeout Error',
    ];
    
}
