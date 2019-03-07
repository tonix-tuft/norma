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
use Norma\Data\Structure\Graph\VertexInterface;
use Norma\Data\Structure\Graph\UnknownVertexException;

/**
 * The interface of a directed graph.
 *
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
interface DirectedGraphInterface extends GraphInterface {
    
    /**
     * Gets the transpose graph of this directed graph.
     * 
     * @return DirectedGraphInterface The transpose graph.
     */
    public function getTranspose(): DirectedGraphInterface;
    
    /**
     * Gets the incoming neighbour vertices of the given vertex (vertices from which the given one is reachable).
     * 
     * @param VertexInterface $vertex The vertex.
     * @return \Traversable A traversable collection of vertices of type {@link VertexInterface}.
     *                                   An empty collection in case the vertex doesn't have incoming neighbours.
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function getVertexIncomingEdgeNeighbours(VertexInterface $vertex);
    
    /**
     * Gets the outgoing neighbour vertices of the given vertex (vertices which are reachable from the given one).
     * 
     * @param VertexInterface $vertex The vertex.
     * @return \Traversable A traversable collection of vertices of type {@link VertexInterface}.
     *                                   An empty collection in case the vertex doesn't have incoming neighbours.
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function getVertexOutgoingEdgeNeighbours(VertexInterface $vertex);
    
    /**
     * Gets the number of incoming neighbour vertices of the given vertex.
     * 
     * @param VertexInterface $vertex The vertex.
     * @return int The indegree.
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function getVertexIndegree(VertexInterface $vertex): int;
    
    /**
     * Gets the number of outgoing neighbour vertices of the given vertex.
     * 
     * @param VertexInterface $vertex The vertex.
     * @return int The outdegree.
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function getVertexOutdegree(VertexInterface $vertex): int;
    
    /**
     * Tests whether two vertices are incoming adjacent or not, i.e. if the first vertex
     * can be reached from the second vertex.
     * 
     * @param VertexInterface $vertex1 First vertex.
     * @param VertexInterface $vertex2 Second vertex.
     * @return bool TRUE if they are incoming adjacent, FALSE otherwise.
     * @throws UnknownVertexException If one of the given vertices is unknown.
     */
    public function areIncomingAdjacent(VertexInterface $vertex1, VertexInterface $vertex2): bool;
    
    /**
     * Tests whether two vertices are outgoing adjacent or not, i.e. if the second vertex
     * can be reached from the first vertex.
     * 
     * @param VertexInterface $vertex1 First vertex.
     * @param VertexInterface $vertex2 Second vertex.
     * @return bool TRUE if they are incoming adjacent, FALSE otherwise.
     * @throws UnknownVertexException If one of the given vertices is unknown.
     */
    public function areOutgoingAdjacent(VertexInterface $vertex1, VertexInterface $vertex2): bool;
    
    /**
     * Gets all the incoming edges which have the given vertex as an incoming endpoint.
     * 
     * @param VertexInterface $vertex The vertex.
     * @return \Traversable A traversable collection of {@link EdgeInterface}. An empty collection if there are no incoming edges which link to the given vertex.
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function getVertexIncomingEdges(VertexInterface $vertex): \Traversable;
    
    /**
     * Gets all the outgoing edges which have the given vertex as an outgoing endpoint.
     * 
     * @param VertexInterface $vertex The vertex.
     * @return \Traversable A traversable collection of {@link EdgeInterface}. An empty collection if there are no outgoing edges for the given vertex.
     * @throws UnknownVertexException If the given vertex is unknown.
     */
    public function getVertexOutgoingEdges(VertexInterface $vertex): \Traversable;
    
    /**
     * Gets all the incoming edges which have the given vertices as endpoints, i.e. all edges
     * with a direction such as the first vertex is reached from the second vertex.
     * 
     * @param VertexInterface $vertex1 The first vertex.
     * @param VertexInterface $vertex2 The second vertex.
     * @return \Traversable A traversable collection of {@link EdgeInterface}. An empty collection if there are no incoming edges which link the two vertices.
     * @throws UnknownVertexException If one of the given vertices is unknown.
     */
    public function getVerticesIncomingEdges(VertexInterface $vertex1, VertexInterface $vertex2): \Traversable;
    
    /**
     * Gets all the outgoing edges which have the given vertices as endpoints, i.e. all edges
     * with a direction such as the second vertex is reached from the first vertex.
     * 
     * @param VertexInterface $vertex1 The first vertex.
     * @param VertexInterface $vertex2 The second vertex.
     * @return \Traversable A traversable collection of {@link EdgeInterface}. An empty collection if there are no outgoing edges which link the two vertices.
     * @throws UnknownVertexException If one of the given vertices is unknown.
     */
    public function getVerticesOutgoingEdges(VertexInterface $vertex1, VertexInterface $vertex2): \Traversable;
    
}