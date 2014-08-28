<?php

namespace Gliph\Visitor;

use Gliph\Graph\DirectedAdjacencyList;
use Gliph\TestVertex;
use Gliph\Traversal\DepthFirst;

/**
 * @coversDefaultClass \Gliph\Visitor\DepthFirstBasicVisitor
 *
 * TODO these are all integration tests; refactor to unit tests, or at least something closer
 */
class DepthFirstBasicVisitorTest extends SimpleStatefulDepthFirstVisitorTestBase {

    /**
     * A map of pre-made test vertices.
     *
     * @var array
     */
    protected $v;

    /**
     * @var DepthFirstBasicVisitor
     */
    protected $vis;

    /**
     * @var DirectedAdjacencyList
     */
    protected $g;

    public function setUp() {
        $this->v = array(
            'a' => new TestVertex('a'),
            'b' => new TestVertex('b'),
            'c' => new TestVertex('c'),
            'd' => new TestVertex('d'),
            'e' => new TestVertex('e'),
            'f' => new TestVertex('f'),
        );

        $this->g = new DirectedAdjacencyList();
        $this->vis = new DepthFirstBasicVisitor();

        list($a, $b, $c, $d) = array_values($this->v);
        $this->g->addArc($a, $b);
        $this->g->addArc($b, $c);
        $this->g->addArc($a, $c);
        $this->g->addArc($b, $d);
    }

    /**
     * Creates a DepthFirstToposortVisitor in IN_PROGRESS state.
     *
     * @return DepthFirstBasicVisitor
     */
    public function createInProgressVisitor() {
        return new DepthFirstBasicVisitor();
    }

    /**
     * Creates a DepthFirstToposortVisitor in COMPLETED state.
     *
     * @return DepthFirstBasicVisitor
     */
    public function createCompletedVisitor() {
        $stub = new DepthFirstBasicVisitor();

        $prop = new \ReflectionProperty($stub, 'state');
        $prop->setAccessible(TRUE);
        $prop->setValue($stub, StatefulVisitorInterface::COMPLETE);

        return $stub;
    }

    public function completionRequiredMethods() {
        return array(
            array('getReachable', array(new \stdClass())),
        );
    }

    /**
     * @covers ::__construct
     * @covers ::onInitializeVertex
     * @covers ::beginTraversal
     * @covers ::onStartVertex
     * @covers ::onExamineEdge
     * @covers ::onFinishVertex
     * @covers ::endTraversal
     * @covers ::getReachable
     */
    public function testTraversalWithStartPoint() {
        list($a, $b, $c, $d) = array_values($this->v);

        DepthFirst::traverse($this->g, $this->vis);
        $this->assertCount(3, $this->vis->getReachable($a));
        $this->assertCount(2, $this->vis->getReachable($b));
        $this->assertCount(0, $this->vis->getReachable($c));
        $this->assertCount(0, $this->vis->getReachable($d));
    }

    /**
     * @covers ::getReachable
     */
    public function testReachable() {
        list($a, $b, $c, $d) = array_values($this->v);

        DepthFirst::traverse($this->g, $this->vis);
        $this->assertSame(array($b, $c, $d), $this->vis->getReachable($a));
        $this->assertSame(array($c, $d), $this->vis->getReachable($b));
        $this->assertSame(array(), $this->vis->getReachable($c));
        $this->assertSame(array(), $this->vis->getReachable($d));
    }

    /**
     * @depends testReachable
     * @covers ::getReachable
     */
    public function testReachableOnUnknownVertex() {
        list($a, $b, $c, $d, $e) = array_values($this->v);
        DepthFirst::traverse($this->g, $this->vis, $a);
        $this->assertFalse($this->vis->getReachable($e));
    }
}
