<?php

namespace Gliph\Visitor;

use Gliph\Graph\DirectedAdjacencyList;
use Gliph\TestVertex;
use Gliph\Traversal\DepthFirst;

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

        $this->g->addDirectedEdge($this->v['a'], $this->v['b']);
        $this->g->addDirectedEdge($this->v['b'], $this->v['c']);
        $this->g->addDirectedEdge($this->v['a'], $this->v['c']);
        $this->g->addDirectedEdge($this->v['b'], $this->v['d']);
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
     * @covers Gliph\Visitor\DepthFirstBasicVisitor::__construct
     * @covers Gliph\Visitor\DepthFirstBasicVisitor::onInitializeVertex
     * @covers Gliph\Visitor\DepthFirstBasicVisitor::beginTraversal
     * @covers Gliph\Visitor\DepthFirstBasicVisitor::onStartVertex
     * @covers Gliph\Visitor\DepthFirstBasicVisitor::onExamineEdge
     * @covers Gliph\Visitor\DepthFirstBasicVisitor::onFinishVertex
     * @covers Gliph\Visitor\DepthFirstBasicVisitor::endTraversal
     * @covers Gliph\Visitor\DepthFirstBasicVisitor::getReachable
     * @covers Gliph\Visitor\DepthFirstBasicVisitor::getTsl
     */
    public function testTraversalWithStartPoint() {
        DepthFirst::traverse($this->g, $this->vis, $this->v['a']);
        $this->assertCount(3, $this->vis->getReachable($this->v['a']));
        $this->assertCount(2, $this->vis->getReachable($this->v['b']));
        $this->assertCount(0, $this->vis->getReachable($this->v['c']));
        $this->assertCount(0, $this->vis->getReachable($this->v['d']));

        // Not the greatest test since we're implicitly locking in to one of
        // two valid TSL solutions - but that's linked to the determinism in
        // the ordering of how the graph class stores vertices, which is a
        // much bigger problem than can be solved right here. So, good enough.
        $this->assertEquals(array($this->v['c'], $this->v['d'], $this->v['b'], $this->v['a']), $this->vis->getTsl());
    }

    /**
     * @expectedException Gliph\Exception\RuntimeException
     * @covers Gliph\Visitor\DepthFirstBasicVisitor::onBackEdge
     * @covers Gliph\Visitor\DepthFirstBasicVisitor::onInitializeVertex
     */
    public function testErrorOnCycle() {
        $this->g->addDirectedEdge($this->v['d'], $this->v['b']);
        DepthFirst::traverse($this->g, $this->vis);
    }

    public function testReachableExceptionOnUnknownVertex() {
        DepthFirst::traverse($this->g, $this->vis, $this->v['a']);
        $this->assertFalse($this->vis->getReachable($this->v['e']));
    }
}
