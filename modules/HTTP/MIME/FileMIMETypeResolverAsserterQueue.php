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

namespace Norma\HTTP\MIME;

use Norma\HTTP\MIME\FileMIMETypeResolverInterface;

/**
 * A file MIME type resolver which uses a queue of other resolvers and asserts that all the resolvers
 * of the queue return the same MIME type specified during construction.
 * If even one of the enqueued resolvers of the queue return a MIME type for a file which differs from
 * the assertion MIME type specified during construction, then this class throws a {@link \RuntimeException} exception.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class FileMIMETypeResolverAsserterQueue extends \SplQueue implements FileMIMETypeResolverInterface {
    
    /**
     * @var string
     */
    protected $assertionMIMEType;
    
    /**
     * @var bool
     */
    protected $shouldThrowExceptionIfAssertionFails;
    
    /**
     * Constructs a new file MIME type resolver.
     * 
     * @param array<FileMIMETypeResolverInterface> $resolvers The initial resolvers to add to the queue.
     * @param string $assertionMIMEType The MIME type to use for the assertion. All the added resolvers must return this MIME type
     *                                                          when resolving the MIME type of a filename unless {@link FileMIMETypeResolverAsserterQueue::setAssertionMIMEType()} has been
     *                                                          called subsequently, otherwise an exception is thrown unless `$shouldThrowExceptionIfAssertionFails` is FALSE,
     *                                                          If `$shouldThrowExceptionIfAssertionFails` is FALSE then the first MIME type returned by a resolver of the queue which does not equal to the
     *                                                          assertion MIME type will be returned when calling {@link FileMIMETypeResolverAsserterQueue::getMIMEType()} and no exception will be thrown.
     * @param bool $shouldThrowExceptionIfAssertionFails Whether or not to throw an exception if the assertion of the file MIME type fails when looping through the resolvers queue.
     * @throws \InvalidArgumentException If one of the elements of the array does not implement the {@link FileMIMETypeResolverInterface} interface.
     */
    public function __construct($resolvers, $assertionMIMEType, $shouldThrowExceptionIfAssertionFails = TRUE) {
        $this->assertionMIMEType = $assertionMIMEType;
        $this->shouldThrowExceptionIfAssertionFails = $shouldThrowExceptionIfAssertionFails;
        foreach ($resolvers as $resolver) {
            $this->enqueue($resolver);
        }
    }
    
    /**
     * Sets the MIME type to use for the assertion.
     * 
     * @param string $assertionMIMEType The MIME type to use for the assertion. All the added resolvers must return this MIME type
     *                                                               when resolving the MIME type of a filename unless {@link static::setAssertionMIMEType()} has been
     *                                                               called subsequently, otherwise an exception is thrown.
     * @return void
     */
    public function setAssertionMIMEType($assertionMIMEType) {
        $this->assertionMIMEType = $assertionMIMEType;
    }
    
    /**
     * Sets whether or not to throw an exception if the assertion of the file MIME type fails when looping through the resolvers queue.
     * If the internal flag set through this method is FALSE, then the first MIME type returned by a resolver of the queue which does not equal to the
     * assertion MIME type will be returned and no exception will be thrown.
     * 
     * @param bool $bool Whether or not to throw an exception if the assertion of the file MIME type fails when looping through the resolvers queue.
     */
    public function setShouldThrowExceptionIfAssertionFails($bool) {
        $this->shouldThrowExceptionIfAssertionFails = $bool;
    }

    /**
     * {@inheritdoc}
     */
    public function getMIMEType($filename) {
        foreach ($this as $resolver) {
            $this->throwExceptionIfNotFileMIMETypeResolver($resolver);
            $MIMEType = $resolver->getMIMEType($filename);
            if ($this->assertionMIMEType !== $MIMEType) {
                if ($this->shouldThrowExceptionIfAssertionFails) {
                    throw new \RuntimeException(
                        sprintf(
                            'The resolver of type "%1$s" returned a MIME type of "%2$s" instead of the expected "%3$s" for the filename "%4$s".',
                            get_class($resolver),
                            $MIMEType,
                            $this->assertionMIMEType,
                            $filename
                        )
                    );
                }
                else {
                    return $MIMEType;
                }
            }
        }
        return $this->assertionMIMEType;
    }
    
    /**
     * Throws an exception if the given parameter is not a file MIME type resolver.
     * 
     * @param mixed $resolver The parameter to test.
     * @return void
     * @throws \InvalidArgumentException If the given parameter does not implement the {@link FileMIMETypeResolverInterface} interface.
     */
    protected function throwExceptionIfNotFileMIMETypeResolver($resolver) {
        if (!($resolver instanceof FileMIMETypeResolverInterface)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The class "%1$s" needs only components which implement "%2$s", "%3$s" received.',
                    get_class($this),
                    FileMIMETypeResolverInterface::class,
                    is_object($resolver) ? get_class($resolver) : gettype($resolver)
                )
            );
        }
    }

}
