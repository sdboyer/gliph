<?php

namespace Gliph\Traversal;


use Gliph\Exception\NonexistentVertexException;
use Gliph\Graph\DirectedAdjacencyList;
use Gliph\TestVertex;
use Gliph\Visitor\DepthFirstNoOpVisitor;

class DepthFirstTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var DirectedAdjacencyList
     */
    protected $g;
    protected $v;

    public function setUp() {
        $this->g = new DirectedAdjacencyList();
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

    /**
     * @covers Gliph\Traversal\DepthFirst::traverse
     * @expectedException Gliph\Exception\RuntimeException
     */
    public function testExceptionOnEmptyTraversalQueue() {
        // Create a cycle that ensures there are no source vertices
        $this->g->addDirectedEdge($this->v['d'], $this->v['a']);
        DepthFirst::traverse($this->g, new DepthFirstNoOpVisitor());
    }

    /**
     * @covers Gliph\Traversal\DepthFirst::traverse
     */
    public function testProvideQueueAsStartPoint() {
        $queue = new \SplQueue();
        $queue->push($this->v['a']);
        $queue->push($this->v['e']);

        $this->g->addVertex($this->v['a']);
        $this->g->addVertex($this->v['e']);

        DepthFirst::traverse($this->g, new DepthFirstNoOpVisitor(), $queue);
    }
}
