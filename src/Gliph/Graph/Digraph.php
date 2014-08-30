<?php

namespace Gliph\Graph;

use Gliph\Exception\NonexistentVertexException;

/**
 * Interface for directed graph datastructures.
 */
interface Digraph extends Graph {

    /**
     * Enumerates each successor vertex to the provided vertex via a generator.
     *
     * A vertex (B) is a successor to another vertex (A) if an arc points from
     * A to B. In such an arc, A is considered the 'tail', B is considered the
     * 'head', as it would be visually represented with an arrow:
     *
     *      A -> B
     *
     * @param object $vertex
     *   The vertex whose successor vertices should be enumerated.
     *
     * @return \Generator
     *   A generator that yields successor vertices as values.
     *
     * @throws NonexistentBertexException
     *   Thrown if the vertex provided is not present in the graph.
     */
    public function successorsOf($vertex);

    /**
     * Enumerates each predecessor vertex to the provided vertex via a generator.
     *
     * A vertex (B) is a predecessor to another vertex (A) if an arc points from
     * B to A. In such an arc, B is considered the 'tail', A is considered the
     * 'head', as it would be visually represented with an arrow:
     *
     *      B -> A
     *
     * @param object $vertex
     *   The vertex whose predecessor vertices should be enumerated.
     *
     * @return \Generator
     *   A generator that yields predecessor vertices as values.
     *
     * @throws NonexistentVertexException
     *   Thrown if the vertex provided is not present in the graph.
     */
    public function predecessorsOf($vertex);

    /**
     * Returns the in-degree (number of incoming edges) for the provided vertex.
     *
     * In undirected graphs, in-degree and out-degree are the same.
     *
     * @param object $vertex
     *   The vertex for which to retrieve in-degree information.
     *
     * @return int
     *
     * @throws NonexistentVertexException
     *   Thrown if the vertex provided in the first parameter is not present in
     *   the graph.
     *
     */
    public function inDegreeOf($vertex);

    /**
     * Returns the out-degree (count of outgoing edges) for the provided vertex.
     *
     * In undirected graphs, in-degree and out-degree are the same.
     *
     * @param object $vertex
     *   The vertex for which to retrieve out-degree information.
     *
     * @return int
     *
     * @throws NonexistentVertexException
     *   Thrown if the vertex provided in the first parameter is not present in
     *   the graph.
     *
     */
    public function outDegreeOf($vertex);

    /**
     * Returns the transpose of this graph.
     *
     * A transpose is identical to the current graph, except that its edges
     * have had their directionality reversed.
     *
     * Transposed graphs are sometimes called the 'reverse' or 'converse'.
     *
     * @return Digraph
     */
    public function transpose();

    /**
     * Indicates whether or not this graph is acyclic.
     *
     * @return bool
     */
    public function isAcyclic();

    /**
     * Returns the cycles in this graph, if any.
     *
     * @return array
     *   An array of arrays, each subarray representing a full cycle in the
     *   graph. If the array is empty, the graph is acyclic.
     */
    public function getCycles();
}
