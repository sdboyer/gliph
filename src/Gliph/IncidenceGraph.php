<?php

namespace Gliph;

class IncidenceGraph {

    protected $vertices;

    public function __construct() {
        $this->vertices = new \SplObjectStorage();
    }

    public function addVertex($vertex) {
        if (!$this->hasVertex($vertex)) {
            $this->vertices[$vertex] = new \SplObjectStorage();
        }
    }

    public function addEdge($from, $to) {
        $this->addVertex($from);
        $this->addVertex($to);
        $this->vertices[$from]->attach($to);
        $this->vertices[$to]->attach($from);
    }

    public function removeVertex($vertex) {
        foreach ($this->vertices[$vertex] as $adjacent) {
            $this->vertices[$adjacent]->detach($vertex);
        }
        unset($this->vertices[$vertex]);
    }

    public function removeEdge($from, $to) {
        $this->vertices[$from]->detach($to);
        $this->vertices[$to]->detach($from);
    }

    public function eachAdjacent($vertex, $callback) {
        foreach ($this->vertices[$vertex] as $adjacent) {
            call_user_func($callback, $adjacent);
        }
    }

    public function eachVertex($callback) {
        $this->fev(function ($v, $adjacent) use ($callback) {
            call_user_func($callback, $v, $adjacent);
        });
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

    public function hasVertex($vertex) {
        return $this->vertices->contains($vertex);
    }

    protected function fev($callback) {
        foreach ($this->vertices as $vertex) {
            $adjacent = $this->vertices->getInfo();
            $callback($vertex, $adjacent);
        }
    }
}