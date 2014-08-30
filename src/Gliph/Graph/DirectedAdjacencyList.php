<?php

namespace Gliph\Graph;

use Gliph\Algorithm\ConnectedComponent;
use Gliph\Exception\NonexistentVertexException;
use Gliph\Exception\RuntimeException;
use Gliph\Traversal\DepthFirst;
use Gliph\Visitor\DepthFirstToposortVisitor;

class DirectedAdjacencyList implements MutableDigraph {
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

        foreach ($this->vertices() as $v2 => $adjacent) {
            if ($adjacent->contains($vertex)) {
                yield $v2;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function successorsOf($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in graph; cannot iterate over its successor vertices.');
        }

        $set = $this->getTraversableSplos($this->vertices[$vertex]);
        foreach ($set as $successor) {
            yield $successor;
        }
        $this->walking->detach($set);
    }

    /**
     * {@inheritdoc}
     */
    public function predecessorsOf($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in graph; cannot iterate over its predecessor vertices.');
        }

        foreach ($this->vertices() as $v2 => $adjacent) {
            if ($adjacent->contains($vertex)) {
                yield $v2;
            }
        }
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

        foreach ($this->vertices() as $v2 => $adjacent) {
            if ($adjacent->contains($vertex)) {
                yield array($v2, $vertex);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function ensureArc($tail, $head) {
        $this->ensureVertex($tail)->ensureVertex($head);
        if (!$this->vertices[$tail]->contains($head)) {
            $this->size++;
        }

        $this->vertices[$tail]->attach($head);
    }

    /**
     * {@inheritdoc}
     */
    public function removeVertex($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in the graph, it cannot be removed.', E_WARNING);
        }

        foreach ($this->vertices() as $v => $outgoing) {
            $outgoing->detach($vertex);
        }
        unset($this->vertices[$vertex]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeArc($tail, $head) {
        $this->vertices[$tail]->detach($head);
    }

    /**
     * {@inheritdoc}
     */
    public function edges() {
        foreach ($this->vertices() as $tail => $outgoing) {
            $set = $this->getTraversableSplos($outgoing);
            foreach ($set as $head) {
                yield array($tail, $head);
            }
            $this->walking->detach($set);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function transpose() {
        $graph = new self();
        foreach ($this->edges() as $edge) {
            $graph->ensureArc($edge[1], $edge[0]);
        }

        return $graph;
    }

    /**
     * {@inheritdoc}
     */
    public function isAcyclic() {
        // The DepthFirstToposortVisitor throws an exception on cycles.
        try {
            DepthFirst::traverse($this, new DepthFirstToposortVisitor());
            return TRUE;
        }
        catch (RuntimeException $e) {
            return FALSE;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCycles() {
        $scc = ConnectedComponent::tarjan_scc($this);
        return $scc->getConnectedComponents();
    }

    /**
     * {@inheritdoc}
     */
    public function inDegreeOf($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in the graph, in-degree information cannot be provided', E_WARNING);
        }

        $count = 0;
        foreach ($this->vertices() as $adjacent) {
            if ($adjacent->contains($vertex)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function outDegreeOf($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in the graph, out-degree information cannot be provided', E_WARNING);
        }

        return $this->vertices[$vertex]->count();
    }

    /**
     * {@inheritdoc}
     */
    public function degreeOf($vertex) {
        return $this->inDegreeOf($vertex) + $this->outDegreeOf($vertex);
    }
}
