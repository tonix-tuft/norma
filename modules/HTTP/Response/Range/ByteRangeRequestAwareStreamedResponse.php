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

namespace Norma\HTTP\Response\Range;

use Norma\HTTP\Response\Range\RangeRequestAwareStreamedResponseInterface;
use Norma\HTTP\Response\StreamedResponse;
use Norma\HTTP\Request\Server\ServerRequestInterface;
use Norma\HTTP\Request\RequestMethodEnum;
use Norma\Algorithm\Utils\FrameworkAlgorithmUtilsTrait;
use Norma\HTTP\HTTPStatusCodeEnum;
use Norma\HTTP\HTTPTrait;
use Psr\Http\Message\StreamInterface;

/**
 * A streamed response aware of byte range requests.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class ByteRangeRequestAwareStreamedResponse extends StreamedResponse implements RangeRequestAwareStreamedResponseInterface {
    
    use FrameworkAlgorithmUtilsTrait;
    use HTTPTrait;
    
    /**
     * @var ServerRequestInterface|null
     */
    protected $request;
    
    /**
     * @var bool
     */
    protected $isDefaultStatusCode;
    
    /**
     * @var array|null
     */
    protected $byteRanges;
    
    /**
     * @var bool
     */
    protected $isMultipartByteRanges;
    
    /**
     * @var string|null
     */
    protected $byteRangeContentType;
    
    /**
     * @var string|int|null
     */
    protected $completeLength;
    
    /**
     * @var string|null
     */
    protected $multipartByteRangesBoundary;
    
    /**
     * {@inheritdoc}
     * @param ServerRequestInterface|null $request A server request interface to use to extrapolate requested byte ranges from the `Range` HTTP header.
     */
    public function __construct(StreamInterface $body, $statusCode = HTTPStatusCodeEnum::OK, array $headers = [], $bufferSize = 8192, ServerRequestInterface $request = null) {        
        $this->bufferSize = $bufferSize;
        $this->request = $request;
        
        if ($statusCode == HTTPStatusCodeEnum::OK) {
            $this->isDefaultStatusCode = true;
        }
        
        parent::__construct($body, $statusCode, $headers, $bufferSize);
    }
    
    /**
     * Computes the content length of the body from the body of the response.
     * 
     * @return int The size of the response body, in bytes.
     */
    protected function computeContentLengthFromBody() {
        $this->body->rewind();
        $size = 0;
        while (!$this->body->eof()) {
            $size += strlen($this->body->read($this->bufferSize));
        }
        $this->body->rewind();
        return $size;
    }
    
    /**
     * Parses the ranges of the `Range` HTTP header.
     * 
     * @source https://tools.ietf.org/html/rfc7233
     * @source https://httpwg.org/specs/rfc7233.html
     * 
     * @param ServerRequestInterface $request The value of the `Range` HTTP header.
     * @param int $contentLength The length of the current representation of the response body in bytes, or NULL if unknown.
     * @return bool|null|array FALSE is returned if all the ranges are unsatisfiable (the byte-range-set of the Range HTTP header is unsatisfiable),
     *                                       yet the ranges being all syntactically valid.
     *                                       NULL is returned if the `Range` header is missing
     *                                       or if it must be ignored because of a non-GET request method
     *                                       or if the value of the `Range` header is not a valid `byte-ranges-specifier`
     *                                       or if it contains a syntactically invalid byte range and must be therefore ignored.
     *                                       If at least one range is satisfiable, and all ranges are syntactically valid, an array with the following keys is returned:
     *                                       - ranges: an array with the parsed satisfiable ranges. Each value is an array with two keys, `first_byte_pos` and `last_byte_pos`.
     *                                          Overlapping ranges are coalesced;
     *                                       - multipart: a boolean indicating whether a multipart response should be generated or not (TRUE if a multipart response should be generated);
     */
    protected function parseRequestByteRanges(ServerRequestInterface $request, $contentLength) {
        
        /*
         * Some drafts taken from the RFC 7233 specification.
         * 
         * byte-ranges-specifier = bytes-unit "=" byte-range-set
         * 
         * byte-range-set  = 1#( byte-range-spec | suffix-byte-range-spec )     ; comma-separated list
         * byte-range-set = *( "," OWS )
         *                               (
         *                                 byte-range-spec /
         *                                 suffix-byte-range-spec
         *                               ) *( OWS "," [ OWS ( byte-range-spec / suffix-byte-range-spec ) ] )
         * 
         * byte-range-spec = first-byte-pos "-" [last-byte-pos]        ; <start>-<end> OR <start>-
         * first-byte-pos  = 1*DIGIT
         * last-byte-pos   = 1*DIGIT
         * suffix-byte-range-spec = "-" suffix-length                        ; -<end>
         * suffix-length = 1*DIGIT
         * 
         * The byte positions specified are inclusive. Byte offsets start at zero.
         * If the last-byte-pos value is present, it MUST be greater than or equal to the first-byte-pos in that byte-range-spec, or the byte-range-spec is syntactically invalid.
         * The recipient of a byte-range-set that includes one or more syntactically invalid byte-range-spec values MUST ignore the header field that includes that byte-range-set.
         * 
         * If the last-byte-pos value is absent, or if the value is greater than or equal to the current length of the entity-body,
         * last-byte-pos is taken to be equal to one less than the current length of the entity-body in bytes.
         * A suffix-byte-range-spec specifies the last N bytes of an entity-body. If the entity is shorter than the specified suffix-length, the entire entity-body is used.
         * 
         * If a syntactically valid byte-range-set includes at least one byte-range-spec whose first-byte-pos is less than the current length of the entity-body,
         * or at least one suffix-byte-range-spec with a non-zero suffix-length, then the byte-range-set is satisfiable.
         * Otherwise, the byte-range-set is unsatisfiable. If the byte-range-set is unsatisfiable, the server SHOULD return a response with a status of 416
         * (Requested range not satisfiable).
         * Otherwise, the server SHOULD return a response with a status of 206 (Partial Content) containing the satisfiable ranges of the entity-body.
         * 
         * A byte-range-spec is syntactically invalid if the last-byte-pos value is present and less than the first-byte-pos.
         * A client can limit the number of bytes requested without knowing the size of the selected representation.
         * If the last-byte-pos value is absent, or if the value is greater than or equal to the current length of the representation data,
         * the byte range is interpreted as the remainder of the representation
         * (i.e., the server replaces the value of last-byte-pos with a value that is one less than the current length of the selected representation).
         * 
         * Other valid (but not canonical) specifications of the second 500 bytes (byte offsets 500-999, inclusive):
         *      bytes=500-600,601-999
         *      bytes=500-700,601-999
         * 
         * An origin server MUST ignore a Range header field that contains a range unit it does not understand.
         * 
         * A server that supports range requests MAY ignore or reject a Range header field that consists of more than two overlapping ranges,
         * or a set of many small ranges that are not listed in ascending order, since both are indications of either a broken client or a deliberate denial-of-service attack.
         * 
         * If a valid byte-range-set includes at least one byte-range-spec with a first-byte-pos that is less than the current length of the representation,
         * or at least one suffix-byte-range-spec with a non-zero suffix-length, then the byte-range-set is satisfiable.
         * Otherwise, the byte-range-set is unsatisfiable.
         * The 206 (Partial Content) status code indicates that the server is successfully fulfilling a range request for the target resource by transferring
         * one or more parts of the selected representation that correspond to the satisfiable ranges found in the request's Range header field
         * 
         *      HTTP/1.1 206 Partial Content
         *      Date: Wed, 15 Nov 1995 06:25:24 GMT
         *      Last-Modified: Wed, 15 Nov 1995 04:58:08 GMT
         *      Content-Length: 1741
         *      Content-Type: multipart/byteranges; boundary=THIS_STRING_SEPARATES
         * 
         *      --THIS_STRING_SEPARATES
         *      Content-Type: application/pdf
         *      Content-Range: bytes 500-999/8000
         * 
         *      ...the first range...
         *      --THIS_STRING_SEPARATES
         *      Content-Type: application/pdf
         *      Content-Range: bytes 7000-7999/8000
         * 
         *      ...the second range
         *      --THIS_STRING_SEPARATES--
         * 
         * A server MUST NOT generate a multipart response to a request for a single range, since a client that does not request multiple parts might not support multipart responses.
         * However, a server MAY generate a multipart/byteranges payload with only a single body part if multiple ranges were requested and only one range was found to be satisfiable
         * or only one range remained after coalescing.
         * 
         * A client cannot rely on receiving the same ranges that it requested, nor the same order that it requested.
         * 
         * The 416 (Range Not Satisfiable) status code indicates that none of the ranges in the request's Range header field overlap the current extent of the selected resource
         * or that the set of ranges requested has been rejected due to invalid ranges or an excessive request of small or overlapping ranges.
         * For byte ranges, failing to overlap the current extent means that the first-byte-pos of all of the byte-range-spec values were greater than or equal to the current length of the selected representation.
         */
        
        /*
         * Check for a missing `Range` header or non-GET request.
         * A server MUST ignore a Range header field received with a request method other than GET (as per RFC 7233).
         * 
         * @source https://httpwg.org/specs/rfc7233.html#header.range
         */
        if (!$this->request->hasHeader('Range') || $this->request->getMethod() !== RequestMethodEnum::GET) {
            return null;
        }
        
        // byte-range-set.
        $byteRangeHeaderValue = $request->getHeaderLine('Range');
        
        /*
         * @see https://regex101.com/r/FPVS8K/1 Regex for byte ranges.
         */
        if (preg_match('/^
                bytes
                =
                (?P<byte_range_set>
                    (?P<byte_range_set_alternatives>
                        (?P<byte_range_spec>
                            (?P<OWS>
                                (?:(?:\r\n)?(?:[ ]|\t))*
                            )
                            # <start>-<end> OR <start>-
                            ([0-9]+-(?:[0-9]+)?)
                            (?P>OWS)
                        )
                        |
                        (?P<suffix_byte_range_spec>
                            (?P>OWS)
                            # -<end>
                            (-[0-9]+)
                            (?P>OWS)
                        )
                    )
                    (?:
                        ,
                        (?P>byte_range_set_alternatives)
                    )*
                )
                $/x', $byteRangeHeaderValue)
        ) {
            // `Range` header value.
            $isMultipartByteRanges = false;
            
            list(, $rangesStr) = explode('=', $byteRangeHeaderValue, 2);
            $ranges = array_map('trim', explode(',', $rangesStr));
            
            if (count($ranges) > 1) {
                $isMultipartByteRanges = true;
            }
            else {
                $isMultipartByteRanges = false;
            }
            
            // Ranges must all be syntactically valid and at least one satisfiable range must be added to `$satisfiableByteRanges`.
            $satisfiableByteRanges = [];
            foreach ($ranges as $rangeStr) {
                $range = explode('-', $rangeStr);
                
                $start = 0;
                $end = $contentLength - 1; // The length of the body must be known.
                
                if ($range[0] !== '') {
                    // byte_range_spec (<start>-<end> or <start>-).
                    $start = $range[0];
                    if ($range[1] !== '') {
                        $end = $range[1];
                        if ($end < $start) {
                            // Syntactically invalid byte range, `Range` header must be ignored.
                            return null;
                        }
                        else {
                            // If the value of `last-byte-pos` is greater than or equal to the current length of the entity-body,
                            // `last-byte-pos` is taken to be equal to one less than the current length of the entity-body in bytes.
                            $end = $end >= $contentLength ? $contentLength - 1 : $end;
                        }
                    }
                    
                    if ($start >= $contentLength) {
                        // Range not satisfiable.
                        continue;
                    }
                }
                else {
                    // suffix_byte_range_spec (-<end>).
                    $lastNBytes = $range[1];
                    if ($lastNBytes == 0) {
                        // Range not satisfiable.
                        continue;
                    }
                    
                    if ($contentLength < $range[1]) {
                        $start = 0;
                    }
                    else {
                        $start = $contentLength - $range[1];
                    }
                }
                
                // Satisfiable range.
                $satisfiableByteRanges[] = [
                    (int) $start,
                    (int) $end
                ];
            }
            
            if (empty($satisfiableByteRanges)) {
                // All ranges are unsatisfiable.
                return false;
            }
            
            $mergedSegments = $this->mergeSegments($satisfiableByteRanges);
            $coalescedSatisfiableByteRanges = array_map(function($mergedSegment) {
                return [
                    'first_byte_pos' => $mergedSegment[0],
                    'last_byte_pos' => $mergedSegment[1]
                ];
            }, $mergedSegments);
            
            return [
                'ranges' => $coalescedSatisfiableByteRanges,
                'multipart' => $isMultipartByteRanges
            ];
        }
        else {
            // The value of the `Range` header is not a valid `byte-ranges-specifier`
            return null;
        }
    }
    
    /**
     * Tests whether the result of a byte ranges parse should be rejected.
     * 
     * @param array $parseResult The result of the parse as an array with the keys `ranges` and `multipart` as returned by {@link ByteRangeRequestAwareStreamedResponse::parseRequestByteRanges()}.
     * @return bool TRUE if the parse result should be rejected with a 416 status code, FALSE otherwise.
     */
    protected function shouldRejectByteRangesParseResult($parseResult) {
        return count($parseResult['ranges']) > 10;
    }
    
    /**
     * {@inheritdoc}
     */
    public function send() {
        $this->byteRanges = null;
        $this->isMultipartByteRanges = false;
        $this->byteRangeContentType = null;
        $this->completeLength = null;
        $this->multipartByteRangesBoundary = null;
        
        if (!is_null($this->request)) {
            $contentLength = $this->getHeaderLine('Content-Length');
            if (empty($contentLength)) {
                $contentLength = $this->body->getSize();
            }
            
            if (empty($contentLength)) {
                $contentLength = $this->computeContentLengthFromBody();
                $this->completeLength = '*';
            }
            else {
                $this->completeLength = $contentLength;
            }
            
            $parseResult = $this->parseRequestByteRanges($this->request, $contentLength);
            if (!is_null($parseResult)) {
                if ($parseResult === false || $this->shouldRejectByteRangesParseResult($parseResult)) {
                    $this->setStatus(HTTPStatusCodeEnum::RANGE_NOT_SATISFIABLE, HTTPStatusCodeEnum::$texts[HTTPStatusCodeEnum::RANGE_NOT_SATISFIABLE])
                            ->setHeaderIfNone('Content-Range', sprintf('bytes */%s', $this->completeLength));
                }
                else {
                    $this->byteRanges = $parseResult['ranges'];
                    $this->isMultipartByteRanges = $parseResult['multipart'];
                    $this->setStatus(HTTPStatusCodeEnum::PARTIAL_CONTENT, HTTPStatusCodeEnum::$texts[HTTPStatusCodeEnum::PARTIAL_CONTENT]);
                    
                    if ($this->isMultipartByteRanges) {
                        // `multipart/byteranges` response.
                        $this->byteRangeContentType = $this->getHeaderLine('Content-Type');
                        $this->multipartByteRangesBoundary = $this->generateMultipartBoundary();
                        
                        $this->setHeader('Content-Type', 'multipart/byteranges')
                                ->unsetHeader('Content-Range')
                                ->unsetHeader('Content-Length')
                        ;
                    }
                    else {
                        // Single range response.
                        $range = $this->byteRanges[0];
                        $this->setHeader('Content-Range', sprintf('bytes %s-%s/%s',
                                        $range['first_byte_pos'], $range['last_byte_pos'], $this->completeLength
                                    ))
                                ->setHeader('Content-Length', $range['last_byte_pos'] - $range['first_byte_pos'] + 1)
                        ;
                    }
                }
            }
        }
        
        $this->setHeaderIfNone('Accept-Ranges', 'bytes');
        
        parent::send();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function sendHTTPBody() {
        if (!empty($this->byteRanges)) {
            $this->sendHTTPBodyByteRanges($this->byteRanges, $this->isMultipartByteRanges);
        }
        else if ($this->getStatusCode() != HTTPStatusCodeEnum::RANGE_NOT_SATISFIABLE) {
            parent::sendHTTPBody();
        }
    }
    
    /**
     * Sends the response body taking byte ranges into account.
     * 
     * @param array $byteRanges The byte ranges to send.
     * @param bool $isMultipartByteRanges A boolean indicating whether the response should be a `multipart/byteranges` response.
     * @return void
     */
    protected function sendHTTPBodyByteRanges($byteRanges, $isMultipartByteRanges) {
        if (!$isMultipartByteRanges) {
            // Single range, no `multipart/byteranges` response.
            $byteRange = $byteRanges[0];
            $this->sendHTTPBodyByteRange($byteRange['first_byte_pos'], $byteRange['last_byte_pos']);
        }
        else {
            // `multipart/byteranges` response.
            $this->sendHTTPBodyMultipartByteRanges($byteRanges, $this->byteRangeContentType, $this->multipartByteRangesBoundary, $this->completeLength);
        }
    }
    
    /**
     * Sends a byte range.
     * 
     * @param int $firstBytePos The fist byte pos (inclusive).
     * @param int $lastBytePos The last byte pos (inclusive).
     * @return void
     */
    protected function sendHTTPBodyByteRange($firstBytePos, $lastBytePos) {
        $this->body->seek($firstBytePos);
        $length = $lastBytePos - $firstBytePos + 1;
        while ($this->bufferSize <= $length && !$this->body->eof()) {
            $buffer = $this->body->read($this->bufferSize);
            $length -= strlen($buffer);
            echo $buffer;
        }
        if ($length > 0 && !$this->body->eof()) {
            echo $this->body->read($length);
        }
    }
    
    /**
     * Sends the byte ranges of a `multipart/byteranges` response.
     * 
     * @param array $byteRanges The byte ranges to send.
     * @param string $byteRangeContentType The byte ranges content type to use for each chunk.
     * @param string $multipartByteRangesBoundary The boundary string to use for the multipart response.
     * @param string|int $completeLength The complete length to use for the `Content-Range` multipart header ('*' if unknown).
     * @return void
     */
    protected function sendHTTPBodyMultipartByteRanges($byteRanges, $byteRangeContentType, $multipartByteRangesBoundary, $completeLength) {
        foreach ($byteRanges as $byteRange) {
            $firstBytePos = $byteRange['first_byte_pos'];
            $lastBytePos = $byteRange['last_byte_pos'];
            $completeLength = empty($completeLength) ? '*' : $completeLength;
            
            echo sprintf("--%s\r\n", $multipartByteRangesBoundary);
            if (!empty($byteRangeContentType)) {
                echo sprintf("Content-Type: %s\r\n", $byteRangeContentType);
            }
            echo sprintf("Content-Range: %s-%s/%s\r\n", $firstBytePos, $lastBytePos, $completeLength);
            echo "\r\n";
            $this->sendHTTPBodyByteRange($firstBytePos, $lastBytePos);
            echo "\r\n";
        }
        echo sprintf("--%s--", $multipartByteRangesBoundary);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function withRequest(ServerRequestInterface $request) {
        $clone = $this->cloneThis();
        $clone->request = $request;
        
        return $clone;
    }
    
    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '') {
        $this->isDefaultStatusCode = false;
        parent::withStatus($code, $reasonPhrase);
    }

}
