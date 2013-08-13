<?php

namespace Gliph\Traversal;

use Gliph\DirectedAdjacencyGraph;
use Gliph\Util\HashMap;

class DepthFirstSearch {

    /**
     * @var \Gliph\DirectedAdjacencyGraph
     */
    protected $graph;

    protected $visiting = array();
    protected $visited = array();
    protected $handlers = array();

    public function __construct(DirectedAdjacencyGraph $graph) {
        $this->graph = $graph;
    }

    public function walk() {
        $graph = $this->graph->transpose();
        $queue = $this->findSources($graph);
        $this->visiting = $this->visited = array();

        while (!$queue->isEmpty()) {
            // TODO just call directly; BFS shouldn't inherit from DFS
            $vertex = $this->nextVertex($queue);
            $this->visit($graph, $vertex);
        }
    }

    protected function findSources(DirectedAdjacencyGraph $graph) {
        $incomings = new HashMap();
        $queue = new \SplDoublyLinkedList();
        $that = $this;

        $graph->eachEdge(function ($edge) use (&$incomings) {
            if (!isset($incomings[$edge[1]])) {
                $incomings[$edge[1]] = array();
            }

            $in = &$incomings->get($edge[1]);
            $in[] = $edge[0];
        });

        // Prime the queue with vertices that have no incoming edges.
        $graph->eachVertex(function($vertex) use (&$queue, &$incomings, &$that) {
            if (empty($incomings[$vertex])) {
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
        if (array_search($vertex, $this->visiting) !== FALSE) {
            // Indicates a cycle in the graph
            $this->emit('onBackEdge', $vertex);
        }
        else if (array_search($vertex, $this->visited) === FALSE) {
            $this->emit('onStartVertex', $vertex);

            $this->visiting[] = $vertex;
            $that = $this;
            $graph->eachAdjacent($vertex, function($to) use (&$that, &$graph, &$vertex) {
                $that->emit('onExamineEdge', $vertex, $to);
                $that->visit($graph, $to);
            });

            $this->emit('onFinishVertex', $vertex);

            $k = array_search($vertex, $this->visiting);
            unset($this->visiting[$k]);

            $this->visited[] = $vertex;
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