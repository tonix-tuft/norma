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

namespace Norma\Data\Structure\Graph;

use Norma\Data\Structure\Graph\AbstractGraph;
use Norma\Data\Structure\Graph\DirectedGraphInterface;
use Norma\Data\Structure\Graph\GraphTrait;

/**
 * An implementation of a directed graph (digraph).
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class DirectedGraph extends AbstractGraph implements DirectedGraphInterface {
    
    use GraphTrait;
    
    /**
     * @var \SplObjectStorage
     */
    protected $verticesIndegreeMap;
    
    /**
     * @var \SplObjectStorage
     */
    protected $verticesOutdegreeMap;
    
    /**
     * {@inheritdoc}
     */
    public function __construct() {
        parent::__construct();
        
        $this->verticesIndegreeMap = $this->makeObjectStorage();
        $this->verticesOutdegreeMap = $this->makeObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function areAdjacent(VertexInterface $vertex1, VertexInterface $vertex2): bool {
        return $this->areIncomingAdjacent($vertex1, $vertex2) || $this->areOutgoingAdjacent($vertex1, $vertex2);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDensity() {
        $numberOfEdges = $this->getDimension();
        $numberOfVertices = $this->getOrder();
        return $numberOfEdges / $numberOfVertices * ($numberOfVertices - 1);
    }
    
    /**
     * {@inheritdoc}
     */
    public function isComplete(): bool {
        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function areIncomingAdjacent(VertexInterface $vertex1, VertexInterface $vertex2): bool {
        $this->throwExceptionIfVertexDoesNotExist($vertex1);
        return isset($this->adjacencyMap[$vertex2][$vertex1]);
    }

    /**
     * {@inheritdoc}
     */
    public function areOutgoingAdjacent(VertexInterface $vertex1, VertexInterface $vertex2): bool {
        $this->throwExceptionIfVertexDoesNotExist($vertex2);
        return isset($this->adjacencyMap[$vertex1][$vertex2]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranspose(): DirectedGraphInterface {
        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function getVertexIncomingEdgeNeighbors(VertexInterface $vertex): \Iterator {
        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function getVertexIncomingEdges(VertexInterface $vertex): \Iterator {
        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function getVertexIndegree(VertexInterface $vertex): int {
        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function getVertexOutdegree(VertexInterface $vertex): int {
        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function getVertexOutgoingEdgeNeighbors(VertexInterface $vertex): \Iterator {
        // TODO
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVertexOutgoingEdges(VertexInterface $vertex): \Iterator {
        // TODO
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVerticesIncomingEdges(VertexInterface $vertex1, VertexInterface $vertex2): \Iterator {
        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function getVerticesOutgoingEdges(VertexInterface $vertex1, VertexInterface $vertex2): \Iterator {
        // TODO
    }
    
    public function getDegreeSequence(): \Iterator {
        // TODO
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIndegreeSequence(): \Iterator {
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOutdegreeSequence(): \Iterator {
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function addCollapsedEdgeToSkeleton(GraphInterface $skeleton, VertexInterface $vertex1, VertexInterface $vertex2, $skeletonKeepEdgeMode = GraphInterface::SKELETON_MODE_KEEP_MAX_EDGE, $edgeWeightToIntegerCallback = NULL) {
        $edgesToKeep = [];
        
        $edgeToKeep1 = $this->edgeToKeep($vertex1, $vertex2, $skeletonKeepEdgeMode, $edgeWeightToIntegerCallback);
        if (!empty($edgeToKeep1)) {
            $edgesToKeep[] = $edgeToKeep1;
        }
        $edgeToKeep2 = $this->edgeToKeep($vertex2, $vertex1, $skeletonKeepEdgeMode, $edgeWeightToIntegerCallback);
        if (!empty($edgeToKeep2)) {
            $edgesToKeep[] = $edgeToKeep2;
        }
        
        foreach ($edgesToKeep as $edgeToKeep) {
            list($edge,, $sourceVertex, $targetVertex) = $edgeToKeep;
            $skeleton->addEdge($sourceVertex, $edge, $targetVertex);
        }
    }
    
}
