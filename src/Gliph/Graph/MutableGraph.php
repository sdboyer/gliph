<?php
namespace Gliph\Graph;

/**
 * Describes an undirected graph that can be modified after initial creation.
 */
interface MutableGraph extends Graph, MutableVertexSet {

    /**
     * Removes an undirected edge from the graph.
     *
     * @param $u
     *   One vertex in the edge pair to remove.
     * @param $v
     *   The other vertex in the edge pair to remove.
     *
     * @return MutableGraph
     *   The current graph instance.
     */
    public function removeEdge($u, $v);

    /**
     * Adds an undirected edge to this graph.
     *
     * @param object $u
     *   One object vertex in the edge pair. The vertex will be added to
     *   the graph if it is not already present.
     * @param object $v
     *   The other object vertex in the edge pair. The vertex will be added to
     *   the graph if it is not already present.
     *
     * @return MutableGraph
     *   The current graph instance.
     */
    public function addEdge($u, $v);
}