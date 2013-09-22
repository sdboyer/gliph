<?php

namespace Gliph\Visitor;

use Gliph\Exception\OutOfRangeException;
use Gliph\Exception\RuntimeException;

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
    protected $paths;

    /**
     * @var array
     */
    protected $tsl;

    public function __construct() {
        $this->active = new \SplObjectStorage();
        $this->paths = new \SplObjectStorage();
        $this->tsl = array();
    }

    public function onBackEdge($vertex, \Closure $visit) {
        throw new RuntimeException(sprintf('Cycle detected in provided graph.'));
    }

    public function onInitializeVertex($vertex, $source, \SplQueue $queue) {
        $this->paths[$vertex] = array();
    }

    public function onStartVertex($vertex, \Closure $visit) {
        $this->active->attach($vertex);
        if (!isset($this->paths[$vertex])) {
            $this->paths[$vertex] = array();
        }
    }

    public function onExamineEdge($from, $to, \Closure $visit) {
        foreach ($this->active as $vertex) {
            // TODO this check makes this much less efficient - find a better algo
            if (!in_array($to, $this->paths[$vertex])) {
                $path = $this->paths[$vertex];
                $path[] = $to;
                $this->paths[$vertex] = $path;
            }
        }
    }

    public function onFinishVertex($vertex, \Closure $visit) {
        $this->active->detach($vertex);
        $this->tsl[] = $vertex;
    }

    /**
     * Returns valid topological sort of the visited graph as an array.
     *
     * @return array
     */
    public function getTsl() {
        return $this->tsl;
    }

    /**
     * Returns a queue of all vertices reachable from the given vertex.
     *
     * This should only be called after the visitor has been used in a
     * depth-first traversal.
     *
     * @param object $vertex
     *   A vertex present in the graph for
     *
     * @return array
     *
     * @throws \OutOfRangeException
     */
    public function getReachable($vertex) {
        if (!isset($this->paths[$vertex])) {
            throw new OutOfRangeException('Unknown vertex provided.');
        }

        return $this->paths[$vertex];
    }
}