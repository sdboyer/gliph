<?php

namespace Gliph\Graph;

use Gliph\Graph\TestTraits\GraphSpec;
use Gliph\Graph\TestTraits\ObjectVertices;

/**
 * @coversDefaultClass \Gliph\Graph\DirectedAdjacencyList
 */
class DirectedAdjacencyListTest extends \PHPUnit_Framework_TestCase {
    use GraphSpec;
    use ObjectVertices;

    /**
     * @var DirectedAdjacencyList
     */
    protected $g;

    public function setUp() {
        $this->getTestVertices();
        $this->g = new DirectedAdjacencyList();
    }

    protected function ensureEdge(MutableDigraph $g, $tail, $head) {
        $g->addDirectedEdge($tail, $head);
    }

    /**
     * Implicitly depends on AdjacencyList::addVertex.
     *
     * @covers ::addDirectedEdge
     */
    public function testAddDirectedEdge() {
        list($a, $b) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);

        $this->assertAttributeContains($a, 'vertices', $this->g);
        $this->assertAttributeContains($b, 'vertices', $this->g);
        $this->assertEquals(2, $this->g->order());
    }

    /**
     * @depends testAddDirectedEdge
     * @covers ::eachAdjacentTo
     */
    public function testEachAdjacentTo() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);
        $this->g->addDirectedEdge($a, $c);

        $found = array();
        foreach ($this->g->eachAdjacentTo($a) as $head) {
            $found[] = $head;
        }
        $this->assertEquals(array($b, $c), $found);

        $found = array();
        foreach ($this->g->eachAdjacentTo($b) as $head) {
            $found[] = $head;
        }
        $this->assertEmpty($found);

        foreach ($this->g->eachAdjacentTo($c) as $head) {
            $found[] = $head;
        }
        $this->assertEmpty($found);

        // nested
        $found = array();
        foreach ($this->g->eachAdjacentTo($a) as $head) {
            $found[] = $head;
            foreach ($this->g->eachAdjacentTo($a) as $head) {
                $found[] = $head;
            }
            foreach ($this->g->eachAdjacentTo($a) as $head) {
                $found[] = $head;
            }
        }
        $this->assertEquals(array($b, $b, $c, $b, $c, $c, $b, $c, $b, $c), $found);
    }

    /**
     * @depends testAddDirectedEdge
     * @depends testEachAdjacentTo
     * @covers ::removeVertex
     */
    public function testRemoveVertex() {
        list($a, $b) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);
        $this->assertEquals(2, $this->g->order());

        $this->g->removeVertex($b);
        $this->assertEquals(1, $this->g->order());

        // Ensure that b was correctly removed from a's outgoing edges
        $found = array();
        foreach ($this->g->eachAdjacentTo($a) as $edge => $head) {
            $found[] = $head;
        }

        $this->assertEmpty($found);
    }

    /**
     * @depends testAddDirectedEdge
     * @covers ::removeEdge
     */
    public function testRemoveEdge() {
        list($a, $b) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);
        $this->g->removeEdge($a, $b);

        $this->assertEquals(2, $this->g->order());
    }

    /**
     * @depends testAddDirectedEdge
     * @depends testEachAdjacentTo
     * @covers ::eachEdge
     */
    public function testEachEdge() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);
        $this->g->addDirectedEdge($a, $c);

        $found = array();
        foreach ($this->g->eachEdge() as $edge) {
            $found[] = $edge;
        }

        $this->assertCount(2, $found);
        $this->assertEquals(array($a, $b), $found[0]);
        $this->assertEquals(array($a, $c), $found[1]);

        $found = array();
        foreach ($this->g->eachEdge() as $edge) {
            $found[] = $edge;
            foreach ($this->g->eachEdge() as $edge) {
                $found[] = $edge;
            }
            foreach ($this->g->eachEdge() as $edge) {
                $found[] = $edge;
            }
        }

        $expected = array(
            array($a, $b),
            array($a, $b),
            array($a, $c),
            array($a, $b),
            array($a, $c),
            array($a, $c),
            array($a, $b),
            array($a, $c),
            array($a, $b),
            array($a, $c),
        );
        $this->assertEquals($expected, $found);
    }

    /**
     * @depends testAddDirectedEdge
     * @depends testEachEdge
     * @covers ::transpose
     */
    public function testTranspose() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);
        $this->g->addDirectedEdge($a, $c);

        $transpose = $this->g->transpose();
        $this->assertEquals(3, $transpose->order());

        $found = array();
        foreach ($transpose->eachEdge() as $edge) {
            $found[] = $edge;
        }

        $this->assertCount(2, $found);
        $this->assertContains(array($b, $a), $found);
        $this->assertContains(array($c, $a), $found);
    }

    /**
     * @expectedException \Gliph\Exception\NonexistentVertexException
     * @covers ::removeVertex
     */
    public function testRemoveNonexistentVertex() {
        list($a) = array_values($this->v);
        $this->g->removeVertex($a);
    }

    /**
     * @covers ::isAcyclic()
     */
    public function testIsAcyclic() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);
        $this->g->addDirectedEdge($b, $c);
        $this->assertTrue($this->g->isAcyclic());

        $this->g->addDirectedEdge($c, $a);
        $this->assertFalse($this->g->isAcyclic());
    }

    /**
     * This is primarily a test of the Tarjan SCC algo, but the coverage scoping
     * ensures that we are only focused on the graph's method for returning
     * correct outputs.
     *
     * @covers ::getCycles()
     */
    public function testGetCycles() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);
        $this->g->addDirectedEdge($b, $c);

        $this->assertEmpty($this->g->getCycles());

        $this->g->addDirectedEdge($c, $a);
        $this->assertEquals(array(array($c, $b, $a)), $this->g->getCycles());
    }

    /**
     * @depends testAddDirectedEdge
     * @covers ::inDegree
     */
    public function testInDegree() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);
        $this->g->addDirectedEdge($b, $c);

        $this->assertSame(0, $this->g->inDegree($a));
        $this->assertSame(1, $this->g->inDegree($b));
        $this->assertSame(1, $this->g->inDegree($c));

        $this->setExpectedException('\\Gliph\\Exception\\NonexistentVertexException');
        $this->g->inDegree(new \stdClass());
    }


    /**
     * @depends testAddDirectedEdge
     * @covers ::outDegree
     */
    public function testOutDegree() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);
        $this->g->addDirectedEdge($b, $c);

        $this->assertSame(1, $this->g->outDegree($a));
        $this->assertSame(1, $this->g->outDegree($b));
        $this->assertSame(0, $this->g->outDegree($c));

        $this->setExpectedException('\\Gliph\\Exception\\NonexistentVertexException');
        $this->g->outDegree(new \stdClass());
    }

    /**
     * @depends testAddDirectedEdge
     * @covers ::size
     */
    public function testSize() {
        list($a, $b) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);

        $this->assertEquals(1, $this->g->size());
    }
}
