<?php

namespace Gliph\Algorithm;

use Gliph\Graph\DirectedGraph;
use Gliph\Visitor\TarjanSCCVisitor;

/**
 * Contains algorithms for discovering connected components.
 */
class ConnectedComponent {

    /**
     * Finds connected components in the provided directed graph.
     *
     * @param DirectedGraph $graph
     *   The DirectedGraph to search for connected components.
     * @param TarjanSCCVisitor $visitor
     *   The visitor that will collect and store the connected components. One
     *   will be created if not provided.
     *
     * @return TarjanSCCVisitor
     *   The finalized visitor.
     */
    public static function tarjan_scc(DirectedGraph $graph, TarjanSCCVisitor $visitor = NULL) {
        $visitor = $visitor ?: new TarjanSCCVisitor();
        $counter = 0;
        $stack = array();
        $indices = new \SplObjectStorage();
        $lowlimits = new \SplObjectStorage();

        $visit = function($vertex) use (&$visit, &$counter, $graph, &$stack, $indices, $lowlimits, $visitor) {
            $indices->attach($vertex, $counter);
            $lowlimits->attach($vertex, $counter);
            $stack[] = $vertex;
            $counter++;

            foreach ($graph->eachAdjacent($vertex) as $edge => $head) {
                if (!$indices->contains($head)) {
                    $visit($head);
                    $lowlimits[$vertex] = min($lowlimits[$vertex], $lowlimits[$head]);
                }
                else if (in_array($head, $stack)) {
                    $lowlimits[$vertex] = min($lowlimits[$vertex], $indices[$head]);
                }
            }

            if ($lowlimits[$vertex] === $indices[$vertex]) {
                $visitor->newComponent();
                do {
                    $other = array_pop($stack);
                    $visitor->addToCurrentComponent($other);
                } while ($other != $vertex);
            }
        };

        foreach ($graph->eachVertex() as $v => $outgoing) {
            if (!$indices->contains($v)) {
                $visit($v);
            }
        }

        return $visitor;
    }
}