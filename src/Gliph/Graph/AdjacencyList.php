<?php

namespace Gliph\Graph;

use Gliph\Exception\InvalidVertexTypeException;
use Gliph\Exception\NonexistentVertexException;

/**
 * Core logic for an adjacency list-based graph representation.
 *
 * Adjacency lists store vertices directly, and edges relative to the vertices
 * they connect. That means there is no overall list of edges in the graph; only
 * a list of the graph's vertices. In this implementation, that list is keyed by
 * vertex, with the value being a list of all the vertices to which that vertex
 * is adjacent - hence, "adjacency list."
 *
 * Consequently, this structure offers highly efficient access to vertices, but
 * less efficient access to edges.
 *
 * In an undirected graph, the edges are stored in both vertices' adjacency
 * lists. In a directed graph, only the out-edges are stored in each vertex's
 * adjacency list. This makes accessing in-edge information in a directed graph
 * highly inefficient.
 */
trait AdjacencyList /* implements MutableGraph */ {

    /**
     * Contains the adjacency list of vertices.
     *
     * @var \SplObjectStorage
     */
    protected $vertices;

    /**
     * Bookkeeper for nested iteration.
     *
     * @var \SplObjectStorage
     */
    protected $walking;

    /**
     * Count of the number of edges in the graph.
     *
     * We keep track because calculating it on demand is expensive.
     *
     * @var int
     */
    protected $size = 0;

    public function __construct() {
        $this->vertices = new \SplObjectStorage();
        $this->walking = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function addVertex($vertex) {
        if (!is_object($vertex)) {
            throw new InvalidVertexTypeException('Vertices must be objects; non-object provided.');
        }

        if (!$this->hasVertex($vertex)) {
            $this->vertices[$vertex] = new \SplObjectStorage();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eachVertex() {
        $set = $this->getTraversableSplos($this->vertices);
        foreach ($set as $vertex) {
            $adjacent = $set->getInfo();
            yield $vertex => $adjacent;
        }
        $this->walking->detach($set);
    }

    /**
     * {@inheritdoc}
     */
    public function hasVertex($vertex) {
        return $this->vertices->contains($vertex);
    }

    /**
     * {@inheritdoc}
     */
    public function order() {
        return $this->vertices->count();
    }

    /**
     * {@inheritdoc}
     */
    public function size() {
        return $this->size;
    }

    /**
     * Helper function to ensure SPLOS traversal pointer is not overridden.
     *
     * This would otherwise occur if nested calls are made that traverse the
     * same SPLOS. This keeps track of which SPLOSes are currently being
     * traversed, and if it's in use, it returns a clone.
     *
     * It is incumbent on the calling code to release the semaphore directly
     * by calling $this->walking->detach($splos) when the traversal in
     * question is complete. (This is very important!)
     *
     * @param \SplObjectStorage $splos
     *   The SPLOS to traverse.
     *
     * @return \SplObjectStorage
     *   A SPLOS that is safe for traversal; may or may not be a clone of the
     *   original.
     */
    protected function getTraversableSplos(\SplObjectStorage $splos) {
        if ($this->walking->contains($splos)) {
            return clone $splos;
        }
        else {
            $this->walking->attach($splos);
            return $splos;
        }
    }
}