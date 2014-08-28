<?php

/**
 * @file
 * Contains \Gliph\Graph\GraphTest.
 */

namespace Gliph\Graph\TestTraits;

use Gliph\Util;

/**
 * Provides a trait to test the methods of Graph and MutableGraph.
 */
trait GraphSpec {

    /**
     * @expectedException \Gliph\Exception\InvalidVertexTypeException
     * @dataProvider invalidVertexTypesProvider
     */
    public function testInvalidVertexTypes($invalid_vertex) {
        $this->g->ensureVertex($invalid_vertex);
    }

    /**
     * Technically depends on order(), but that would create a cycle. We have no
     * choice but to break that cycle somewhere, so we do it here.
     *
     * @covers ::ensureVertex
     */
    public function testAddVertex() {
        list($a) = array_values($this->v);
        $this->g->ensureVertex($a);

        $this->assertEquals(1, $this->g->order());
    }

    /**
     * @depends testAddVertex
     * @covers ::eachVertex
     */
    public function testEachVertex() {
        list($a, $b) = array_values($this->v);
        $this->g->ensureVertex($a);
        $this->g->ensureVertex($b);

        $found = array();
        foreach ($this->g->eachVertex() as $vertex => $adjacent) {
            $found[] = $vertex;
        }

        $this->assertEquals(array($a, $b), $found);

        // Now, test nested iteration
        $found = array();
        foreach ($this->g->eachVertex() as $vertex => $adjacent) {
            $found[] = $vertex;
            foreach ($this->g->eachVertex() as $vertex => $adjacent) {
                $found[] = $vertex;
            }
        }
        $this->assertEquals(array($a, $a, $b, $b, $a, $b), $found);
    }

    /**
     * @depends testAddVertex
     * @covers ::hasVertex
     */
    public function testHasVertex() {
        list($a) = array_values($this->v);
        $this->assertFalse($this->g->hasVertex($a));

        $this->g->ensureVertex($a);
        $this->assertTrue($this->g->hasVertex($a));
    }

    /**
     * @depends testHasVertex
     * @covers ::ensureVertex
     */
    public function testAddVertexTwice() {
        list($a) = array_values($this->v);
        // Adding a vertex twice should be a no-op.
        $this->g->ensureVertex($a);
        $this->g->ensureVertex($a);

        $this->assertTrue($this->g->hasVertex($a));
        $this->assertEquals(1, $this->g->order());
    }
    /**
     * @covers ::eachAdjacentTo
     */
    public function testEachAdjacentTo() {
        list($a, $b, $c) = array_values($this->v);
        Util::ensureEdge($this->g, $a, $b);
        Util::ensureEdge($this->g, $b, $c);

        // Edge directionality is irrelevant to adjacency; for both directed and
        // undirected, $b should have two adjacent vertices.
        $found = array();
        foreach ($this->g->eachAdjacentTo($b) as $edge => $adjacent) {
            $found[] = $adjacent;
        }

        $this->assertCount(2, $found);

        // test nesting
        $found = array();
        foreach ($this->g->eachAdjacentTo($b) as $edge => $adjacent) {
            $found[] = $adjacent;
            foreach ($this->g->eachAdjacentTo($b) as $edge => $adjacent) {
                $found[] = $adjacent;
            }
            foreach ($this->g->eachAdjacentTo($b) as $edge => $adjacent) {
                $found[] = $adjacent;
            }
        }

        $this->assertCount(10, $found);
        $this->assertEquals(array($a, $a, $c, $a, $c, $c, $a, $c, $a, $c), $found);
    }

    public function testAddEdge() {
        list($a, $b) = array_values($this->v);
        Util::ensureEdge($this->g, $a, $b);

        $this->assertAttributeContains($a, 'vertices', $this->g);
        $this->assertAttributeContains($b, 'vertices', $this->g);
        $this->assertEquals(2, $this->g->order());
    }

    /**
     * @depends testAddEdge
     * @covers ::removeVertex
     */
    public function testRemoveVertex() {
        list($a, $b) = array_values($this->v);
        Util::ensureEdge($this->g, $a, $b);

        $this->g->removeVertex($a);
        $this->assertEquals(1, $this->g->order());
    }

    /**
     * @expectedException \Gliph\Exception\NonexistentVertexException
     */
    public function testEachAdjacentToMissingVertex() {
        list($a) = array_values($this->v);
        foreach ($this->g->eachAdjacentTo($a) as $adjacent) {
            $this->fail();
        }
    }

    /**
     * @depends testAddVertex
     * @covers ::order
     */
    public function testOrder() {
        list($a) = array_values($this->v);
        $this->g->ensureVertex($a);

        $this->assertEquals(1, $this->g->order());
    }

    /**
     * @depends testAddEdge
     * @depends testEachAdjacentTo
     */
    public function testRemoveEdge() {
        list($a, $b, $c) = array_values($this->v);
        Util::ensureEdge($this->g, $a, $b);
        Util::ensureEdge($this->g, $b, $c);

        Util::removeEdge($this->g, $b, $c);
        $this->assertEquals(3, $this->g->order());

        $found = array();
        foreach ($this->g->eachAdjacentTo($a) as $edge => $adjacent) {
            $found[] = $adjacent;
        }

        $this->assertEquals(array($b), $found);
    }

    /**
     * @depends testAddEdge
     * @covers ::eachEdge
     */
    public function testEachEdge() {
        list($a, $b, $c) = array_values($this->v);
        Util::ensureEdge($this->g, $a, $b);
        Util::ensureEdge($this->g, $b, $c);

        $found = array();
        foreach ($this->g->eachEdge() as $edge) {
            $found[] = $edge;
        }

        $this->assertCount(2, $found);
        $this->assertEquals(array($a, $b), $found[0]);
        $this->assertEquals(array($b, $c), $found[1]);

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
        list($a) = array_values($this->v);
        $this->g->removeVertex($a);
    }

    /**
     * @depends testAddEdge
     * @covers ::inDegree
     */
    public function testInDegree() {
        list($a, $b, $c) = array_values($this->v);
        Util::ensureEdge($this->g, $a, $b);
        Util::ensureEdge($this->g, $b, $c);

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
        Util::ensureEdge($this->g, $a, $b);
        Util::ensureEdge($this->g, $b, $c);

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
        Util::ensureEdge($this->g, $a, $b);

        $this->assertEquals(1, $this->g->size());
    }
}