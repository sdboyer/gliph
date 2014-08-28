<?php
namespace Gliph\Graph;

/**
 * Describes a directed graph that can be modified after initial creation.
 */
interface MutableDigraph extends Digraph, MutableVertexSet {

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
     * @return MutableDigraph
     *   The current graph instance.
     */
    public function addArc($tail, $head);

    /**
     * Removes an arc from the graph.
     *
     * @param $tail
     *   The tail vertex in the arc to remove.
     * @param $head
     *   The head vertex in the arc to remove.
     *
     * @return MutableDigraph
     *   The current graph instance.
     */
    public function removeArc($tail, $head);
}