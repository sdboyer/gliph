<?php

namespace Gliph\Graph;

use Gliph\TestVertex;

class AdjacencyListBase extends \PHPUnit_Framework_TestCase {

    /**
     * Creates a set of vertices and an empty graph for testing.
     */
    public function setUp() {
        $this->v = array(
            'a' => new TestVertex('a'),
            'b' => new TestVertex('b'),
            'c' => new TestVertex('c'),
            'd' => new TestVertex('d'),
            'e' => new TestVertex('e'),
            'f' => new TestVertex('f'),
            'g' => new TestVertex('g'),
        );
    }

    /**
     * Asserts that an AdjacencyList contains the expected number of vertices.
     *
     * @param int $expectedCount
     * @param AdjacencyList $graph
     * @param string $message
     */
    public function assertVertexCount($expectedCount, AdjacencyList $graph, $message = '') {
        $this->assertAttributeCount($expectedCount, 'vertices', $graph);
    }
}