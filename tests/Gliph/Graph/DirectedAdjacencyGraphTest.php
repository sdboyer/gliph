<?php

namespace Gliph\Graph;

use Gliph\TestVertex;

class DirectedAdjacencyGraphTest extends \PHPUnit_Framework_TestCase {

    protected $v = array();

    /**
     * @var DirectedAdjacencyGraph
     */
    protected $g;

    /**
     * Creates a set of vertices and an empty graph for testing.
     */
    public function setUp() {
        $this->v = array(
            'a' => new TestVertex('a'),
            'b' => new TestVertex('b'),
            'c' => new TestVertex('c'),
            'd' => new TestVertex('d'),
            'e' => new TestVertex('e'),
            'f' => new TestVertex('f'),
            'g' => new TestVertex('g'),
        );

        $this->g = new DirectedAdjacencyGraph();
    }

    /**
     * Tests that an exception is thrown if a string vertex is provided.
     *
     * @expectedException OutOfBoundsException
     */
    public function testAddStringVertex() {
        $this->g->addVertex('a');
    }

    /**
     * Tests that an exception is thrown if an integer vertex is provided.
     *
     * @expectedException OutOfBoundsException
     */
    public function testAddIntegerVertex() {
        $this->g->addVertex(1);
    }

    /**
     * Tests that an exception is thrown if a float vertex is provided.
     *
     * @expectedException OutOfBoundsException
     */
    public function testAddFloatVertex() {
        $this->g->addVertex((float) 1);
    }

    /**
     * Tests that an exception is thrown if an array vertex is provided.
     *
     * @expectedException OutOfBoundsException
     */
    public function testAddArrayVertex() {
        $this->g->addVertex(array());
    }

    /**
     * Tests that an exception is thrown if a resource vertex is provided.
     *
     * @expectedException OutOfBoundsException
     */
    public function testAddResourceVertex() {
        $this->g->addVertex(fopen(__FILE__, 'r'));
    }

    public function testAddVertex() {
        $this->g->addVertex($this->v['a']);

        $this->assertTrue($this->g->hasVertex($this->v['a']));
        $this->doCheckVertexCount(1, $this->g);
    }

    public function testAddVertexTwice() {
        // Adding a vertex twice should be a no-op.
        $this->g->addVertex($this->v['a']);
        $this->g->addVertex($this->v['a']);

        $this->assertTrue($this->g->hasVertex($this->v['a']));
        $this->doCheckVertexCount(1, $this->g);
    }

    public function testAddDirectedEdge() {
        $this->g->addDirectedEdge($this->v['a'], $this->v['b']);

        $this->doCheckVerticesEqual(array($this->v['a'], $this->v['b']), $this->g);
    }

    public function testRemoveVertex() {
        $this->g->addVertex($this->v['a']);
        $this->doCheckVertexCount(1, $this->g);

        $this->g->removeVertex($this->v['a']);
        $this->doCheckVertexCount(0, $this->g);
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testRemoveNonexistentVertex() {
        $this->g->removeVertex($this->v['a']);
    }

    public function testRemoveEdge() {
        $this->g->addDirectedEdge($this->v['a'], $this->v['b']);
        $this->doCheckVerticesEqual(array($this->v['a'], $this->v['b']), $this->g);

        $this->g->removeEdge($this->v['a'], $this->v['b']);
        $this->doCheckVertexCount(2, $this->g);

        $this->assertTrue($this->g->hasVertex($this->v['a']));
        $this->assertTrue($this->g->hasVertex($this->v['b']));
    }

    public function testEachAdjacent() {
        $this->g->addDirectedEdge($this->v['a'], $this->v['b']);
        $this->g->addDirectedEdge($this->v['a'], $this->v['c']);

        $found = array();
        $this->g->eachAdjacent($this->v['a'], function($to) use (&$found) {
            $found[] = $to;
        });

        $this->assertEquals(array($this->v['b'], $this->v['c']), $found);
    }

    public function testEachEdge() {
        $this->g->addDirectedEdge($this->v['a'], $this->v['b']);
        $this->g->addDirectedEdge($this->v['a'], $this->v['c']);

        $found = array();
        $this->g->eachEdge(function($edge) use (&$found) {
            $found[] = $edge;
        });

        $this->assertCount(2, $found);
        $this->assertEquals(array($this->v['a'], $this->v['b']), $found[0]);
        $this->assertEquals(array($this->v['a'], $this->v['c']), $found[1]);
    }

    public function doCheckVerticesEqual($vertices, DirectedAdjacencyGraph $graph) {
        $found = array();

        $graph->eachVertex(function ($vertex) use (&$found) {
            $found[] = $vertex;
        });

        $this->assertEquals($vertices, $found);
    }

    public function doCheckVertexCount($count, $graph) {
        $found = array();

        $graph->eachVertex(function ($vertex) use (&$found) {
            $found[] = $vertex;
        });

        $this->assertCount($count, $found);
    }
}
