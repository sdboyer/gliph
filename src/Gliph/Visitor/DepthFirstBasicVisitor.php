<?php

namespace Gliph\Visitor;

use Gliph\Exception\WrongVisitorStateException;

/**
 * Basic depth-first visitor.
 *
 * This visitor records reachability data for each vertex and creates a
 * topologically sorted list.
 */
class DepthFirstBasicVisitor extends DepthFirstToposortVisitor {

    /**
     * @var \SplObjectStorage
     */
    public $active;

    /**
     * @var \SplObjectStorage
     */
    protected $paths;

    /**
     * @var \SplObjectStorage
     */
    protected $sources;

    public function __construct() {
        $this->active = new \SplObjectStorage();
        $this->paths = new \SplObjectStorage();
        $this->sources = new \SplObjectStorage();
    }

    public function onInitializeVertex($vertex, $source, \SplQueue $queue) {
        parent::onInitializeVertex($vertex, $source, $queue);

        $this->paths[$vertex] = array();
    }

    public function onStartVertex($vertex, \Closure $visit) {
        parent::onStartVertex($vertex, $visit);

        $this->active->attach($vertex);
        if (!isset($this->paths[$vertex])) {
            $this->paths[$vertex] = array();
        }

        // Initialize the sources array for the case of vertices that have no
        // edges to them.
        if (!isset($this->sources[$vertex])) {
          $this->sources[$vertex] = array();
        }
    }

    public function onExamineEdge($from, $to, \Closure $visit) {
        parent::onExamineEdge($from, $to, $visit);

        // Initialize the sources array, as onExamineEdge() is called for a
        // vertext before onStartVertex().
        if (!isset($this->sources[$to])) {
            $this->sources[$to] = array();
        }

        foreach ($this->active as $vertex) {
            // TODO this check makes this less efficient - find a better algo
            if (!in_array($to, $this->paths[$vertex], TRUE)) {
                $path = $this->paths[$vertex];
                $path[] = $to;
                $this->paths[$vertex] = $path;
            }

            // Add all the active vertices to the source list for the edge's
            // destination vertex.
            if (!in_array($vertex, $this->sources[$to], TRUE)) {
                $source = $this->sources[$to];
                $source[] = $vertex;
                $this->sources[$to] = $source;
            }
        }
    }

    public function onFinishVertex($vertex, \Closure $visit) {
        parent::onFinishVertex($vertex, $visit);

        $this->active->detach($vertex);
    }

    /**
     * Returns an array of all vertices reachable from the given vertex.
     *
     * @param object $vertex
     *   The vertex for which reachability data is desired.
     *
     * @return array|bool
     *   An array of reachable vertices, or FALSE if the vertex could not be
     *   found in the reachability data. Note that an empty array will be
     *   returned for vertices that zero reachable vertices. This is a different
     *   from FALSE, so the identity operator (===) should be used to verify
     *   returns.
     *
     * @throws WrongVisitorStateException
     *   Thrown if reachability data is requested before the traversal algorithm
     *   completes.
     */
    public function getReachable($vertex) {
        if ($this->getState() !== self::COMPLETE) {
            throw new WrongVisitorStateException('Correct reachability data cannot be retrieved until traversal is complete.');
        }

        if (!isset($this->paths[$vertex])) {
            return FALSE;
        }

        return $this->paths[$vertex];
    }

    /**
     * Returns an array of all vertices that reach the given vertex.
     *
     * @param object $vertex
     *   The vertex for which reachability data is desired.
     *
     * @return array|bool
     *   An array of reaching vertices, or FALSE if the vertex could not be
     *   found in the reachability data. Note that an empty array will be
     *   returned for vertices that zero reaching vertices. This is a different
     *   from FALSE, so the identity operator (===) should be used to verify
     *   returns.
     *
     * @throws WrongVisitorStateException
     *   Thrown if reachability data is requested before the traversal algorithm
     *   completes.
     */
    public function getReaching($vertex) {
      if ($this->getState() !== self::COMPLETE) {
          throw new WrongVisitorStateException('Correct reachability data cannot be retrieved until traversal is complete.');
      }

      if (!isset($this->sources[$vertex])) {
          return FALSE;
      }

      return $this->sources[$vertex];
  }

}