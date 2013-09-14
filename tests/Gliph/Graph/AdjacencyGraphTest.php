<?php

namespace Gliph\Graph;


use Gliph\TestVertex;

abstract class AdjacencyGraphTest extends \PHPUnit_Framework_TestCase {

    protected $v = array();

    /**
     * @var AdjacencyGraph
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
    }

    public function doCheckVerticesEqual($vertices, AdjacencyGraph $graph = NULL) {
        $found = array();
        $graph = is_null($graph) ? $this->g : $graph;

        $graph->eachVertex(function ($vertex) use (&$found) {
            $found[] = $vertex;
        });

        $this->assertEquals($vertices, $found);
    }

    public function doCheckVertexCount($count, AdjacencyGraph $graph = NULL) {
        $found = array();
        $graph = is_null($graph) ? $this->g : $graph;

        $graph->eachVertex(function ($vertex) use (&$found) {
            $found[] = $vertex;
        });

        $this->assertCount($count, $found);
    }

    /**
     * Tests that an exception is thrown if a string vertex is provided.
     *
     * @expectedException \Gliph\Exception\InvalidVertexTypeException
     */
    public function testAddStringVertex() {
        $this->g->addVertex('a');
    }

    /**
     * Tests that an exception is thrown if an integer vertex is provided.
     *
     * @expectedException \Gliph\Exception\InvalidVertexTypeException
     */
    public function testAddIntegerVertex() {
        $this->g->addVertex(1);
    }

    /**
     * Tests that an exception is thrown if a float vertex is provided.
     *
     * @expectedException \Gliph\Exception\InvalidVertexTypeException
     */
    public function testAddFloatVertex() {
        $this->g->addVertex((float) 1);
    }

    /**
     * Tests that an exception is thrown if an array vertex is provided.
     *
     * @expectedException \Gliph\Exception\InvalidVertexTypeException
     */
    public function testAddArrayVertex() {
        $this->g->addVertex(array());
    }

    /**
     * Tests that an exception is thrown if a resource vertex is provided.
     *
     * @expectedException \Gliph\Exception\InvalidVertexTypeException
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

    /**
     * @expectedException OutOfBoundsException
     */
    public function testRemoveNonexistentVertex() {
        $this->g->removeVertex($this->v['a']);
    }
}
