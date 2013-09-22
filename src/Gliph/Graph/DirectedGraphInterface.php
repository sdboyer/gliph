<?php

namespace Gliph\Graph;

/**
 * Interface for directed graph datastructures.
 */
interface DirectedGraphInterface extends GraphInterface {

    /**
     * Adds a directed edge to this graph.
     *
     * Directed edges are also often referred to as 'arcs'.
     *
     * @param object $tail
     *   An object vertex from which the edge originates. The vertex will be
     *   added to the graph if it is not already present.
     * @param object $head
     *   An object vertex to which the edge points. The vertex will be added to
     *   the graph if it is not already present.
     *
     * @return DirectedGraphInterface
     *   The current graph instance.
     */
    public function addDirectedEdge($tail, $head);

    /**
     * Returns the transpose of this graph.
     *
     * A transpose is identical to the current graph, except that its edges
     * have had their directionality reversed.
     *
     * Transposed graphs are sometimes called the 'reverse' or 'converse'.
     *
     * @return DirectedGraphInterface
     */
    public function transpose();
}