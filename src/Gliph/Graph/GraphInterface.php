<?php

namespace Gliph\Graph;

use Gliph\Exception\InvalidVertexTypeException;
use Gliph\Exception\NonexistentVertexException;

/**
 * The most basic interface for graph datastructures.
 */
interface GraphInterface {

    /**
     * Adds a vertex to the graph.
     *
     * Gliph requires that its graph vertices be objects; beyond that, it does
     * not care about vertex type.
     *
     * @param object $vertex
     *   An object to use as a vertex in the graph.
     *
     * @return GraphInterface
     *   The current graph instance.
     *
     * @throws InvalidVertexTypeException
     *   Thrown if an invalid type of data is provided as a vertex.
     */
    public function addVertex($vertex);

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
     * @return GraphInterface
     *   The current graph instance.
     *
     * @throws NonexistentVertexException
     *   Thrown if the vertex provided in the first parameter is not present in
     *   the graph.
     */
    public function eachAdjacent($vertex, $callback);

    /**
     * Calls the callback for each vertex in the graph.
     *
     * @param $callback
     *   The callback is called once for each vertex in the graph. Two
     *   parameters are provided:
     *    - The vertex being inspected.
     *    - An SplObjectStorage containing a list of all the vertices adjacent
     *      to the vertex being inspected.
     *
     * @return GraphInterface
     *   The current graph instance.
     */
    public function eachVertex($callback);

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