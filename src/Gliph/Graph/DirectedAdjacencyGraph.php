<?php

namespace Gliph\Graph;

use Gliph\Tarjan;

class DirectedAdjacencyGraph {
    protected $vertices;

    protected $vertexTypes;

    public function __construct() {
        $this->vertices = new \SplObjectStorage();
    }

    public function addVertex($vertex) {
        if (!is_object($vertex)) {
            throw new \OutOfBoundsException('Vertices must be objects; non-object provided.');
        }

        if (!$this->hasVertex($vertex)) {
            $this->vertices[$vertex] = new \SplObjectStorage();
        }
    }

    public function addDirectedEdge($from, $to) {
        $this->addVertex($from);
        $this->addVertex($to);
        $this->vertices[$from]->attach($to);
    }

    public function removeVertex($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new \OutOfRangeException('Vertex is not in the graph, it cannot be removed.', E_WARNING);
        }

        unset($this->vertices[$vertex]);
        $this->eachVertex(function($v, $outgoing) use ($vertex) {
            if ($outgoing->contains($vertex)) {
                $outgoing->detach($vertex);
            }
        });
    }

    public function removeEdge($from, $to) {
        $this->vertices[$from]->detach($to);
    }

    public function eachAdjacent($vertex, $callback) {
        foreach ($this->vertices[$vertex] as $e) {
            call_user_func($callback, $e);
        }
    }

    public function eachVertex($callback) {
        $this->fev(function ($v, $outgoing) use ($callback) {
            call_user_func($callback, $v, $outgoing);
        });
    }

    public function eachEdge($callback) {
        $edges = array();
        $this->fev(function ($from, $outgoing) use (&$edges) {
            foreach ($outgoing as $to) {
                $edges[] = array($from, $to);
            }
        });

        foreach ($edges as $edge) {
            call_user_func($callback, $edge);
        }
    }

    public function hasVertex($vertex) {
        return $this->vertices->contains($vertex);
    }

    protected function fev($callback) {
        foreach ($this->vertices as $vertex) {
            $outgoing = $this->vertices->getInfo();
            $callback($vertex, $outgoing);
        }
    }

    /**
     * Returns the transpose of this graph.
     *
     * A transpose is identical to the current graph, except that
     * its edges have had their directionality reversed.
     *
     * Also sometimes known as the 'reverse' or 'converse'.
     *
     * @return \Gliph\Graph\DirectedAdjacencyGraph
     */
    public function transpose() {
        $graph = new self();
        $this->eachEdge(function($edge) use (&$graph) {
            $graph->addDirectedEdge($edge[1], $edge[0]);
        });

        return $graph;
    }

    public function getCycles() {
        $tarjan = new Tarjan();
        $scc = $tarjan->getCycles($this);
        return $scc->count() > 0 ? $scc : FALSE;
    }
}

