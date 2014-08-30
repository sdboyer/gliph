<?php

namespace Gliph\Graph\TestTraits;

use Gliph\TestVertex;

/**
 * A standard vertex set and invalid vertex set for graphs that work only with
 * object vertices.
 */
trait ObjectVertices {

    /**
     * @var array
     *
     * An array of test vertices.
     */
    protected $v;

    /**
     * Returns a set of vertices for testing.
     */
    protected function getTestVertices() {
        if (!is_array($this->v)) {
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

        return $this->v;
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
}
