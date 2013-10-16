<?php

namespace Gliph\Visitor;

abstract class SimpleStatefulDepthFirstVisitorTestBase extends \PHPUnit_Framework_TestCase {

    abstract public function createInProgressVisitor();
    abstract public function createCompletedVisitor();

    /**
     * Returns a list of all methods that require visitor state to be COMPLETE.
     *
     * @return array
     */
    public function completionRequiredMethods() {
        return array();
    }

    public function testEndTraversal() {
        $vis = $this->createInProgressVisitor();

        $vis->endTraversal();
        $this->assertEquals(StatefulVisitorInterface::COMPLETE, $vis->getState());
    }

    /**
     * @expectedException \Gliph\Exception\WrongVisitorStateException
     */
    public function testEndTraversalAlreadyEnded() {
        $vis = $this->createCompletedVisitor();

        $vis->endTraversal();
    }

    /**
     * @dataProvider completionRequiredMethods
     * @expectedException \Gliph\Exception\WrongVisitorStateException
     */
    public function testCompletionRequiredMethods($method, $args) {
        call_user_func_array(array($this->createInProgressVisitor(), $method), $args);
    }
}
