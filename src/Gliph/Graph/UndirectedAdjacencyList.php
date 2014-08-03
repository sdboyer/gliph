<?php

namespace Gliph\Graph;

use Gliph\Exception\NonexistentVertexException;

class UndirectedAdjacencyList extends AdjacencyList implements MutableUndirectedGraph {

    /**
     * {@inheritdoc}
     */
    public function addEdge($from, $to) {
        $this->addVertex($from)->addVertex($to);
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
    public function eachEdge() {
        $complete = new \SplObjectStorage();
        foreach ($this->eachVertex() as $v => $adjacent) {
            $set = $this->getTraversableSplos($adjacent);
            foreach ($set as $a) {
                if (!$complete->contains($a)) {
                    yield array($v, $a);
                }
            }
            $complete->attach($v);
            $this->walking->detach($set);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function inDegree($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in the graph, in-degree information cannot be provided', E_WARNING);
        }

        return $this->vertices[$vertex]->count();
    }

    /**
     * {@inheritdoc}
     */
    public function outDegree($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in the graph, out-degree information cannot be provided', E_WARNING);
        }

        return $this->vertices[$vertex]->count();
    }
}