<?php

namespace Gliph\Graph;

use Gliph\Exception\InvalidVertexTypeException;
use Gliph\Exception\NonexistentVertexException;

/**
 * A graph, represented as an adjacency list.
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
abstract class AdjacencyList implements MutableGraph {

    protected $vertices;

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
    public function eachAdjacent($vertex) {
        if (!$this->hasVertex($vertex)) {
            throw new NonexistentVertexException('Vertex is not in graph; cannot iterate over its adjacent vertices.');
        }

        foreach ($this->vertices[$vertex] as $adjacent_vertex) {
            yield array($vertex, $adjacent_vertex) => $adjacent_vertex;
        }
    }


    /**
     * {@inheritdoc}
     */
    public function eachVertex() {
        foreach ($this->vertices as $vertex) {
            $adjacent = $this->vertices->getInfo();
            yield $vertex => $adjacent;
        }
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
}