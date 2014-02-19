<?php

namespace Gliph\Graph;

/**
 * @coversDefaultClass \Gliph\Graph\UndirectedAdjacencyList
 */
class UndirectedAdjacencyListTest extends AdjacencyListBase {

    /**
     * @var UndirectedAdjacencyList
     */
    protected $g;

    /**
     * Creates a set of vertices and an empty graph for testing.
     */
    public function setUp() {
        parent::setUp();
        $this->g = new UndirectedAdjacencyList();
    }

    /**
     * @covers ::addEdge
     */
    public function testAddEdge() {
        list($a, $b) = array_values($this->v);
        $this->g->addEdge($a, $b);

        $this->assertAttributeContains($a, 'vertices', $this->g);
        $this->assertAttributeContains($b, 'vertices', $this->g);
        $this->assertVertexCount(2, $this->g);
    }

    /**
     * @depends testAddEdge
     * @covers ::removeVertex
     */
    public function testRemoveVertex() {
        list($a, $b) = array_values($this->v);
        $this->g->addEdge($a, $b);

        $this->g->removeVertex($a);
        $this->assertVertexCount(1, $this->g);
    }

    /**
     * @depends testAddEdge
     * @covers ::eachAdjacent
     */
    public function testEachAdjacent() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addEdge($a, $b);
        $this->g->addEdge($b, $c);

        // Ensure bidirectionality of created edges
        $found = array();
        $this->g->eachAdjacent($b, function($adjacent) use (&$found) {
            $found[] = $adjacent;
        });

        $this->assertCount(2, $found);
    }

    /**
     * @depends testAddEdge
     * @depends testEachAdjacent
     * @covers ::removeEdge
     */
    public function testRemoveEdge() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addEdge($a, $b);
        $this->g->addEdge($b, $c);

        $this->g->removeEdge($b, $c);
        $this->assertVertexCount(3, $this->g);

        $found = array();
        $this->g->eachAdjacent($a, function($adjacent) use (&$found) {
            $found[] = $adjacent;
        });

        $this->assertEquals(array($b), $found);
    }

    /**
     * @depends testAddEdge
     * @covers ::eachEdge
     */
    public function testEachEdge() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addEdge($a, $b);
        $this->g->addEdge($b, $c);

        $found = array();
        $this->g->eachEdge(function ($edge) use (&$found) {
            $found[] = $edge;
        });

        $this->assertCount(2, $found);
        $this->assertEquals(array($a, $b), $found[0]);
        $this->assertEquals(array($b, $c), $found[1]);
    }

    /**
     * @expectedException \Gliph\Exception\NonexistentVertexException
     */
    public function testRemoveNonexistentVertex() {
        $this->g->removeVertex($this->v['a']);
    }

    /**
     * @depends testAddEdge
     * @covers ::inDegree
     */
    public function testInDegree() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addEdge($a, $b);
        $this->g->addEdge($b, $c);

        $this->assertSame(1, $this->g->inDegree($a));
        $this->assertSame(2, $this->g->inDegree($b));
        $this->assertSame(1, $this->g->inDegree($c));

        $this->setExpectedException('\\Gliph\\Exception\\NonexistentVertexException');
        $this->g->inDegree(new \stdClass());
    }


    /**
     * @depends testAddEdge
     * @covers ::outDegree
     */
    public function testOutDegree() {
        list($a, $b, $c) = array_values($this->v);
        $this->g->addEdge($a, $b);
        $this->g->addEdge($b, $c);

        $this->assertSame(1, $this->g->outDegree($a));
        $this->assertSame(2, $this->g->outDegree($b));
        $this->assertSame(1, $this->g->outDegree($c));

        $this->setExpectedException('\\Gliph\\Exception\\NonexistentVertexException');
        $this->g->outDegree(new \stdClass());
    }

    /**
     * @depends testAddEdge
     * @covers ::size
     */
    public function testSize() {
        list($a, $b) = array_values($this->v);
        $this->g->addEdge($a, $b);

        $this->assertEquals(1, $this->g->size());
    }
}
