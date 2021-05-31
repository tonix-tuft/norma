<?php

/*
 * Copyright (c) 2021 Anton Bagdatyev (Tonix)
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

namespace Norma\AOP\Stream;

use Norma\AOP\Weaving\AspectWeaverInterface;
use Norma\AOP\AOPException;
use php_user_filter as PHPUserFilter;

/**
 * The implementation of an AOP filename stream filter rewriter.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class AOPFilenameStreamFilterRewriter extends PHPUserFilter implements AOPFilenameStreamFilterRewriterInterface {
    
    /**
     * The name of the filter.
     */
    const STREAM_FILTER_FILTERNAME = 'norma.aop';
    
    /**
     * The stream filter read prefix for.
     */
    const STREAM_FILTER_READ_PREFIX = 'php://filter/read=';
    
    /**
     * @var bool
     */
    protected static $registered = FALSE;
    
    /**
     * @var AspectWeaverInterface|null
     */
    protected static $aspectWeaver = NULL;
    
    /**
     * @var string
     */
    protected $source;
    
    /**
     * Constructs a new stream filter rewriter.
     * 
     * @param AspectWeaverInterface $aspectWeaver
     */
    public function __construct(AspectWeaverInterface $aspectWeaver = NULL) {
        if (static::$aspectWeaver === NULL) {
            static::$aspectWeaver = $aspectWeaver;
        }
        $this->source = '';
    }
    
    /**
     * {@inheritdoc}
     */
    public function filter($in, $out, &$consumed, $closing) {
        if (static::$aspectWeaver === NULL) {
            throw new AOPException(sprintf('Aspect weaver not set for stream filter. "%s" stream filter must be instantiated at least once with a valid reference to an aspect weaver instance.', get_class($this)));
        }
        
        while ($bucket = stream_bucket_make_writeable($in)) {
            $this->source .= $bucket->data;
        }
        
        if ($closing) {
            if (!is_resource($this->stream)) {
                throw new AOPException('Stream not set for stream filter. Attempting to create a new bucket would be impossible.');
            }
            
            $consumed = strlen($this->source);

            $source = static::$aspectWeaver->weaveSourceCodeIfNeeded($this->source);

            $bucket = stream_bucket_new($this->stream, $source);
            stream_bucket_append($out, $bucket);

            return PSFS_PASS_ON;
        }
        
        return PSFS_FEED_ME;
    }
    
    /**
     * {@inheritdoc}
     */
    public function register() {
        $res = stream_filter_register(static::STREAM_FILTER_FILTERNAME, get_class($this));
        if (!$res) {
            throw new AOPException(sprintf('Could not register the stream filter with filtername "%s".', static::STREAM_FILTER_FILTERNAME));
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function rewrite($filename) {
        return static::STREAM_FILTER_READ_PREFIX . static::STREAM_FILTER_FILTERNAME . '/resource=' . $filename;
    }
    
}
