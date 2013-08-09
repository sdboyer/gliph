<?php


namespace Gliph\Traversal;

use Gliph\Graph;
use Gliph\Util\HashMap;

class TopologicalSort implements \IteratorAggregate {
    public $graph;

    public $startVertex;

    /**
     * @var \SplStack
     */
    public $waiting;

    /**
     * @var \SplQueue
     */
    public $solution;

    public $currentVertex;

    /**
     * @var HashMap
     */
    public $inDegrees;

    public $visiting;

    public $visited;

    public function __construct(Graph $graph, $start_vertex) {
        $this->graph = $graph;
        $this->startVertex = $start_vertex;
    }

    public function rewind() {

    }

    public function next() {
        $graph = $this->graph;
        $that = $this;

        $v = $this->currentVertex = $this->waiting->pop();
        $graph->eachAdjacent($v, function ($u) use ($that, $graph, $v) {
            $vc = &$that->inDegrees->get($u);
            if (!--$vc) {
                $that->waiting->push($u);
            }
        });
        return $v;
    }

    public function key() {
        return $this->waiting->key();
    }

    public function current() {
        return $this->currentVertex;
    }

    public function valid() {
        return $this->waiting->count() > 0;
    }

    public function getIterator() {
        $graph = $this->graph;
        $that = $this;
        $this->inDegrees = new HashMap();
        $this->visited = $this->visiting = array();
        $this->waiting = new \SplStack();
        $this->solution = new \SplQueue();

        $graph->eachVertex(function ($v) use ($that, $graph) {
            if (!isset($that->inDegrees[$v])) {
                $that->inDegrees[$v] = 0;
            }
            $graph->eachAdjacent($v, function ($e) use ($that, $v) {
                $vc = &$that->inDegrees->get($v);
                $vc++;
            });
        });

        for ($this->inDegrees->rewind(); $this->inDegrees->valid(); $this->inDegrees->next()) {
            list($v, $count) = $this->inDegrees->pair();
            if (!empty($count)) {
                $this->waiting->push($v);
            }
        }

        while (!$this->waiting->isEmpty()) {
            $v = $this->waiting->pop();
            $this->visit($v);
        }

        return $this->solution;
    }

    public function visit($v) {
        if ($visiting_key = array_search($v, $this->visiting) !== FALSE) {
            throw new \RuntimeException('Cycle detected - provided graph is not acyclic, topsort is not possible.', E_RECOVERABLE_ERROR);
        }

        if (array_search($v, $this->visited) === FALSE) {
            $this->visiting[] = $v;
            $this->graph->eachAdjacent($v, array($this, 'visit'));

            $k = array_search($v, $this->visiting);
            unset($this->visiting[$k]);

            $this->visited[] = $v;
            $this->solution->enqueue($v);
        }
    }

}