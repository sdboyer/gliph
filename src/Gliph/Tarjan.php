<?php

namespace Gliph;

use Gliph\Graph\DirectedAdjacencyGraph;
use Gliph\Util\HashMap;

class Tarjan {
    /**
     * @var \SplObjectStorage;
     */
    public $vertexIndices;
    public $vertexLowLimits;

    /**
     * @var \SplQueue
     */
    protected $scc;

    public $stack;
    protected $index;

    /**
     * @var DirectedAdjacencyGraph
     */
    protected $graph;

    protected $storeNonCycles = FALSE;

    public function getCycles(DirectedAdjacencyGraph $graph) {
        $this->index = 0;
        $this->scc = new \SplQueue();
        $this->stack = array();

        $this->graph = $graph;
        if ($graph->getVertexTypes() == DirectedAdjacencyGraph::OBJECT_VERTICES) {
            $this->vertexIndices = new \SplObjectStorage();
            $this->vertexLowLimits = new \SplObjectStorage();
        }
        else {
            $this->vertexIndices = new HashMap();
            $this->vertexLowLimits = new HashMap();
        }

        $that = $this;
        $graph->eachVertex(function($vertex) use (&$that, &$graph) {
            if (!$that->vertexIndices->contains($vertex)) {
                $that->strongconnect($vertex);
            }
        });

        return $this->scc;
    }

    public function strongconnect($vertex) {
        $this->vertexIndices[$vertex] = $this->index;
        $this->vertexLowLimits[$vertex] = $this->index;
        $this->index++;
        $this->stack[] = $vertex;

        $that = $this;
        $this->graph->eachAdjacent($vertex, function($to) use (&$vertex, &$that) {
            if (!$that->vertexIndices->contains($to)) {
                $that->strongconnect($to);
                $ll = min($that->vertexLowLimits[$vertex], $that->vertexLowLimits[$to]);
                $that->vertexLowLimits[$vertex] = $ll;
            }
            // FIXME Tarjan dictates this search should be constant time. ruh roh.
            else if (array_search($to, $that->stack, TRUE) !== FALSE) {
                $min = min($that->vertexLowLimits[$vertex], $that->vertexIndices[$to]);
                $that->vertexLowLimits[$vertex] = $min;
            }
        });

        if ($this->vertexIndices[$vertex] == $this->vertexLowLimits[$vertex]) {
            $component = new \SplQueue();
            do {
                $popped = array_pop($this->stack);
                $component->push($popped);
            } while ($vertex !== $popped);

            if ($component->count() > 1 || $this->storeNonCycles) {
                $this->scc->push($component);
            }
        }
    }
}