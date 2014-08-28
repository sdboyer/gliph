<?php

namespace Gliph\Graph;

use Gliph\Exception\NonexistentVertexException;

/**
 * The most basic interface for graph datastructures.
 */
interface Graph {

    /**
     * Loops over each vertex that is adjacent to the given vertex.
     *
     * A vertex is adjacent to another vertex if they share an edge. Edge
     * direction, if any, does not matter.
     *
     * The generator yields only a value: the adjacent vertex. The
     *
     * @param object $vertex
     *   The vertex whose adjacent vertices should be visited.
     *
     * @return \Generator
     *   A generator that yields adjacent vertices as values.
     *
     * @throws NonexistentVertexException
     *   Thrown if the vertex provided is not present in the graph.
     */
    public function adjacentTo($vertex);

    /**
     * Returns a generator that loops through each vertex in the graph.
     *
     * @return \Generator
     *   A generator that yields the vertex as key and its connected edges as
     *   value. The form of the connected edges may value from one graph
     *   implementation to the next, but it is guaranteed to be Traversable.
     */
    public function vertices();

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
    public function edges();

    /**
     * Returns the degree (number of incident edges) for the provided vertex.
     *
     * @param object $vertex
     *   The vertex for which to retrieve degree information.
     *
     * @return int
     *
     * @throws NonexistentVertexException
     *   Thrown if the vertex provided in the first parameter is not present in
     *   the graph.
     *
     */
    public function degreeOf($vertex);

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

    /**
     * Returns the number of edges in the graph.
     *
     * @return int
     */
    public function size();

    /**
     * Returns the number of vertices in the graph.
     *
     * @return int
     */
    public function order();
}
