<?php

namespace Gliph\Graph;

use Gliph\Algorithm\ConnectedComponent;
use Gliph\Exception\NonexistentVertexException;
use Gliph\Exception\RuntimeException;
use Gliph\Traversal\DepthFirst;
use Gliph\Visitor\DepthFirstToposortVisitor;

class DirectedAdjacencyList extends AdjacencyList implements MutableDirectedGraph {

    /**
     * {@inheritdoc}
     */
    public function addDirectedEdge($tail, $head) {
        if (!$this->hasVertex($tail)) {
            $this->addVertex(($tail));
        }

        if (!$this->hasVertex($head)) {
            $this->addVertex($head);
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

        foreach ($this->eachVertex() as $v => $outgoing) {
            if ($outgoing->contains($vertex)) {
                $outgoing->detach($vertex);
            }
        }
        unset($this->vertices[$vertex]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeEdge($tail, $head) {
        $this->vertices[$tail]->detach($head);
    }

    /**
     * {@inheritdoc}
     */
    public function eachEdge() {
        foreach ($this->eachVertex() as $tail => $outgoing) {
            foreach ($outgoing as $head) {
                yield array($tail, $head);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function transpose() {
        $graph = new self();
        foreach ($this->eachEdge() as $edge) {
            $graph->addDirectedEdge($edge[1], $edge[0]);
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
}

