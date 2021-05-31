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

namespace Norma\Data\Structure\Graph;

use Norma\Data\Structure\Graph\AbstractGraph;
use Norma\Data\Structure\Graph\GraphInterface;
use Norma\Data\Structure\Graph\GraphTrait;

/**
 * An implementation of an undirected graph.
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
 */
class UndirectedGraph extends AbstractGraph {
    
    use GraphTrait;
    
    /**
     * {@inheritdoc}
     */
    public function areAdjacent(VertexInterface $vertex1, VertexInterface $vertex2): bool {
        $this->throwExceptionIfVertexDoesNotExist($vertex1);
        $this->throwExceptionIfVertexDoesNotExist($vertex2);
        return isset($this->adjacencyMap[$vertex1][$vertex2]) || isset($this->adjacencyMap[$vertex2][$vertex1]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDensity() {
        $numberOfEdges = $this->getDimension();
        $numberOfVertices = $this->getOrder();
        return 2 * $numberOfEdges / $numberOfVertices * ($numberOfVertices - 1);
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
        
        if (empty($edgesToKeep)) {
            return;
        }
        if (count($edgesToKeep) === 1) {
            list($edge,, $sourceVertex, $targetVertex) = $edgesToKeep;
            $skeleton->addEdge($sourceVertex, $edge, $targetVertex);
        }
        else {
            $firstEdgeToKeep = $edgesToKeep[0];
            $secondEdgeToKeep = $edgesToKeep[1];
            list(, $firstEdgeIntWeight) = $firstEdgeToKeep;
            list(, $secondEdgeIntWeight) = $secondEdgeToKeep;
            
            $comparison = $firstEdgeIntWeight <=> $secondEdgeIntWeight;
            
            $edgeToKeep = NULL;
            if ($skeletonKeepEdgeMode === GraphInterface::SKELETON_MODE_KEEP_MAX_EDGE) {
                // Max.
                if ($comparison >= 0) {
                    $edgeToKeep = $firstEdgeToKeep;
                }
                else {
                    $edgeToKeep = $secondEdgeToKeep;
                }
            }
            else {
                // Min.
                if ($comparison <= 0) {
                    $edgeToKeep = $firstEdgeToKeep;
                }
                else {
                    $edgeToKeep = $secondEdgeToKeep;
                }
            }
            
            list($edge,, $sourceVertex, $targetVertex) = $edgeToKeep;
            $skeleton->addEdge($sourceVertex, $edge, $targetVertex);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function verticesPairDoesNotMakeGraphComplete(VertexInterface $vertex, VertexInterface $otherVertex): bool {
        // All vertices must be adjacent, multi-archs are not allowed.
        if (
            !(
                !isset($this->adjacencyMap[$vertex][$otherVertex])
                XOR
                !isset($this->adjacencyMap[$otherVertex][$vertex])
            )
        ) {
            return TRUE;
        }
        
        if (
            $this->adjacencyMap[$vertex][$otherVertex]->count() > 1
            ||
            $this->adjacencyMap[$otherVertex][$vertex]->count() > 1
        ) {
            return TRUE;
        }
        
        return FALSE;
    }
    
}
