<?php

namespace Gliph\Graph;

use Gliph\Exception\NonexistentVertexException;

class UndirectedAdjacencyList implements MutableGraph {
    use AdjacencyList;

    /**
     * {@inheritdoc}
     */
    public function adjacentTo($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in graph; cannot iterate over its adjacent vertices.');
        }

        $set = $this->getTraversableSplos($this->vertices[$vertex]);
        foreach ($set as $adjacent_vertex) {
            yield $adjacent_vertex;
        }
        $this->walking->detach($set);
    }

    /**
     * {@inheritdoc}
     */
    public function incidentTo($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in graph; cannot iterate over its incident edges.');
        }

        $set = $this->getTraversableSplos($this->vertices[$vertex]);
        foreach ($set as $adjacent_vertex) {
            yield array($vertex, $adjacent_vertex);
        }
        $this->walking->detach($set);
    }

    /**
     * {@inheritdoc}
     */
    public function ensureEdge($from, $to) {
        $this->ensureVertex($from)->ensureVertex($to);
        if (!$this->vertices[$from]->contains($to)) {
            $this->size++;
        }

        $this->vertices[$from]->attach($to);
        $this->vertices[$to]->attach($from);
    }

    /**
     * {@inheritdoc}
     */
    public function removeVertex($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in the graph, it cannot be removed.', E_WARNING);
        }

        foreach ($this->vertices[$vertex] as $adjacent) {
            $this->vertices[$adjacent]->detach($vertex);
        }
        unset($this->vertices[$vertex]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeEdge($from, $to) {
        $this->vertices[$from]->detach($to);
        $this->vertices[$to]->detach($from);
    }

    /**
     * {@inheritdoc}
     */
    public function edges() {
        $complete = new \SplObjectStorage();

        $oset = $this->getTraversableSplos($this->vertices);
        foreach ($oset as $v) {
            $set = $this->getTraversableSplos($this->vertices[$v]);
            foreach ($set as $a) {
                if (!$complete->contains($a)) {
                    yield array($v, $a);
                }
            }
            $complete->attach($v);
            $this->walking->detach($set);
        }
        $this->walking->detach($oset);
    }

    /**
     * {@inheritdoc}
     */
    public function degreeOf($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in the graph, in-degree information cannot be provided', E_WARNING);
        }

        return $this->vertices[$vertex]->count();
    }
}