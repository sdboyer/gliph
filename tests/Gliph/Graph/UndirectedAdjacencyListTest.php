<?php

namespace Gliph\Graph;

use Gliph\Graph\TestTraits\GraphSpec;
use Gliph\Graph\TestTraits\ObjectVertices;

/**
 * @coversDefaultClass \Gliph\Graph\UndirectedAdjacencyList
 */
class UndirectedAdjacencyListTest extends \PHPUnit_Framework_TestCase {
    use GraphSpec;
    use ObjectVertices;

    /**
     * @var UndirectedAdjacencyList
     */
    protected $g;

    /**
     * Creates a set of vertices and an empty graph for testing.
     */
    public function setUp() {
        $this->getTestVertices();
    }

    /**
     * Returns a new UndirectedAdjacencyList.
     *
     * @return UndirectedAdjacencyList
     */
    protected function g() {
        return new UndirectedAdjacencyList();
    }
}
