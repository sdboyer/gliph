<?php

namespace Gliph\Visitor;

/**
 * Basic depth-first visitor.
 *
 * This visitor records reachability data for each vertex and creates a
 * topologically sorted list.
 */
class DepthFirstBasicVisitor implements DepthFirstVisitorInterface {

    /**
     * @var \SplObjectStorage
     */
    public $active;

    /**
     * @var \SplObjectStorage
     */
    public $paths;

    /**
     * @var \SplQueue
     */
    public $tsl;

    public function __construct() {
        $this->active = new \SplObjectStorage();
        $this->paths = new \SplObjectStorage();
        $this->tsl = new \SplQueue();
    }

    public function onBackEdge($vertex, \Closure $visit) {
        throw new \RuntimeException(sprintf('Cycle detected in provided graph.'));
    }

    public function onInitializeVertex($vertex, $source, \SplQueue $queue) {
        $this->paths[$vertex] = new \SplQueue();
    }

    public function onStartVertex($vertex, \Closure $visit) {
        $this->active->attach($vertex);
        if (!isset($this->paths[$vertex])) {
            $this->paths[$vertex] = new \SplQueue();
        }
    }

    public function onExamineEdge($from, $to, \Closure $visit) {
        foreach ($this->active as $vertex) {
           $this->paths[$vertex]->enqueue($to);
        }
    }

    public function onFinishVertex($vertex, \Closure $visit) {
        $this->tsl->push($vertex);
    }
}