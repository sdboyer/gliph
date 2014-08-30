<?php

namespace Gliph\Graph\TestTraits;

/**
 * Provides a trait to test the methods of Digraph and MutableDigraph.
 */
trait DirectedGraphSpec {

    /**
     * Factory function to produce the graph object type under test.
     *
     * @return Digraph|MutableVertexSet
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
     * @covers ::successorsOf
     */
    public function testSuccessorsOf() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $g->ensureArc($a, $c);

        $found = array();
        foreach ($g->successorsOf($a) as $head) {
            $found[] = $head;
        }
        $this->assertEquals(array($b, $c), $found);

        $found = array();
        foreach ($g->successorsOf($b) as $head) {
            $found[] = $head;
        }
        $this->assertEmpty($found);

        foreach ($g->successorsOf($c) as $head) {
            $found[] = $head;
        }
        $this->assertEmpty($found);

        // nested
        $found = array();
        foreach ($g->successorsOf($a) as $head) {
            $found[] = $head;
            foreach ($g->successorsOf($a) as $head) {
                $found[] = $head;
            }
            foreach ($g->successorsOf($a) as $head) {
                $found[] = $head;
            }
        }
        $this->assertEquals(array($b, $b, $c, $b, $c, $c, $b, $c, $b, $c), $found);
    }

    /**
     * @expectedException \Gliph\Exception\NonexistentVertexException
     */
    public function testSuccessorsOfMissingVertex() {
        list($a) = array_values($this->getTestVertices());
        $g = $this->g();

        foreach ($g->successorsOf($a) as $edge) {
            $this->fail();
        }
    }

    /**
     * @depends testEnsureArc
     * @covers ::predecessorsOf
     */
    public function testPredecessorsOf() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($b, $a);
        $g->ensureArc($c, $a);

        $found = array();
        foreach ($g->predecessorsOf($a) as $head) {
            $found[] = $head;
        }
        $this->assertEquals(array($b, $c), $found);

        $found = array();
        foreach ($g->predecessorsOf($b) as $head) {
            $found[] = $head;
        }
        $this->assertEmpty($found);

        foreach ($g->predecessorsOf($c) as $head) {
            $found[] = $head;
        }
        $this->assertEmpty($found);

        // nested
        $found = array();
        foreach ($g->predecessorsOf($a) as $head) {
            $found[] = $head;
            foreach ($g->predecessorsOf($a) as $head) {
                $found[] = $head;
            }
            foreach ($g->predecessorsOf($a) as $head) {
                $found[] = $head;
            }
        }
        $this->assertEquals(array($b, $b, $c, $b, $c, $c, $b, $c, $b, $c), $found);
    }

    /**
     * @expectedException \Gliph\Exception\NonexistentVertexException
     */
    public function testPredecessorsOfMissingVertex() {
        list($a) = array_values($this->getTestVertices());
        $g = $this->g();

        foreach ($g->predecessorsOf($a) as $edge) {
            $this->fail();
        }
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
     * @depends testEdges
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
        foreach ($transpose->edges() as $edge) {
            $found[] = $edge;
        }

        $this->assertCount(2, $found);
        $this->assertContains(array($b, $a), $found);
        $this->assertContains(array($c, $a), $found);
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
     * @covers ::inDegreeOf
     */
    public function testInDegree() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $g->ensureArc($b, $c);

        $this->assertSame(0, $g->inDegreeOf($a));
        $this->assertSame(1, $g->inDegreeOf($b));
        $this->assertSame(1, $g->inDegreeOf($c));

        $this->setExpectedException('\\Gliph\\Exception\\NonexistentVertexException');
        $g->inDegreeOf(new \stdClass());
    }


    /**
     * @depends testEnsureArc
     * @covers ::outDegreeOf
     */
    public function testOutDegree() {
        list($a, $b, $c) = array_values($this->getTestVertices());
        $g = $this->g();

        $g->ensureArc($a, $b);
        $g->ensureArc($b, $c);

        $this->assertSame(1, $g->outDegreeOf($a));
        $this->assertSame(1, $g->outDegreeOf($b));
        $this->assertSame(0, $g->outDegreeOf($c));

        $this->setExpectedException('\\Gliph\\Exception\\NonexistentVertexException');
        $g->outDegreeOf(new \stdClass());
    }
}
