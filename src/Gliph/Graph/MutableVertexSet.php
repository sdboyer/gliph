<?php

namespace Gliph\Graph;

use Gliph\Exception\InvalidVertexTypeException;

/**
 * Interface describing a mutable vertex set.
 *
 * This is an atomic component interface for graphs.
 */
interface MutableVertexSet {

    /**
     * Ensures a vertex is present in the graph.
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
    public function ensureVertex($vertex);

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

}