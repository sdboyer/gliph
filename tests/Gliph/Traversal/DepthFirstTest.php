<?php

namespace Gliph\Traversal;

use Gliph\Graph\DirectedAdjacencyList;
use Gliph\TestVertex;
use Gliph\Visitor\DepthFirstNoOpVisitor;

/**
 * @coversDefaultClass \Gliph\Traversal\DepthFirst
 */
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
        );
        list($a, $b, $c, $d) = array_values($this->v);

        $this->g->addDirectedEdge($a, $b);
        $this->g->addDirectedEdge($b, $c);
        $this->g->addDirectedEdge($a, $c);
        $this->g->addDirectedEdge($b, $d);
    }

    /**
     * @covers \Gliph\Traversal\DepthFirst::find_sources
     */
    public function testFindSources() {
        list($a) = array_values($this->v);

        $visitor = $this->getMock('Gliph\\Visitor\\DepthFirstNoOpVisitor');
        $visitor->expects($this->exactly(4))->method('onInitializeVertex');

        $expected = new \SplQueue();
        $expected->push($a);

        /*
        // TODO this is more proper, but not doable in phpunit without binding tightly to vertex iteration order, which should be arbitrary.
        $visitor->expects($this->once())->method('onInitializeVertex')->with($a, TRUE, $expected);
        $visitor->expects($this->once())->method('onInitializeVertex')->with($b, FALSE, $expected);
        $visitor->expects($this->once())->method('onInitializeVertex')->with($c, FALSE, $expected);
        $visitor->expects($this->once())->method('onInitializeVertex')->with($d, FALSE, $expected);
        */

        $queue = DepthFirst::find_sources($this->g, $visitor);

        $this->assertEquals($expected, $queue);
    }

    /**
     * @covers ::traverse
     */
    public function testBasicAcyclicDepthFirstTraversal() {
        $visitor = $this->getMock('Gliph\\Visitor\\DepthFirstNoOpVisitor');
        $visitor->expects($this->exactly(4))->method('onInitializeVertex');
        $visitor->expects($this->exactly(0))->method('onBackEdge');
        $visitor->expects($this->exactly(4))->method('onStartVertex');
        $visitor->expects($this->exactly(4))->method('onExamineEdge');
        $visitor->expects($this->exactly(4))->method('onFinishVertex');

        DepthFirst::traverse($this->g, $visitor);
    }

    /**
     * @covers ::traverse
     */
    public function testDirectCycleDepthFirstTraversal() {
        list($a, $b, $c, $d) = array_values($this->v);

        $this->g->addDirectedEdge($d, $b);

        $visitor = $this->getMock('Gliph\\Visitor\\DepthFirstNoOpVisitor');
        $visitor->expects($this->exactly(1))->method('onBackEdge');

        DepthFirst::traverse($this->g, $visitor);
    }

    /**
     * @covers ::traverse
     */
    public function testIndirectCycleDepthFirstTraversal() {
        list($a, $b, $c, $d) = array_values($this->v);

        $this->g->addDirectedEdge($d, $a);

        $visitor = $this->getMock('Gliph\\Visitor\\DepthFirstNoOpVisitor');
        $visitor->expects($this->exactly(1))->method('onBackEdge');

        DepthFirst::traverse($this->g, $visitor, $a);
    }

    /**
     * @covers ::traverse
     * @expectedException \Gliph\Exception\RuntimeException
     */
    public function testExceptionOnEmptyTraversalQueue() {
        list($a, $b, $c, $d) = array_values($this->v);

        // Create a cycle that ensures there are no source vertices
        $this->g->addDirectedEdge($d, $a);
        DepthFirst::traverse($this->g, new DepthFirstNoOpVisitor());
    }

    /**
     * @covers ::traverse
     */
    public function testProvideQueueAsStartPoint() {
        list($a, $b, $c, $d, $e) = array_values($this->v);

        $queue = new \SplQueue();
        $queue->push($a);
        $queue->push($e);

        $this->g->addVertex($e);

        $visitor = $this->getMock('Gliph\\Visitor\\DepthFirstNoOpVisitor');
        $visitor->expects($this->exactly(0))->method('onBackEdge');
        $visitor->expects($this->exactly(5))->method('onStartVertex');
        $visitor->expects($this->exactly(4))->method('onExamineEdge');
        $visitor->expects($this->exactly(5))->method('onFinishVertex');

        DepthFirst::traverse($this->g, $visitor, $queue);
    }

    /**
     * @covers ::toposort
     * @expectedException \Gliph\Exception\RuntimeException
     *   Thrown by the visitor after adding a cycle to the graph.
     */
    public function testToposort() {
        list($a, $b, $c, $d) = array_values($this->v);

        $this->assertEquals(array($c, $d, $b, $a), DepthFirst::toposort($this->g, $a));

        $this->g->addDirectedEdge($d, $a);
        DepthFirst::toposort($this->g, $a);
    }
}
