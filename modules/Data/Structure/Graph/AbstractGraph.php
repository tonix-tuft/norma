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

use Norma\Data\Structure\Graph\GraphInterface;
use Norma\Data\Structure\Graph\VertexAlreadyExistsException;
use Norma\Data\Structure\Graph\UnknownVertexException;
use Norma\Data\Structure\Map\ObjectMapInterface;
use Norma\Data\Structure\Map\LinkedObjectMap;
use Norma\Data\Structure\Graph\EdgeInterface;
use Norma\Data\Structure\Graph\EdgeAlreadyExistsException;
use Norma\Data\Structure\Graph\UnknownEdgeException;
use Norma\Algorithm\Sorting\SortingAlgorithmInterface;
use Norma\Algorithm\Sorting\BuiltinQuicksort;
use Norma\Data\Structure\Graph\VertexInterface;
use Norma\Data\Structure\Graph\Vertex;
use Norma\Data\Structure\Graph\Edge;

/**
 * An abstract graph base class which internally uses map data structures
 * to store vertices, their neighbors and the edges which connect them together.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
abstract class AbstractGraph implements GraphInterface {
    
    /**
     * @var ObjectMapInterface
     */
    protected $adjacencyMap;
    
    /**
     * @var ObjectMapInterface
     */
    protected $verticesMap;
    
    /**
     * @var ObjectMapInterface
     */
    protected $edgesMap;
    
    /**
     * @var \SplObjectStorage
     */
    protected $verticesDegreeMap;
    
    /**
     * @var \SplObjectStorage
     */
    protected $edgeVerticesMap;
    
    /**
     * @var array
     */
    protected $degreeMap;
    
    /**
     * @var SortingAlgorithmInterface
     */
    protected $sortingAlgorithm;
    
    /**
     * @var \SplObjectStorage
     */
    protected $neighborsMap;
    
    /**
     * Constructs a new graph.
     */
    public function __construct() {
        $this->adjacencyMap = $this->makeObjectMap();
        
        // Internal data structures for vertices.
        $this->verticesMap = $this->makeObjectMap();
        $this->verticesDegreeMap = $this->makeObjectStorage();
        $this->neighborsMap = $this->makeObjectStorage();
        
        // Internal data structure for degree sequence.
        $this->degreeMap = [];
        
        // Internal data structures for edges.
        $this->edgesMap = $this->makeObjectMap();
        $this->edgeVerticesMap = $this->makeObjectStorage();
        
        // Sorting algorithm to use internally to keep the degree sequence ordered.
        $this->sortingAlgorithm = $this->makeSortingAlgorithm();
    }
    
    /**
     * Creates a new empty graph.
     * 
     * @return GraphInterface A new empty graph. The graph MUST be empty, i.e. without vertices nor edges.
     */
    abstract protected function newEmptyGraph(): GraphInterface;
    
    /**
     * Gives the opportunity to add an edge to a given graph skeleton given a pair of vertices which are apparently adjacent
     * and a skeleton mode to keep the max or min edge with an optional callable required if the weights of the edges of the graph
     * are not integers.
     * 
     * @param GraphInterface $skeleton The skeleton graph.
     * @param VertexInterface $vertex1 First vertex of pair.
     * @param VertexInterface $vertex2 Second vertex of pair.
     * @param int $skeletonKeepEdgeMode The mode to use when collapsing multiple edges.
     *                                                            Available modes are:
     *                                                            - `GraphInterface::SKELETON_MODE_KEEP_MAX_EDGE`: If a pair of vertex has two or more edges, the edge which will be kept will be the edge with the highest weight.
     *                                                            - `GraphInterface::SKELETON_MODE_KEEP_MIN_EDGE`:  If a pair of vertex has two or more edges, the edge which will be kept will be the edge with the lowest weight.
     *                                                            The default is to keep the edge with the highest weight.
     * @param callable|null $edgeWeightToIntegerCallback For multiple edges connecting the same pair of vertices, if the weight of the edges is not an integer,
     *                                                                                     this callback MUST be called with the given parameters, in order: the edge, the source and target vertices.
     *                                                                                     Its return value is expected to be an integer to use for identifying the max or min edge to keep, depending on the mode.
     * @return void
     * @throws \UnexpectedValueException If either the weight of an edge is not an int and there isnt't an `$edgeWeightToIntegerCallback` set or the return value of the callback is not an int.
     */
    abstract protected function addCollapsedEdgeToSkeleton(GraphInterface $skeleton, VertexInterface $vertex1, VertexInterface $vertex2, $skeletonKeepEdgeMode = GraphInterface::SKELETON_MODE_KEEP_MAX_EDGE, $edgeWeightToIntegerCallback = NULL);
    
    /**
     * Tests whether a pair of vertices has multiple edges which don't make the graph complete.
     * 
     * @param \Norma\Data\Structure\Graph\VertexInterface $vertex The first vertex of the pair.
     * @param \Norma\Data\Structure\Graph\VertexInterface $otherVertex The second vertex of the pair.
     * @return bool Implementors MUST return TRUE if the pair of vertices has multiple edges or if the graph
     *                      pair is not connected by an edge taking into account whether the graph is directed or not,
     *                      or return FALSE otherwise.
     *                      Loops are not considered multiple edges and this method SHOULD NOT check for loops.
     */
    abstract protected function verticesPairDoesNotMakeGraphComplete(VertexInterface $vertex, VertexInterface $otherVertex): bool;
    
    /**
     * Factory method which creates a new sorting algorithm to be used internally
     * by the graph data structure.
     * 
     * @return SortingAlgorithmInterface A sorting algorithm implementation.
     */
    protected function makeSortingAlgorithm(): SortingAlgorithmInterface {
        return new BuiltinQuicksort();
    }
    
    /**
     * Factory method which creates a new internal unordered map with objects as keys.
     * 
     * @return \SplObjectStorage The unordered map.
     */
    protected function makeObjectStorage(): \SplObjectStorage {
        return new \SplObjectStorage();
    }
    
    /**
     * Factory method to create a new ordered map with objects as keys.
     * 
     * @return ObjectMapInterface The ordered map.
     */
    protected function makeObjectMap(): ObjectMapInterface {
        return new LinkedObjectMap();
    }
    
    /**
     * Throws an exception if the given vertex already exists.
     * 
     * @param VertexInterface $vertex A vertex.
     * @return void
     * @throws VertexAlreadyExistsException If the vertex already exists in the graph.
     */
    protected function throwExceptionIfVertexAlreadyExists(VertexInterface $vertex) {
        if ($this->hasVertex($vertex)) {
            throw new VertexAlreadyExistsException(sprintf('The given vertex of type "%s" already exists in the graph.', get_class($vertex)));
        }
    }
    
    /**
     * Throws an exception if the given vertex does not exist.
     * 
     * @param VertexInterface $vertex A vertex.
     * @return void
     * @throws UnknownVertexException If the vertex does not exist.
     */
    protected function throwExceptionIfVertexDoesNotExist(VertexInterface $vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new UnknownVertexException(sprintf('Unknown vertex of type "%s". The vertex does not exist in the graph.', get_class($vertex)));
        }
    }
    
    /**
     * Throws an exception if the given edge already exists in the graph.
     * 
     * @param EdgeInterface $edge The edge.
     * @return void
     * @throws EdgeAlreadyExistsException If the edge already exists in the graph.
     */
    protected function throwExceptionIfEdgeAlreadyExists(EdgeInterface $edge) {
        if (isset($this->edgesMap[$edge])) {
            throw new EdgeAlreadyExistsException(sprintf('The edge of type "%s" already exists in the graph.', get_class($edge)));
        }
    }
    
    /**
     * Throws an exception if the given edge does not exist in the graph.
     * 
     * @param EdgeInterface $edge The edge.
     * @return void
     * @throws UnknownEdgeException If the edge does not exist.
     */
    protected function throwExceptionIfEdgeDoesNotExist(EdgeInterface $edge) {
        if (!$this->hasEdge($edge)) {
            throw new UnknownEdgeException(sprintf('The edge of type "%s" does not exist in the graph.', get_class($edge)));
        }
    }
    
    /**
     * Decrements a vertex degree count.
     * 
     * @param int $vertexDegree The vertex degree.
     * @return void
     */
    protected function decrementVertexDegreeCount($vertexDegree) {
        $this->decrementVertexDegreeCountOfMap($this->degreeMap, $vertexDegree);
    }
    
    /**
     * Decrements a vertex degree count, given an internal degree map.
     * 
     * @param array $map The internal degree map.
     * @param int $vertexDegree The vertex degree count to decrement.
     * @return void
     */
    protected function decrementVertexDegreeCountOfMap(&$map, $vertexDegree) {
        $map[$vertexDegree]--;
        if ($map[$vertexDegree] <= 0) {
            unset($map[$vertexDegree]);
        }
    }
    
    /**
     * Increments a vertex degree count.
     * 
     * @param int $vertexDegree The vertex degree.
     * @return void
     */
    protected function incrementVertexDegreeCount($vertexDegree) {
        $this->incrementVertexDegreeCountOfMap($this->degreeMap, $vertexDegree);
    }
    
    /**
     * Increments a vertex degree count of a degree map and sorts the degree map if needed.
     * 
     * @param array $map The degree map.
     * @param int $vertexDegree The vertex outdegree.
     * @return void
     */
    protected function incrementVertexDegreeCountOfMap(&$map, $vertexDegree) {
        $sort = FALSE;
        if (!isset($map[$vertexDegree])) {
            // There wasn't a vertex with this degree yet.
            $map[$vertexDegree] = 0;
            
            // If the vertex degree is greater than 0, the degree map must be resorted.
            $sort = $vertexDegree > 0;
        }
        $map[$vertexDegree]++;
        
        if ($sort) {
            $this->sortingAlgorithm->ksort($map, function($degree1, $degree2) {
                // Highest degree comes first.
                return $degree2 <=> $degree1;
            });
        }
    }
    
    /**
     * Increments the degree of a vertex creating, if none existed yet, a new vertex degree entry
     * in the internal degree map for the new degree of the vertex.
     * 
     * @param VertexInterface $vertex The vertex.
     * @return void
     */
    protected function incrementNewVertexDegree(VertexInterface $vertex) {
        $this->updateNewVertexDegree($vertex, TRUE);
    }
    
    /**
     * Decrements the degree of a vertex creating, if none existed yet, a new vertex degree entry
     * in the internal degree map for the new degree of the vertex.
     * 
     * @param VertexInterface $vertex The vertex.
     * @return void
     */
    protected function decrementNewVertexDegree(VertexInterface $vertex) {
        $this->updateNewVertexDegree($vertex, FALSE);
    }
    
    /**
     * Updates the degree of a vertex incrementing or decrementing it and creating, if none existed yet, a new vertex degree entry
     * in the internal degree map for the new degree of the vertex.
     * 
     * @param VertexInterface $vertex The vertex
     * @param bool $increment TRUE if the new vertex degree is incremented by one, FALSE if decremented by one.
     * @return void
     */
    protected function updateNewVertexDegree(VertexInterface $vertex, $increment = TRUE) {
        // Decrementing the old degree count.
        $vertexDegree = $this->verticesDegreeMap[$vertex];
        $this->decrementVertexDegreeCount($vertexDegree);
        
        // New incremented/decremented degree.
        $increment ? $vertexDegree++ : $vertexDegree--;
        $this->verticesDegreeMap[$vertex] = $vertexDegree;
        $this->incrementVertexDegreeCount($vertexDegree);
    }
    
    /**
     * Filters the edges of a graph if the two vertices are adjacent finding the one with the max or min weight and returns a tuple
     * containing the edge, its integer weight and the source and target vertices given as parameters.
     * 
     * @param VertexInterface $sourceVertex The source vertex.
     * @param VertexInterface $targetVertex The target vertex.
     * @param int $skeletonKeepEdgeMode The mode to use when collapsing multiple edges.
     *                                                            Available modes are:
     *                                                            - `GraphInterface::SKELETON_MODE_KEEP_MAX_EDGE`: If a pair of vertex has two or more edges, the edge which will be kept will be the edge with the highest weight.
     *                                                            - `GraphInterface::SKELETON_MODE_KEEP_MIN_EDGE`:  If a pair of vertex has two or more edges, the edge which will be kept will be the edge with the lowest weight.
     *                                                            The default is to keep the edge with the highest weight.
     * @param callable|null $edgeWeightToIntegerCallback For multiple edges connecting the same pair of vertices, if the weight of the edges is not an integer,
     *                                                                                     this callback MUST be called with the given parameters, in order: the edge, the source and target vertices.
     *                                                                                     Its return value is expected to be an integer to use for identifying the max or min edge to keep, depending on the mode.
     * @return array|null A tuple containing, in order: the max or min edge to keep, its integer weight, source and target vertices.
     *                              NULL in case the source vertex is not adjacent to the target vertex.
     * @throws \UnexpectedValueException If either the weight of an edge is not an int and there isnt't an `$edgeWeightToIntegerCallback` set or the return value of the callback is not an int.
     */
    protected function edgeToKeep(VertexInterface $sourceVertex, VertexInterface $targetVertex, $skeletonKeepEdgeMode = GraphInterface::SKELETON_MODE_KEEP_MAX_EDGE, $edgeWeightToIntegerCallback = NULL): array {        
        if (!isset($this->adjacencyMap[$sourceVertex][$targetVertex])) {
            return NULL;
        }
        
        $edges = $this->adjacencyMap[$sourceVertex][$targetVertex];
        
        $edgeToKeep = NULL;
        $winningIntegerWeight = $skeletonKeepEdgeMode === GraphInterface::SKELETON_MODE_KEEP_MAX_EDGE ? PHP_INT_MIN : PHP_INT_MAX;
        foreach ($edges as $edge) {
            /* @var $edge EdgeInterface */
            $weight = $edge->getWeight();
            if (!is_int($weight) && !is_callable($edgeWeightToIntegerCallback)) {
                throw new \UnexpectedValueException(
                    sprintf(
                        'The edge of type "%s" has a weight of type "%s" and no callback returing its integer representation was given.',
                        get_class($edge),
                        is_object($weight) ? get_class($weight) : gettype($weight)
                    )
                );
            }
            else if (is_callable($edgeWeightToIntegerCallback)) {
                $weight = $edgeWeightToIntegerCallback($edge, $sourceVertex, $targetVertex);
                if (!is_int($weight)) {
                    throw new \UnexpectedValueException(
                        sprintf(
                            'The edge weight to integer callback must return an integer representing the weight of the edge, but returned a value of type "%s" for the edge of type "%s".',
                            is_object($weight) ? get_class($weight) : gettype($weight),
                            get_class($edge)
                        )
                    );
                }
            }
            
            // At this point, `$weight` is guaranteed to be an int.
            $comparison = $weight <=> $winningIntegerWeight;
            if (
                $skeletonKeepEdgeMode === GraphInterface::SKELETON_MODE_KEEP_MAX_EDGE
            ) {
                // Max.
                if ($comparison >= 0) {
                    $edgeToKeep = $edge;
                    $winningIntegerWeight = $weight;   
                }
            }
            else if ($comparison <= 0) {
                // Min.
                $edgeToKeep = $edge;
                $winningIntegerWeight = $weight;
            }
        }
        
        return [$edgeToKeep, $winningIntegerWeight, $sourceVertex, $targetVertex];
    }
    
    /**
     * Removes the associations between neighbor vertices, if it exists.
     * 
     * @param VertexInterface $sourceVertex Source vertex.
     * @param VertexInterface $targetVertex Target vertex.
     * @return void
     */
    protected function removeNeighborsAssociation(VertexInterface $sourceVertex, VertexInterface $targetVertex) {
        if (isset($this->neighborsMap[$sourceVertex][$targetVertex])) {
            unset($this->neighborsMap[$sourceVertex][$targetVertex]);
        }
    }
    
    /**
     * Gets the maximum degree of a degree map.
     * 
     * @param array $map The degree map.
     * @return int The maximum degree of the map or -1 if the map is empty.
     */
    protected function getMaxFromDegreeMap(&$map) {
        if (empty($map)) {
            return -1;
        }
        reset($map);
        $degree = key($map);
        return $degree;
    }
    
    /**
     * Gets the minimum degree of a degree map.
     * 
     * @param array $map The degree map.
     * @return int The minimum degree of the map or -1 if the map is empty.
     */
    protected function getMinFromDegreeMap(&$map) {
        if (empty($map)) {
            return -1;
        }
        end($map);
        $degree = key($map);
        reset($map);
        return $degree;
    }
    
    /**
     * Internal factory method for creating a new vertex.
     * 
     * @param mixed $vertexValue The value of the vertex.
     * @return VertexInterface The vertex.
     */
    protected function makeNewVertex($vertexValue): VertexInterface {
        $vertex = new Vertex();
        $vertex->setValue($vertexValue);
        return $vertex;
    }
    
    /**
     * Internal factory method for creating a new edge.
     * 
     * @param mixed $edgeWeight The weight of the edge.
     * @return EdgeInterface The edge.
     */
    protected function makeNewEdge($edgeWeight): EdgeInterface {
        $edge = new Edge();
        $edge->setWeight($edgeWeight);
        return $edge;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOrder(): int {
        return $this->adjacencyMap->count();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDimension(): int {
        return $this->edgeVerticesMap->count();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMaximumDegree(): int {
        return $this->getMaxFromDegreeMap($this->degreeMap);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMinimumDegree(): int {
        return $this->getMinFromDegreeMap($this->degreeMap);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVertexDegree(VertexInterface $vertex): int {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        return $this->verticesDegreeMap[$vertex];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVertexNeighbors(VertexInterface $vertex): \Iterator {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        return $this->neighborsMap[$vertex];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVertexNeighborhood(VertexInterface $vertex): GraphInterface {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        
        $graph = $this->newEmptyGraph();
        
        $neighbors = $this->getVertexNeighbors($vertex);
        $i = 0;
        $vertices = [];
        foreach ($neighbors as $neighbor) {
            $graph->addVertex($neighbor);
            
            $vertices[] = $neighbor;
            $k = $i;
            while ($k >= 0) {
                $vertex = $vertices[$k];
                if (isset($this->adjacencyMap[$vertex][$neighbor])) {
                    $edges = $this->adjacencyMap[$vertex][$neighbor];
                    foreach ($edges as $edge) {
                        $graph->addEdge($vertex, $edge, $neighbor);
                    }
                }
                if (isset($this->adjacencyMap[$neighbor][$vertex])) {
                    $edges = $this->adjacencyMap[$vertex][$neighbor];
                    foreach ($edges as $edge) {
                        if (!$graph->hasEdge($edge)) {
                            $graph->addEdge($neighbor, $edge, $vertex);
                        }
                    }
                }
                $k--;
            }
            $i++;
        }
        
        return $graph;
    }
    
    /**
     * {@inheritdoc}
     */
    public function addVertex(VertexInterface $vertex) {
        $this->throwExceptionIfVertexAlreadyExists($vertex);
        
        $this->adjacencyMap[$vertex] = $this->makeObjectMap();
        
        $this->verticesMap[$vertex] = $vertex;
        $this->verticesDegreeMap[$vertex] = 0;
        $this->neighborsMap[$vertex] = $this->makeObjectMap();
        
        $this->incrementVertexDegreeCount(0);
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createVertex($vertexValue = NULL): VertexInterface {
        $vertex = $this->makeNewVertex($vertexValue);
        $this->addVertex($vertex);
        return $vertex;
    }
    
    /**
     * {@inheritdoc}
     */
    public function removeVertex(VertexInterface $vertex) {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        
        /*
         * Remove all connected edges which connect other vertices to the given vertex
         * updating the degrees of all vertices connected to this vertex.
         */
        $neighbors = $this->getVertexNeighbors($vertex);
        foreach ($neighbors as $neighbor) {
            $edgesFromVertexToNeighbor = $this->adjacencyMap[$vertex][$neighbor] ?? [];
            foreach ($edgesFromVertexToNeighbor as $edge) {
                $this->removeEdge($edge);
            }
            
            $edgesFromNeighborToVertex = $this->adjacencyMap[$neighbor][$vertex] ?? [];
            foreach ($edgesFromNeighborToVertex as $edge) {
                $this->removeEdge($edge);
            }
        }
        
        unset($this->adjacencyMap[$vertex]);
        unset($this->verticesMap[$vertex]);
        unset($this->neighborsMap[$vertex]);
        unset($this->verticesDegreeMap[$vertex]);
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasVertex(VertexInterface $vertex): bool {
        return isset($this->adjacencyMap[$vertex]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function addEdge(VertexInterface $vertex1, EdgeInterface $edge, VertexInterface $vertex2) {
        $this->throwExceptionIfVertexDoesNotExist($vertex1);
        $this->throwExceptionIfEdgeAlreadyExists($edge);
        $this->throwExceptionIfVertexDoesNotExist($vertex2);
        
        if (!isset($this->adjacencyMap[$vertex1][$vertex2])) {
            $this->adjacencyMap[$vertex1][$vertex2] = $this->makeObjectMap();
        }
        $this->adjacencyMap[$vertex1][$vertex2][$edge] = $edge;
        
        $this->neighborsMap[$vertex1][$vertex2] = $vertex2;
        $this->neighborsMap[$vertex2][$vertex1] = $vertex1;
        
        $this->edgesMap[$edge] = $edge;
        $this->edgeVerticesMap[$edge] = [$vertex1, $vertex2];
        
        // Update vertices degrees.
        $this->incrementNewVertexDegree($vertex1);
        $this->incrementNewVertexDegree($vertex2);
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createEdge(VertexInterface $vertex1, VertexInterface $vertex2, $edgeWeight = NULL): EdgeInterface {
        $edge = $this->makeNewEdge($edgeWeight);
        $this->addEdge($vertex1, $edge, $vertex2);
        return $edge;
    }
    
    /**
     * {@inheritdoc}
     */
    public function removeEdge(EdgeInterface $edge) {
        $this->throwExceptionIfEdgeDoesNotExist($edge);
        
        list($sourceVertex, $targetVertex) = $this->edgeVerticesMap[$edge];
        
        // Remove edge.
        unset($this->edgesMap[$edge]);
        unset($this->edgeVerticesMap[$edge]);
        
        // Remove edge from adjacency map.
        unset($this->adjacencyMap[$sourceVertex][$targetVertex][$edge]);
        if ($this->adjacencyMap[$sourceVertex][$targetVertex]->count() <= 0) {
            unset($this->adjacencyMap[$sourceVertex][$targetVertex]);
            
            // Remove neighbors association.
            $this->removeNeighborsAssociation($sourceVertex, $targetVertex);
        }
        
        // Update vertices degrees.
        $this->decrementNewVertexDegree($sourceVertex);
        $this->decrementNewVertexDegree($targetVertex);
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasEdge(EdgeInterface $edge): bool {
        return isset($this->edgeVerticesMap[$edge]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVertexEdges(VertexInterface $vertex): \Iterator {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        $neighbors = $this->getVertexNeighbors($vertex);
        foreach ($neighbors as $neighbor) {
            yield from $this->getVerticesEdges($vertex, $neighbor);
        }
        yield from [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVerticesEdges(VertexInterface $vertex1, VertexInterface $vertex2): \Iterator {
        $this->throwExceptionIfVertexDoesNotExist($vertex1);
        $this->throwExceptionIfVertexDoesNotExist($vertex2);
        
        if (isset($this->adjacencyMap[$vertex1][$vertex2])) {
            $edges = $this->adjacencyMap[$vertex1][$vertex2];
            yield from $edges;
        }
        if (isset($this->adjacencyMap[$vertex2][$vertex1])) {
            $edges = $this->adjacencyMap[$vertex2][$vertex1];
            yield from $edges;
        }
        yield from [];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAllVertices(): \Iterator {
        return $this->verticesMap;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAllEdges(): \Iterator {
        return $this->edgesMap;
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasLoop(VertexInterface $vertex): bool {
        $this->throwExceptionIfVertexDoesNotExist($vertex);
        return isset($this->adjacencyMap[$vertex][$vertex]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function hasLoops(): bool {
        foreach ($this->adjacencyMap as $vertex) {
            if (isset($this->adjacencyMap[$vertex][$vertex])) {
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isComplete(): bool {
        if ($this->neighborsMap->count() <= 0) {
            return FALSE;
        }
        $vertices = [];
        $i = 0;
        foreach ($this->verticesMap as $vertex) {
            if (isset($this->adjacencyMap[$vertex][$vertex])) {
                // A complete graph is simple and therefore does not allow loops.
                return FALSE;
            }
            
            $vertices[] = $vertex;
            
            $k = $i - 1;
            while ($k >= 0) {
                $otherVertex = $vertices[$k];
                
                if ($this->verticesPairDoesNotMakeGraphComplete($vertex, $otherVertex)) {
                    return FALSE;
                }
                
                $k--;
            }
            $i++;
        }
        return TRUE;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSkeleton($skeletonKeepEdgeMode = GraphInterface::SKELETON_MODE_KEEP_MAX_EDGE, $edgeWeightToIntegerCallback = NULL): GraphInterface {
        $skeleton = $this->newEmptyGraph();
        $allVertices = $this->verticesMap;
        $vertices = [];
        $i = 0;
        foreach ($allVertices as $vertexToAdd) {
            $skeleton->addVertex($vertexToAdd);
            
            $k = $i - 1;
            while ($k >= 0) {
                $vertex = $vertices[$k];
                $this->addCollapsedEdgeToSkeleton($skeleton, $vertexToAdd, $vertex, $skeletonKeepEdgeMode, $edgeWeightToIntegerCallback);
                $k--;
            }
            $i++; 
        }
        return $skeleton;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDegreeSequence(): \Iterator {
        foreach ($this->degreeMap as $degree => $verticesCount) {
            while ($verticesCount > 0) {
                $verticesCount--;
                yield $degree;
            }
        }
        yield from [];
    }
    
}
