<?php

namespace Gliph;

use Gliph\Util\HashMap;

class DirectedAdjacencyGraph {
    protected $vertices;

    public function __construct($object_vertices = FALSE) {
        $this->vertices = $object_vertices ? new \SplObjectStorage() : new HashMap();
    }

    public function addVertex($vertex) {
        if (!$this->hasVertex($vertex)) {
            $this->vertices[$vertex] = array();
        }
    }

    public function addDirectedEdge($from, $to) {
        $this->addVertex($from);
        $this->addVertex($to);
        $val = $this->vertices[$from];
        $val[] = $to;
        $this->vertices[$from] = $val;
    }

    public function removeVertex($vertex) {
        unset($this->vertices[$vertex]);
    }

    public function removeEdge($from, $to) {
        $val = $this->vertices[$from];
        unset($val[array_search($to, $val)]);
        $this->vertices[$from] = $val;
    }

    public function eachAdjacent($vertex, $callback) {
        foreach ($this->vertices[$vertex] as $e) {
            call_user_func($callback, $e);
        }
    }

    public function eachVertex($callback) {
        $this->fev(function ($v, $outgoing) use ($callback) {
            call_user_func($callback, $v);
        });
    }

    public function eachEdge($callback) {
        $edges = array();
        $this->fev(function ($from, $outgoing) use (&$edges) {
            foreach ($outgoing as $to) {
                $arr = new \SplFixedArray(2);
                $arr[0] = $from;
                $arr[1] = $to;
                $edges[] = $arr;
            }
        });

        foreach ($edges as $edge) {
            call_user_func($callback, $edge);
        }
    }

    public function hasVertex($vertex) {
        return isset($this->vertices[$vertex]);
    }

    protected function fev($callback) {
        if ($this->vertices instanceof \SplObjectStorage) {
            foreach ($this->vertices as $vertex) {
                $outgoing = $this->vertices->getInfo();
                $callback($vertex, $outgoing);
            }
        }
        else {
            for ($this->vertices->rewind(); $this->vertices->valid(); $this->vertices->next()) {
                list($vertex, $outgoing) = $this->vertices->pair();
                $callback($vertex, $outgoing);
            }
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
     * @return DirectedAdjacencyGraph
     */
    public function transpose() {
        $graph = new self();
        $this->eachEdge(function($edge) use (&$graph) {
            $graph->addDirectedEdge($edge[1], $edge[0]);
        });

        return $graph;
    }
}

