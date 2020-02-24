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

namespace Norma\Core\Parsing;

/**
 * An implementation of a use statements parser.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class ParsedUseStatement implements ParsedUseStatementInterface {
    
    /**
     * @var string The alias of the parsed import statement.
     */
    protected $alias;
    
    /**
     *
     * @var string The name of the parsed import statement.
     */
    protected $name;
    
    /**
     * Constructs a new parsed use statement object.
     * 
     * @param string $alias The alias of the use statement.
     * @param string $name The name of the use statement.
     */
    public function __construct($alias, $name) {
        $this->alias = $alias;
        $this->name = $name;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return $this->name;
    }

}
