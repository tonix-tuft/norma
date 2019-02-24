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

namespace Norma\HTTP\URI;

use Norma\HTTP\URI\RFC3986URIHostValidatorInterface;

/**
 * An implementation of the RFC 3986 URI host validator.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class RFC3986URIHostValidator implements RFC3986URIHostValidatorInterface {
    
    /**
     * {@inheritdoc}
     */
    public function isValidIPLiteral($URIHost) {
        /*
         * @see https://regex101.com/r/ubDXoH/5
         */
        return preg_match('/^
                \[
                (?:
                    (?P<IPv6address>

                        # 6( h16 ":" ) ls32
                        # h16 = 1*4HEXDIG
                        # ls32 = ( h16 ":" h16 ) | IPv4address
                        (?:
                            (?:
                                (?:
                                    (?P<h16>
                                        [A-Fa-f0-9]{1,4}
                                    )
                                    :
                                )
                                {6}
                                (?P<ls32>
                                    (?:
                                        (?P>h16)
                                        :
                                        (?P>h16)
                                    )
                                    |
                                    (?P<IPv4address>
                                        (?P<dec_octet>
                                            (?:
                                                (?:[2][5][\x30-\x35])
                                                |
                                                (?:[2][\x30-\x34][0-9])
                                                |
                                                (?:[1][0-9][0-9])
                                                |
                                                (?:[\x31-\x39][0-9])
                                                |
                                                (?:[0-9])
                                            )
                                        )
                                        \.
                                        (?P>dec_octet)
                                        \.
                                        (?P>dec_octet)
                                        \.
                                        (?P>dec_octet)
                                    )
                                )
                            )
                        )
                        |

                        # "::" 5( h16 ":" ) ls32
                        (?:
                            ::
                            (?:
                                (?P>h16)
                                :
                            )
                            {5}
                            (?P>ls32)
                        )
                        |

                        # [ h16 ] "::" 4( h16 ":" ) ls32
                        (?:
                            (?:
                                (?P>h16)
                            )?
                            ::
                            (?:
                                (?P>h16)
                                :
                            )
                            {4}
                            (?P>ls32)
                        )
                        |

                        # [ *1( h16 ":" ) h16 ] "::" 3( h16 ":" ) ls32
                        (?:
                            (?:
                                (?:
                                    (?P>h16)
                                    :
                                )?
                                (?P>h16)
                            )?
                            ::
                            (?:
                                (?P>h16)
                                :
                            )
                            {3}
                            (?P>ls32)
                        )
                        |

                        # [ *2( h16 ":" ) h16 ] "::" 2( h16 ":" ) ls32
                        (?:
                            (?:
                                (?:
                                    (?P>h16)
                                    :
                                ){0,2}
                                (?P>h16)
                            )?
                            ::
                            (?:
                                (?P>h16)
                                :
                            )
                            {2}
                            (?P>ls32)
                        )
                        |

                        # [ *3( h16 ":" ) h16 ] "::" h16 ":" ls32
                        (?:
                            (?:
                                (?:
                                    (?P>h16)
                                    :
                                )
                                {0,3}
                                (?P>h16)
                            )?
                            ::
                            (?P>h16)
                            :
                            (?P>ls32)
                        )
                        |

                        # [ *4( h16 ":" ) h16 ] "::" ls32
                        (?:
                            (?:
                                (?:
                                    (?P>h16)
                                    :
                                )
                                {0,4}
                                (?P>h16)
                            )?
                            ::
                            (?P>ls32)
                        )
                        |

                        # [ *5( h16 ":" ) h16 ] "::" h16
                        (?:
                            (?:
                                (?:
                                    (?P>h16)
                                    :
                                )
                                {0,5}
                                (?P>h16)
                            )?
                            ::
                            (?P>h16)
                        )
                        |

                        # [ *6( h16 ":" ) h16 ] "::"
                        (?:
                            (?:
                                (?:
                                    (?P>h16)
                                    :
                                )
                                {0,6}
                                (?P>h16)
                            )?
                            ::
                        )

                    )
                    |

                    # IPvFuture = "v" 1*HEXDIG "." 1*( unreserved | sub-delims | ":" )
                    (?P<IPvFuture>
                        v
                        [a-fA-F0-9]+
                        \.
                        (?:
                            (?:[-._~0-9A-Za-z])                      # unreserved
                            |
                            (?:[!$&\')(*+,;=])                            # sub-delims
                            |
                            [:]
                        )+
                    )
                )
                \]
                $/x', $URIHost);
    }

    /**
     * {@inheritdoc}
     */
    public function isValidIPv4Address($URIHost) {
        /*
         * @see https://regex101.com/r/ztva7D/2
         */
        return preg_match('/^
                (?P<dec_octet>
                    (?:
                        (?:[2][5][\x30-\x35])
                        |
                        (?:[2][\x30-\x34][0-9])
                        |
                        (?:[1][0-9][0-9])
                        |
                        (?:[\x31-\x39][0-9])
                        |
                        (?:[0-9])
                    )
                )
                \.
                (?P>dec_octet)
                \.
                (?P>dec_octet)
                \.
                (?P>dec_octet)
                $/x', $URIHost);
    }

    /**
     * {@inheritdoc}
     */
    public function isValidRegName($URIHost) {
        /*
         * @see https://regex101.com/r/2F6GjS/2
         * @source https://stackoverflow.com/questions/7994287/is-url-percent-encoding-case-sensitive#answer-18347870
         */
        return preg_match('/^
                (?:
                    (?:[-._~0-9A-Za-z])     # unreserved
                    |
                    (?:%[A-Fa-f0-9]{2})      # pct-encoded
                    |
                    (?:[!$&\')(*+,;=])           # sub-delims
                )*
                $/x', $URIHost);
    }

    /**
     * {@inheritdoc}
     */
    public function isValidURIHost($URIHost) {
        return $this->isValidIPv4Address($URIHost) ||
                $this->isValidIPLiteral($URIHost) ||
                $this->isValidRegName($URIHost);
    }

}