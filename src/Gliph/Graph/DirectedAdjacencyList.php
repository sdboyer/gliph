<?php

namespace Gliph\Graph;

use Gliph\Exception\NonexistentVertexException;

class DirectedAdjacencyList extends AdjacencyList {

    public function addDirectedEdge($from, $to) {
        if (!$this->hasVertex($from)) {
            $this->addVertex(($from));
        }

        if (!$this->hasVertex($to)) {
            $this->addVertex($to);
        }

        $this->vertices[$from]->attach($to);
    }

    public function removeVertex($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in the graph, it cannot be removed.', E_WARNING);
        }

        $this->eachVertex(function($v, $outgoing) use ($vertex) {
            if ($outgoing->contains($vertex)) {
                $outgoing->detach($vertex);
            }
        });
        unset($this->vertices[$vertex]);
    }

    public function removeEdge($from, $to) {
        $this->vertices[$from]->detach($to);
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

    /**
     * Returns the transpose of this graph.
     *
     * A transpose is identical to the current graph, except that
     * its edges have had their directionality reversed.
     *
     * Also sometimes known as the 'reverse' or 'converse'.
     *
     * @return \Gliph\Graph\DirectedAdjacencyList
     */
    public function transpose() {
        $graph = new self();
        $this->eachEdge(function($edge) use (&$graph) {
            $graph->addDirectedEdge($edge[1], $edge[0]);
        });

        return $graph;
    }
}

