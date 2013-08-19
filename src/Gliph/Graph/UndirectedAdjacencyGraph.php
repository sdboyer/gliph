<?php

namespace Gliph\Graph;

class UndirectedAdjacencyGraph extends AdjacencyGraph {

    public function addEdge($from, $to) {
        $this->addVertex($from);
        $this->addVertex($to);
        $this->vertices[$from]->attach($to);
        $this->vertices[$to]->attach($from);
    }

    public function removeVertex($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new \OutOfRangeException('Vertex is not in the graph, it cannot be removed.', E_WARNING);
        }

        foreach ($this->vertices[$vertex] as $adjacent) {
            $this->vertices[$adjacent]->detach($vertex);
        }
        unset($this->vertices[$vertex]);
    }

    public function removeEdge($from, $to) {
        $this->vertices[$from]->detach($to);
        $this->vertices[$to]->detach($from);
    }

    public function eachEdge($callback) {
        $edges = array();
        $complete = new \SplObjectStorage();
        $this->fev(function ($a, $adjacent) use (&$edges, &$complete) {
            foreach ($adjacent as $b) {
                if (!$complete->contains($b)) {
                    $edges[] = array($a, $b);
                }
            }
            $complete->attach($a);
        });

        foreach ($edges as $edge) {
            call_user_func($callback, $edge);
        }
    }
}