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
     * Returns a new UndirectedAdjacencyList.
     *
     * @return UndirectedAdjacencyList
     */
    protected function g() {
        return new UndirectedAdjacencyList();
    }
}
