<?php


namespace Gliph\Traversal;

use Gliph\DirectedAdjacencyGraph;
use Gliph\Util\HashMap;

class BFSTopSort implements \IteratorAggregate {
    public $graph;

    public function __construct(DirectedAdjacencyGraph $graph) {
        $this->graph = $graph;
    }

    public function getIterator() {
        $graph = $this->graph->reverse();
        $incomings = new HashMap();
        $queue = new \SplDoublyLinkedList();
        $tsl = new \SplQueue();

        $graph->eachEdge(function ($edge) use (&$incomings) {
            if (!isset($incomings[$edge[1]])) {
                $incomings[$edge[1]] = array();
            }

            $in = &$incomings->get($edge[1]);
            $in[] = $edge[0];
        });

        // Prime the queue with vertices that have no incoming edges.
        $graph->eachVertex(function($vertex) use (&$queue, &$incomings) {
            if (empty($incomings[$vertex])) {
                print "{$vertex->val}, ";
                $queue->push($vertex);
            }
        });

        while (!$queue->isEmpty()) {
            $vertex = $this->queueProcessor($queue);
            $tsl->push($vertex);

            $graph->eachAdjacent($vertex, function($to) use (&$vertex, &$incomings, &$queue) {
                $ins = &$incomings->get($to);
                $key = array_search($vertex, $ins);
                unset($ins[$key]);

                if (count($ins) === 0) {
                    print "{$to->val}, ";
                    $queue->push($to);
                }
            });
        }

        return $tsl;
    }

    protected function queueProcessor(\SplDoublyLinkedList $queue) {
        return $queue->shift();
    }

    protected function visit($v) {
        if (array_search($v, $this->visiting) !== FALSE) {
            throw new \RuntimeException('Cycle detected - provided graph is not acyclic, topsort is not possible.', E_RECOVERABLE_ERROR);
        }

        if (array_search($v, $this->visited) === FALSE) {
            $this->visiting[] = $v;
            $waiting = $this->waiting;
            $this->graph->eachAdjacent($v, function($t) use ($waiting) {
                $waiting->push($t);
            });

            $k = array_search($v, $this->visiting);
            unset($this->visiting[$k]);

            $this->visited[] = $v;
        }
    }

}
