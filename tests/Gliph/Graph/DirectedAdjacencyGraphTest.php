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
        $this->g->addVertex($this->v['a']);
        $this->doCheckVertexCount(1);

        $this->g->removeVertex($this->v['a']);
        $this->doCheckVertexCount(0);
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

}
