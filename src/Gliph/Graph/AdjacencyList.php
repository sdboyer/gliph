<?php

namespace Gliph\Graph;

use Gliph\Exception\InvalidVertexTypeException;

abstract class AdjacencyList {

    protected $vertices;

    public function __construct() {
        $this->vertices = new \SplObjectStorage();
    }

    public function addVertex($vertex) {
        if (!is_object($vertex)) {
            throw new InvalidVertexTypeException('Vertices must be objects; non-object provided.');
        }

        if (!$this->hasVertex($vertex)) {
            $this->vertices[$vertex] = new \SplObjectStorage();
        }
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

    public function hasVertex($vertex) {
        return $this->vertices->contains($vertex);
    }

    protected function fev($callback) {
        foreach ($this->vertices as $vertex) {
            $outgoing = $this->vertices->getInfo();
            $callback($vertex, $outgoing);
        }
    }
}