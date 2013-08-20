<?php

namespace Gliph\Traversal;


use Gliph\Graph\DirectedAdjacencyGraph;
use Gliph\TestVertex;

class DepthFirstTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var DirectedAdjacencyGraph
     */
    protected $g;
    protected $v;

    public function setUp() {
        $this->g = new DirectedAdjacencyGraph();
        $this->v = array(
            'a' => new TestVertex('a'),
            'b' => new TestVertex('b'),
            'c' => new TestVertex('c'),
            'd' => new TestVertex('d'),
            'e' => new TestVertex('e'),
            'f' => new TestVertex('f'),
            'g' => new TestVertex('g'),
        );

        $this->g->addDirectedEdge($this->v['a'], $this->v['b']);
        $this->g->addDirectedEdge($this->v['b'], $this->v['c']);
        $this->g->addDirectedEdge($this->v['a'], $this->v['c']);
        $this->g->addDirectedEdge($this->v['b'], $this->v['d']);
    }

    public function testBasicAcyclicDepthFirstTraversal() {
        $visitor = $this->getMock('Gliph\\Visitor\\DepthFirstNoOpVisitor');
        $visitor->expects($this->exactly(4))->method('onInitializeVertex');
        $visitor->expects($this->exactly(0))->method('onBackEdge');
        $visitor->expects($this->exactly(4))->method('onStartVertex');
        $visitor->expects($this->exactly(4))->method('onExamineEdge');
        $visitor->expects($this->exactly(4))->method('onFinishVertex');

        DepthFirst::traverse($this->g, $visitor);
    }

    public function testDirectCycleDepthFirstTraversal() {
        $this->g->addDirectedEdge($this->v['d'], $this->v['b']);

        $visitor = $this->getMock('Gliph\\Visitor\\DepthFirstNoOpVisitor');
        $visitor->expects($this->exactly(1))->method('onBackEdge');

        DepthFirst::traverse($this->g, $visitor);
    }

    public function testIndirectCycleDepthFirstTraversal() {
        $this->g->addDirectedEdge($this->v['d'], $this->v['a']);

        $visitor = $this->getMock('Gliph\\Visitor\\DepthFirstNoOpVisitor');
        $visitor->expects($this->exactly(1))->method('onBackEdge');

        DepthFirst::traverse($this->g, $visitor, $this->v['a']);
    }
}
