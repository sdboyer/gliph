<?php

namespace Gliph;

use Gliph\Visitor\DepthFirstVisitorInterface;

class Gliph {

    /**
     * Perform a depth-first traversal on the provided graph.
     *
     * @param DirectedAdjacencyGraph $graph
     *   The graph on which to perform the depth-first search.
     * @param DepthFirstVisitorInterface $visitor
     *   The visitor object to use during the traversal.
     * @param \SplDoublyLinkedList $queue
     *   A queue of vertices to ensure are visited. The traversal will deque
     *   them in order and visit them.
     */
    public static function depth_first_traverse(DirectedAdjacencyGraph $graph, DepthFirstVisitorInterface $visitor, \SplDoublyLinkedList $queue = NULL) {
        if ($queue === NULL) {
            self::find_sources($graph, $visitor);
        }

        $visiting = new \SplObjectStorage();
        $visited = new \SplObjectStorage();

        $visit = function($vertex) use ($graph, $visitor, &$visit, $visiting, $visited) {
            if ($visiting->contains($vertex)) {
                $visitor->onBackEdge($vertex, $visit);
            }
            else if ($visited->contains($vertex)) {
                $visiting->attach($vertex);

                $visitor->onStartVertex($vertex, $visit);

                $graph->eachAdjacent($vertex, function($to) use ($vertex, &$visit, $visitor) {
                    $visitor->onExamineEdge($vertex, $to, $visit);
                });

                $visitor->onFinishVertex($vertex, $visit);

                $visiting->detach($vertex);
                $visited->attach($vertex);
            }
        };

        while (!$queue->isEmpty()) {
            $vertex = $queue->shift();
            $visit($vertex);
        }
    }

    /**
     * Finds source vertices in a DirectedAdjacencyGraph, then enqueues them.
     *
     * @param DirectedAdjacencyGraph $graph
     * @param DepthFirstVisitorInterface $visitor
     *
     * @return \SplQueue
     */
    public static function find_sources(DirectedAdjacencyGraph $graph, DepthFirstVisitorInterface $visitor) {
        $incomings = new \SplObjectStorage();
        $queue = new \SplQueue();

        $graph->eachEdge(function ($edge) use (&$incomings) {
            if (!isset($incomings[$edge[1]])) {
                $incomings[$edge[1]] = new \SplObjectStorage();
            }
            $incomings[$edge[1]]->attach($edge[0]);
        });

        // Prime the queue with vertices that have no incoming edges.
        $graph->eachVertex(function($vertex) use ($queue, $incomings, $visitor) {
            if (!$incomings->contains($vertex)) {
                $queue->push($vertex);
                // TRUE second param indicates source vertex
                $visitor->onInitializeVertex($vertex, TRUE, $queue);
            }
            else {
                $visitor->onInitializeVertex($vertex, FALSE, $queue);
            }
        });

        return $queue;
    }
}