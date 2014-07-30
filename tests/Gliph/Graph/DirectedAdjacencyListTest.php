<?php

namespace Gliph\Graph;

/**
 * @coversDefaultClass \Gliph\Graph\DirectedAdjacencyList
 */
class DirectedAdjacencyListTest extends AdjacencyListBase {

    /**
     * @var DirectedAdjacencyList
     */
    protected $g;

    public function setUp() {
        parent::setUp();
        $this->g = new DirectedAdjacencyList();
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
        $this->assertVertexCount(2, $this->g);
    }

    /**
     * @depends testAddDirectedEdge
     * @covers ::eachAdjacent
     */
    public function testEachAdjacent() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);
        $this->g->addDirectedEdge($a, $c);

        $found = array();
        foreach ($this->g->eachAdjacent($a) as $head) {
            $found[] = $head;
        }
        $this->assertEquals(array($b, $c), $found);

        $found = array();
        foreach ($this->g->eachAdjacent($b) as $head) {
            $found[] = $head;
        }
        $this->assertEmpty($found);

        foreach ($this->g->eachAdjacent($c) as $head) {
            $found[] = $head;
        }
        $this->assertEmpty($found);

        // nested
        $found = array();
        foreach ($this->g->eachAdjacent($a) as $head) {
            $found[] = $head;
            foreach ($this->g->eachAdjacent($a) as $head) {
                $found[] = $head;
            }
        }
        $this->assertEquals(array($b, $b, $c, $c, $b, $c), $found);
    }

    /**
     * @depends testAddDirectedEdge
     * @depends testEachAdjacent
     * @covers ::removeVertex
     */
    public function testRemoveVertex() {
        list($a, $b) = array_values($this->v);
        $this->g->addDirectedEdge($a, $b);
        $this->assertVertexCount(2, $this->g);

        $this->g->removeVertex($b);
        $this->assertVertexCount(1, $this->g);

        // Ensure that b was correctly removed from a's outgoing edges
        $found = array();
        foreach ($this->g->eachAdjacent($a) as $edge => $head) {
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

        $this->assertVertexCount(2, $this->g);
    }

    /**
     * @depends testAddDirectedEdge
     * @depends testEachAdjacent
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
        }

        $expected = array(
            array($a, $b),
            array($a, $b),
            array($a, $c),
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

        $this->assertVertexCount(3, $transpose);

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
        $this->g->removeVertex($this->v['a']);
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
        $this->assertEquals(array(array($this->v['c'], $this->v['b'], $this->v['a'])), $this->g->getCycles());
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
