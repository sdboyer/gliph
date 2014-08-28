<?php

namespace Gliph\Graph;

use Gliph\Graph\TestTraits\GraphSpec;
use Gliph\Graph\TestTraits\DirectedGraphSpec;
use Gliph\Graph\TestTraits\ObjectVertices;

/**
 * @coversDefaultClass \Gliph\Graph\DirectedAdjacencyList
 */
class DirectedAdjacencyListTest extends \PHPUnit_Framework_TestCase {
    use GraphSpec;
    use DirectedGraphSpec;
    use ObjectVertices;

    /**
     * Returns a new DirectedAdjacencyList.
     *
     * @return DirectedAdjacencyList
     */
    protected function g() {
        return new DirectedAdjacencyList();
    }
}
