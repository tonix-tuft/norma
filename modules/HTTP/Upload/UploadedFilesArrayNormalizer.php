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

namespace Norma\HTTP\Upload;

use Norma\HTTP\Upload\UploadedFilesArrayNormalizerInterface;
use Norma\Core\Utils\FrameworkArrayUtilsTrait;
use Psr\Http\Message\UploadedFileInterface;
use Norma\HTTP\Upload\UploadedFileFactoryInterface;

/**
 * The implementation of an uploaded files normalizer.
 *
 * @author Tonix-Tuft <antonytuft@gmail.com>
 */
class UploadedFilesArrayNormalizer implements UploadedFilesArrayNormalizerInterface {
    
    use FrameworkArrayUtilsTrait;
    
    /**
     * @var UploadedFileFactoryInterface
     */
    protected $uploadedFileFactory;
    
    /**
     * Constructs a new uploaded files array normalizer.
     * 
     * @param UploadedFileFactoryInterface $uploadedFileFactory An uploaded file factory to use when creating the uploaded file instances.
     */
    public function __construct(UploadedFileFactoryInterface $uploadedFileFactory) {
        $this->uploadedFileFactory = $uploadedFileFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function normalize(array $files) {
        if (empty($files)) {
            return [];
        }
        
        $loosenArray = $this->privateLoosenInternalMultiDimensionalArrayPathForEachVal($files);
        $uploadedFilesTree = [];
        $uploadedFilesTreePaths = [];
        foreach ($loosenArray as $loosenArrayLeafNodes) {
            list($leafValue, $path) = $loosenArrayLeafNodes;
            
            $isMultiple = is_int($path[count($path) - 1]);
            if (!$isMultiple) {
                $uploadedFilesTreePaths[] = $path;
                $this->privateArrayMultiDim($uploadedFilesTree, array_merge($path, [$leafValue]));
            }
            else {
                $nthMultipleIndex = array_pop($path);
                $filePropertyKey = array_pop($path);
                
                $path = array_merge($path, [$nthMultipleIndex, $filePropertyKey]);
                $uploadedFilesTreePaths[] = $path;
                $this->privateArrayMultiDim($uploadedFilesTree, array_merge($path, [$leafValue]));
            }
        }

        $normalizedUploadedFilesTree = [];
        foreach ($uploadedFilesTreePaths as $path) {
            $uploadedFile = $this->privateNestedArrayValue($uploadedFilesTree, $path);
            $normalizedUploadedFile = $this->createUploadedFileFromArray($uploadedFile);
            $this->privateArrayMultiDim($normalizedUploadedFilesTree, array_merge($path, [$normalizedUploadedFile]));
        }
        
        return $normalizedUploadedFilesTree;
    }
    
    /**
     * An internal method used to create an uploaded file instance from an array containing the uploaded file data
     * using the uploaded file factory provided during the construction of this normalizer.
     * 
     * @param array $uploadedFile The array containing the uploaded file data.
     * @return UploadedFileInterface The uploaded file.
     * @throws \InvalidArgumentException If the structure of the uploaded file array is invalid.
     */
    protected function createUploadedFileFromArray($uploadedFile): UploadedFileInterface {
        if (!isset($uploadedFile['tmp_name']) || !isset($uploadedFile['error'])) {
            throw new \InvalidArgumentException('Uploaded file array structure is missing mandatory keys "tmp_name" or "error" or both. They are required to create an uploaded file instance.');
        }
        return $this->uploadedFileFactory->makeUploadedFile(
            $uploadedFile['tmp_name'],
            $uploadedFile['error'],
            $uploadedFile['size'] ?? NULL,
            $uploadedFile['name'] ?? NULL,
            $uploadedFile['type'] ?? NULL
        );
    }

}
