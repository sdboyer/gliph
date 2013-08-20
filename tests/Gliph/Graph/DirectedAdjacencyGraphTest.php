<?php

namespace Gliph\Graph;

class DirectedAdjacencyGraphTest extends AdjacencyGraphTest {

    /**
     * @var DirectedAdjacencyGraph
     */
    protected $g;

    public function setUp() {
        parent::setUp();
        $this->g = new DirectedAdjacencyGraph();
    }


    public function testAddDirectedEdge() {
        $this->g->addDirectedEdge($this->v['a'], $this->v['b']);

        $this->doCheckVerticesEqual(array($this->v['a'], $this->v['b']), $this->g);
    }

    public function testRemoveVertex() {
        $this->g->addDirectedEdge($this->v['a'], $this->v['b']);
        $this->doCheckVertexCount(2);

        $this->g->removeVertex($this->v['b']);
        $this->doCheckVertexCount(1);

        // Ensure that b was correctly removed from a's outgoing edges
        $found = array();
        $this->g->eachAdjacent($this->v['a'], function($to) use (&$found) {
            $found[] = $to;
        });

        $this->assertEquals(array(), $found);
    }


    public function testRemoveEdge() {
        $this->g->addDirectedEdge($this->v['a'], $this->v['b']);
        $this->doCheckVerticesEqual(array($this->v['a'], $this->v['b']), $this->g);

        $this->g->removeEdge($this->v['a'], $this->v['b']);
        $this->doCheckVertexCount(2);

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

    public function testTranspose() {
        $this->g->addDirectedEdge($this->v['a'], $this->v['b']);
        $this->g->addDirectedEdge($this->v['a'], $this->v['c']);

        $transpose = $this->g->transpose();

        $this->doCheckVertexCount(3, $transpose);
        $this->doCheckVerticesEqual(array($this->v['b'], $this->v['a'], $this->v['c']), $transpose);
    }
}
