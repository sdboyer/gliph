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

        $graph->ensureArc($a, $d);
        $graph->ensureArc($a, $b);
        $graph->ensureArc($b, $c);
        $graph->ensureArc($c, $d);
        $graph->ensureArc($d, $a);
        $graph->ensureArc($e, $d);
        $graph->ensureArc($f, $g);
        $graph->ensureArc($g, $h);
        $graph->ensureArc($h, $f);

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
