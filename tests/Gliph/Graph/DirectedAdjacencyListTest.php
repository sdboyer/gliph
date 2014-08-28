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

    public function setUp() {
        $this->getTestVertices();
    }

    /**
     * Returns a new DirectedAdjacencyList.
     *
     * @return DirectedAdjacencyList
     */
    protected function g() {
        return new DirectedAdjacencyList();
    }

    /**
     * @depends!! testEnsureVertex (for some reason this suddenly stopped working)
     * @covers ::ensureArc
     */
    public function testEnsureArc() {
        list($a, $b) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);

        $this->assertAttributeContains($a, 'vertices', $g);
        $this->assertAttributeContains($b, 'vertices', $g);
        $this->assertEquals(2, $g->order());
    }

    /**
     * @depends testEnsureArc
     * @covers ::eachAdjacentTo
     */
    public function testEachAdjacentTo() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $g->ensureArc($a, $c);

        $found = array();
        foreach ($g->eachAdjacentTo($a) as $head) {
            $found[] = $head;
        }
        $this->assertEquals(array($b, $c), $found);

        $found = array();
        foreach ($g->eachAdjacentTo($b) as $head) {
            $found[] = $head;
        }
        $this->assertEmpty($found);

        foreach ($g->eachAdjacentTo($c) as $head) {
            $found[] = $head;
        }
        $this->assertEmpty($found);

        // nested
        $found = array();
        foreach ($g->eachAdjacentTo($a) as $head) {
            $found[] = $head;
            foreach ($g->eachAdjacentTo($a) as $head) {
                $found[] = $head;
            }
            foreach ($g->eachAdjacentTo($a) as $head) {
                $found[] = $head;
            }
        }
        $this->assertEquals(array($b, $b, $c, $b, $c, $c, $b, $c, $b, $c), $found);
    }

    /**
     * @depends testEnsureArc
     * @depends testEachAdjacentTo
     * @covers ::removeVertex
     */
    public function testRemoveVertex() {
        list($a, $b) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $this->assertEquals(2, $g->order());

        $g->removeVertex($b);
        $this->assertEquals(1, $g->order());

        // Ensure that b was correctly removed from a's outgoing edges
        $found = array();
        foreach ($g->eachAdjacentTo($a) as $edge => $head) {
            $found[] = $head;
        }

        $this->assertEmpty($found);
    }

    /**
     * @depends testEnsureArc
     * @covers ::removeArc
     */
    public function testRemoveArc() {
        list($a, $b) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $g->removeArc($a, $b);

        $this->assertEquals(2, $g->order());
    }

    /**
     * @depends testEnsureArc
     * @depends testEachAdjacentTo
     * @covers ::eachEdge
     */
    public function testEachEdge() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $g->ensureArc($a, $c);

        $found = array();
        foreach ($g->eachEdge() as $edge) {
            $found[] = $edge;
        }

        $this->assertCount(2, $found);
        $this->assertEquals(array($a, $b), $found[0]);
        $this->assertEquals(array($a, $c), $found[1]);

        $found = array();
        foreach ($g->eachEdge() as $edge) {
            $found[] = $edge;
            foreach ($g->eachEdge() as $edge) {
                $found[] = $edge;
            }
            foreach ($g->eachEdge() as $edge) {
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
     * @depends testEnsureArc
     * @depends testEachEdge
     * @covers ::transpose
     */
    public function testTranspose() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $g->ensureArc($a, $c);

        $transpose = $g->transpose();
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
        list($a) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->removeVertex($a);
    }

    /**
     * @covers ::isAcyclic()
     */
    public function testIsAcyclic() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $g->ensureArc($b, $c);
        $this->assertTrue($g->isAcyclic());

        $g->ensureArc($c, $a);
        $this->assertFalse($g->isAcyclic());
    }

    /**
     * This is primarily a test of the Tarjan SCC algo, but the coverage scoping
     * ensures that we are only focused on the graph's method for returning
     * correct outputs.
     *
     * @covers ::getCycles()
     */
    public function testGetCycles() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $g->ensureArc($b, $c);

        $this->assertEmpty($g->getCycles());

        $g->ensureArc($c, $a);
        $this->assertEquals(array(array($c, $b, $a)), $g->getCycles());
    }

    /**
     * @depends testEnsureArc
     * @covers ::inDegree
     */
    public function testInDegree() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $g->ensureArc($b, $c);

        $this->assertSame(0, $g->inDegree($a));
        $this->assertSame(1, $g->inDegree($b));
        $this->assertSame(1, $g->inDegree($c));

        $this->setExpectedException('\\Gliph\\Exception\\NonexistentVertexException');
        $g->inDegree(new \stdClass());
    }


    /**
     * @depends testEnsureArc
     * @covers ::outDegree
     */
    public function testOutDegree() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $g->ensureArc($b, $c);

        $this->assertSame(1, $g->outDegree($a));
        $this->assertSame(1, $g->outDegree($b));
        $this->assertSame(0, $g->outDegree($c));

        $this->setExpectedException('\\Gliph\\Exception\\NonexistentVertexException');
        $g->outDegree(new \stdClass());
    }

    /**
     * @depends testEnsureArc
     * @covers ::size
     */
    public function testSize() {
        list($a, $b) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);

        $this->assertEquals(1, $g->size());
    }
}
