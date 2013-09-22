<?php

/**
 * @file
 * Contains \Gliph\Visitor\DepthFirstToposortVisitor.
 */

namespace Gliph\Visitor;

use Gliph\Exception\RuntimeException;

/**
 * Visitor that produces a topologically sorted list on a depth first traversal.
 */
class DepthFirstToposortVisitor implements DepthFirstVisitorInterface {

    /**
     * @var array
     */
    protected $tsl = array();

    public function onBackEdge($vertex, \Closure $visit) {
        throw new RuntimeException(sprintf('Cycle detected in provided graph; toposort is not possible.'));
    }

    public function onInitializeVertex($vertex, $source, \SplQueue $queue) {}

    public function onStartVertex($vertex, \Closure $visit) {}

    public function onExamineEdge($from, $to, \Closure $visit) {}

    public function onFinishVertex($vertex, \Closure $visit) {
        $this->tsl[] = $vertex;
    }

    /**
     * Returns a valid topological sort of the visited graph as an array.
     *
     * @return array
     */
    public function getTsl() {
        return $this->tsl;
    }
}