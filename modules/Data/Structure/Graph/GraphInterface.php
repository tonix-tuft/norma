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

use Norma\Data\Structure\Graph\VertexInterface;
use Norma\Data\Structure\Graph\UnknownVertexException;
use Norma\Data\Structure\Graph\VertexAlreadyExistsException;
use Norma\Data\Structure\Graph\EdgeInterface;
use Norma\Data\Structure\Graph\EdgeAlreadyExistsException;

/**
 * An interface representing a graph data structure.
 * 
 * @see https://en.wikipedia.org/wiki/Graph_(abstract_data_type)
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface GraphInterface {
    
    /**
     * Skeleton mode to keep edges with the highest weight when creating a skeleton graph.
     */
    const SKELETON_MODE_KEEP_MAX_EDGE = 1;
    
    /**
     * Skeleton mode to keep edges with the highest weight when creating a skeleton graph.
     */
    const SKELETON_MODE_KEEP_MIN_EDGE = 2;
    
    /**
     * Tests whether two vertices are adjacent or not.
     * 
     * @param VertexInterface $vertex1 First vertex.
     * @param VertexInterface $vertex2 Second vertex.
     * @return bool TRUE if they are adjacent, FALSE otherwise.
     * @throws UnknownVertexException If one of the given vertices is unknown.
     */
    public function areAdjacent(VertexInterface $vertex1, VertexInterface $vertex2): bool;
    
    /**
     * Gets the order of the graph, i.e. the number of vertices.
     * 
     * @return int The order of the graph (number of vertices).
     */
    public function getOrder(): int;
    
    /**
     * Gets the dimension of the graph, i.e. the number of edges, the cardinality of edges of the graph.
     * 
     * @return int The number of edges.
     */
    public function getDimension(): int;
    
    /**
     * Gets the maximum degree of the graph considering all its vertices.
     * The maximum degree is the degree of the vertex with the highest degree considering all the others.
     * 
     * @return int The maximum degree of the graph. -1 MUST be returned if the graph is empty (does not have vertices yet).
     */
    public function getMaximumDegree(): int;
    
    /**
     * Gest the minimum degree of the graph considering all its vertices.
     * The minimum degree is the degree of the vertex with the lowest degree considering all the others.
     * 
     * @return int The minimum degree of the graph. -1 MUST be returned if the graph is empty (does not have vertices yet).
     */
    public function getMinimumDegree(): int;
    
    /**
     * Gets the density of the graph.
     * 
     * For simple undirected graphs, the density is defined by the following formula:
     * 
     *     Δ = 2L / n(n-1)
     * 
     * For simple directed graphs, the density is defined by the following formula:
     * 
     *     Δ = L / n(n-1)
     * 
     * where `Δ` is the density, `L` is the number (cardinality) of edges and `n` is the number (cardinality) of vertices.
     * 
     * @see https://en.wikipedia.org/wiki/Dense_graph
     * 
     * @return float The density of the graph.
     */
    public function getDensity();
    
    /**
     * Gets the degree of a vertex, i.e. the number of edges incident to the vertex,
     * with loops counted twice.
     * 
     * @see https://en.wikipedia.org/wiki/Degree_(graph_theory)
     * 
     * @param VertexInterface $vertex The vertex.
     * @return int The degree of the vertex.
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function getVertexDegree(VertexInterface $vertex): int;
    
    /**
     * Gets the neighbor vertices of the given vertex.
     * 
     * @param VertexInterface $vertex
     * @return \Iterator A traversable collection of vertices of type {@link VertexInterface}.
     *                                   An empty collection in case the vertex doesn't have neighbors.
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function getVertexNeighbors(VertexInterface $vertex): \Iterator;
    
    /**
     * Gets the neighborhood of a vertex as a new graph.
     * 
     * The neighborhood of a vertex of a graph is a new graph consisting of all the vertices
     * adjacent to the given vertex with all their eventual edges which connect them to each other.
     * 
     * @param VertexInterface $vertex
     * @return GraphInterface The graph representing the neighborhood.
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function getVertexNeighborhood(VertexInterface $vertex): GraphInterface;
    
    /**
     * Adds a vertex to the graph.
     * 
     * @param VertexInterface $vertex The vertex to add.
     * @return static
     * @throws VertexAlreadyExistsException If the given vertex already exists in the graph.
     */
    public function addVertex(VertexInterface $vertex);
    
    /**
     * Removes a vertex from the graph.
     * 
     * @param VertexInterface $vertex The vertex to remove.
     * @return static
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function removeVertex(VertexInterface $vertex);
    
    /**
     * Tests whether a vertex exists or not in the graph.
     * 
     * @param VertexInterface $vertex The vertex to test.
     * @return bool TRUE if the vertex exists in the graph, FALSE otherwise.
     */
    public function hasVertex(VertexInterface $vertex): bool;
    
    /**
     * Connects two vertices of the graph with an edge, making them adjacent.
     * 
     * @param VertexInterface $vertex1 The first vertex.
     * @param EdgeInterface $edge The edge to use to connect the two vertices.
     * @param VertexInterface $vertex2 The second vertex.
     * @return static
     * @throws UnknownVertexException If one of the given vertices is unknown.
     * @throws EdgeAlreadyExistsException If the edge already exists in the graph.
     */
    public function addEdge(VertexInterface $vertex1, EdgeInterface $edge, VertexInterface $vertex2);
    
    /**
     * Removes an edge.
     * 
     * @param EdgeInterface $edge The edge to remove.
     * @return static
     * @throws UnknownEdgeException If the given edge is not defined in the graph.
     */
    public function removeEdge(EdgeInterface $edge);
    
    /**
     * Tests whether an edge exists or not in the graph.
     * 
     * @param EdgeInterface $edge The edge to test.
     * @return bool TRUE if the edge exists in the graph, FALSE otherwise.
     */
    public function hasEdge(EdgeInterface $edge): bool;
    
    /**
     * Gets all the edges which have the given vertex as an endpoint.
     * 
     * @param VertexInterface $vertex The vertex.
     * @return \Iterator A traversable collection of {@link EdgeInterface}. An empty collection if there are no edges which link to the given vertex.
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function getVertexEdges(VertexInterface $vertex): \Iterator;
    
    /**
     * Gets all the edges which have the given vertices as endpoints.
     * 
     * @param VertexInterface $vertex1 The first vertex.
     * @param VertexInterface $vertex2 The second vertex.
     * @return \Iterator A traversable collection of {@link EdgeInterface}. An empty collection if there are no edges which link the two vertices.
     * @throws UnknownVertexException If one of the given vertices is unknown.
     */
    public function getVerticesEdges(VertexInterface $vertex1, VertexInterface $vertex2): \Iterator;
    
    /**
     * Gets all the vertices of the graph.
     * 
     * @return \Iterator A traversable collection of vertices. An empty collection in case there are no vertices.
     */
    public function getAllVertices(): \Iterator;
    
    /**
     * Gets all the edges of the graph.
     * 
     * @return \Iterator A traversable collection of edges. An empty collection in case there are no edges.
     */
    public function getAllEdges(): \Iterator;
    
    /**
     * Tests if a vertex has a loop, i.e. it has an edge which links it back to itself.
     * 
     * @param VertexInterface $vertex The vertex
     * @return bool TRUE if the vertex has a loop.
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function hasLoop(VertexInterface $vertex): bool;
    
    /**
     * Tests if the graph has loops, i.e. at least one node (vertex) has an edge which links back to itself.
     * 
     * @return bool TRUE if the graph has loops, FALSE otherwise.
     */
    public function hasLoops(): bool;
    
    /**
     * Tests if the graph is complete.
     * 
     * A graph is complete if and only if it is simple and any pair of its vertices is a pair such that
     * the two vertices of the pair are adjacent.
     * 
     * @see https://en.wikipedia.org/wiki/Complete_graph
     * 
     * @return bool TRUE if the graph is complete, FALSE otherwise.
     */
    public function isComplete(): bool;
    
    /**
     * Gets the skeleton graph of this graph, i.e. a new graph without eventual loops
     * and with eventual multiple edges collapsed into a single one.
     * 
     * The underlying vertices MUST remain untouched (they still MUST reference the same original vertices).
     * 
     * Loop edges MUST be removed, collapsing multiple edges MUST be recreated.
     * 
     * @param int $skeletonKeepEdgeMode The mode to use when collapsing multiple edges.
     *                                                            Available modes are:
     *                                                            - `GraphInterface::SKELETON_MODE_KEEP_MAX_EDGE`: If a pair of vertex has two or more edges, the edge which will be kept will be the edge with the highest weight.
     *                                                            - `GraphInterface::SKELETON_MODE_KEEP_MIN_EDGE`:  If a pair of vertex has two or more edges, the edge which will be kept will be the edge with the lowest weight.
     *                                                            The default is to keep the edge with the highest weight.
     * @param callable|null $edgeWeightToIntegerCallback For multiple edges connecting the same pair of vertices, if the weight of the edges is not an integer,
     *                                                                                     this callback MUST be called with the given parameters, in order: the edge, the source and target vertices.
     *                                                                                     Its return value is expected to be an integer to use for identifying the max or min edge to keep, depending on the mode.
     * @return GraphInterface The skeleton graph.
     * @throws \UnexpectedValueException If either the weight of an edge is not an int and there isnt't an `$edgeWeightToIntegerCallback` set or the return value of the callback is not an int.
     */
    public function getSkeleton($skeletonKeepEdgeMode = GraphInterface::SKELETON_MODE_KEEP_MAX_EDGE, $edgeWeightToIntegerCallback = NULL): GraphInterface;
    
    /**
     * Gets the degree sequence of the graph.
     * For undirected graphs, it would be the non-increasing sequence of its vertex degrees.
     * For directed graphs, it would be the list of its indegree and outdegree pairs.
     * 
     * @see https://en.wikipedia.org/wiki/Degree_(graph_theory)#Degree_sequence
     * @see https://en.wikipedia.org/wiki/Directed_graph#Degree_sequence
     */
    public function getDegreeSequence(): \Iterator;
    
}
