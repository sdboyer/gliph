<?php
namespace Gliph\Graph;

/**
 * Describes a graph that can be modified after initial creation.
 */
interface MutableGraph extends Graph, MutableVertexSet {

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
}