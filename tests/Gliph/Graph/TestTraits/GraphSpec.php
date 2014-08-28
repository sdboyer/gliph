<?php

/**
 * @file
 * Contains \Gliph\Graph\GraphTest.
 */

namespace Gliph\Graph\TestTraits;

use Gliph\Util;
use Gliph\Graph\Graph;
use Gliph\Graph\MutableVertexSet;

/**
 * Provides a trait to test the methods of Graph and MutableGraph.
 */
trait GraphSpec {

    /**
     * Factory function to produce the graph object type under test.
     *
     * @return Graph|MutableVertexSet
     */
    abstract protected function g();

    /**
     * Factory function to produce the vertex set for the current test.
     *
     * @return array
     *   An array of vertices for the test.
     */
    abstract protected function getTestVertices();

    /**
     * @expectedException \Gliph\Exception\InvalidVertexTypeException
     * @dataProvider invalidVertexTypesProvider
     */
    public function testInvalidVertexTypes($invalid_vertex) {
        $g = $this->g();
        $g->ensureVertex($invalid_vertex);
    }

    /**
     * Technically depends on order(), but that would create a cycle. We have no
     * choice but to break that cycle somewhere, so we do it here.
     *
     * @covers ::ensureVertex
     */
    public function testEnsureVertex() {
        list($a) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureVertex($a);

        $this->assertEquals(1, $g->order());
    }

    /**
     * @depends testEnsureVertex
     * @covers ::eachVertex
     */
    public function testEachVertex() {
        list($a, $b) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureVertex($a);
        $g->ensureVertex($b);

        $found = array();
        foreach ($g->eachVertex() as $vertex => $adjacent) {
            $found[] = $vertex;
        }

        $this->assertEquals(array($a, $b), $found);

        // Now, test nested iteration
        $found = array();
        foreach ($g->eachVertex() as $vertex => $adjacent) {
            $found[] = $vertex;
            foreach ($g->eachVertex() as $vertex => $adjacent) {
                $found[] = $vertex;
            }
        }
        $this->assertEquals(array($a, $a, $b, $b, $a, $b), $found);
    }

    /**
     * @depends testEnsureVertex
     * @covers ::hasVertex
     */
    public function testHasVertex() {
        list($a) = array_values($this->getTestVertices());
        $g = $this->g();

        $this->assertFalse($g->hasVertex($a));

        $g->ensureVertex($a);
        $this->assertTrue($g->hasVertex($a));
    }

    /**
     * @depends testHasVertex
     * @covers ::ensureVertex
     */
    public function testEnsureVertexTwice() {
        list($a) = array_values($this->getTestVertices());
        $g = $this->g();

        // Adding a vertex twice should be a no-op.
        $g->ensureVertex($a);
        $g->ensureVertex($a);

        $this->assertTrue($g->hasVertex($a));
        $this->assertEquals(1, $g->order());
    }
    /**
     * @covers ::eachAdjacentTo
     */
    public function testEachAdjacentTo() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);
        Util::ensureEdge($g, $b, $c);

        // Edge directionality is irrelevant to adjacency; for both directed and
        // undirected, $b should have two adjacent vertices.
        $found = array();
        foreach ($g->eachAdjacentTo($b) as $edge => $adjacent) {
            $found[] = $adjacent;
        }

        $this->assertCount(2, $found);

        // test nesting
        $found = array();
        foreach ($g->eachAdjacentTo($b) as $edge => $adjacent) {
            $found[] = $adjacent;
            foreach ($g->eachAdjacentTo($b) as $edge => $adjacent) {
                $found[] = $adjacent;
            }
            foreach ($g->eachAdjacentTo($b) as $edge => $adjacent) {
                $found[] = $adjacent;
            }
        }

        $this->assertCount(10, $found);
        $this->assertEquals(array($a, $a, $c, $a, $c, $c, $a, $c, $a, $c), $found);
    }

    public function testEnsureEdge() {
        list($a, $b) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);

        $this->assertAttributeContains($a, 'vertices', $g);
        $this->assertAttributeContains($b, 'vertices', $g);
        $this->assertEquals(2, $g->order());
    }

    /**
     * @depends testEnsureEdge
     * @covers ::removeVertex
     */
    public function testRemoveVertex() {
        list($a, $b) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);

        $g->removeVertex($a);
        $this->assertEquals(1, $g->order());
    }

    /**
     * @expectedException \Gliph\Exception\NonexistentVertexException
     */
    public function testEachAdjacentToMissingVertex() {
        list($a) = array_values($this->getTestVertices());
        $g = $this->g();

        foreach ($g->eachAdjacentTo($a) as $adjacent) {
            $this->fail();
        }
    }

    /**
     * @depends testEnsureVertex
     * @covers ::order
     */
    public function testOrder() {
        list($a) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureVertex($a);

        $this->assertEquals(1, $g->order());
    }

    /**
     * @depends testEnsureEdge
     * @depends testEachAdjacentTo
     */
    public function testRemoveEdge() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);
        Util::ensureEdge($g, $b, $c);

        Util::removeEdge($g, $b, $c);
        $this->assertEquals(3, $g->order());

        $found = array();
        foreach ($g->eachAdjacentTo($a) as $edge => $adjacent) {
            $found[] = $adjacent;
        }

        $this->assertEquals(array($b), $found);
    }

    /**
     * @depends testEnsureEdge
     * @covers ::eachEdge
     */
    public function testEachEdge() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);
        Util::ensureEdge($g, $b, $c);

        $found = array();
        foreach ($g->eachEdge() as $edge) {
            $found[] = $edge;
        }

        $this->assertCount(2, $found);
        $this->assertEquals(array($a, $b), $found[0]);
        $this->assertEquals(array($b, $c), $found[1]);

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

        $this->assertCount(10, $found);
        $expected = array(
          array($a, $b),
          array($a, $b),
          array($b, $c),
          array($a, $b),
          array($b, $c),
          array($b, $c),
          array($a, $b),
          array($b, $c),
          array($a, $b),
          array($b, $c),
        );
        $this->assertEquals($expected, $found);
    }

    /**
     * @expectedException \Gliph\Exception\NonexistentVertexException
     */
    public function testRemoveNonexistentVertex() {
        list($a) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->removeVertex($a);
    }

    /**
     * @depends testEnsureEdge
     * @covers ::inDegree
     */
    public function testInDegree() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);
        Util::ensureEdge($g, $b, $c);

        $this->assertSame(1, $g->inDegree($a));
        $this->assertSame(2, $g->inDegree($b));
        $this->assertSame(1, $g->inDegree($c));

        $this->setExpectedException('\\Gliph\\Exception\\NonexistentVertexException');
        $g->inDegree(new \stdClass());
    }

    /**
     * @depends testEnsureEdge
     * @covers ::outDegree
     */
    public function testOutDegree() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);
        Util::ensureEdge($g, $b, $c);

        $this->assertSame(1, $g->outDegree($a));
        $this->assertSame(2, $g->outDegree($b));
        $this->assertSame(1, $g->outDegree($c));

        $this->setExpectedException('\\Gliph\\Exception\\NonexistentVertexException');
        $g->outDegree(new \stdClass());
    }

    /**
     * @depends testEnsureEdge
     * @covers ::size
     */
    public function testSize() {
        list($a, $b) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);

        $this->assertEquals(1, $g->size());
    }
}