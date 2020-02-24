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

namespace Norma\Data\Structure\Graph;

use Norma\Data\Structure\Graph\AbstractGraph;
use Norma\Data\Structure\Graph\DirectedGraphInterface;
use Norma\Data\Structure\Graph\GraphTrait;
use Norma\Data\Structure\Graph\VertexInterface;

/**
 * An implementation of a directed graph (digraph).
 *
 * @author Anton Bagdatyev (Tonix) <antonytuft@gmail.com>
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
     * @var array
     */
    protected $indegreeMap;
    
    /**
     * @var array
     */
    protected $outdegreeMap;
    
    /**
     * @var array
     */
    protected $indegreeOutdegreeMap;
    
    /**
     * @var \SplObjectStorage
     */
    protected $incomingNeighborsMap;

    /**
     * @var \SplObjectStorage
     */
    protected $outgoingNeighborsMap;
    
    /**
     * {@inheritdoc}
     */
    public function __construct() {
        parent::__construct();
        
        // Internal data structures for vertices.
        $this->verticesIndegreeMap = $this->makeObjectStorage();
        $this->verticesOutdegreeMap = $this->makeObjectStorage();
        $this->incomingNeighborsMap = $this->makeObjectStorage();
        $this->outgoingNeighborsMap = $this->makeObjectStorage();
                
        // // Internal data structure for degree sequences.
        $this->indegreeOutdegreeMap = [];
        $this->indegreeMap = [];
        $this->outdegreeMap = [];
    }
    
    /**
     * Increments a vertex indegree count.
     * 
     * @param int $vertexDegree The vertex indegree.
     * @return void
     */
    protected function incrementVertexIndegreeCount($vertexDegree) {
        $this->incrementVertexDegreeCountOfMap($this->indegreeMap, $vertexDegree);
    }
    
    /**
     * Increments a vertex outdegree count.
     * 
     * @param int $vertexDegree The vertex outdegree.
     * @return void
     */
    protected function incrementVertexOutdegreeCount($vertexDegree) {
        $this->incrementVertexDegreeCountOfMap($this->outdegreeMap, $vertexDegree);
    }
    
    /**
     * Decrements a vertex indegree count.
     * 
     * @param int $vertexDegree The vertex indegree.
     * @return void
     */
    protected function decrementVertexIndegreeCount($vertexDegree) {
        $this->decrementVertexDegreeCountOfMap($this->indegreeMap, $vertexDegree);
    }
    
    /**
     * Decrements a vertex outdegree count.
     * 
     * @param int $vertexDegree The vertex outdegree.
     * @return void
     */
    protected function decrementVertexOutdegreeCount($vertexDegree) {
        $this->decrementVertexDegreeCountOfMap($this->outdegreeMap, $vertexDegree);
    }
    
    /**
     * Increments or decrements a vertex outdegree.
     * 
     * @param VertexInterface $vertex The vertex.
     * @param bool $increment TRUE if the outdegree of the vertex must be incremented, FALSE if decremented.
     * @return void
     */
    protected function updateNewVertexOutdegree(VertexInterface $vertex, $increment = TRUE) {
        $vertexIndegree = $this->verticesIndegreeMap[$vertex];
        
        // Decrementing the old outdegree count.
        $vertexOutdegree = $this->verticesOutdegreeMap[$vertex];
        $this->decrementVertexOutdegreeCount($vertexOutdegree);
        $this->decrementIndegreeOutdegreeMap($vertexIndegree, $vertexOutdegree);
        
        // New incremented or decremented outdegree.
        $increment ? $vertexOutdegree++ : $vertexOutdegree--;
        $this->verticesOutdegreeMap[$vertex] = $vertexOutdegree;
        $this->incrementVertexOutdegreeCount($vertexOutdegree);
        $this->incrementIndegreeOutdegreeMap($vertexIndegree, $vertexOutdegree);
    }
    
    /**
     * Increments the outdegree of a vertex creating, if none existed yet, a new vertex outdegree entry
     * in the internal indegree outdegree map and outdegree map for the new outdegree of the vertex.
     * 
     * @param VertexInterface $vertex The vertex.
     * @return void
     */
    protected function incrementNewVertexOutdegree(VertexInterface $vertex) {
        $this->updateNewVertexOutdegree($vertex, TRUE);
    }
    
    /**
     * Increments or decrements a vertex indegree.
     * 
     * @param VertexInterface $vertex The vertex.
     * @param bool $increment TRUE if the indegree of the vertex must be incremented, FALSE if decremented.
     * @return void
     */
    protected function updateNewVertexIndegree(VertexInterface $vertex, $increment = TRUE) {
        $vertexOutdegree = $this->verticesOutdegreeMap[$vertex];
        
        // Decrementing the old indegree count.
        $vertexIndegree = $this->verticesIndegreeMap[$vertex];
        $this->decrementVertexIndegreeCount($vertexIndegree);
        $this->decrementIndegreeOutdegreeMap($vertexIndegree, $vertexOutdegree);
        
        // New incremented or decremented outdegree.
        $increment ? $vertexIndegree++ : $vertexIndegree--;
        $this->verticesIndegreeMap[$vertex] = $vertexIndegree;
        $this->incrementVertexIndegreeCount($vertexIndegree);
        $this->incrementIndegreeOutdegreeMap($vertexIndegree, $vertexOutdegree);
    }
    
    /**
     * Increments the indegree of a vertex creating, if none existed yet, a new vertex indegree entry
     * in the internal indegree outdegree map and indegree map for the new indegree of the vertex.
     * 
     * @param VertexInterface $vertex The vertex.
     * @return void
     */
    protected function incrementNewVertexIndegree(VertexInterface $vertex) {
        $this->updateNewVertexIndegree($vertex, TRUE);
    }
    
    /**
     * Decrements the outdegree of a vertex creating, if none existed yet, a new vertex outdegree entry
     * in the internal indegree outdegree map and outdegree map for the new outdegree of the vertex.
     * 
     * @param VertexInterface $vertex The vertex.
     * @return void
     */
    protected function decrementNewVertexOutdegree(VertexInterface $vertex) {
        $this->updateNewVertexOutdegree($vertex, FALSE);
    }
    
    /**
     * Decrements the indegree of a vertex creating, if none existed yet, a new vertex indegree entry
     * in the internal indegree outdegree map and indegree map for the new indegree of the vertex.
     * 
     * @param VertexInterface $vertex The vertex.
     * @return void
     */
    protected function decrementNewVertexIndegree(VertexInterface $vertex) {
        $this->updateNewVertexIndegree($vertex, FALSE);
    }
    
    /**
     * Yields the the indegrees or outdegrees of a given indegree or outdegree map.
     * 
     * @param array $map The indegree or outdegree map.
     * @return \Generator A generator which yields the indegrees or outdegrees of the given indegree or outdegree map.
     */
    protected function yieldDegreeSequenceFromMap(&$map): \Generator {
        end($map);
        while (($degree = key()) !== NULL) {
            $verticesCount = current($map);
            while ($verticesCount > 0) {
                $verticesCount--;
                yield $degree;
            }
            prev($map);
        }
        reset($map);
        
        yield from [];
    }
    
    /**
     * Increments the internal indegree outdegree map.
     * 
     * @param int $indegree The indegree.
     * @param int $outdegree The outdegree.
     * @return void
     */
    protected function incrementIndegreeOutdegreeMap($indegree, $outdegree) {
        $shouldSortIndegrees = FALSE;
        $shouldSortOutdegrees = FALSE;
        
        if (!isset($this->indegreeOutdegreeMap[$indegree])) {
            $this->indegreeOutdegreeMap[$indegree] = [];
            $shouldSortIndegrees = $indegree > 0;
        }
        
        if (!isset($this->indegreeOutdegreeMap[$indegree][$outdegree])) {
            $this->indegreeOutdegreeMap[$indegree][$outdegree] = 0;
            $shouldSortOutdegrees = $outdegree > 0;
        }
        $this->indegreeOutdegreeMap[$indegree][$outdegree]++;
        
        if ($shouldSortIndegrees) {
            $this->sortingAlgorithm->ksort($this->indegreeOutdegreeMap, function($indegree1, $indegree2) {
                return $indegree2 <=> $indegree1;
            });
        }
        if ($shouldSortOutdegrees) {
            $this->sortingAlgorithm->ksort($this->indegreeOutdegreeMap[$indegree], function($outdegree1, $outdegree2) {
                return $outdegree2 <=> $outdegree1;
            });
        }
    }
    
    /**
     * Decrements the internal indegree outdegree map.
     * 
     * @param int $indegree The indegree.
     * @param int $outdegree The outdegree.
     * @return void
     */
    protected function decrementIndegreeOutdegreeMap($indegree, $outdegree) {
        $this->indegreeOutdegreeMap[$indegree][$outdegree]--;
        if ($this->indegreeOutdegreeMap[$indegree][$outdegree] <= 0) {
            unset($this->indegreeOutdegreeMap[$indegree][$outdegree]);
            if (empty($this->indegreeOutdegreeMap[$indegree])) {
                unset($this->indegreeOutdegreeMap[$indegree]);
            }
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function removeNeighborsAssociation(VertexInterface $sourceVertex, VertexInterface $targetVertex) {
        parent::removeNeighborsAssociation($sourceVertex, $targetVertex);
        if (isset($this->incomingNeighborsMap[$sourceVertex][$targetVertex])) {
            unset($this->incomingNeighborsMap[$sourceVertex][$targetVertex]);
        }
        if (isset($this->outgoingNeighborsMap[$sourceVertex][$targetVertex])) {
            unset($this->outgoingNeighborsMap[$sourceVertex][$targetVertex]);
        }
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
    public function addVertex(VertexInterface $vertex) {
        parent::addVertex($vertex);
        
        $this->incomingNeighborsMap[$vertex] = $this->makeObjectMap();
        $this->outgoingNeighborsMap[$vertex] = $this->makeObjectMap();
        $this->verticesIndegreeMap[$vertex] = 0;
        $this->verticesOutdegreeMap[$vertex] = 0;
        $this->incrementIndegreeOutdegreeMap(0, 0);
        $this->incrementVertexIndegreeCount(0);
        $this->incrementVertexOutdegreeCount(0);
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function removeVertex(VertexInterface $vertex) {
        parent::removeVertex($vertex);
        
        unset($this->verticesIndegreeMap[$vertex]);
        unset($this->verticesOutdegreeMap[$vertex]);
        unset($this->incomingNeighborsMap[$vertex]);
        unset($this->outgoingNeighborsMap[$vertex]);
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function addEdge(VertexInterface $vertex1, EdgeInterface $edge, VertexInterface $vertex2) {
        parent::addEdge($vertex1, $edge, $vertex2);
        
        $this->incrementNewVertexOutdegree($vertex1);
        $this->incrementNewVertexIndegree($vertex2);
        
        $this->incomingNeighborsMap[$vertex2][$vertex1] = $vertex1;
        $this->outgoingNeighborsMap[$vertex1][$vertex2] = $vertex2;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function removeEdge(EdgeInterface $edge) {
        $this->throwExceptionIfEdgeDoesNotExist($edge);
        
        list($sourceVertex, $targetVertex) = $this->edgeVerticesMap[$edge];
        
        parent::removeEdge($edge);
        
        $this->decrementNewVertexOutdegree($sourceVertex);
        $this->decrementNewVertexIndegree($targetVertex);
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDegreeSequence(): \Iterator {
        foreach ($this->indegreeOutdegreeMap as $indegree => $outdegreeMap) {
            foreach ($outdegreeMap as $outdegree => $verticesCount) {
                while ($verticesCount > 0) {
                    $verticesCount--;
                    yield [$indegree, $outdegree];
                }
            }
        }
        yield from [];
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
    
    /**
     * {@inheritdoc}
     */
    protected function verticesPairDoesNotMakeGraphComplete(VertexInterface $vertex, VertexInterface $otherVertex): bool {
        // All vertices must be adjacent with an incoming and outgoing connection, multi-archs are not allowed.
        if (
            !isset($this->adjacencyMap[$vertex][$otherVertex])
            ||
            !isset($this->adjacencyMap[$otherVertex][$vertex])
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
        $transpose = $this->newEmptyGraph();
        $allVertices = $this->verticesMap;
        $vertices = [];
        $i = 0;
        foreach ($allVertices as $vertexToAdd) {
            $transpose->addVertex($vertexToAdd);
            
            $k = $i;
            while ($k >= 0) {
                $vertex = $vertices[$k];
                if (isset($this->adjacencyMap[$vertexToAdd][$vertex])) {
                    $edges = $this->adjacencyMap[$vertexToAdd][$vertex];
                    foreach ($edges as $edge) {
                        $transpose->addEdge($vertex, $edge, $vertexToAdd);
                    }
                }
                if (isset($this->adjacencyMap[$vertex][$vertexToAdd])) {
                    $edges = $this->adjacencyMap[$vertex][$vertexToAdd];
                    foreach ($edges as $edge) {
                        if (!$transpose->hasEdge($edge)) {
                            $transpose->addEdge($vertexToAdd, $edge, $vertex);
                        }
                    }
                }
                $k--;
            }
            $i++; 
        }
        return $transpose;
    }

    /**
     * {@inheritdoc}
     */
    public function getVertexIncomingEdgeNeighbors(VertexInterface $vertex): \Iterator {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        return $this->incomingNeighborsMap[$vertex];
    }

    /**
     * {@inheritdoc}
     */
    public function getVertexOutgoingEdgeNeighbors(VertexInterface $vertex): \Iterator {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        return $this->outgoingNeighborsMap[$vertex];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVertexIndegree(VertexInterface $vertex): int {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        return $this->verticesIndegreeMap[$vertex];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVertexOutdegree(VertexInterface $vertex): int {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        return $this->verticesOutdegreeMap[$vertex];
    }

    /**
     * {@inheritdoc}
     */
    public function getVertexIncomingEdges(VertexInterface $vertex): \Iterator {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        $neighbors = $this->getVertexIncomingEdgeNeighbors($vertex);
        foreach ($neighbors as $neighbor) {
            yield from $this->getVerticesIncomingEdges($vertex, $neighbor);
        }
        yield from [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVertexOutgoingEdges(VertexInterface $vertex): \Iterator {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        $neighbors = $this->getVertexIncomingEdgeNeighbors($vertex);
        foreach ($neighbors as $neighbor) {
            yield from $this->getVerticesOutgoingEdges($vertex, $neighbor);
        }
        yield from [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVerticesIncomingEdges(VertexInterface $vertex1, VertexInterface $vertex2): \Iterator {
        $this->throwExceptionIfVertexDoesNotExist($vertex1);
        $this->throwExceptionIfVertexDoesNotExist($vertex2);
        
        if (isset($this->adjacencyMap[$vertex2][$vertex1])) {
            $edges = $this->adjacencyMap[$vertex2][$vertex1];
            yield from $edges;
        }
        yield from [];
    }

    /**
     * {@inheritdoc}
     */
    public function getVerticesOutgoingEdges(VertexInterface $vertex1, VertexInterface $vertex2): \Iterator {
        $this->throwExceptionIfVertexDoesNotExist($vertex1);
        $this->throwExceptionIfVertexDoesNotExist($vertex2);
        
        if (isset($this->adjacencyMap[$vertex1][$vertex2])) {
            $edges = $this->adjacencyMap[$vertex1][$vertex2];
            yield from $edges;
        }
        yield from [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIndegreeSequence(): \Iterator {
        yield from $this->yieldDegreeSequenceFromMap($this->indegreeMap);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOutdegreeSequence(): \Iterator {
        yield from $this->yieldDegreeSequenceFromMap($this->outdegreeMap);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaximumIndegree(): int {
        return $this->getMaxFromDegreeMap($this->indegreeMap);
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimumIndegree(): int {
        return $this->getMinFromDegreeMap($this->indegreeMap);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMaximumOutdegree(): int {
        return $this->getMaxFromDegreeMap($this->outdegreeMap);
    }

    /**
     * {@inheritdoc}
     */
    public function getMinimumOutdegree(): int {
        return $this->getMinFromDegreeMap($this->outdegreeMap);
    }
    
}
