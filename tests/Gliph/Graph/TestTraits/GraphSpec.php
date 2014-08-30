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
     * @covers ::vertices
     */
    public function testVertices() {
        list($a, $b) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureVertex($a);
        $g->ensureVertex($b);

        $found = array();
        foreach ($g->vertices() as $vertex => $adjacent) {
            $found[] = $vertex;
        }

        $this->assertEquals(array($a, $b), $found);

        // Now, test nested iteration
        $found = array();
        foreach ($g->vertices() as $vertex => $adjacent) {
            $found[] = $vertex;
            foreach ($g->vertices() as $vertex => $adjacent) {
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
     * @covers ::adjacentTo
     */
    public function testAdjacentTo() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);
        Util::ensureEdge($g, $b, $c);

        // Edge directionality is irrelevant to adjacency; for both directed and
        // undirected, $b should have two adjacent vertices.
        $f1 = array();
        foreach ($g->adjacentTo($b) as $adjacent) {
            $f1[] = $adjacent;
        }

        $this->assertCount(2, $f1);

        // test nesting
        $found = array();
        foreach ($g->adjacentTo($b) as $edge => $adjacent) {
            $found[] = $adjacent;
            foreach ($g->adjacentTo($b) as $edge => $adjacent) {
                $found[] = $adjacent;
            }
            foreach ($g->adjacentTo($b) as $edge => $adjacent) {
                $found[] = $adjacent;
            }
        }

        $this->assertCount(10, $found);

        // This is a tough test. We can't require a particular ordering of the
        // output, but it's fair to assume that the ordering will be consistent
        // across nested iterations. So, work backwards from what we found in
        // the non-nested test.
        $expected = array(
            $f1[0],
            $f1[0],
            $f1[1],
            $f1[0],
            $f1[1],
            $f1[1],
            $f1[0],
            $f1[1],
            $f1[0],
            $f1[1],
        );
        $this->assertEquals($expected, $found);
    }

    /**
     * @expectedException \Gliph\Exception\NonexistentVertexException
     */
    public function testAdjacentToMissingVertex() {
        list($a) = array_values($this->getTestVertices());
        $g = $this->g();

        foreach ($g->adjacentTo($a) as $adjacent) {
            $this->fail();
        }
    }

    /**
     * @covers ::incidentTo
     */
    public function testIncidentTo() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);
        Util::ensureEdge($g, $b, $c);

        // Edge directionality is irrelevant to adjacency; for both directed and
        // undirected, $b should have two adjacent vertices.
        $found = array();
        foreach ($g->incidentTo($b) as $edge) {
            $found[] = $edge;
        }

        $this->assertCount(2, $found);

        // test nesting
        $found = array();
        foreach ($g->incidentTo($b) as $edge) {
            $found[] = $edge;
            foreach ($g->incidentTo($b) as $edge) {
                $found[] = $edge;
            }
            foreach ($g->incidentTo($b) as $edge) {
                $found[] = $edge;
            }
        }

        // TODO super lazy, but proper verification is SO EXHAUSTIVE. later...
        $this->assertCount(10, $found);
    }

    /**
     * @expectedException \Gliph\Exception\NonexistentVertexException
     */
    public function testIncidentToMissingVertex() {
        list($a) = array_values($this->getTestVertices());
        $g = $this->g();

        foreach ($g->incidentTo($a) as $edge) {
            $this->fail();
        }
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
     * @depends testAdjacentTo
     */
    public function testRemoveEdge() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);
        Util::ensureEdge($g, $b, $c);

        Util::removeEdge($g, $b, $c);
        $this->assertEquals(3, $g->order());

        $found = array();
        foreach ($g->adjacentTo($a) as $edge => $adjacent) {
            $found[] = $adjacent;
        }

        $this->assertEquals(array($b), $found);
    }

    /**
     * @depends testEnsureEdge
     * @covers ::edges
     */
    public function testEdges() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);
        Util::ensureEdge($g, $b, $c);

        $found = array();
        foreach ($g->edges() as $edge) {
            $found[] = $edge;
        }

        $this->assertCount(2, $found);
        $this->assertEquals(array($a, $b), $found[0]);
        $this->assertEquals(array($b, $c), $found[1]);

        $found = array();
        foreach ($g->edges() as $edge) {
            $found[] = $edge;
            foreach ($g->edges() as $edge) {
                $found[] = $edge;
            }
            foreach ($g->edges() as $edge) {
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
     * @covers ::degreeOf
     */
    public function testDegreeOf() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        Util::ensureEdge($g, $a, $b);
        Util::ensureEdge($g, $b, $c);

        $this->assertSame(1, $g->degreeOf($a));
        $this->assertSame(2, $g->degreeOf($b));
        $this->assertSame(1, $g->degreeOf($c));

        $this->setExpectedException('\\Gliph\\Exception\\NonexistentVertexException');
        $g->degreeOf(new \stdClass());
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
