<?php

/**
 * @file
 * Contains \Gliph\Graph\GraphTest.
 */

namespace Gliph\Graph\TestTraits;

/**
 * Provides a trait to test the methods of Graph and MutableGraph.
 */
trait GraphSpec {

    /**
     * @expectedException \Gliph\Exception\InvalidVertexTypeException
     * @dataProvider invalidVertexTypesProvider
     */
    public function testInvalidVertexTypes($invalid_vertex) {
        $this->g->addVertex($invalid_vertex);
    }

    /**
     * Technically depends on order(), but that would create a cycle. We have no
     * choice but to break that cycle somewhere, so we do it here.
     *
     * @covers ::addVertex
     */
    public function testAddVertex() {
        list($a) = array_values($this->v);
        $this->g->addVertex($a);

        $this->assertEquals(1, $this->g->order());
    }

    /**
     * @depends testAddVertex
     * @covers ::eachVertex
     * @covers ::getTraversableSplos
     */
    public function testEachVertex() {
        list($a, $b) = array_values($this->v);
        $this->g->addVertex($a);
        $this->g->addVertex($b);

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

        $this->g->addVertex($a);
        $this->assertTrue($this->g->hasVertex($a));
    }

    /**
     * @depends testHasVertex
     * @covers ::addVertex
     */
    public function testAddVertexTwice() {
        list($a) = array_values($this->v);
        // Adding a vertex twice should be a no-op.
        $this->g->addVertex($a);
        $this->g->addVertex($a);

        $this->assertTrue($this->g->hasVertex($a));
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
        $this->g->addVertex($a);

        $this->assertEquals(1, $this->g->order());
    }
}