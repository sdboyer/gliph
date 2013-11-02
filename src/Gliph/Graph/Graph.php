<?php

namespace Gliph\Graph;

use Gliph\Exception\InvalidVertexTypeException;
use Gliph\Exception\NonexistentVertexException;

/**
 * The most basic interface for graph datastructures.
 */
interface Graph {

    /**
     * Adds a vertex to the graph.
     *
     * Gliph requires that its graph vertices be objects; beyond that, it does
     * not care about vertex type.
     *
     * @param object $vertex
     *   An object to use as a vertex in the graph.
     *
     * @return Graph
     *   The current graph instance.
     *
     * @throws InvalidVertexTypeException
     *   Thrown if an invalid type of data is provided as a vertex.
     */
    public function addVertex($vertex);

    /**
     * Remove a vertex from the graph.
     *
     * This will also remove any edges that include the vertex.
     *
     * @param object $vertex
     *   A vertex object to remove from the graph.
     *
     * @return Graph
     *   The current graph instance.
     *
     * @throws NonexistentVertexException
     *   Thrown if the provided vertex is not present in the graph.
     */
    public function removeVertex($vertex);

    /**
     * Removes an edge from the graph.
     *
     * @param $a
     *   The first vertex in the edge pair to remove. In a directed graph, this
     *   is the tail vertex.
     * @param $b
     *   The second vertex in the edge pair to remove. In a directed graph, this
     *   is the head vertex.
     *
     * @return Graph
     *   The current graph instance.
     */
    public function removeEdge($a, $b);

    /**
     * Calls the callback with each vertex adjacent to the provided vertex.
     *
     * The meaning of "adjacency" depends on the type of graph. In a directed
     * graph, it refers to all the out-edges of the provided vertex. In an
     * undirected graph, in-edges and out-edges are the same, so this method
     * will iterate over both.
     *
     * @param object $vertex
     *   The vertex whose out-edges should be visited.
     * @param callback $callback
     *   The callback to fire. For each vertex found along an out-edge, this
     *   callback will be called with that vertex as the sole parameter.
     *
     * @return Graph
     *   The current graph instance.
     *
     * @throws NonexistentVertexException
     *   Thrown if the vertex provided in the first parameter is not present in
     *   the graph.
     */
    public function eachAdjacent($vertex, $callback);

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