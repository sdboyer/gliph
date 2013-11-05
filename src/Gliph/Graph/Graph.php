<?php

namespace Gliph\Graph;

use Gliph\Exception\InvalidVertexTypeException;
use Gliph\Exception\NonexistentVertexException;

/**
 * The most basic interface for graph datastructures.
 */
interface Graph {

    /**
     * Loops over each vertex that is adjacent to the given vertex.
     *
     * The meaning of "adjacency" depends on the type of graph. In a directed
     * graph, it refers to all the out-edges of the provided vertex. In an
     * undirected graph, in-edges and out-edges are the same, so this method
     * will iterate over both.
     *
     * The generator yields an edge as key and the adjacent vertex as value. The
     * form by which the edge is represented may vary from one graph
     * implementation to another, but the representation should be the same as
     * produced by the graph's eachEdge() implementation.
     *
     * @see Graph::eachEdge()
     *
     * @param object $vertex
     *   The vertex whose out-edges should be visited.
     *
     * @return \Generator
     *   A generator that yields the edge as key and adjacent vertex as value.
     *
     * @throws NonexistentVertexException
     *   Thrown if the vertex provided in the first parameter is not present in
     *   the graph.
     */
    public function eachAdjacent($vertex);

    /**
     * Returns a generator that loops through each vertex in the graph.
     *
     * @return \Generator
     *   A generator that yields the vertex as key and its connected edges as
     *   value. The form of the connected edges may value from one graph
     *   implementation to the next, but it is guaranteed to be Traversable.
     */
    public function eachVertex();

    /**
     * Loops over each edge in the graph via a generator.
     *
     * Different graphs may represent the edge in different ways. A graph with a
     * simple edge concept - e.g., no edge weighting or typing, etc. - may
     * represent the edge as a 2-tuple (an indexed array with two elements).
     * More complex edges may be represented as an object. If no additional
     * information is provided by the method implementation's phpdoc, a 2-tuple
     * should be assumed.
     *
     * @return \Generator
     *   A generator that produces a single value representing an edge on each
     *   iteration.
     */
    public function eachEdge();

    /**
     * Indicates whether or not the provided vertex is present in the graph.
     *
     * @param object $vertex
     *   The vertex object to check for membership in the graph.
     *
     * @return bool
     *   TRUE if the vertex is present, FALSE otherwise.
     */
    public function hasVertex($vertex);
}
