<?php

namespace Gliph\Graph;

/**
 * @coversDefaultClass \Gliph\Graph\AdjacencyList
 */
class AdjacencyListTest extends AdjacencyListBase {

    protected $v = array();

    /**
     * @var AdjacencyList
     */
    protected $g;

    public function setUp() {
        parent::setUp();
        $this->g = $this->getMockForAbstractClass('Gliph\Graph\AdjacencyList');
    }

    /**
     * Data provider of non-object types for invalidation.
     *
     * @return array
     */
    public function invalidVertexTypesProvider() {
        return array(
            array('a'),
            array(1),
            array((float) 1.1),
            array(array()),
            array(fopen(__FILE__, 'r')),
            array(FALSE),
            array(NULL),
        );
    }

    /**
     * @expectedException \Gliph\Exception\InvalidVertexTypeException
     * @dataProvider invalidVertexTypesProvider
     */
    public function testInvalidVertexTypes($invalid_vertex) {
        $this->g->addVertex($invalid_vertex);
    }

    /**
     * @covers ::addVertex
     */
    public function testAddVertex() {
        extract($this->v);
        $this->g->addVertex($a);

        $this->assertAttributeContains($a, 'vertices', $this->g);
    }

    /**
     * @depends testAddVertex
     * @covers ::eachVertex
     * @covers ::fev
     * @covers ::walkSplos
     */
    public function testEachVertex() {
        extract($this->v);
        $this->g->addVertex($a);
        $this->g->addVertex($b);

        $found = array();
        $this->g->eachVertex(
            function ($vertex) use (&$found) {
                $found[] = $vertex;
            }
        );

        $this->assertEquals(array($a, $b), $found);

        // Now, test nested iteration
        $found = array();
        $g = $this->g;
        $this->g->eachVertex(
                function ($vertex) use (&$found, $g) {
                    $found[] = $vertex;
                    $g->eachVertex(
                            function ($vertex) use (&$found) {
                                $found[] = $vertex;
                            }
                    );
                }
        );
        $this->assertEquals(array($a, $a, $b, $b, $a, $b), $found);
    }

    /**
     * @depends testAddVertex
     * @covers ::hasVertex
     */
    public function testHasVertex() {
        extract($this->v);
        $this->assertFalse($this->g->hasVertex($a));

        $this->g->addVertex($a);
        $this->assertTrue($this->g->hasVertex($a));
    }

    /**
     * @depends testHasVertex
     * @covers ::addVertex
     */
    public function testAddVertexTwice() {
        // Adding a vertex twice should be a no-op.
        $this->g->addVertex($this->v['a']);
        $this->g->addVertex($this->v['a']);

        $this->assertTrue($this->g->hasVertex($this->v['a']));
        $this->assertVertexCount(1, $this->g);
    }

    /**
     * @expectedException \Gliph\Exception\NonexistentVertexException
     */
    public function testEachAdjacentMissingVertex() {
        $this->g->eachAdjacent($this->v['a'], function() {});
    }

    /**
     * @depends testAddVertex
     * @covers ::order
     */
    public function testOrder() {
        extract($this->v);
        $this->g->addVertex($a);

        $this->assertEquals(1, $this->g->order());
    }
}
