<?php

namespace Gliph;

use Gliph\Util\HashMap;

class DirectedAdjacencyGraph {
    protected $vertices;

    public function __construct() {
        $this->vertices = new HashMap();
    }

    public function addVertex($vertex) {
        if (!$this->hasVertex($vertex)) {
            $this->vertices[$vertex] = array();
        }
    }

    public function addDirectedEdge($from, $to) {
        $this->addVertex($from);
        $this->addVertex($to);
        $val = &$this->vertices->get($from);
        $val[] = $to;
    }

    public function removeVertex($vertex) {
        unset($this->vertices[$vertex]);
    }

    public function removeEdge($from, $to) {
        $val = &$this->vertices->get($from);
        unset($val[array_search($to, $val)]);
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
        for ($this->vertices->rewind(); $this->vertices->valid(); $this->vertices->next()) {
            list($vertex, $outgoing) = $this->vertices->pair();
            $callback($vertex, $outgoing);
        }
    }

    /**
     * Returns the reverse of this graph.
     *
     * Also sometimes known as the 'transpose' or 'converse'.
     *
     * @return DirectedAdjacencyGraph
     */
    public function reverse() {
        $graph = new self();
        $this->eachEdge(function($edge) use (&$graph) {
            $graph->addDirectedEdge($edge[1], $edge[0]);
        });

        return $graph;
    }
}

