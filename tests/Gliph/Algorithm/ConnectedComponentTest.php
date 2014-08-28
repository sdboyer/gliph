<?php

/**
 * @file
 * Contains \Gliph\Algorithm\ConnectedComponentTest.
 */

namespace Gliph\Algorithm;

use Gliph\Graph\DirectedAdjacencyList;
use Gliph\TestVertex;

/**
 * @coversDefaultClass \Gliph\Algorithm\ConnectedComponent
 */
class ConnectedComponentTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::tarjan_scc()
     */
    public function testTarjanScc() {
        $a = new TestVertex('a');
        $b = new TestVertex('b');
        $c = new TestVertex('c');
        $d = new TestVertex('d');
        $e = new TestVertex('e');
        $f = new TestVertex('f');
        $g = new TestVertex('g');
        $h = new TestVertex('h');

        $graph = new DirectedAdjacencyList();

        $graph->addArc($a, $d);
        $graph->addArc($a, $b);
        $graph->addArc($b, $c);
        $graph->addArc($c, $d);
        $graph->addArc($d, $a);
        $graph->addArc($e, $d);
        $graph->addArc($f, $g);
        $graph->addArc($g, $h);
        $graph->addArc($h, $f);

        $visitor = ConnectedComponent::tarjan_scc($graph);

        $expected_full = array(
            array($c, $b, $d, $a),
            array($e),
            array($h, $g, $f),
        );
        $this->assertEquals($expected_full, $visitor->getComponents());

        $expected_full = array(
            array($c, $b, $d, $a),
            array($h, $g, $f),
        );
        $this->assertEquals($expected_full, $visitor->getConnectedComponents());
    }
}
