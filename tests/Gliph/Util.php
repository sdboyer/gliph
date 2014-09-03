<?php

namespace Gliph;

use Gliph\Exception\IncompatibleGraphTypeException;
use Gliph\Graph\Graph;
use Gliph\Graph\MutableDigraph;
use Gliph\Graph\MutableGraph;

/**
 * Utility methods for tests.
 */
class Util {

    /**
     * Adds an edge to the provided graph using the appropriate method.
     *
     * This works by inspecting the interface of the provided graph and passing
     * along the vertex arguments to the corresponding method.
     *
     * @param Graph $g
     *   The graph to which an edge should be added.
     * @param mixed $u
     *   The first vertex in the edge pair. If $g is directed, it will be the
     *   tail.
     * @param mixed $v
     *   The second vertex in the edge pair. If $g is directed, it will be the
     *   head.
     *
     * @throws IncompatibleGraphTypeException
     *   Thrown if an unsupported graph type is provided.
     */
    public static function ensureEdge(Graph $g, $u, $v) {
        if ($g instanceof MutableDigraph) {
            $g->ensureArc($u, $v);
        }
        else if ($g instanceof MutableGraph) {
            $g->ensureEdge($u, $v);
        }
        else {
            throw new IncompatibleGraphTypeException('Can only ensureEdge on either a MutableDigraph or MutableUndirectedGraph');
        }
    }

    /**
     * Removes an edge from the provided graph using the appropriate method.
     *
     * This works by inspecting the interface of the provided graph and passing
     * along the vertex arguments to the corresponding method.
     *
     * This is fundamentally a hack that dodges around graph semantics by just
     * assuming that all the test implementations will respect vertex ordering.
     *
     * @param Graph $g
     *   The graph from which an edge should be removed.
     * @param mixed $u
     *   The first vertex in the edge pair. If $g is directed, it will be the
     *   tail.
     * @param mixed $v
     *   The second vertex in the edge pair. If $g is directed, it will be the
     *   head.
     *
     * @throws IncompatibleGraphTypeException
     *   Thrown if an unsupported graph type is provided.
     */
    public static function removeEdge(Graph $g, $u, $v) {
        if ($g instanceof MutableDigraph) {
            $g->removeArc($u, $v);
        }
        else if ($g instanceof MutableGraph) {
            $g->removeEdge($u, $v);
        }
        else {
            throw new IncompatibleGraphTypeException('Can only ensureEdge on either a MutableDigraph or MutableUndirectedGraph');
        }
    }
}