<?php

namespace Gliph\Traversal;

use Gliph\DirectedAdjacencyGraph;
use Gliph\Util\HashMap;

class DepthFirstSearch {

    /**
     * @var \Gliph\DirectedAdjacencyGraph
     */
    protected $graph;

    protected $visiting;
    protected $visited;
    protected $handlers = array();

    public function __construct(DirectedAdjacencyGraph $graph) {
        $this->graph = $graph;
    }

    public function walk() {
        $graph = $this->graph->transpose();
        $queue = $this->findSources($graph);
        $this->visiting = new \SplObjectStorage();
        $this->visited = new \SplObjectStorage();

        while (!$queue->isEmpty()) {
            // TODO just call directly; BFS shouldn't inherit from DFS
            $vertex = $this->nextVertex($queue);
            $this->visit($graph, $vertex);
        }
    }

    protected function findSources(DirectedAdjacencyGraph $graph) {
        $incomings = new \SplObjectStorage();
        $queue = new \SplDoublyLinkedList();
        $that = $this;

        $graph->eachEdge(function ($edge) use (&$incomings) {
            if (!isset($incomings[$edge[1]])) {
                $incomings[$edge[1]] = new \SplObjectStorage();
            }
            $incomings[$edge[1]]->attach($edge[0]);
        });

        // Prime the queue with vertices that have no incoming edges.
        $graph->eachVertex(function($vertex) use (&$queue, &$incomings, &$that) {
            if (!$incomings->contains($vertex)) {
                $queue->push($vertex);
                // TRUE second param indicates source vertex
                $that->emit('onInitializeVertex', $vertex, TRUE);
            }
            else {
                $that->emit('onInitializeVertex', $vertex, FALSE);
            }
        });

        return $queue;
    }

    public function visit($graph, $vertex) {
        if ($this->visiting->contains($vertex)) {
            // Indicates a cycle in the graph
            $this->emit('onBackEdge', $vertex);
        }
        else if (!$this->visited->contains($vertex)) {
            $this->emit('onStartVertex', $vertex);

            $this->visiting->attach($vertex);
            $that = $this;
            $graph->eachAdjacent($vertex, function($to) use (&$that, &$graph, &$vertex) {
                $that->emit('onExamineEdge', $vertex, $to);
                $that->visit($graph, $to);
            });

            $this->emit('onFinishVertex', $vertex);

            $this->visiting->detach($vertex);
            $this->visited->attach($vertex);
        }
    }

    public function search($source, $target = NULL) {
        if (!$this->graph->hasVertex($source)) {
            throw new \Exception('Origin vertex does not exist in graph.');
        }
        if (!$this->graph->hasVertex($target)) {
            throw new \Exception('Target vertex does not exist in graph.');
        }


    }

    protected function nextVertex(\SplDoublyLinkedList $queue) {
        return $queue->shift();
    }

    public function setHandler($event, $callback) {
        if (is_callable($callback)) {
            $this->handlers[$event] = $callback;
        }
        else {
            throw new \InvalidArgumentException(sprintf('Callback provided for %s event was not callable.', $event));
        }
    }

    public function setHandlers($callbacks) {
        foreach ($callbacks as $event => $callback) {
            $this->setHandler($event, $callback);
        }
    }

    public function emit($event) {
        if (!isset($this->handlers[$event])) {
            return;
        }

        $args = func_get_args();
        array_shift($args);
        call_user_func_array($this->handlers[$event], $args);
    }
}